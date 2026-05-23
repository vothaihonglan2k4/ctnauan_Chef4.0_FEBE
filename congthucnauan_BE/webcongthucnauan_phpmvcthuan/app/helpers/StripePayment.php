<?php
/**
 * Stripe Payment Gateway Helper
 * Tích hợp thanh toán qua Stripe
 * Documentation: https://stripe.com/docs
 */

class StripePayment {
    private $secretKey;
    private $publishableKey;
    private $apiVersion;
    private $currency;
    
    public function __construct() {
        // Cấu hình Stripe - Môi trường test
        // Trong production, cần thay bằng thông tin thực tế từ Stripe Dashboard
        
        // TODO: Thay YOUR_KEY bằng API keys từ Stripe Dashboard
        // Lấy tại: https://dashboard.stripe.com/test/apikeys
        
        $this->publishableKey = "pk_test_51SQAg40j0Ekxnzx5qwbtky0mR4vqnChgbOgSxluVVDGgiUHkuzfJbfqGiT6UtcLeFVQVgtW0bnWjAN19cORMbGx500BmIx13iS"; // ← Paste Publishable key vào đây
        $this->secretKey = "sk_test_51SQAg40j0Ekxnzx57CHM5aOsSp7NhRfrdSsPhWjrc65cFAy9q3eOkow6k3W5APxvsPvrLAtGy7VSZXXm54ReKUFA00RBa6EfEd"; // ← Paste Secret key vào đây
        
        $this->apiVersion = "2023-10-16";
        $this->currency = "vnd"; // Vietnamese Dong
    }
    
    /**
     * Tạo Payment Intent
     * 
     * @param array $data Thông tin thanh toán
     * @return array Kết quả từ Stripe
     */
    public function createPaymentIntent($data) {
        $amount = $data['amount'];
        $description = $data['description'] ?? 'Payment';
        $metadata = $data['metadata'] ?? [];
        
        // Convert amount to integer (Stripe requires integer, no decimals)
        $amount = intval($amount);
        
        $payload = [
            'amount' => $amount, // Must be integer for VND
            'currency' => $this->currency,
            'description' => $description,
            'metadata' => $metadata,
            'payment_method_types' => ['card'],
            'capture_method' => 'automatic'
        ];
        
        try {
            $result = $this->makeRequest('POST', 'payment_intents', $payload);
            return [
                'success' => true,
                'payment_intent' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Tạo Checkout Session (Hosted Checkout)
     * 
     * @param array $data Thông tin thanh toán
     * @return array
     */
    public function createCheckoutSession($data) {
        $amount = $data['amount'];
        $description = $data['description'] ?? 'Payment';
        $successUrl = $data['success_url'] ?? URL_ROOT . '/payments/stripe_success';
        $cancelUrl = $data['cancel_url'] ?? URL_ROOT . '/payments/stripe_cancel';
        $metadata = $data['metadata'] ?? [];
        
        // Convert amount to integer (Stripe requires integer, no decimals)
        $amount = intval($amount);
        
        // Check if using real API keys or simulator
        if ($this->isSimulatorMode()) {
            // Use simulator instead
            $session_id = 'cs_test_simulator_' . time();
            $simulatorUrl = URL_ROOT . '/public/stripe-simulator.php?' . http_build_query([
                'session_id' => $session_id,
                'amount' => $amount,
                'description' => $description
            ]);
            
            return [
                'success' => true,
                'session' => [
                    'id' => $session_id,
                    'url' => $simulatorUrl,
                    'payment_status' => 'unpaid'
                ],
                'checkout_url' => $simulatorUrl
            ];
        }
        
        $payload = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $this->currency,
                    'product_data' => [
                        'name' => $description,
                    ],
                    'unit_amount' => $amount, // Already converted to integer
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'metadata' => $metadata
        ];
        
        try {
            $result = $this->makeRequest('POST', 'checkout/sessions', $payload);
            return [
                'success' => true,
                'session' => $result,
                'checkout_url' => $result['url']
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Check if in simulator mode (no real API keys)
     * 
     * @return bool
     */
    private function isSimulatorMode() {
        return strpos($this->secretKey, 'YOUR_') !== false || 
               strpos($this->secretKey, 'Example') !== false;
    }
    
    /**
     * Retrieve Checkout Session
     * 
     * @param string $sessionId
     * @return array
     */
    public function retrieveSession($sessionId) {
        // If simulator mode, return mock data
        if ($this->isSimulatorMode() || strpos($sessionId, 'cs_test_simulator_') !== false) {
            return [
                'success' => true,
                'session' => [
                    'id' => $sessionId,
                    'payment_status' => 'paid',
                    'payment_intent' => 'pi_simulator_' . time(),
                    'amount_total' => 50000
                ]
            ];
        }
        
        try {
            $result = $this->makeRequest('GET', "checkout/sessions/{$sessionId}");
            return [
                'success' => true,
                'session' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Retrieve Payment Intent
     * 
     * @param string $paymentIntentId
     * @return array
     */
    public function retrievePaymentIntent($paymentIntentId) {
        try {
            $result = $this->makeRequest('GET', "payment_intents/{$paymentIntentId}");
            return [
                'success' => true,
                'payment_intent' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Confirm Payment Intent
     * 
     * @param string $paymentIntentId
     * @param array $paymentMethodData
     * @return array
     */
    public function confirmPaymentIntent($paymentIntentId, $paymentMethodData = []) {
        $payload = [
            'payment_method_data' => $paymentMethodData
        ];
        
        try {
            $result = $this->makeRequest('POST', "payment_intents/{$paymentIntentId}/confirm", $payload);
            return [
                'success' => true,
                'payment_intent' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Tạo Payment Method từ card token
     * 
     * @param string $token
     * @return array
     */
    public function createPaymentMethod($token) {
        $payload = [
            'type' => 'card',
            'card' => [
                'token' => $token
            ]
        ];
        
        try {
            $result = $this->makeRequest('POST', 'payment_methods', $payload);
            return [
                'success' => true,
                'payment_method' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Xử lý webhook từ Stripe
     * 
     * @param string $payload
     * @param string $signature
     * @return array
     */
    public function handleWebhook($payload, $signature) {
        // Webhook secret từ Stripe Dashboard
        $webhookSecret = "whsec_test_..."; // Thay bằng webhook secret thật
        
        try {
            // Verify signature
            // Trong môi trường thật cần verify signature đầy đủ
            $event = json_decode($payload, true);
            
            return [
                'success' => true,
                'event' => $event
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Make HTTP request to Stripe API
     * 
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    private function makeRequest($method, $endpoint, $data = []) {
        $url = "https://api.stripe.com/v1/{$endpoint}";
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
            'Stripe-Version: ' . $this->apiVersion,
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL Error: " . $error);
        }
        
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $errorMessage = isset($result['error']['message']) ? $result['error']['message'] : 'Unknown error';
            throw new Exception("Stripe API Error: " . $errorMessage);
        }
        
        return $result;
    }
    
    /**
     * Lấy Publishable Key (để dùng ở client-side)
     * 
     * @return string
     */
    public function getPublishableKey() {
        return $this->publishableKey;
    }
    
    /**
     * Lấy thông tin cấu hình
     * 
     * @return array
     */
    public function getConfig() {
        return [
            'publishableKey' => $this->publishableKey,
            'currency' => $this->currency,
            'apiVersion' => $this->apiVersion
        ];
    }
    
    /**
     * Test cards cho môi trường test
     * 
     * @return array
     */
    public static function getTestCards() {
        return [
            'success' => [
                'number' => '4242424242424242',
                'exp' => '12/34',
                'cvc' => '123',
                'description' => 'Thanh toán thành công'
            ],
            'declined' => [
                'number' => '4000000000000002',
                'exp' => '12/34',
                'cvc' => '123',
                'description' => 'Thẻ bị từ chối'
            ],
            'insufficient_funds' => [
                'number' => '4000000000009995',
                'exp' => '12/34',
                'cvc' => '123',
                'description' => 'Không đủ tiền'
            ],
            '3d_secure' => [
                'number' => '4000002500003155',
                'exp' => '12/34',
                'cvc' => '123',
                'description' => 'Yêu cầu xác thực 3D Secure'
            ],
            'visa' => [
                'number' => '4242424242424242',
                'exp' => '12/34',
                'cvc' => '123',
                'description' => 'Visa - Success'
            ],
            'mastercard' => [
                'number' => '5555555555554444',
                'exp' => '12/34',
                'cvc' => '123',
                'description' => 'Mastercard - Success'
            ]
        ];
    }
}

