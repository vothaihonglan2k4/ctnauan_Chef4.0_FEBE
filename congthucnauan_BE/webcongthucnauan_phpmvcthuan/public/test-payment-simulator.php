<?php
/**
 * MoMo & VNPay Payment Test Simulator
 * Công cụ test giả lập callback từ payment gateway
 */

// Lấy base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . "://" . $host;
$projectPath = str_replace('/public/test-payment-simulator.php', '', $_SERVER['PHP_SELF']);
$baseUrl .= $projectPath;

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway Test Simulator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .simulator-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }
        .payment-option {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-option:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        .payment-option.active {
            border-color: #667eea;
            background: #f8f9ff;
        }
        .btn-simulate {
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .gateway-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="simulator-card">
                    <div class="card-header text-center">
                        <h2 class="mb-0"><i class="fas fa-vial"></i> Payment Gateway Test Simulator</h2>
                        <p class="mb-0 mt-2">Công cụ test thanh toán MoMo & VNPay</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="info-box">
                            <i class="fas fa-info-circle text-primary"></i>
                            <strong>Hướng dẫn:</strong> Công cụ này giả lập callback từ MoMo/VNPay để test thanh toán mà không cần vào trang payment gateway thật.
                        </div>

                        <form id="simulatorForm">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Chọn Payment Gateway:</label>
                                
                                <div class="payment-option" data-gateway="momo">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-wallet fa-3x text-danger"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">MoMo Payment</h5>
                                            <small class="text-muted">Giả lập callback từ MoMo</small>
                                        </div>
                                        <div>
                                            <input type="radio" name="gateway" value="momo" class="form-check-input" style="width: 24px; height: 24px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="payment-option" data-gateway="vnpay">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-credit-card fa-3x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">VNPay Payment</h5>
                                            <small class="text-muted">Giả lập callback từ VNPay</small>
                                        </div>
                                        <div>
                                            <input type="radio" name="gateway" value="vnpay" class="form-check-input" style="width: 24px; height: 24px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Transaction ID:</label>
                                <input type="text" class="form-control form-control-lg" id="transactionId" 
                                       placeholder="Nhập mã giao dịch từ URL (requestId hoặc vnp_TxnRef)"
                                       value="<?php echo isset($_GET['orderId']) ? htmlspecialchars($_GET['orderId']) : ''; ?>">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb"></i> Lấy từ URL thanh toán (tham số: orderId, requestId hoặc vnp_TxnRef)
                                </small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Kết quả thanh toán:</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="result" id="success" value="success" checked>
                                    <label class="btn btn-outline-success btn-lg" for="success">
                                        <i class="fas fa-check-circle"></i> Thành công
                                    </label>

                                    <input type="radio" class="btn-check" name="result" id="failed" value="failed">
                                    <label class="btn btn-outline-danger btn-lg" for="failed">
                                        <i class="fas fa-times-circle"></i> Thất bại
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Số tiền (VNĐ):</label>
                                <input type="number" class="form-control form-control-lg" id="amount" 
                                       value="50000" min="1000">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-simulate btn-lg">
                                    <i class="fas fa-play-circle"></i> Giả lập thanh toán
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="info-box bg-warning bg-opacity-10 border-warning">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            <strong>Lưu ý:</strong> Đây chỉ là công cụ test, chỉ sử dụng trong môi trường development!
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>URL Callback (TEST MODE):</strong><br>
                                • MoMo: <code><?php echo $baseUrl; ?>/payments/momo_return_test</code><br>
                                • VNPay: <code><?php echo $baseUrl; ?>/payments/vnpay_return_test</code><br>
                                <span class="badge bg-warning text-dark mt-2">⚠️ Test endpoints - Bỏ qua signature verification</span>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="text-center mt-4">
                    <a href="<?php echo $baseUrl; ?>/payments/checkout" class="btn btn-light btn-lg me-2">
                        <i class="fas fa-shopping-cart"></i> Thanh toán gói
                    </a>
                    <a href="<?php echo $baseUrl; ?>/courses" class="btn btn-light btn-lg">
                        <i class="fas fa-graduation-cap"></i> Khóa học
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const baseUrl = '<?php echo $baseUrl; ?>';
        
        // Handle payment option selection
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Handle form submission
        document.getElementById('simulatorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const gateway = document.querySelector('input[name="gateway"]:checked');
            const result = document.querySelector('input[name="result"]:checked').value;
            const transactionId = document.getElementById('transactionId').value;
            const amount = document.getElementById('amount').value;
            
            if (!gateway) {
                alert('Vui lòng chọn payment gateway!');
                return;
            }
            
            if (!transactionId) {
                alert('Vui lòng nhập Transaction ID!');
                return;
            }
            
            // Build callback URL based on gateway
            let callbackUrl = '';
            
            if (gateway.value === 'momo') {
                // MoMo callback parameters
                const params = new URLSearchParams({
                    'partnerCode': 'MOMOBKUN20180529',
                    'orderId': transactionId,
                    'requestId': transactionId,
                    'amount': amount,
                    'orderInfo': 'Test payment',
                    'orderType': 'momo_wallet',
                    'transId': Math.floor(Math.random() * 1000000000),
                    'resultCode': result === 'success' ? '0' : '1',
                    'message': result === 'success' ? 'Successful.' : 'Transaction failed',
                    'payType': 'qr',
                    'responseTime': new Date().getTime(),
                    'extraData': '',
                    'signature': 'test_signature_' + Math.random().toString(36).substring(7)
                });
                
                callbackUrl = baseUrl + '/payments/momo_return_test?' + params.toString();
                
            } else if (gateway.value === 'vnpay') {
                // VNPay callback parameters
                const params = new URLSearchParams({
                    'vnp_Amount': amount * 100,
                    'vnp_BankCode': 'NCB',
                    'vnp_BankTranNo': 'VNP' + Math.floor(Math.random() * 1000000000),
                    'vnp_CardType': 'ATM',
                    'vnp_OrderInfo': 'Test payment',
                    'vnp_PayDate': new Date().toISOString().replace(/[-:]/g, '').split('.')[0],
                    'vnp_ResponseCode': result === 'success' ? '00' : '24',
                    'vnp_TmnCode': 'CGWT28A1',
                    'vnp_TransactionNo': Math.floor(Math.random() * 1000000000),
                    'vnp_TransactionStatus': result === 'success' ? '00' : '02',
                    'vnp_TxnRef': transactionId,
                    'vnp_SecureHash': 'test_hash_' + Math.random().toString(36).substring(7)
                });
                
                callbackUrl = baseUrl + '/payments/vnpay_return_test?' + params.toString();
            }
            
            // Show confirmation
            if (confirm('Bạn có chắc muốn giả lập thanh toán ' + (result === 'success' ? 'THÀNH CÔNG' : 'THẤT BẠI') + ' không?')) {
                // Redirect to callback URL
                window.location.href = callbackUrl;
            }
        });

        // Auto-fill transaction ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('orderId') || urlParams.get('requestId') || urlParams.get('vnp_TxnRef');
        if (orderId) {
            document.getElementById('transactionId').value = orderId;
        }
    </script>
</body>
</html>

