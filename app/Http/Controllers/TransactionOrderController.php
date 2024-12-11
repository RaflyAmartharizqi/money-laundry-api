<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiResponse;
use App\Constants\Messages;
use App\Models\TransactionOrderModel;
use App\Models\AddOnItemModel;
use App\Models\CustomerModel;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class TransactionOrderController extends Controller
{
    public function index($usersId)
    {
        try 
        {
            $transactionOrder = TransactionOrderModel::where('users_id', $usersId)->with(['package_laundry', 'customer', 'add_on_item'])->orderBy('order_date', 'desc')->get();
            $transactionOrderCount = $transactionOrder->count();

            $data = [
                'total_data_transaction' => $transactionOrderCount,
                'transaction_order' => $transactionOrder
            ];
            return ApiResponse::success(Messages::SUCCESS_GET_DATA, 200, $data);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_GET_DATA, 404, $e->getMessage());
        }
    }

    public function transactionOrderDetail($transactionOrderId)
    {
        try 
        {
            $transactionOrder = TransactionOrderModel::with(['package_laundry', 'customer', 'add_on_item'])->find($transactionOrderId);
            if (is_null($transactionOrder)) {
                return ApiResponse::error(Messages::ERROR_NOT_FOUND, 404, null);
            }

            return ApiResponse::success(Messages::SUCCESS_GET_DATA, 200, $transactionOrder);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_GET_DATA, 404, $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required|string',
                'customer_phone_number' => 'required|string',
                'users_id' => 'required|integer|exists:users,users_id',
                'package_laundry_id' => 'required|integer|exists:package_laundry,package_laundry_id',
                'order_date' => 'required|date',
                'pick_up_date' => 'required|date|after_or_equal:order_date',
                'status' => 'required|in:new, on process, done',
                'payment_status' => 'required|in:paid,unpaid',
                'weight' => 'required|integer',
                'subtotal' => 'required|integer',
                'subtotal_add_on_item' => 'nullable|integer',
                'total_price' => 'required|integer',
                'quantity' => 'required|integer',
                'add_on_item' => 'nullable|array',
                'add_on_item.*.item_name' => 'required|string',
                'add_on_item.*.quantity' => 'required|integer',
                'add_on_item.*.price_per_item' => 'required|integer',
                'add_on_item.*.subtotal' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(Messages::ERROR_VALIDATION, 400, $validator->errors());
            }

            $customer = CustomerModel::create([
                'users_id' => $request->users_id,
                'name' => $request->customer_name,
                'phone_number' => $request->customer_phone_number,
            ]);

            if ($request->payment_status == "paid") {
                $paymentDate = Carbon::now();
            }

            $transactionOrder = TransactionOrderModel::create([
                'users_id' => $request->users_id,
                'customer_id' => $customer->customer_id,
                'package_laundry_id' => $request->package_laundry_id,
                'order_date' => $request->order_date,
                'pick_up_date' => $request->pick_up_date,
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'payment_date' => $paymentDate ?? null,
                'weight' => $request->weight,
                'subtotal' => $request->subtotal,
                'subtotal_add_on_item' => $request->subtotal_add_on_item,
                'total_price' => $request->total_price,
                'quantity' => $request->quantity,
            ]);

            if ($request->add_on_item != null) {
                foreach ($request->add_on_item as $addOnItem) {
                    $transactionOrder->add_on_item()->create([
                        'item_name' => $addOnItem['item_name'],
                        'quantity' => $addOnItem['quantity'],
                        'price_per_item' => $addOnItem['price_per_item'],
                        'subtotal' => $addOnItem['subtotal'],
                    ]);
                }
            }

            $transactionOrder->load(['add_on_item', 'customer']);
            

            return ApiResponse::success(Messages::SUCCESS_CREATE_TRANSACTION_ORDER, 200, $transactionOrder);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_CREATE_TRANSACTION_ORDER, 404, $e->getMessage());
        }
    }

    public function updatePayment(Request $request, $transactionOrderId)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'payment_status' => 'required|in:paid,unpaid',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(Messages::ERROR_VALIDATION, 400, $validator->errors());
            }

            $transactionOrder = TransactionOrderModel::find($transactionOrderId);
            if (is_null($transactionOrder)) {
                return ApiResponse::error(Messages::ERROR_NOT_FOUND, 404, null);
            }

            if ($request->payment_status == "paid") {
                $paymentDate = Carbon::now();
            }

            $transactionOrder->update([
                'payment_status' => $request->payment_status,
                'payment_date' => $paymentDate,
            ]);

            return ApiResponse::success(Messages::SUCCESS_UPDATE_PAYMENT, 200, $transactionOrder);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_UPDATE_PAYMENT, 404, $e->getMessage());
        }
    }

    public function sendInvoice(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'transaction_order_id' => 'required|integer|exists:transaction_order,transaction_order_id',
            ]);

            $transactionOrder = TransactionOrderModel::where('transaction_order_id', $request->transaction_order_id)
                            ->with('customer')->first();
            $message = $this->generateMessage($request->transaction_order_id);
            $encodedMessage = urlencode($message);

            // Generate WhatsApp URL
            $phoneNumber = $transactionOrder->customer->phone_number;
            if (str_starts_with($phoneNumber, '0')) {
                // Ganti "0" di depan dengan "62"
                $phoneNumber = '62' . substr($phoneNumber, 1);
            }
            $whatsAppUrl = "https://wa.me/{$phoneNumber}?text={$encodedMessage}";

            return ApiResponse::success(Messages::SUCCESS_SEND_WA, 200, $whatsAppUrl);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_SEND_WA, 404, $e->getMessage());
        }
    }

    private function generateMessage($transactionOrderId)
    {
        $transactionOrder = TransactionOrderModel::where('transaction_order_id', $transactionOrderId)
            ->with(['customer', 'add_on_item', 'package_laundry'])
            ->first();

        $days = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];

        $orderDate = date('l, d-m-Y', strtotime($transactionOrder->order_date));
        $pickUpDate = date('l, d-m-Y', strtotime($transactionOrder->pick_up_date));

        // Ganti nama hari
        $orderDate = str_replace(array_keys($days), array_values($days), $orderDate);
        $pickUpDate = str_replace(array_keys($days), array_values($days), $pickUpDate);
    
        return <<<EOT
    *Invoice Money Laundry*
    
    ========== Customer ==========
    
    Nama: {$transactionOrder->customer->name}
    Nomor Telepon: {$transactionOrder->customer->phone_number}
        
    ======== Paket Laundry ========
    
    Nama Paket: {$transactionOrder->package_laundry->name}
    Harga Paket: Rp. {$transactionOrder->package_laundry->price_per_kg} / KG
    Berat: {$transactionOrder->weight} Kg
    Subtotal: Rp. {$transactionOrder->subtotal}
        
    ======== Item Tambahan ========

    {$this->generateAddOnItems($transactionOrder->add_on_item)}
    Subtotal Tambahan Item: Rp. {$transactionOrder->subtotal_add_on_item}
    
    ======= Invoice Laundry ========
    
    Order Id: {$transactionOrder->transaction_order_id}
    Tanggal Order: {$orderDate}
    *Tanggal Pick Up: {$pickUpDate}*
    Status: {$transactionOrder->status}
    Status Pembayaran: {$transactionOrder->payment_status}
    Subtotal: Rp. {$transactionOrder->subtotal}
    Subtotal Tambahan Item: Rp. {$transactionOrder->subtotal_add_on_item}
    Jumlah Item: {$transactionOrder->quantity}

    *| Total Pembayaran: Rp. {$transactionOrder->total_price} |*
    
    ==============================
    
    Thank you!
    
    Money Laundry
    EOT;
    }
    
    private function generateAddOnItems($addOnItems)
    {
        if ($addOnItems->isEmpty()) {
            return "Tidak ada item tambahan.";
        }
    
        $output = "";
        foreach ($addOnItems as $item) {
            $output .= 
            "Nama Item: " . ($item->item_name ?? '-') . "\n" .
            "Harga: Rp. " . ($item->price_per_item ?? 0) . "\n" .
            "Jumlah Item: " . ($item->quantity ?? '-') . "\n" .
            "Subtotal: Rp. " . ($item->subtotal ?? 0) . "\n\n";
        }
        return $output;
    }

    public function updateStatusOrder(Request $request, $transactionOrderId)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'status' => 'required',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(Messages::ERROR_VALIDATION, 400, $validator->errors());
            }

            $transactionOrder = TransactionOrderModel::find($transactionOrderId);
            if (is_null($transactionOrder)) {
                return ApiResponse::error(Messages::ERROR_NOT_FOUND, 404, null);
            }

            $transactionOrder->update([
                'status' => $request->status,
            ]);

            return ApiResponse::success(Messages::SUCCESS_UPDATE_STATUS_ORDER, 200, $transactionOrder);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_UPDATE_STATUS_ORDER, 404, $e->getMessage());
        }
    }

    public function filterTransactionOrder(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:new,on process,done', // Validasi hanya menerima nilai sesuai ENUM
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'user_id' => 'required|integer|exists:users,users_id',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(Messages::ERROR_VALIDATION, 400, $validator->errors());
            }

            if ($request->start_date == null && $request->end_date == null) {
                $transactionOrder = TransactionOrderModel::where('status', $request->status)
                    ->where('users_id', $request->user_id)
                    ->with(['package_laundry', 'customer', 'add_on_item'])
                    ->orderBy('order_date', 'desc')
                    ->get();
            } elseif ($request->status == null) {
                $transactionOrder = TransactionOrderModel::whereBetween('order_date', [$request->start_date, $request->end_date])
                    ->where('users_id', $request->user_id)
                    ->with(['package_laundry', 'customer', 'add_on_item'])
                    ->orderBy('order_date', 'desc')
                    ->get();
            } else {
                $transactionOrder = TransactionOrderModel::whereBetween('order_date', [$request->start_date, $request->end_date])
                    ->where('status', $request->status)
                    ->where('users_id', $request->user_id)
                    ->with(['package_laundry', 'customer', 'add_on_item'])
                    ->orderBy('order_date', 'desc')
                    ->get();
            }

            $transactionOrderCount = $transactionOrder->count();

            $data = [
                'total_data_transaction' => $transactionOrderCount,
                'transaction_order' => $transactionOrder
            ];
            return ApiResponse::success(Messages::SUCCESS_GET_DATA, 200, $data);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_GET_DATA, 404, $e->getMessage());
        }
    }
    
}
