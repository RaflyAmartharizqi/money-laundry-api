<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiResponse;
use App\Constants\Messages;
use App\Models\TransactionMemberModel;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class TransactionMemberController extends Controller
{
    public function transactionMember(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'users_id' => 'required|integer|exists:users,users_id',
                'subscription_range' => 'required|integer',
                'total_price' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(Messages::ERROR_VALIDATION, 400, $validator->errors());
            }

            $admin = Auth::guard('admin-api')->user();

            $transactionMember = TransactionMemberModel::create([
                'users_id' => $request->users_id,
                'admin_id' => $admin->admin_id,
                'subscription_range' => $request->subscription_range,
                'total_price' => $request->total_price,
            ]);

            $days = $request->subscription_range * 30;
            $user = User::find($request->users_id);
            if ($user->active_until == null) {
                $user->active_until = Carbon::now()->addDays($days);
            } else {
                $user->active_until = Carbon::parse($user->active_until)->addDays($days);
            }
            $user->account_status_id = 2;
            $user->save();

            return ApiResponse::success(Messages::SUCCESS_TRANSACTION_MEMBER, 200, $transactionMember);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_TRANSACTION_MEMBER, 404, $e->getMessage());
        }
    }
}
