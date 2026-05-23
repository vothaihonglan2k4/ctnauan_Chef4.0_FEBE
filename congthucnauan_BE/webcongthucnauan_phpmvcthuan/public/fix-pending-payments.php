<?php
/**
 * Tool nhanh để fix các giao dịch pending
 * CHỈ dùng trong môi trường test!
 */

// Load config
require_once '../app/config/config.php';

// Connect to database
try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Get base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$projectPath = str_replace('/public/fix-pending-payments.php', '', $_SERVER['PHP_SELF']);
$baseUrl = $protocol . "://" . $host . $projectPath;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'fix_all') {
        // Update all pending to completed
        $stmt = $db->prepare("UPDATE payments SET status = 'completed', gateway_transaction_id = CONCAT('TEST_', transaction_id), updated_at = NOW() WHERE status = 'pending'");
        $stmt->execute();
        $count = $stmt->rowCount();
        $message = "✅ Đã cập nhật {$count} giao dịch từ 'pending' → 'completed'";
        $messageType = 'success';
    } elseif ($_POST['action'] === 'fix_one' && !empty($_POST['transaction_id'])) {
        // Update specific transaction
        $stmt = $db->prepare("UPDATE payments SET status = 'completed', gateway_transaction_id = CONCAT('TEST_', transaction_id), updated_at = NOW() WHERE transaction_id = :transaction_id");
        $stmt->execute(['transaction_id' => $_POST['transaction_id']]);
        $count = $stmt->rowCount();
        if ($count > 0) {
            $message = "✅ Đã cập nhật giao dịch #{$_POST['transaction_id']} thành công";
            $messageType = 'success';
        } else {
            $message = "❌ Không tìm thấy giao dịch #{$_POST['transaction_id']}";
            $messageType = 'danger';
        }
    }
}

// Get all pending payments
$stmt = $db->query("SELECT * FROM payments WHERE status = 'pending' ORDER BY created_at DESC");
$pendingPayments = $stmt->fetchAll(PDO::FETCH_OBJ);

// Get recent completed payments
$stmt = $db->query("SELECT * FROM payments WHERE status = 'completed' ORDER BY updated_at DESC LIMIT 5");
$completedPayments = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Pending Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 40px 0;
            min-height: 100vh;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .badge-pending {
            background: #ffc107;
            color: #000;
        }
        .badge-completed {
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-tools"></i> Fix Pending Payments</h3>
                        <p class="mb-0 mt-2">Cập nhật các giao dịch "Đang xử lý" → "Hoàn thành"</p>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Chú ý:</strong> Tool này CHỈ dùng trong môi trường test. Không sử dụng trên production!
                        </div>

                        <h5><i class="fas fa-clock"></i> Giao dịch đang pending (<?php echo count($pendingPayments); ?>)</h5>
                        
                        <?php if (count($pendingPayments) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Transaction ID</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Thời gian</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingPayments as $payment): ?>
                                    <tr>
                                        <td>#<?php echo $payment->id; ?></td>
                                        <td><code><?php echo $payment->transaction_id; ?></code></td>
                                        <td><?php echo $payment->user_id; ?></td>
                                        <td><?php echo number_format($payment->amount); ?>₫</td>
                                        <td>
                                            <span class="badge bg-<?php echo $payment->payment_method === 'momo' ? 'danger' : 'primary'; ?>">
                                                <?php echo strtoupper($payment->payment_method); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($payment->created_at)); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="fix_one">
                                                <input type="hidden" name="transaction_id" value="<?php echo $payment->transaction_id; ?>">
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Fix
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <form method="POST" onsubmit="return confirm('Bạn có chắc muốn cập nhật TẤT CẢ giao dịch pending thành completed?');">
                            <input type="hidden" name="action" value="fix_all">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-magic"></i> Fix tất cả (<?php echo count($pendingPayments); ?> giao dịch)
                            </button>
                        </form>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Không có giao dịch pending nào!
                        </div>
                        <?php endif; ?>

                        <hr class="my-4">

                        <h5><i class="fas fa-check-circle text-success"></i> Giao dịch đã hoàn thành gần đây</h5>
                        <?php if (count($completedPayments) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Transaction ID</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Cập nhật</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($completedPayments as $payment): ?>
                                    <tr>
                                        <td>#<?php echo $payment->id; ?></td>
                                        <td><code><?php echo $payment->transaction_id; ?></code></td>
                                        <td><?php echo number_format($payment->amount); ?>₫</td>
                                        <td>
                                            <span class="badge bg-<?php echo $payment->payment_method === 'momo' ? 'danger' : 'primary'; ?>">
                                                <?php echo strtoupper($payment->payment_method); ?>
                                            </span>
                                        </td>
                                        <td><span class="badge badge-completed">✓ Completed</span></td>
                                        <td><?php echo $payment->updated_at ? date('d/m/Y H:i', strtotime($payment->updated_at)) : '-'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-secondary">Chưa có giao dịch hoàn thành nào.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center">
                    <a href="<?php echo $baseUrl; ?>/payments" class="btn btn-light btn-lg me-2">
                        <i class="fas fa-list"></i> Xem lịch sử thanh toán
                    </a>
                    <a href="<?php echo $baseUrl; ?>/public/test-payment-simulator.php" class="btn btn-light btn-lg">
                        <i class="fas fa-vial"></i> Test Simulator
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

