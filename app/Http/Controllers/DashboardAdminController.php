<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionMemberModel;
use App\Models\TransactionOrderModel;
use App\Models\User;
use Carbon\Carbon;
use App\Constants\Messages;
use App\Helpers\ApiResponse;

class DashboardAdminController extends Controller
{
    public function index() 
    {
        try 
        {
            $transactionOrderPaid = TransactionOrderModel::where('payment_status', 'paid')->count();
            $transactionOrder = TransactionOrderModel::count();

            $transactionMember = TransactionMemberModel::count();

            $transactionMemberIncomes = (int) TransactionMemberModel::sum('total_price');

            // Ambil data transaksi dari database
            $transactions = TransactionOrderModel::selectRaw("
                DATE(order_date) as order_date,
                DAYNAME(order_date) as order_day,
                COUNT(*) as total_transaction
            ")
            ->where('order_date', '>=', Carbon::now()->subDays(6)->startOfDay()) // 7 hari terakhir
            ->groupByRaw('DATE(order_date), DAYNAME(order_date)')
            ->orderBy('order_date', 'asc')
            ->get();
            
            // Generate daftar tanggal untuk 7 hari terakhir dan tambahkan transaksi
            $dates = [];
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::now()->subDays(6 - $i); // Iterasi dari 7 hari terakhir
                $formattedDate = $date->format('Y-m-d');
                $dates[] = [
                    'order_date' => $formattedDate,
                    'order_day' => $date->format('l'), // Nama hari
                    'total_transaction' => $transactions->firstWhere('order_date', $formattedDate)['total_transaction'] ?? 0
                ];
            }
            

            $user = User::count();
            $userBasic = User::where('account_status_id', 1)->count();
            $userSilver = User::where('account_status_id', 2)->count();
            $userGold = User::where('account_status_id', 3)->count();
            $userBasicPercentage = $user > 0 ? round(($userBasic / $user) * 100) : 0;
            $userSilverPercentage = $user > 0 ? round(($userSilver / $user) * 100) : 0; 
            $userGoldPercentage = $user > 0 ? round(($userGold / $user) * 100) : 0; 

            $data = [
                'total_transaction' => [
                    'total_order_paid' => $transactionOrderPaid,
                    'total_order' => $transactionOrder,
                ],
                'transaction_member' => $transactionMember,
                'transaction_member_incomes' => $transactionMemberIncomes,
                'user' => [
                    'user_basic_percentage' => $userBasicPercentage,
                    'user_silver_percentage' => $userSilverPercentage,
                    'user_gold_percentage' => $userGoldPercentage,
                ],
                'weekly_transaction' => $dates,
            ];

            return ApiResponse::success(Messages::SUCCESS_GET_DATA, 200, $data);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_GET_DATA, 404, $e->getMessage());
        }
    }
}
