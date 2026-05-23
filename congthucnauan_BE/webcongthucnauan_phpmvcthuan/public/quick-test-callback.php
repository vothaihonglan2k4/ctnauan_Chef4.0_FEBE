<?php
/**
 * Quick Test Callback - Debug Tool
 * Tạo link callback nhanh từ mã giao dịch
 */

// Get base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$projectPath = str_replace('/public/quick-test-callback.php', '', $_SERVER['PHP_SELF']);
$baseUrl = $protocol . "://" . $host . $projectPath;

// Get transaction ID from query string
$transactionId = isset($_GET['id']) ? $_GET['id'] : '';
$gateway = isset($_GET['gateway']) ? $_GET['gateway'] : 'momo';
$result = isset($_GET['result']) ? $_GET['result'] : 'success';

// Auto redirect if all params are set
if (!empty($transactionId) && isset($_GET['auto']) && $_GET['auto'] == '1') {
    if ($gateway === 'momo') {
        $url = $baseUrl . '/payments/momo_return_test?' . http_build_query([
            'orderId' => $transactionId,
            'requestId' => $transactionId,
            'amount' => 50000,
            'orderInfo' => 'Test payment',
            'transId' => rand(100000000, 999999999),
            'resultCode' => $result === 'success' ? '0' : '1',
            'message' => $result === 'success' ? 'Successful' : 'Failed',
            'payType' => 'qr',
            'responseTime' => time() * 1000,
            'extraData' => '',
            'signature' => 'test_' . md5(time())
        ]);
    } else {
        $url = $baseUrl . '/payments/vnpay_return_test?' . http_build_query([
            'vnp_TxnRef' => $transactionId,
            'vnp_Amount' => 50000 * 100,
            'vnp_ResponseCode' => $result === 'success' ? '00' : '24',
            'vnp_TransactionNo' => rand(100000000, 999999999),
            'vnp_BankCode' => 'NCB',
            'vnp_CardType' => 'ATM',
            'vnp_OrderInfo' => 'Test payment',
            'vnp_PayDate' => date('YmdHis'),
            'vnp_TmnCode' => 'CGWT28A1',
            'vnp_TransactionStatus' => $result === 'success' ? '00' : '02',
            'vnp_SecureHash' => 'test_' . md5(time())
        ]);
    }
    header('Location: ' . $url);
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Test Callback - Debug Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .tool-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }
        .test-link {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            word-break: break-all;
        }
        .btn-test {
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 10px;
            margin: 5px;
        }
        .quick-link {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="tool-card">
                    <div class="card-header text-center">
                        <h2 class="mb-0"><i class="fas fa-bug"></i> Quick Test Callback</h2>
                        <p class="mb-0 mt-2">Debug Tool - Tạo link callback nhanh</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="alert alert-info">
                            <i class="fas fa-lightbulb"></i>
                            <strong>Hướng dẫn nhanh:</strong> Copy mã giao dịch từ trang MoMo/VNPay, paste vào đây, click nút test!
                        </div>

                        <form method="GET" id="quickTestForm">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Mã giao dịch:</label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       name="id" 
                                       id="transactionId"
                                       value="<?php echo htmlspecialchars($transactionId); ?>"
                                       placeholder="Ví dụ: 1762188606"
                                       required>
                                <small class="text-muted">Copy từ "Mã đơn hàng" trong trang thanh toán</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Gateway:</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="gateway" id="momo" value="momo" <?php echo $gateway === 'momo' ? 'checked' : ''; ?>>
                                    <label class="btn btn-outline-danger" for="momo">
                                        <i class="fas fa-wallet"></i> MoMo
                                    </label>

                                    <input type="radio" class="btn-check" name="gateway" id="vnpay" value="vnpay" <?php echo $gateway === 'vnpay' ? 'checked' : ''; ?>>
                                    <label class="btn btn-outline-primary" for="vnpay">
                                        <i class="fas fa-credit-card"></i> VNPay
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Kết quả:</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="result" id="success" value="success" <?php echo $result === 'success' ? 'checked' : ''; ?>>
                                    <label class="btn btn-outline-success" for="success">
                                        <i class="fas fa-check-circle"></i> Thành công
                                    </label>

                                    <input type="radio" class="btn-check" name="result" id="failed" value="failed" <?php echo $result === 'failed' ? 'checked' : ''; ?>>
                                    <label class="btn btn-outline-danger" for="failed">
                                        <i class="fas fa-times-circle"></i> Thất bại
                                    </label>
                                </div>
                            </div>

                            <input type="hidden" name="auto" value="1">

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-test btn-lg">
                                    <i class="fas fa-rocket"></i> Test Ngay
                                </button>
                            </div>
                        </form>

                        <?php if (!empty($transactionId) && !isset($_GET['auto'])): ?>
                        <hr class="my-4">
                        
                        <h5><i class="fas fa-link"></i> Link test được tạo:</h5>
                        
                        <div class="quick-link">
                            <strong>✅ Test THÀNH CÔNG:</strong>
                            <div class="test-link">
                                <a href="?id=<?php echo urlencode($transactionId); ?>&gateway=<?php echo $gateway; ?>&result=success&auto=1" 
                                   target="_blank" 
                                   class="text-decoration-none">
                                    Click để test thành công
                                </a>
                            </div>
                        </div>

                        <div class="quick-link">
                            <strong>❌ Test THẤT BẠI:</strong>
                            <div class="test-link">
                                <a href="?id=<?php echo urlencode($transactionId); ?>&gateway=<?php echo $gateway; ?>&result=failed&auto=1" 
                                   target="_blank" 
                                   class="text-decoration-none">
                                    Click để test thất bại
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <hr class="my-4">

                        <div class="alert alert-success">
                            <strong><i class="fas fa-history"></i> Test giao dịch gần đây:</strong>
                            <div class="mt-3">
                                <?php
                                // Quick links for recent transactions
                                $recentIds = ['1762188606', '1762187645', '1762187495'];
                                foreach ($recentIds as $id) {
                                    echo '<div class="mb-2">';
                                    echo '<code>' . $id . '</code> - ';
                                    echo '<a href="?id=' . $id . '&gateway=momo&result=success&auto=1" class="btn btn-sm btn-success me-2">✓ Success</a>';
                                    echo '<a href="?id=' . $id . '&gateway=momo&result=failed&auto=1" class="btn btn-sm btn-danger">✗ Failed</a>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?php echo $baseUrl; ?>/public/fix-pending-payments.php" class="btn btn-light btn-lg me-2">
                        <i class="fas fa-list"></i> Fix Pending
                    </a>
                    <a href="<?php echo $baseUrl; ?>/public/test-payment-simulator.php" class="btn btn-light btn-lg me-2">
                        <i class="fas fa-vial"></i> Full Simulator
                    </a>
                    <a href="<?php echo $baseUrl; ?>/payments" class="btn btn-light btn-lg">
                        <i class="fas fa-wallet"></i> Lịch sử
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-fill from URL hash
        if (window.location.hash) {
            const id = window.location.hash.substring(1);
            if (id) {
                document.getElementById('transactionId').value = id;
            }
        }
    </script>
</body>
</html>

