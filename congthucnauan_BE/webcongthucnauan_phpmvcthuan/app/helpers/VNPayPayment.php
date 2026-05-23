<?php
/**
 * VNPay Payment Gateway Helper
 * Tích hợp thanh toán qua VNPay
 * Documentation: https://sandbox.vnpayment.vn/apis/docs/
 */

class VNPayPayment {
    private $vnp_TmnCode;
    private $vnp_HashSecret;
    private $vnp_Url;
    private $vnp_ReturnUrl;
    
    public function __construct() {
        // Cấu hình VNPay - Môi trường test
        // Thông tin từ VNPAY Sandbox: volan@vothaihonglan.net
        $this->vnp_TmnCode = "TGQ78RUP"; // Terminal ID / Mã Website
        $this->vnp_HashSecret = "1D4XCMDC467EABY7KFV8MH8OYOY2HN4Y"; // Secret Key / Chuỗi bí mật
        $this->vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        
        // URL return phải được đăng ký trong VNPay Merchant Admin
        // Đăng ký tại: https://sandbox.vnpayment.vn/merchantv2/
        $this->vnp_ReturnUrl = "http://localhost/webcongthucnauan/payments/vnpay_return";
    }
    
    /**
     * Tạo URL thanh toán VNPay
     * 
     * @param array $data Thông tin thanh toán
     * @return string URL thanh toán
     */
    public function createPaymentUrl($data) {
        $vnp_TxnRef = time(); // Mã đơn hàng
        $vnp_OrderInfo = $data['orderInfo'] ?? 'Thanh toán đơn hàng';
        $vnp_OrderType = $data['orderType'] ?? 'billpayment';
        $vnp_Amount = $data['amount'] * 100; // VNPay yêu cầu số tiền nhân 100
        $vnp_Locale = $data['locale'] ?? 'vn';
        $vnp_BankCode = $data['bankCode'] ?? '';
        $vnp_IpAddr = $this->getClientIp();
        
        // Lưu thông tin bổ sung vào session để sử dụng sau khi callback
        if(isset($data['extraData'])) {
            $_SESSION['vnpay_extra_' . $vnp_TxnRef] = $data['extraData'];
        }
        
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $this->vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef
        );
        
        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        
        // Sắp xếp dữ liệu theo thứ tự alphabet
        ksort($inputData);
        
        $query = "";
        $i = 0;
        $hashdata = "";
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
        
        if (!empty($this->vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        
        return [
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url,
            'txnRef' => $vnp_TxnRef
        ];
    }
    
    /**
     * Xác thực dữ liệu trả về từ VNPay
     * 
     * @param array $data Dữ liệu từ VNPay
     * @return bool
     */
    public function verifyReturnUrl($data) {
        if(!isset($data['vnp_SecureHash'])) {
            return false;
        }
        
        $vnp_SecureHash = $data['vnp_SecureHash'];
        
        // Loại bỏ các tham số không cần thiết
        $inputData = array();
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
        
        // Sắp xếp dữ liệu theo alphabet
        ksort($inputData);
        
        // Tạo chuỗi hash data
        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        $secureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
        
        return ($secureHash == $vnp_SecureHash);
    }
    
    /**
     * Lấy IP của client
     * 
     * @return string
     */
    private function getClientIp() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = '127.0.0.1';
        return $ipaddress;
    }
    
    /**
     * Lấy thông tin bổ sung từ session
     * 
     * @param string $txnRef Mã giao dịch
     * @return mixed
     */
    public function getExtraData($txnRef) {
        $key = 'vnpay_extra_' . $txnRef;
        if(isset($_SESSION[$key])) {
            $data = $_SESSION[$key];
            unset($_SESSION[$key]); // Xóa sau khi lấy
            return $data;
        }
        return null;
    }
    
    /**
     * Lấy thông tin cấu hình
     */
    public function getConfig() {
        return [
            'tmnCode' => $this->vnp_TmnCode,
            'returnUrl' => $this->vnp_ReturnUrl,
            'paymentUrl' => $this->vnp_Url
        ];
    }
    
    /**
     * Chuyển đổi mã lỗi VNPay thành thông báo
     * 
     * @param string $responseCode
     * @return string
     */
    public function getResponseMessage($responseCode) {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)'
        ];
        
        return isset($messages[$responseCode]) ? $messages[$responseCode] : 'Lỗi không xác định';
    }
}

