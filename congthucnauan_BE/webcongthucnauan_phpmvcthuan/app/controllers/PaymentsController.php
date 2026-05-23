<?php
// Load payment helpers
require_once APP_ROOT . '/helpers/MoMoPayment.php';
require_once APP_ROOT . '/helpers/VNPayPayment.php';
require_once APP_ROOT . '/helpers/StripePayment.php';

class PaymentsController extends Controller {
    private $paymentModel;
    private $momoPayment;
    private $vnpayPayment;
    private $stripePayment;
    
    public function __construct() {
        $this->paymentModel = $this->model('Payment');
        $this->momoPayment = new MoMoPayment();
        $this->vnpayPayment = new VNPayPayment();
        $this->stripePayment = new StripePayment();
    }

    public function index() {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
        }

        $payments = $this->paymentModel->getPaymentsByUser($_SESSION['user_id']);

        $data = [
            'payments' => $payments
        ];

        $this->view('payments/index', $data);
    }

    public function checkout() {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process payment form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'amount' => trim($_POST['amount']),
                'payment_method' => trim($_POST['payment_method']),
                'card_number' => isset($_POST['card_number']) ? trim($_POST['card_number']) : '',
                'card_name' => isset($_POST['card_name']) ? trim($_POST['card_name']) : '',
                'card_expiry' => isset($_POST['card_expiry']) ? trim($_POST['card_expiry']) : '',
                'card_cvv' => isset($_POST['card_cvv']) ? trim($_POST['card_cvv']) : '',
                'amount_err' => '',
                'payment_method_err' => '',
                'card_number_err' => '',
                'card_name_err' => '',
                'card_expiry_err' => '',
                'card_cvv_err' => ''
            ];

            // Validate amount
            if(empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
                $data['amount_err'] = 'Vui lòng nhập số tiền hợp lệ';
            }

            // Validate payment method
            if(empty($data['payment_method'])) {
                $data['payment_method_err'] = 'Vui lòng chọn phương thức thanh toán';
            }

            // Validate card details if credit card payment
            if($data['payment_method'] == 'credit_card') {
                if(empty($data['card_number'])) {
                    $data['card_number_err'] = 'Vui lòng nhập số thẻ';
                }
                if(empty($data['card_name'])) {
                    $data['card_name_err'] = 'Vui lòng nhập tên chủ thẻ';
                }
                if(empty($data['card_expiry'])) {
                    $data['card_expiry_err'] = 'Vui lòng nhập ngày hết hạn';
                }
                if(empty($data['card_cvv'])) {
                    $data['card_cvv_err'] = 'Vui lòng nhập mã CVV';
                }
            }

            // Make sure no errors
            if(empty($data['amount_err']) && empty($data['payment_method_err']) && 
              ($data['payment_method'] != 'credit_card' || 
               (empty($data['card_number_err']) && empty($data['card_name_err']) && 
                empty($data['card_expiry_err']) && empty($data['card_cvv_err'])))) {
                
                // Xử lý thanh toán theo phương thức
                if($data['payment_method'] == 'momo') {
                    // Thanh toán qua MoMo
                    $momoData = [
                        'amount' => $data['amount'],
                        'orderInfo' => 'Thanh toán gói dịch vụ - ' . $_SESSION['user_name'],
                        'extraData' => [
                            'user_id' => $_SESSION['user_id'],
                            'package' => $data['amount']
                        ]
                    ];
                    
                    $result = $this->momoPayment->createPayment($momoData);
                    
                    if(isset($result['payUrl'])) {
                        // Lưu thông tin thanh toán tạm với trạng thái pending
                        $payment_data = [
                            'user_id' => $_SESSION['user_id'],
                            'amount' => $data['amount'],
                            'payment_method' => 'momo',
                            'status' => 'pending',
                            'transaction_id' => $result['requestId']
                        ];
                        $this->paymentModel->addPayment($payment_data);
                        
                        // Chuyển hướng đến trang thanh toán MoMo
                        header('Location: ' . $result['payUrl']);
                        exit;
                    } else {
                        $_SESSION['error_msg'] = 'Không thể tạo giao dịch MoMo: ' . ($result['message'] ?? 'Lỗi không xác định');
                        $this->view('payments/checkout', $data);
                    }
                    
                } elseif($data['payment_method'] == 'vnpay') {
                    // Thanh toán qua VNPay
                    $vnpayData = [
                        'amount' => $data['amount'],
                        'orderInfo' => 'Thanh toán gói dịch vụ - ' . $_SESSION['user_name'],
                        'orderType' => 'billpayment',
                        'extraData' => [
                            'user_id' => $_SESSION['user_id'],
                            'package' => $data['amount']
                        ]
                    ];
                    
                    $result = $this->vnpayPayment->createPaymentUrl($vnpayData);
                    
                    if(isset($result['data'])) {
                        // Lưu thông tin thanh toán tạm với trạng thái pending
                        $payment_data = [
                            'user_id' => $_SESSION['user_id'],
                            'amount' => $data['amount'],
                            'payment_method' => 'vnpay',
                            'status' => 'pending',
                            'transaction_id' => $result['txnRef']
                        ];
                        $this->paymentModel->addPayment($payment_data);
                        
                        // Chuyển hướng đến trang thanh toán VNPay
                        header('Location: ' . $result['data']);
                        exit;
                    } else {
                        $_SESSION['error_msg'] = 'Không thể tạo giao dịch VNPay';
                        $this->view('payments/checkout', $data);
                    }
                    
                } elseif($data['payment_method'] == 'stripe') {
                    // Thanh toán qua Stripe
                    $stripeData = [
                        'amount' => $data['amount'],
                        'description' => 'Thanh toán gói dịch vụ - ' . $_SESSION['user_name'],
                        'success_url' => URL_ROOT . '/payments/stripe_success',
                        'cancel_url' => URL_ROOT . '/payments/stripe_cancel',
                        'metadata' => [
                            'user_id' => $_SESSION['user_id'],
                            'package' => $data['amount']
                        ]
                    ];
                    
                    $result = $this->stripePayment->createCheckoutSession($stripeData);
                    
                    if($result['success']) {
                        // Lưu thông tin thanh toán tạm với trạng thái pending
                        $payment_data = [
                            'user_id' => $_SESSION['user_id'],
                            'amount' => $data['amount'],
                            'payment_method' => 'stripe',
                            'status' => 'pending',
                            'transaction_id' => $result['session']['id']
                        ];
                        $this->paymentModel->addPayment($payment_data);
                        
                        // Chuyển hướng đến trang thanh toán Stripe
                        header('Location: ' . $result['checkout_url']);
                        exit;
                    } else {
                        $_SESSION['error_msg'] = 'Không thể tạo giao dịch Stripe: ' . ($result['error'] ?? 'Lỗi không xác định');
                        $this->view('payments/checkout', $data);
                    }
                    
                } else {
                    // Thanh toán thẻ tín dụng hoặc chuyển khoản ngân hàng (mô phỏng)
                    // Generate a transaction ID
                    $transaction_id = uniqid('TRX');
                    
                    $payment_data = [
                        'user_id' => $_SESSION['user_id'],
                        'amount' => $data['amount'],
                        'payment_method' => $data['payment_method'],
                        'status' => 'completed',
                        'transaction_id' => $transaction_id
                    ];
                    
                    if($this->paymentModel->addPayment($payment_data)) {
                        $_SESSION['success_msg'] = 'Thanh toán thành công';
                        $this->redirect('payments');
                    } else {
                        die('Có lỗi xảy ra');
                    }
                }
            } else {
                // Load view with errors
                $this->view('payments/checkout', $data);
            }
        } else {
            $data = [
                'amount' => '',
                'payment_method' => '',
                'card_number' => '',
                'card_name' => '',
                'card_expiry' => '',
                'card_cvv' => '',
                'amount_err' => '',
                'payment_method_err' => '',
                'card_number_err' => '',
                'card_name_err' => '',
                'card_expiry_err' => '',
                'card_cvv_err' => ''
            ];

            $this->view('payments/checkout', $data);
        }
    }
    
    /**
     * Xử lý callback từ MoMo sau khi thanh toán (TEST VERSION - Bỏ qua signature)
     */
    public function momo_return_test() {
        $data = $_GET;
        
        // TEST MODE - Bỏ qua signature verification
        // Lấy thông tin giao dịch
        $orderId = isset($data['orderId']) ? $data['orderId'] : '';
        $resultCode = isset($data['resultCode']) ? $data['resultCode'] : '-1';
        $message = isset($data['message']) ? $data['message'] : 'Unknown';
        $transId = isset($data['transId']) ? $data['transId'] : time();
        $amount = isset($data['amount']) ? $data['amount'] : 0;
        $requestId = isset($data['requestId']) ? $data['requestId'] : $orderId;
        
        // Cập nhật trạng thái thanh toán
        if($resultCode == 0) {
            // Thanh toán thành công
            $updated = $this->paymentModel->updatePaymentStatusByTransactionId($requestId, 'completed', $transId);
            
            // Log để debug
            error_log("MoMo Test Callback: Updated payment {$requestId} - Result: " . ($updated ? 'Success' : 'Failed'));
            
            // Kiểm tra xem có phải thanh toán khóa học không
            if(isset($_SESSION['pending_course_enrollment'])) {
                $_SESSION['success_msg'] = 'Thanh toán MoMo thành công! Mã giao dịch: ' . $transId;
                error_log("Redirecting to courses/payment_success");
                header('Location: ' . URL_ROOT . '/courses/payment_success');
                exit;
            } else {
                $_SESSION['success_msg'] = 'Thanh toán MoMo thành công! Mã giao dịch: ' . $transId;
                error_log("Redirecting to payments");
                header('Location: ' . URL_ROOT . '/payments');
                exit;
            }
        } else {
            // Thanh toán thất bại
            $this->paymentModel->updatePaymentStatusByTransactionId($requestId, 'failed', null);
            
            // Xóa pending enrollment nếu có
            if(isset($_SESSION['pending_course_enrollment'])) {
                unset($_SESSION['pending_course_enrollment']);
            }
            
            $_SESSION['error_msg'] = 'Thanh toán MoMo thất bại: ' . $message;
            header('Location: ' . URL_ROOT . '/payments');
            exit;
        }
    }
    
    /**
     * Xử lý callback từ MoMo sau khi thanh toán
     */
    public function momo_return() {
        $data = $_GET;
        
        // Xác thực signature từ MoMo
        if($this->momoPayment->verifySignature($data)) {
            // Lấy thông tin giao dịch
            $orderId = $data['orderId'];
            $resultCode = $data['resultCode'];
            $message = $data['message'];
            $transId = $data['transId'];
            $amount = $data['amount'];
            
            // Decode extraData nếu có
            $extraData = null;
            if(isset($data['extraData']) && !empty($data['extraData'])) {
                $extraData = json_decode(base64_decode($data['extraData']), true);
            }
            
            // Cập nhật trạng thái thanh toán
            if($resultCode == 0) {
                // Thanh toán thành công
                $this->paymentModel->updatePaymentStatusByTransactionId($data['requestId'], 'completed', $transId);
                
                // Kiểm tra xem có phải thanh toán khóa học không
                if(isset($_SESSION['pending_course_enrollment'])) {
                    $_SESSION['success_msg'] = 'Thanh toán MoMo thành công! Mã giao dịch: ' . $transId;
                    $this->redirect('courses/payment_success');
                } else {
                    $_SESSION['success_msg'] = 'Thanh toán MoMo thành công! Mã giao dịch: ' . $transId;
                    $this->redirect('payments');
                }
            } else {
                // Thanh toán thất bại
                $this->paymentModel->updatePaymentStatusByTransactionId($data['requestId'], 'failed', null);
                
                // Xóa pending enrollment nếu có
                if(isset($_SESSION['pending_course_enrollment'])) {
                    unset($_SESSION['pending_course_enrollment']);
                }
                
                $_SESSION['error_msg'] = 'Thanh toán MoMo thất bại: ' . $message;
                $this->redirect('payments');
            }
        } else {
            $_SESSION['error_msg'] = 'Xác thực giao dịch MoMo thất bại';
            $this->redirect('payments');
        }
    }
    
    /**
     * Xử lý IPN (Instant Payment Notification) từ MoMo
     */
    public function momo_notify() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Log để debug
        error_log('MoMo IPN: ' . print_r($data, true));
        
        // Xác thực signature
        if($this->momoPayment->verifySignature($data)) {
            $resultCode = $data['resultCode'];
            $transId = $data['transId'];
            
            if($resultCode == 0) {
                // Thanh toán thành công
                $this->paymentModel->updatePaymentStatusByTransactionId($data['requestId'], 'completed', $transId);
            } else {
                // Thanh toán thất bại
                $this->paymentModel->updatePaymentStatusByTransactionId($data['requestId'], 'failed', null);
            }
            
            // Trả về response cho MoMo
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
        }
    }
    
    /**
     * Xử lý callback từ VNPay sau khi thanh toán (TEST VERSION - Bỏ qua signature)
     */
    public function vnpay_return_test() {
        $data = $_GET;
        
        // TEST MODE - Bỏ qua signature verification
        $vnp_TxnRef = isset($data['vnp_TxnRef']) ? $data['vnp_TxnRef'] : '';
        $vnp_Amount = isset($data['vnp_Amount']) ? $data['vnp_Amount'] / 100 : 0;
        $vnp_ResponseCode = isset($data['vnp_ResponseCode']) ? $data['vnp_ResponseCode'] : '99';
        $vnp_TransactionNo = isset($data['vnp_TransactionNo']) ? $data['vnp_TransactionNo'] : time();
        $vnp_BankCode = isset($data['vnp_BankCode']) ? $data['vnp_BankCode'] : '';
        
        // Cập nhật trạng thái thanh toán
        if($vnp_ResponseCode == '00') {
            // Thanh toán thành công
            $this->paymentModel->updatePaymentStatusByTransactionId($vnp_TxnRef, 'completed', $vnp_TransactionNo);
            
            // Kiểm tra xem có phải thanh toán khóa học không
            if(isset($_SESSION['pending_course_enrollment'])) {
                $_SESSION['success_msg'] = 'Thanh toán VNPay thành công! Mã giao dịch: ' . $vnp_TransactionNo;
                $this->redirect('courses/payment_success');
            } else {
                $_SESSION['success_msg'] = 'Thanh toán VNPay thành công! Mã giao dịch: ' . $vnp_TransactionNo;
                $this->redirect('payments');
            }
        } else {
            // Thanh toán thất bại
            $this->paymentModel->updatePaymentStatusByTransactionId($vnp_TxnRef, 'failed', null);
            
            // Xóa pending enrollment nếu có
            if(isset($_SESSION['pending_course_enrollment'])) {
                unset($_SESSION['pending_course_enrollment']);
            }
            
            $errorMessage = $this->vnpayPayment->getResponseMessage($vnp_ResponseCode);
            $_SESSION['error_msg'] = 'Thanh toán VNPay thất bại: ' . $errorMessage;
            $this->redirect('payments');
        }
    }
    
    /**
     * Xử lý callback từ VNPay sau khi thanh toán
     */
    public function vnpay_return() {
        $data = $_GET;
        
        // Xác thực signature từ VNPay
        if($this->vnpayPayment->verifyReturnUrl($data)) {
            $vnp_TxnRef = $data['vnp_TxnRef'];
            $vnp_Amount = $data['vnp_Amount'] / 100; // Chia 100 vì VNPay nhân 100
            $vnp_ResponseCode = $data['vnp_ResponseCode'];
            $vnp_TransactionNo = $data['vnp_TransactionNo'];
            $vnp_BankCode = $data['vnp_BankCode'];
            
            // Lấy extra data từ session
            $extraData = $this->vnpayPayment->getExtraData($vnp_TxnRef);
            
            // Cập nhật trạng thái thanh toán
            if($vnp_ResponseCode == '00') {
                // Thanh toán thành công
                $this->paymentModel->updatePaymentStatusByTransactionId($vnp_TxnRef, 'completed', $vnp_TransactionNo);
                
                // Kiểm tra xem có phải thanh toán khóa học không
                if(isset($_SESSION['pending_course_enrollment'])) {
                    $_SESSION['success_msg'] = 'Thanh toán VNPay thành công! Mã giao dịch: ' . $vnp_TransactionNo;
                    $this->redirect('courses/payment_success');
                } else {
                    $_SESSION['success_msg'] = 'Thanh toán VNPay thành công! Mã giao dịch: ' . $vnp_TransactionNo;
                    $this->redirect('payments');
                }
            } else {
                // Thanh toán thất bại
                $this->paymentModel->updatePaymentStatusByTransactionId($vnp_TxnRef, 'failed', null);
                
                // Xóa pending enrollment nếu có
                if(isset($_SESSION['pending_course_enrollment'])) {
                    unset($_SESSION['pending_course_enrollment']);
                }
                
                $errorMessage = $this->vnpayPayment->getResponseMessage($vnp_ResponseCode);
                $_SESSION['error_msg'] = 'Thanh toán VNPay thất bại: ' . $errorMessage;
                $this->redirect('payments');
            }
        } else {
            $_SESSION['error_msg'] = 'Xác thực giao dịch VNPay thất bại';
            $this->redirect('payments');
        }
    }
    
    /**
     * Xử lý callback thành công từ Stripe
     */
    public function stripe_success() {
        if(!$this->isLoggedIn()) {
            redirect('users/login');
        }
        
        $session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';
        
        if(empty($session_id)) {
            $_SESSION['error_msg'] = 'Không tìm thấy thông tin giao dịch';
            $this->redirect('payments');
        }
        
        // Retrieve session từ Stripe
        $result = $this->stripePayment->retrieveSession($session_id);
        
        if($result['success']) {
            $session = $result['session'];
            $payment_status = $session['payment_status']; // paid, unpaid, no_payment_required
            
            if($payment_status === 'paid') {
                // Cập nhật trạng thái thanh toán
                $this->paymentModel->updatePaymentStatusByTransactionId(
                    $session_id, 
                    'completed', 
                    $session['payment_intent'] ?? $session_id
                );
                
                // Kiểm tra xem có phải thanh toán khóa học không
                if(isset($_SESSION['pending_course_enrollment'])) {
                    $_SESSION['success_msg'] = 'Thanh toán Stripe thành công!';
                    $this->redirect('courses/payment_success');
                } else {
                    $_SESSION['success_msg'] = 'Thanh toán Stripe thành công!';
                    $this->redirect('payments');
                }
            } else {
                $_SESSION['error_msg'] = 'Thanh toán chưa hoàn tất';
                $this->redirect('payments');
            }
        } else {
            $_SESSION['error_msg'] = 'Không thể xác minh giao dịch: ' . ($result['error'] ?? 'Lỗi không xác định');
            $this->redirect('payments');
        }
    }
    
    /**
     * Xử lý callback hủy từ Stripe
     */
    public function stripe_cancel() {
        if(!$this->isLoggedIn()) {
            redirect('users/login');
        }
        
        // Xóa pending enrollment nếu có
        if(isset($_SESSION['pending_course_enrollment'])) {
            unset($_SESSION['pending_course_enrollment']);
        }
        
        $_SESSION['error_msg'] = 'Bạn đã hủy thanh toán';
        $this->redirect('payments/checkout');
    }
    
    /**
     * Xử lý webhook từ Stripe
     */
    public function stripe_webhook() {
        $payload = @file_get_contents('php://input');
        $signature = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : '';
        
        $result = $this->stripePayment->handleWebhook($payload, $signature);
        
        if($result['success']) {
            $event = $result['event'];
            
            // Xử lý các event types
            switch($event['type']) {
                case 'checkout.session.completed':
                    $session = $event['data']['object'];
                    // Cập nhật payment status
                    $this->paymentModel->updatePaymentStatusByTransactionId(
                        $session['id'],
                        'completed',
                        $session['payment_intent'] ?? $session['id']
                    );
                    break;
                    
                case 'payment_intent.succeeded':
                    $paymentIntent = $event['data']['object'];
                    // Log success
                    error_log("Stripe Payment Intent succeeded: " . $paymentIntent['id']);
                    break;
                    
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event['data']['object'];
                    // Log failure
                    error_log("Stripe Payment Intent failed: " . $paymentIntent['id']);
                    break;
            }
            
            http_response_code(200);
            echo json_encode(['received' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => $result['error']]);
        }
    }
}
?>
