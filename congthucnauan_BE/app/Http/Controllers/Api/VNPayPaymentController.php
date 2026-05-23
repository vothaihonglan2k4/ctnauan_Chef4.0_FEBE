<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class VNPayPaymentController extends Controller
{
    private $vnp_TmnCode;
    private $vnp_HashSecret;
    private $vnp_Url;
    private $vnp_ReturnUrl;

    public function __construct()
    {
        $this->vnp_TmnCode = env('VNPAY_TMN_CODE', 'TGQ78RUP');
        $this->vnp_HashSecret = env('VNPAY_HASH_SECRET', '1D4XCMDC467EABY7KFV8MH8OYOY2HN4Y');
        $this->vnp_Url = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        // Return URL phải là full URL của React app
        $this->vnp_ReturnUrl = env('APP_URL', 'http://127.0.0.1:8000') . '/payments/vnpay-return';
    }

    /**
     * Create VNPay payment URL
     */
    public function createPaymentUrl(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'order_info' => 'nullable|string'
        ]);

        $user = $request->user();
        $amount = (int) $request->amount;
        $orderInfo = $request->order_info ?? 'Thanh toán gói thành viên';

        // Create payment record
        $vnp_TxnRef = time() . rand(1000, 9999);
        
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_method' => 'vnpay',
            'status' => 'pending',
            'transaction_id' => 'VNPAY_' . $vnp_TxnRef
        ]);

        // Create pending enrollment if course_id is present
        if ($request->has('course_id')) {
            \App\Models\CourseEnrollment::create([
                'user_id' => $user->id,
                'course_id' => $request->course_id,
                'payment_id' => $payment->id,
                'status' => 'pending',
                'progress' => 0,
                'enrollment_date' => now()
            ]);
        }

        // Build VNPay payment URL
        $vnp_Amount = $amount * 100; // VNPay requires amount * 100
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => $orderInfo,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $this->vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef
        ];

        // Sort data
        ksort($inputData);

        $query = "";
        $hashdata = "";
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $this->vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return response()->json([
            'success' => true,
            'payment_url' => $vnp_Url,
            'txn_ref' => $vnp_TxnRef,
            'payment_id' => $payment->id
        ]);
    }

    /**
     * Handle VNPay return callback
     */
    public function handleReturn(Request $request)
    {
        $inputData = $request->all();

        if (!isset($inputData['vnp_SecureHash'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid response from VNPay'
            ], 400);
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];

        // Remove hash params
        $vnpData = [];
        foreach ($inputData as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $vnpData[$key] = $value;
            }
        }

        unset($vnpData['vnp_SecureHash']);
        unset($vnpData['vnp_SecureHashType']);

        // Sort data
        ksort($vnpData);

        // Build hash data
        $hashdata = "";
        $i = 0;
        foreach ($vnpData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);

        // Verify signature
        if ($secureHash != $vnp_SecureHash) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature'
            ], 400);
        }

        // Find payment
        $txnRef = $inputData['vnp_TxnRef'];
        $payment = Payment::where('transaction_id', 'VNPAY_' . $txnRef)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        // Update payment status
        $responseCode = $inputData['vnp_ResponseCode'];
        
        if ($responseCode == '00') {
            $payment->update([
                'status' => 'completed',
                'transaction_id' => $inputData['vnp_TransactionNo'] ?? $payment->transaction_id
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
            $payment->update(['status' => 'failed']);

            return response()->json([
                'success' => false,
                'message' => $this->getResponseMessage($responseCode),
                'response_code' => $responseCode
            ]);
        }
    }

    /**
     * Get VNPay config
     */
    public function getConfig()
    {
        return response()->json([
            'tmn_code' => $this->vnp_TmnCode,
            'return_url' => $this->vnp_ReturnUrl,
            'payment_url' => $this->vnp_Url
        ]);
    }

    /**
     * Get response message from code
     */
    private function getResponseMessage($responseCode)
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ',
            '09' => 'Thẻ/Tài khoản chưa đăng ký InternetBanking',
            '10' => 'Xác thực thông tin không đúng quá 3 lần',
            '11' => 'Đã hết hạn chờ thanh toán',
            '12' => 'Thẻ/Tài khoản bị khóa',
            '13' => 'Nhập sai mật khẩu OTP',
            '24' => 'Khách hàng hủy giao dịch',
            '51' => 'Tài khoản không đủ số dư',
            '65' => 'Vượt quá hạn mức giao dịch trong ngày',
            '75' => 'Ngân hàng đang bảo trì',
            '79' => 'Nhập sai mật khẩu quá số lần quy định',
            '99' => 'Lỗi không xác định'
        ];

        return $messages[$responseCode] ?? 'Lỗi không xác định';
    }
}
