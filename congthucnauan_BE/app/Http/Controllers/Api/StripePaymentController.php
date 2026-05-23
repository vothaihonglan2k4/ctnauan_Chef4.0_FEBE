<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripePaymentController extends Controller
{
    public function __construct()
    {
        // Set Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET_KEY', 'sk_test_51SQAg40j0Ekxnzx57CHM5aOsSp7NhRfrdSsPhWjrc65cFAy9q3eOkow6k3W5APxvsPvrLAtGy7VSZXXm54ReKUFA00RBa6EfEd'));
    }

    /**
     * Create Stripe Checkout Session
     */
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string'
        ]);

        $user = $request->user();
        $amount = (int) $request->amount;
        $description = $request->description ?? 'Thanh toán gói thành viên';

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_method' => 'stripe',
            'status' => 'pending',
            'transaction_id' => 'STRIPE_' . time() . rand(1000, 9999)
        ]);

        // Create pending enrollment if course_id is present
        if ($request->has('metadata') && isset($request->metadata['course_id'])) {
            \App\Models\CourseEnrollment::create([
                'user_id' => $user->id,
                'course_id' => $request->metadata['course_id'],
                'payment_id' => $payment->id,
                'status' => 'pending',
                'progress' => 0,
                'enrollment_date' => now()
            ]);
        }

        try {
            $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:3000'), '/');

            // Create Stripe Checkout Session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'vnd',
                        'product_data' => [
                            'name' => $description,
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $frontendUrl . '/payments/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $frontendUrl . '/payments/cancel',
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $user->id
                ]
            ]);

            // Update payment with session ID
            $payment->update([
                'transaction_id' => $session->id
            ]);

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'checkout_url' => $session->url,
                'payment_id' => $payment->id
            ]);

        } catch (\Exception $e) {
            // Delete payment if session creation fails
            $payment->delete();

            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo phiên thanh toán: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Stripe Payment
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $session = Session::retrieve($request->session_id);

            // Find payment by session ID
            $payment = Payment::where('transaction_id', $request->session_id)->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thanh toán'
                ], 404);
            }

            // Update payment status based on Stripe session
            if ($session->payment_status === 'paid') {
                $payment->update([
                    'status' => 'completed'
                ]);

                // Activate enrollment if exists
                $enrollment = \App\Models\CourseEnrollment::where('payment_id', $payment->id)->first();
                if ($enrollment) {
                    $enrollment->update(['status' => 'active']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Thanh toán thành công',
                    'payment' => [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'status' => $payment->status,
                        'transaction_id' => $payment->transaction_id,
                        'course_id' => $enrollment ? $enrollment->course_id : null
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Thanh toán chưa hoàn tất',
                    'payment_status' => $session->payment_status
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực thanh toán: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Stripe Config (for frontend)
     */
    public function getConfig()
    {
        return response()->json([
            'publishable_key' => env('STRIPE_PUBLISHABLE_KEY', 'pk_test_51SQAg40j0Ekxnzx5qwbtky0mR4vqnChgbOgSxluVVDGgiUHkuzfJbfqGiT6UtcLeFVQVgtW0bnWjAN19cORMbGx500BmIx13iS'),
            'currency' => 'vnd'
        ]);
    }

    /**
     * Webhook handler for Stripe events
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            // Handle the event
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    
                    // Update payment status
                    $payment = Payment::where('transaction_id', $session->id)->first();
                    if ($payment) {
                        $payment->update(['status' => 'completed']);
                        
                        // Activate enrollment
                        $enrollment = \App\Models\CourseEnrollment::where('payment_id', $payment->id)->first();
                        if ($enrollment) {
                            $enrollment->update(['status' => 'active']);
                        }
                    }
                    break;

                case 'checkout.session.expired':
                    $session = $event->data->object;
                    
                    // Update payment status
                    $payment = Payment::where('transaction_id', $session->id)->first();
                    if ($payment) {
                        $payment->update(['status' => 'cancelled']);
                    }
                    break;

                default:
                    // Unexpected event type
                    return response()->json(['error' => 'Unexpected event type'], 400);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
