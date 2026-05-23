<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentStatusRequest;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Get all payments for authenticated user
     */
    public function index(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'payments' => $payments->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                    'transaction_id' => $payment->transaction_id,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at
                ];
            })
        ]);
    }

    /**
     * Get single payment
     */
    public function show(Request $request, $id)
    {
        $payment = Payment::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'transaction_id' => $payment->transaction_id,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at
            ]
        ]);
    }

    /**
     * Create new payment
     */
    public function store(StorePaymentRequest $request)
    {
        // Generate transaction ID
        $transactionId = 'TXN' . time() . rand(1000, 9999);

        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'transaction_id' => $transactionId
        ]);

        return response()->json([
            'message' => 'Thanh toán đã được tạo',
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'transaction_id' => $payment->transaction_id,
                'created_at' => $payment->created_at
            ]
        ], 201);
    }

    /**
     * Update payment status
     */
    public function updateStatus(UpdatePaymentStatusRequest $request, $id)
    {
        $payment = Payment::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $payment->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Cập nhật trạng thái thanh toán thành công',
            'payment' => [
                'id' => $payment->id,
                'status' => $payment->status,
                'updated_at' => $payment->updated_at
            ]
        ]);
    }

    /**
     * Get payment statistics
     */
    public function statistics(Request $request)
    {
        $userId = $request->user()->id;

        $totalPayments = Payment::where('user_id', $userId)->count();
        $completedPayments = Payment::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();
        $totalAmount = Payment::where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('amount');

        return response()->json([
            'statistics' => [
                'total_payments' => $totalPayments,
                'completed_payments' => $completedPayments,
                'total_amount' => $totalAmount,
                'pending_payments' => Payment::where('user_id', $userId)
                    ->where('status', 'pending')
                    ->count()
            ]
        ]);
    }
}
