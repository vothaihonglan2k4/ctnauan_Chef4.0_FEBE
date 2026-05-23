<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SePayPaymentController extends Controller
{
    private $merchantCode;
    private $secretKey;
    private $environment;
    private $apiBaseUrl;
    private $checkoutBaseUrl;
    private $returnUrl;
    private $notifyUrl;

    public function __construct()
    {
        $this->merchantCode = env('SEPAY_MERCHANT_CODE');
        $this->secretKey = env('SEPAY_SECRET_KEY');
        $this->environment = env('SEPAY_ENV', 'sandbox');

        $this->apiBaseUrl = $this->environment === 'production'
            ? 'https://pgapi.sepay.vn'
            : 'https://pgapi-sandbox.sepay.vn';

        $this->checkoutBaseUrl = $this->environment === 'production'
            ? 'https://pay.sepay.vn'
            : 'https://pay-sandbox.sepay.vn';

        $this->returnUrl = env('SEPAY_RETURN_URL', env('APP_URL') . '/payments/sepay-success');
        $this->notifyUrl = env('SEPAY_NOTIFY_URL', env('APP_URL') . '/api/v1/sepay/ipn');
    }

    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'order_info' => 'nullable|string|max:255',
            'course_id' => 'nullable|integer',
        ]);

        if (!$this->merchantCode || !$this->secretKey) {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu cấu hình SePay trong file .env',
            ], 500);
        }

        $user = $request->user();
        $amount = (int) $request->amount;
        $orderInfo = $request->order_info ?? 'Thanh toán khóa học';
        $invoiceNumber = 'SEPAY_' . time() . '_' . rand(1000, 9999);

        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_method' => 'sepay',
            'status' => 'pending',
            'transaction_id' => $invoiceNumber,
        ]);

        if ($request->filled('course_id')) {
            CourseEnrollment::create([
                'user_id' => $user->id,
                'course_id' => $request->course_id,
                'payment_id' => $payment->id,
                'status' => 'pending',
                'progress' => 0,
                'enrollment_date' => now(),
            ]);
        }

        $checkoutData = [
            'operation' => 'PURCHASE',
            'orderInvoiceNumber' => $invoiceNumber,
            'orderAmount' => $amount,
            'currency' => 'VND',
            'paymentMethod' => 'BANK_TRANSFER',
            'orderDescription' => $orderInfo,
            'customerId' => (string) $user->id,
            'successUrl' => $this->returnUrl,
            'errorUrl' => $this->returnUrl . '?error=1',
            'cancelUrl' => env('FRONTEND_URL', env('APP_URL')),
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->merchantCode . ':' . $this->secretKey),
            ])->timeout(30)->post($this->apiBaseUrl . '/api/v1/checkout/init', $checkoutData);

            $data = $response->json();
            $httpCode = $response->status();

            $payment->update([
                'gateway_response' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);

            if ($httpCode !== 200 || empty($data['data']['checkoutUrl'])) {
                Log::error('SePay checkout init failed', [
                    'status' => $httpCode,
                    'body' => $data,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $data['message'] ?? 'Không thể tạo thanh toán SePay',
                    'sepay_status' => $httpCode,
                ], 500);
            }

            $checkoutResult = $data['data'];

            return response()->json([
                'success' => true,
                'checkout_url' => $checkoutResult['checkoutUrl'],
                'order_id' => $checkoutResult['orderId'] ?? null,
                'payment_id' => $payment->id,
                'transaction_id' => $invoiceNumber,
            ]);
        } catch (\Throwable $e) {
            Log::error('SePay connection error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi kết nối SePay: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function handleReturn(Request $request)
    {
        $orderId = $request->query('orderId') ?? $request->query('order_id');
        $invoiceNumber = $request->query('invoiceNumber') ?? $request->query('invoice_number');

        if (!$orderId && !$invoiceNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu thông tin đơn hàng',
            ], 400);
        }

        if ($request->query('error')) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng đã hủy thanh toán',
            ]);
        }

        $invoiceNumber = $invoiceNumber ?: $orderId;
        $payment = Payment::where('transaction_id', $invoiceNumber)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thanh toán',
            ], 404);
        }

        if ($payment->status === 'completed') {
            $enrollment = CourseEnrollment::where('payment_id', $payment->id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán SePay đã hoàn thành trước đó',
                'payment' => [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'transaction_id' => $payment->transaction_id,
                    'course_id' => $enrollment ? $enrollment->course_id : null,
                ],
            ]);
        }

        $sepayOrderId = $request->query('orderId');
        if ($sepayOrderId) {
            $this->verifyAndProcessPayment($payment, $sepayOrderId);
        }

        $enrollment = CourseEnrollment::where('payment_id', $payment->id)->first();

        return response()->json([
            'success' => $payment->status === 'completed',
            'message' => $payment->status === 'completed'
                ? 'Thanh toán SePay thành công'
                : 'Thanh toán đang được xử lý',
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'transaction_id' => $payment->transaction_id,
                'course_id' => $enrollment ? $enrollment->course_id : null,
            ],
        ]);
    }

    public function handleIpn(Request $request)
    {
        $secretKeyHeader = $request->header('X-Secret-Key');

        if ($secretKeyHeader && $secretKeyHeader !== $this->secretKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $data = $request->all();
        $notificationType = $data['notification_type'] ?? null;

        if (!$notificationType) {
            return response()->json(['success' => false, 'message' => 'Missing notification_type'], 400);
        }

        $invoiceNumber = data_get($data, 'order.order_invoice_number');
        if (!$invoiceNumber) {
            Log::warning('SePay IPN missing order_invoice_number', ['data' => $data]);
            return response()->json(['success' => false, 'message' => 'Missing invoice number'], 400);
        }

        $payment = Payment::where('transaction_id', $invoiceNumber)->first();
        if (!$payment) {
            Log::warning('SePay IPN payment not found', ['invoice_number' => $invoiceNumber]);
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        if ($notificationType === 'ORDER_PAID') {
            $payment->update([
                'status' => 'completed',
                'gateway_transaction_id' => data_get($data, 'order.order_id'),
                'gateway_response' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);

            $enrollment = CourseEnrollment::where('payment_id', $payment->id)->first();
            if ($enrollment && $enrollment->status !== 'active') {
                $enrollment->update(['status' => 'active']);
            }

            Log::info('SePay IPN: payment completed', [
                'payment_id' => $payment->id,
                'invoice_number' => $invoiceNumber,
            ]);
        } elseif ($notificationType === 'TRANSACTION_VOID') {
            $payment->update([
                'status' => 'cancelled',
                'gateway_response' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);

            Log::info('SePay IPN: transaction voided', [
                'payment_id' => $payment->id,
                'invoice_number' => $invoiceNumber,
            ]);
        }

        return response()->json(['success' => true], 200);
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'transaction_id' => 'nullable|string',
        ]);

        $orderId = $request->order_id;
        $invoiceNumber = $request->transaction_id;

        if ($invoiceNumber) {
            $payment = Payment::where('transaction_id', $invoiceNumber)->first();
        } else {
            $payment = Payment::where('gateway_transaction_id', $orderId)->first();
        }

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thanh toán',
            ], 404);
        }

        if ($payment->status === 'completed') {
            $enrollment = CourseEnrollment::where('payment_id', $payment->id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán đã hoàn thành',
                'payment' => [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'course_id' => $enrollment ? $enrollment->course_id : null,
                ],
            ]);
        }

        $this->verifyAndProcessPayment($payment, $orderId);

        $enrollment = CourseEnrollment::where('payment_id', $payment->id)->first();

        return response()->json([
            'success' => $payment->status === 'completed',
            'message' => $payment->status === 'completed'
                ? 'Thanh toán thành công'
                : 'Chưa hoàn tất',
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'course_id' => $enrollment ? $enrollment->course_id : null,
            ],
        ]);
    }

    public function getConfig()
    {
        return response()->json([
            'merchant_code' => $this->merchantCode,
            'environment' => $this->environment,
            'api_base_url' => $this->apiBaseUrl,
            'checkout_base_url' => $this->checkoutBaseUrl,
            'return_url' => $this->returnUrl,
            'notify_url' => $this->notifyUrl,
        ]);
    }

    private function verifyAndProcessPayment(Payment $payment, string $sepayOrderId): void
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->merchantCode . ':' . $this->secretKey),
            ])->timeout(15)->get($this->apiBaseUrl . '/api/v1/orders/' . $sepayOrderId);

            $orderData = $response->json();

            if ($response->successful() && data_get($orderData, 'data.order_status') === 'CAPTURED') {
                $payment->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $sepayOrderId,
                    'gateway_response' => json_encode($orderData, JSON_UNESCAPED_UNICODE),
                ]);

                $enrollment = CourseEnrollment::where('payment_id', $payment->id)->first();
                if ($enrollment && $enrollment->status !== 'active') {
                    $enrollment->update(['status' => 'active']);
                }
            }
        } catch (\Throwable $e) {
            Log::error('SePay verify order failed: ' . $e->getMessage());
        }
    }
}
