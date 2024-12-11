<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Helpers\ApiResponse;
use App\Models\OtpModel;
use App\Mail\OtpMail;
use App\Constants\Messages;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminModel;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function loginAdmin(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);
            if ($validator->fails()) {
                return ApiResponse::error(Messages::ERROR_VALIDATION, 400, $validator->errors());
            }
    
            if (!Auth::guard('admin')->attempt($request->only('email', 'password'))) {
                return ApiResponse::error(Messages::ERROR_UNAUTHORIZED, 404);
            }
    
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('auth_token')->plainTextToken;
    
            $data = [
                "token" => $token,
                "admin" => $admin,
            ];
    
            return ApiResponse::success(Messages::SUCCESS_LOGIN, 200, $data);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_UNAUTHORIZED, 404, $e->getMessage());
        }
        
    }

    public function loginUser(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);
            if ($validator->fails()) {
                return ApiResponse::error(Messages::ERROR_VALIDATION, 400, $validator->errors());
            }
    
            if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
                return ApiResponse::error(Messages::ERROR_UNAUTHORIZED, 404);
            }
    
            $user = Auth::guard('web')->user();
            $token = $user->createToken('auth_token')->plainTextToken;

            $data = [
                "token" => $token,
                "user" => $user,
            ];
    
            return ApiResponse::success(Messages::SUCCESS_LOGIN, 200, $data);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_UNAUTHORIZED, 404, $e->getMessage());
        }
    }

    public function registerUser(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'account_status_id' => 'integer|max:255',
                'store_name' => 'required|string|max:255',
                'store_address' => 'required|string|max:255',
                'phone_number' => 'required|string|max:255',
            ]);
    
            if ($validator->fails()) {
                return ApiResponse::error(Messages::ERROR_VALIDATION, 400, $validator->errors());
            }

            $users = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'account_status_id' => $request->account_status_id,
                'store_name' => $request->store_name,
                'store_address' => $request->store_address,
                'phone_number' => $request->phone_number,
            ]);

            return ApiResponse::success(Messages::SUCCESS_REGISTER, 200, $users);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_REGISTER, 404, $e->getMessage());
        }
    }

    public function sendForgotPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return ApiResponse::error(Messages::ERROR_NOT_FOUND, 404);
        }
    
        $otp = rand(1000, 9999);
        OtpModel::updateOrCreate(
            ['email' => $request->email, 'type' => 'forgot_password'],
            ['otp' => $otp, 'created_at' => now()]
        );
    
        // Send OTP email
        Mail::to($request->email)->send(new OtpMail($otp));
    
        return ApiResponse::success(Messages::SUCCESS_OTP_SENT, 200, null);
    }

    public function verifyForgotPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|integer',
        ]);

        $otpRecord = OtpModel::where('email', $request->email)
                            ->where('otp', $request->otp)
                            ->where('type', 'forgot_password')
                            ->first();
        if (!$otpRecord) {
            return ApiResponse::error(Messages::ERROR_INVALID_OTP, 400);
        }

        return ApiResponse::success(Messages::SUCCESS_OTP_VERIFIED, 200, null);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|integer',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        $otpRecord = OtpModel::where('email', $request->email)
                             ->where('otp', $request->otp)
                             ->where('type', 'forgot_password')
                             ->first();
    
        if (!$otpRecord) {
            return ApiResponse::error(Messages::ERROR_INVALID_OTP, 400);
        }
    
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
    
        $otpRecord->delete();
    
        return ApiResponse::success(Messages::SUCCESS_PASSWORD_RESET, 200, null);
    }

    public function resendForgotPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse::error(Messages::ERROR_NOT_FOUND, 404);
        }

        $otpRecord = OtpModel::where('email', $request->email)
                            ->where('type', 'forgot_password')
                            ->first();

        if ($otpRecord && $otpRecord->created_at->diffInMinutes(now()) < 5) {
            $otp = $otpRecord->otp;
        } else {
            $otp = rand(1000, 9999);
            OtpModel::updateOrCreate(
                ['email' => $request->email, 'type' => 'forgot_password'],
                ['otp' => $otp, 'created_at' => now()]
            );
        }

        Mail::to($request->email)->send(new OtpMail($otp));

        return ApiResponse::success(Messages::SUCCESS_OTP_RESENT, 200, null);
    }
}
