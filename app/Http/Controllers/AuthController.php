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
            return ApiResponse::error(Messages::ERROR_UNAUTHORIZED, 404);
        }
        
    }
}
