<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionOrderModel;
use App\Models\User;
use Carbon\Carbon;
use App\Constants\Messages;
use App\Helpers\ApiResponse;

class DashboardUserController extends Controller
{
    public function index($userId) 
    {
        try 
        {
            $transactionOrder = TransactionOrderModel::whereDate('order_date', Carbon::today())->where('users_id', $userId)->count();
            $transactionOrderPaid = TransactionOrderModel:: where('payment_status', 'paid')->
                whereDate('order_date', Carbon::today())
                ->where('users_id', $userId)
                ->sum('total_price');
            
            $data = [
                'number_of_transactions' => $transactionOrder,
                'total_transaction_paid' => $transactionOrderPaid,
            ];

            return ApiResponse::success(Messages::SUCCESS_GET_DATA, 200, $data);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_GET_DATA, 404, $e->getMessage());
        }
    }
}
