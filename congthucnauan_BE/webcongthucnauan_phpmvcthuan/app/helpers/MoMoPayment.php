<?php
/**
 * MoMo Payment Gateway Helper
 * Tích hợp thanh toán qua MoMo
 * Documentation: https://developers.momo.vn/
 */

class MoMoPayment {
    private $endpoint;
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $returnUrl;
    private $notifyUrl;
    
    public function __construct() {
        // Cấu hình MoMo - Môi trường test
        $this->endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        
        // Thông tin đối tác (sử dụng test credentials)
        // Trong production, cần thay bằng thông tin thực tế từ MoMo
        $this->partnerCode = "MOMOBKUN20180529";
        $this->accessKey = "klm05TvNBzhg7h7j";
        $this->secretKey = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa";
        
        // URLs callback
        $this->returnUrl = URL_ROOT . "/payments/momo_return";
        $this->notifyUrl = URL_ROOT . "/payments/momo_notify";
    }
    
    /**
     * Tạo yêu cầu thanh toán MoMo
     * 
     * @param array $data Thông tin thanh toán
     * @return array Kết quả từ MoMo
     */
    public function createPayment($data) {
        $orderId = time() . "";
        $requestId = time() . "";
        $amount = $data['amount'];
        $orderInfo = $data['orderInfo'] ?? 'Thanh toán đơn hàng';
        $extraData = isset($data['extraData']) ? base64_encode(json_encode($data['extraData'])) : "";
        
        // Tạo chữ ký (signature)
        $rawHash = "accessKey=" . $this->accessKey . 
                   "&amount=" . $amount . 
                   "&extraData=" . $extraData . 
                   "&ipnUrl=" . $this->notifyUrl . 
                   "&orderId=" . $orderId . 
                   "&orderInfo=" . $orderInfo . 
                   "&partnerCode=" . $this->partnerCode . 
                   "&redirectUrl=" . $this->returnUrl . 
                   "&requestId=" . $requestId . 
                   "&requestType=captureWallet";
        
        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);
        
        // Dữ liệu gửi đến MoMo
        $requestData = array(
            'partnerCode' => $this->partnerCode,
            'partnerName' => "Công Thức Nấu Ăn",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $this->returnUrl,
            'ipnUrl' => $this->notifyUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => 'captureWallet',
            'signature' => $signature
        );
        
        // Gửi request đến MoMo
        $result = $this->execPostRequest($this->endpoint, json_encode($requestData));
        $jsonResult = json_decode($result, true);
        
        return $jsonResult;
    }
    
    /**
     * Xác thực callback từ MoMo
     * 
     * @param array $data Dữ liệu từ MoMo
     * @return bool
     */
    public function verifySignature($data) {
        $rawHash = "accessKey=" . $this->accessKey . 
                   "&amount=" . $data['amount'] . 
                   "&extraData=" . $data['extraData'] . 
                   "&message=" . $data['message'] . 
                   "&orderId=" . $data['orderId'] . 
                   "&orderInfo=" . $data['orderInfo'] . 
                   "&orderType=" . $data['orderType'] . 
                   "&partnerCode=" . $data['partnerCode'] . 
                   "&payType=" . $data['payType'] . 
                   "&requestId=" . $data['requestId'] . 
                   "&responseTime=" . $data['responseTime'] . 
                   "&resultCode=" . $data['resultCode'] . 
                   "&transId=" . $data['transId'];
        
        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);
        
        return ($signature == $data['signature']);
    }
    
    /**
     * Kiểm tra trạng thái giao dịch
     * 
     * @param string $orderId Mã đơn hàng
     * @param string $requestId Mã request
     * @return array
     */
    public function checkTransactionStatus($orderId, $requestId) {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/query";
        
        $rawHash = "accessKey=" . $this->accessKey . 
                   "&orderId=" . $orderId . 
                   "&partnerCode=" . $this->partnerCode . 
                   "&requestId=" . $requestId;
        
        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);
        
        $requestData = array(
            'partnerCode' => $this->partnerCode,
            'requestId' => $requestId,
            'orderId' => $orderId,
            'lang' => 'vi',
            'signature' => $signature
        );
        
        $result = $this->execPostRequest($endpoint, json_encode($requestData));
        $jsonResult = json_decode($result, true);
        
        return $jsonResult;
    }
    
    /**
     * Gửi HTTP POST request
     * 
     * @param string $url
     * @param string $data
     * @return string
     */
    private function execPostRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        
        // Execute post
        $result = curl_exec($ch);
        
        // Close connection
        curl_close($ch);
        
        return $result;
    }
    
    /**
     * Lấy thông tin cấu hình
     */
    public function getConfig() {
        return [
            'partnerCode' => $this->partnerCode,
            'returnUrl' => $this->returnUrl,
            'notifyUrl' => $this->notifyUrl
        ];
    }
}

