<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionMemberController;
use App\Http\Controllers\PackageLaundryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionOrderController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\FinanceReportController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/admin/login', [AuthController::class, 'loginAdmin']);


Route::post('/user/login', [AuthController::class, 'loginUser']);
Route::post('/user/register', [AuthController::class, 'registerUser']);

Route::post('/user/forgot-password/send-otp', [AuthController::class, 'sendForgotPasswordOtp']);
Route::post('/user/forgot-password/verify-otp', [AuthController::class, 'verifyForgotPasswordOtp']);
Route::post('/user/forgot-password/reset', [AuthController::class, 'resetPassword']);
Route::post('/user/forgot-password/resend-otp', [AuthController::class, 'resendForgotPasswordOtp']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:api')->prefix('/user')->group(function () {

    Route::get('/package-laundry/{usersId}', [PackageLaundryController::class, 'index']);
    Route::post('/package-laundry', [PackageLaundryController::class, 'store']);
    Route::put('/package-laundry/{packageLaundryId}', [PackageLaundryController::class, 'update']);
    Route::delete('/package-laundry/{packageLaundryId}', [PackageLaundryController::class, 'destroy']);

    Route::get('/transaction-order/{usersId}', [TransactionOrderController::class, 'index']);
    Route::get('/transaction-order-detail/{transactionOrderId}', [TransactionOrderController::class, 'transactionOrderDetail']);
    Route::post('/transaction-order', [TransactionOrderController::class, 'store']);
    Route::put('/update-payment/{transactionOrderId}', [TransactionOrderController::class, 'updatePayment']);

    Route::post('/send-invoice-wa', [TransactionOrderController::class, 'sendInvoice']);

    Route::get('/dashboard/{userId}', [DashboardUserController::class, 'index']);

    Route::get('/keuangan/{userId}', [FinanceReportController::class, 'index']);

    Route::post('/filter-transaction-order/{userId}', [TransactionOrderController::class, 'filterTransactionOrder']);
    Route::put('/update-status-order/{transactionOrderId}', [TransactionOrderController::class, 'updateStatusOrder']);

});

Route::middleware('auth:admin-api')->prefix('/admin')->group(function () {
    Route::get('/user', [UserController::class, 'index']);
    Route::delete('/user/{userId}', [UserController::class, 'destroy']);

    Route::post('/transaction-member', [TransactionMemberController::class, 'transactionMember']);
    Route::get('/dashboard', [DashboardAdminController::class, 'index']);

    Route::get('/transaction-order/{usersId}', [TransactionOrderController::class, 'index']);

});
