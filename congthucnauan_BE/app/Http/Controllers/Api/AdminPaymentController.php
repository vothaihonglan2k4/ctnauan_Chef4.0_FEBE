<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'enrollment.course']);
        
        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Search by transaction_id or user name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $payments = $query->orderBy('created_at', 'desc')->get();
        
        // Statistics
        $stats = [
            'total' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
        ];
        
        return response()->json([
            'payments' => $payments,
            'stats' => $stats
        ]);
    }

    public function show($id)
    {
        $payment = Payment::with(['user', 'enrollment.course', 'logs'])->findOrFail($id);
        return response()->json(['payment' => $payment]);
    }

    public function updateStatus(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded'
        ]);
        
        $payment->update(['status' => $request->status]);
        
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công',
            'payment' => $payment->fresh(['user', 'enrollment.course'])
        ]);
    }

    public function statistics()
    {
        $stats = [
            'total_payments' => Payment::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'by_method' => Payment::where('status', 'completed')
                ->selectRaw('payment_method, count(*) as count, sum(amount) as total')
                ->groupBy('payment_method')
                ->get(),
            'recent_7_days' => Payment::where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(7))
                ->sum('amount'),
        ];
        
        return response()->json(['stats' => $stats]);
    }
}
