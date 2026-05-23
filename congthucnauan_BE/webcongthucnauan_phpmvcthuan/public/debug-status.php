<?php
/**
 * Debug Status Page
 * Xem trạng thái hệ thống và test redirect
 */

session_start();

// Load config
require_once '../app/config/config.php';

// Get base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$projectPath = str_replace('/public/debug-status.php', '', $_SERVER['PHP_SELF']);
$baseUrl = $protocol . "://" . $host . $projectPath;

// Connect to database
try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbStatus = '<span class="badge bg-success">✓ Connected</span>';
} catch(PDOException $e) {
    $dbStatus = '<span class="badge bg-danger">✗ Failed: ' . $e->getMessage() . '</span>';
    $db = null;
}

// Get latest payment
$latestPayment = null;
if ($db) {
    $stmt = $db->query("SELECT * FROM payments ORDER BY id DESC LIMIT 1");
    $latestPayment = $stmt->fetch(PDO::FETCH_OBJ);
}

// Check session
$sessionData = [
    'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set',
    'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Not set',
    'user_email' => isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'Not set',
    'pending_course_enrollment' => isset($_SESSION['pending_course_enrollment']) ? json_encode($_SESSION['pending_course_enrollment'], JSON_PRETTY_PRINT) : 'Not set',
    'success_msg' => isset($_SESSION['success_msg']) ? $_SESSION['success_msg'] : 'Not set',
    'error_msg' => isset($_SESSION['error_msg']) ? $_SESSION['error_msg'] : 'Not set',
];

// Test action
$testResult = '';
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'test_redirect':
            header('Location: ' . $baseUrl . '/payments');
            exit;
            break;
        case 'clear_session':
            session_destroy();
            session_start();
            $testResult = '<div class="alert alert-success">✓ Session cleared!</div>';
            break;
        case 'set_success_msg':
            $_SESSION['success_msg'] = 'Test success message - ' . date('H:i:s');
            $testResult = '<div class="alert alert-success">✓ Success message set!</div>';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            padding: 20px;
        }
        .debug-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .status-good { color: #28a745; }
        .status-bad { color: #dc3545; }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h1><i class="fas fa-bug"></i> Debug Status Page</h1>
        <p class="text-muted">Kiểm tra trạng thái hệ thống và test redirect</p>

        <?php echo $testResult; ?>

        <!-- System Status -->
        <div class="row">
            <div class="col-md-6">
                <div class="debug-card">
                    <h4><i class="fas fa-server"></i> System Status</h4>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>PHP Version:</strong></td>
                            <td><?php echo phpversion(); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Database:</strong></td>
                            <td><?php echo $dbStatus; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Session ID:</strong></td>
                            <td><code><?php echo session_id(); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Base URL:</strong></td>
                            <td><code><?php echo $baseUrl; ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>URL_ROOT:</strong></td>
                            <td><code><?php echo defined('URL_ROOT') ? URL_ROOT : 'Not defined'; ?></code></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="debug-card">
                    <h4><i class="fas fa-user"></i> Session Data</h4>
                    <table class="table table-sm">
                        <?php foreach ($sessionData as $key => $value): ?>
                        <tr>
                            <td><strong><?php echo $key; ?>:</strong></td>
                            <td><?php echo is_string($value) ? htmlspecialchars($value) : $value; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Latest Payment -->
        <?php if ($latestPayment): ?>
        <div class="debug-card">
            <h4><i class="fas fa-receipt"></i> Latest Payment</h4>
            <pre><?php print_r($latestPayment); ?></pre>
        </div>
        <?php endif; ?>

        <!-- Test Actions -->
        <div class="debug-card">
            <h4><i class="fas fa-flask"></i> Test Actions</h4>
            <div class="btn-group" role="group">
                <a href="?action=test_redirect" class="btn btn-primary">
                    <i class="fas fa-arrow-right"></i> Test Redirect to /payments
                </a>
                <a href="?action=set_success_msg" class="btn btn-success">
                    <i class="fas fa-check"></i> Set Success Message
                </a>
                <a href="?action=clear_session" class="btn btn-warning" onclick="return confirm('Clear session?')">
                    <i class="fas fa-trash"></i> Clear Session
                </a>
            </div>

            <hr>

            <h5>Quick Links:</h5>
            <div class="btn-group-vertical w-100">
                <a href="<?php echo $baseUrl; ?>/payments" class="btn btn-outline-primary" target="_blank">
                    → Open /payments
                </a>
                <a href="<?php echo $baseUrl; ?>/courses/my_courses" class="btn btn-outline-primary" target="_blank">
                    → Open /courses/my_courses
                </a>
                <a href="<?php echo $baseUrl; ?>/payments/checkout" class="btn btn-outline-success" target="_blank">
                    → Open /payments/checkout
                </a>
                <a href="quick-test-callback.php" class="btn btn-outline-info">
                    → Quick Test Callback
                </a>
            </div>
        </div>

        <!-- Error Log -->
        <div class="debug-card">
            <h4><i class="fas fa-exclamation-triangle"></i> Recent Error Log</h4>
            <?php
            $errorLog = '../app/logs/error.log';
            if (file_exists($errorLog)) {
                $lines = array_slice(file($errorLog), -20);
                echo '<pre style="max-height: 300px; overflow-y: auto;">';
                echo htmlspecialchars(implode('', $lines));
                echo '</pre>';
            } else {
                echo '<p class="text-muted">No error log found</p>';
            }
            ?>
        </div>

        <!-- Test Payment Callback -->
        <div class="debug-card bg-info bg-opacity-10">
            <h4><i class="fas fa-rocket"></i> Test Payment Now</h4>
            <p>Test với giao dịch mới nhất:</p>
            <?php if ($latestPayment): ?>
            <div class="d-grid gap-2">
                <a href="quick-test-callback.php?id=<?php echo $latestPayment->transaction_id; ?>&gateway=momo&result=success&auto=1" 
                   class="btn btn-success btn-lg">
                    <i class="fas fa-check-circle"></i> Test SUCCESS với giao dịch #<?php echo $latestPayment->id; ?>
                </a>
                <a href="../payments/momo_return_test?orderId=<?php echo $latestPayment->transaction_id; ?>&requestId=<?php echo $latestPayment->transaction_id; ?>&amount=<?php echo $latestPayment->amount; ?>&transId=<?php echo rand(100000000, 999999999); ?>&resultCode=0&message=Success" 
                   class="btn btn-primary btn-lg">
                    <i class="fas fa-play"></i> Direct Callback Test (MoMo)
                </a>
            </div>
            <?php else: ?>
            <p class="text-muted">No payment found. Create one first!</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

