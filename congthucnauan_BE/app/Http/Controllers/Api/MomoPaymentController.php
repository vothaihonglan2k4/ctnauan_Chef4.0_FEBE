<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MomoPaymentController extends Controller
{
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $endpoint;
    private $returnUrl;
    private $notifyUrl;

    public function __construct()
    {
        $this->partnerCode = env('MOMO_PARTNER_CODE');
        $this->accessKey = env('MOMO_ACCESS_KEY');
        $this->secretKey = env('MOMO_SECRET_KEY');
        $this->endpoint = env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $this->returnUrl = env('MOMO_RETURN_URL', env('APP_URL') . '/payments/momo-return');
        $this->notifyUrl = env('MOMO_NOTIFY_URL', env('APP_URL') . '/api/v1/momo/ipn');
    }

    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'order_info' => 'nullable|string|max:255',
            'course_id' => 'nullable|integer'
        ]);

        if (!$this->partnerCode || !$this->accessKey || !$this->secretKey) {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu cấu hình MoMo trong file .env'
            ], 500);
        }

        $user = $request->user();
        $amount = (int) $request->amount;
        $orderInfo = $request->order_info ?? 'Thanh toán gói thành viên';

        $orderId = 'MOMO' . time() . rand(1000, 9999);
        $requestId = $orderId . '_' . $user->id;
        $extraData = $request->course_id ? base64_encode(json_encode(['course_id' => (int) $request->course_id])) : '';

        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_method' => 'momo',
            'status' => 'pending',
            'transaction_id' => 'MOMO_' . $orderId
        ]);

        if ($request->filled('course_id')) {
            \App\Models\CourseEnrollment::create([
                'user_id' => $user->id,
                'course_id' => $request->course_id,
                'payment_id' => $payment->id,
                'status' => 'pending',
                'progress' => 0,
                'enrollment_date' => now()
            ]);
        }

        $requestType = env('MOMO_REQUEST_TYPE', 'payWithMethod'); // payWithMethod: cho user chọn phương thức, captureWallet: ưu tiên ví/QR

        $rawSignature = "accessKey={$this->accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$this->notifyUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$this->partnerCode}&redirectUrl={$this->returnUrl}&requestId={$requestId}&requestType={$requestType}";
        $signature = hash_hmac('sha256', $rawSignature, $this->secretKey);

        $payload = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'CongThucNauAn',
            'storeId' => 'CongThucNauAnStore',
            'requestId' => $requestId,
            'amount' => (string) $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $this->returnUrl,
            'ipnUrl' => $this->notifyUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        try {
            $response = Http::timeout(20)->post($this->endpoint, $payload);
            $data = $response->json() ?: [];

            $payment->update([
                'gateway_response' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'gateway_transaction_id' => isset($data['transId']) ? (string) $data['transId'] : null,
            ]);

            if (!$response->ok() || !isset($data['resultCode']) || (int)$data['resultCode'] !== 0 || empty($data['payUrl'])) {
                return response()->json([
                    'success' => false,
                    'message' => $data['message'] ?? 'Không thể tạo thanh toán MoMo',
                    'momo_result_code' => $data['resultCode'] ?? null,
                ], 500);
            }

            return response()->json([
                'success' => true,
                'payment_url' => $data['payUrl'],
                'order_id' => $orderId,
                'request_id' => $requestId,
                'payment_id' => $payment->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kết nối MoMo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function handleReturn(Request $request)
    {
        $data = $request->all();

        if (!$this->verifyReturnSignature($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], 400);
        }

        $orderId = $data['orderId'] ?? null;
        if (!$orderId) {
            return response()->json([
                'success' => false,
                'message' => 'Missing orderId',
            ], 400);
        }

        $payment = Payment::where('transaction_id', 'MOMO_' . $orderId)->first();
        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        $resultCode = (int)($data['resultCode'] ?? -1);

        if ($resultCode === 0) {
            $payment->update([
                'status' => 'completed',
                'gateway_transaction_id' => isset($data['transId']) ? (string) $data['transId'] : $payment->gateway_transaction_id,
                'gateway_response' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);

            $enrollment = \App\Models\CourseEnrollment::where('payment_id', $payment->id)->first();
            if ($enrollment) {
                $enrollment->update(['status' => 'active']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán MoMo thành công',
                'payment' => [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'transaction_id' => $payment->transaction_id,
                    'course_id' => $enrollment ? $enrollment->course_id : null,
                ],
            ]);
        }

        $payment->update([
            'status' => 'failed',
            'gateway_response' => json_encode($data, JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json([
            'success' => false,
            'message' => $data['message'] ?? 'Thanh toán thất bại',
            'result_code' => $resultCode,
        ]);
    }

    public function handleIpn(Request $request)
    {
        $data = $request->all();

        if (!$this->verifyReturnSignature($data)) {
            return response()->json([
                'resultCode' => 97,
                'message' => 'Invalid signature',
            ]);
        }

        $orderId = $data['orderId'] ?? null;
        if (!$orderId) {
            return response()->json([
                'resultCode' => 98,
                'message' => 'Missing orderId',
            ]);
        }

        $payment = Payment::where('transaction_id', 'MOMO_' . $orderId)->first();
        if (!$payment) {
            return response()->json([
                'resultCode' => 99,
                'message' => 'Payment not found',
            ]);
        }

        $resultCode = (int)($data['resultCode'] ?? -1);

        if ($resultCode === 0) {
            $payment->update([
                'status' => 'completed',
                'gateway_transaction_id' => isset($data['transId']) ? (string) $data['transId'] : $payment->gateway_transaction_id,
                'gateway_response' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);

            $enrollment = \App\Models\CourseEnrollment::where('payment_id', $payment->id)->first();
            if ($enrollment && $enrollment->status !== 'active') {
                $enrollment->update(['status' => 'active']);
            }
        } else {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);
        }

        return response()->json([
            'resultCode' => 0,
            'message' => 'Confirm Success',
        ]);
    }

    public function getConfig()
    {
        return response()->json([
            'partner_code' => $this->partnerCode,
            'endpoint' => $this->endpoint,
            'return_url' => $this->returnUrl,
            'notify_url' => $this->notifyUrl,
        ]);
    }

    private function verifyReturnSignature(array $data): bool
    {
        if (empty($data['signature'])) {
            return false;
        }

        $raw = $this->buildReturnRawData($data);
        if ($raw === '') {
            return false;
        }

        $calculatedSignature = hash_hmac('sha256', $raw, $this->secretKey);
        return hash_equals($calculatedSignature, (string)$data['signature']);
    }

    private function buildReturnRawData(array $data): string
    {
        $values = [
            'accessKey' => (string) $this->accessKey,
            'amount' => (string) ($data['amount'] ?? ''),
            'extraData' => (string) ($data['extraData'] ?? ''),
            'message' => (string) ($data['message'] ?? ''),
            'orderId' => (string) ($data['orderId'] ?? ''),
            'orderInfo' => (string) ($data['orderInfo'] ?? ''),
            'orderType' => (string) ($data['orderType'] ?? ''),
            'partnerCode' => (string) ($data['partnerCode'] ?? ''),
            'payType' => (string) ($data['payType'] ?? ''),
            'requestId' => (string) ($data['requestId'] ?? ''),
            'responseTime' => (string) ($data['responseTime'] ?? ''),
            'resultCode' => (string) ($data['resultCode'] ?? ''),
            'transId' => (string) ($data['transId'] ?? ''),
        ];

        $parts = [];
        foreach ($values as $key => $value) {
            $parts[] = $key . '=' . $value;
        }

        return implode('&', $parts);
    }
}
