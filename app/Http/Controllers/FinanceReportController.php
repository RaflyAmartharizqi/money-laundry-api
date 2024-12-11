<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionOrderModel;
use App\Models\User;
use Carbon\Carbon;
use App\Constants\Messages;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Validator;   

class FinanceReportController extends Controller
{
    public function index($userId)
    {
        try 
        {
            $transactionsFinance = TransactionOrderModel::selectRaw("
                DATE(payment_date) as payment_date,
                DAYNAME(payment_date) as order_day,
                SUM(total_price) as total_money
            ")
            ->where('payment_date', '>=', Carbon::now()->subDays(6)->startOfDay()) // 7 hari terakhir
            ->where('users_id', $userId)
            ->where('payment_status', 'paid')
            ->groupByRaw('DATE(payment_date), DAYNAME(payment_date)')
            ->orderBy('payment_date', 'asc')
            ->get();
            
            // Generate daftar tanggal untuk 7 hari terakhir dan tambahkan transaksi
            $financeReport = [];
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::now()->subDays(6 - $i); // Iterasi dari 7 hari terakhir
                $formattedDate = $date->format('Y-m-d');
                $financeReport[] = [
                    'payment_date' => $formattedDate,
                    'order_day' => $date->format('l'), // Nama hari
                    'total_money' => $transactionsFinance->firstWhere('payment_date', $formattedDate)['total_money'] ?? 0
                ];
            }

                        // Ambil data transaksi dari database
            $transactions = TransactionOrderModel::selectRaw("
                DATE(order_date) as order_date,
                DAYNAME(order_date) as order_day,
                COUNT(*) as total_transaction
            ")
            ->where('order_date', '>=', Carbon::now()->subDays(6)->startOfDay()) // 7 hari terakhir
            ->where('users_id', $userId)
            ->groupByRaw('DATE(order_date), DAYNAME(order_date)')
            ->orderBy('order_date', 'asc')
            ->get();
            
            // Generate daftar tanggal untuk 7 hari terakhir dan tambahkan transaksi
            $totalTransaction = [];
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::now()->subDays(6 - $i); // Iterasi dari 7 hari terakhir
                $formattedDate = $date->format('Y-m-d');
                $totalTransaction[] = [
                    'order_date' => $formattedDate,
                    'order_day' => $date->format('l'), // Nama hari
                    'total_transaction' => $transactions->firstWhere('order_date', $formattedDate)['total_transaction'] ?? 0
                ];
            }

            $transactionOrderNew = TransactionOrderModel::where('users_id', $userId)
                ->where('status', 'new')
                ->count();
        
            $transactionOrderOnProcess = TransactionOrderModel::where('users_id', $userId)
                ->where('status', 'on process')
                ->count();
            
            $transactionOrderDone = TransactionOrderModel::where('users_id', $userId)
                ->where('status', 'done')
                ->count();
        
            // Mendapatkan total_price untuk bulan ini
            $transactionOrderIncome = TransactionOrderModel::where('users_id', $userId)
                ->where('payment_status', 'paid')
                ->whereMonth('payment_date', Carbon::now()->month)
                ->whereYear('payment_date', Carbon::now()->year)
                ->sum('total_price');

            $data = [
                'total_money' => $financeReport,
                'total_transaction' => $totalTransaction,
                'transaction_order_new' => $transactionOrderNew,
                'transaction_order_on_process' => $transactionOrderOnProcess,
                'transaction_order_done' => $transactionOrderDone,
                'transaction_order_income' => $transactionOrderIncome,
            ];
            return ApiResponse::success(Messages::SUCCESS_GET_DATA, 200, $data);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_GET_DATA, 404, $e->getMessage());
        }
    }
}
