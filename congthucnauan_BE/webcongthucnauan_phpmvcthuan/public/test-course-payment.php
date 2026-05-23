<?php
/**
 * Test Course Payment Flow
 * Debug tool để test thanh toán khóa học
 */

session_start();

// Load config
require_once '../app/config/config.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    die('Vui lòng <a href="' . URL_ROOT . '/users/login">đăng nhập</a> trước!');
}

// Get base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$projectPath = str_replace('/public/test-course-payment.php', '', $_SERVER['PHP_SELF']);
$baseUrl = $protocol . "://" . $host . $projectPath;

// Connect to database
try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

// Get all courses
$stmt = $db->query("SELECT * FROM courses WHERE status = 'published' ORDER BY created_at DESC LIMIT 10");
$courses = $stmt->fetchAll(PDO::FETCH_OBJ);

// Get error log
$errorLog = '../app/logs/error.log';
$logLines = [];
if (file_exists($errorLog)) {
    $logLines = array_slice(file($errorLog), -30);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Course Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .test-card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .course-item { border: 2px solid #e0e0e0; border-radius: 10px; padding: 15px; margin: 10px 0; transition: all 0.3s; }
        .course-item:hover { border-color: #635BFF; background: #f8f9ff; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-vial"></i> Test Course Payment Flow</h1>
        <p class="text-muted">Debug tool - Test thanh toán khóa học với Stripe</p>

        <!-- User Info -->
        <div class="test-card">
            <h4><i class="fas fa-user"></i> Thông tin đăng nhập</h4>
            <table class="table table-sm">
                <tr>
                    <td><strong>User ID:</strong></td>
                    <td><?php echo $_SESSION['user_id']; ?></td>
                </tr>
                <tr>
                    <td><strong>User Name:</strong></td>
                    <td><?php echo $_SESSION['user_name']; ?></td>
                </tr>
                <tr>
                    <td><strong>User Email:</strong></td>
                    <td><?php echo $_SESSION['user_email']; ?></td>
                </tr>
            </table>
        </div>

        <!-- Available Courses -->
        <div class="test-card">
            <h4><i class="fas fa-graduation-cap"></i> Danh sách khóa học có thể test</h4>
            <?php if (count($courses) > 0): ?>
                <?php foreach ($courses as $course): ?>
                <div class="course-item">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-1"><?php echo htmlspecialchars($course->title); ?></h5>
                            <p class="mb-0 text-muted">Giá: <strong><?php echo number_format($course->price); ?>₫</strong></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?php echo $baseUrl; ?>/courses/checkout/<?php echo $course->id; ?>" 
                               class="btn btn-primary me-2">
                                <i class="fas fa-shopping-cart"></i> Checkout
                            </a>
                            <a href="<?php echo $baseUrl; ?>/courses/show/<?php echo $course->id; ?>" 
                               class="btn btn-outline-secondary" target="_blank">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning">Không có khóa học nào!</div>
            <?php endif; ?>
        </div>

        <!-- Stripe Test Cards -->
        <div class="test-card bg-success bg-opacity-10">
            <h4><i class="fab fa-cc-stripe"></i> Stripe Test Cards</h4>
            <div class="row">
                <div class="col-md-6">
                    <h6>✅ Thanh toán thành công:</h6>
                    <code>4242 4242 4242 4242</code><br>
                    <small>Exp: 12/34 | CVC: 123</small>
                </div>
                <div class="col-md-6">
                    <h6>❌ Thẻ bị từ chối:</h6>
                    <code>4000 0000 0000 0002</code><br>
                    <small>Exp: 12/34 | CVC: 123</small>
                </div>
            </div>
        </div>

        <!-- Flow Instructions -->
        <div class="test-card">
            <h4><i class="fas fa-list-ol"></i> Hướng dẫn test</h4>
            <ol>
                <li>Nhấn nút <strong>"Checkout"</strong> ở khóa học bất kỳ phía trên</li>
                <li>Tại trang checkout, <strong>Stripe đã được chọn sẵn</strong></li>
                <li>Nhấn nút <strong>"Thanh toán"</strong></li>
                <li>Sẽ được chuyển đến <strong>trang Stripe Checkout</strong> (thật)</li>
                <li>Nhập thẻ test: <code>4242 4242 4242 4242</code></li>
                <li>Nhấn <strong>"Pay"</strong></li>
                <li>✅ <strong>Thành công!</strong> Tự động đăng ký khóa học</li>
            </ol>
        </div>

        <!-- Recent Error Log -->
        <div class="test-card">
            <h4><i class="fas fa-bug"></i> Recent Error Log (30 lines)</h4>
            <?php if (count($logLines) > 0): ?>
                <pre><?php echo htmlspecialchars(implode('', $logLines)); ?></pre>
            <?php else: ?>
                <p class="text-muted">No error log found</p>
            <?php endif; ?>
        </div>

        <!-- Quick Links -->
        <div class="text-center mt-4">
            <a href="<?php echo $baseUrl; ?>/courses/my_courses" class="btn btn-success btn-lg me-2">
                <i class="fas fa-book"></i> Khóa học của tôi
            </a>
            <a href="<?php echo $baseUrl; ?>/payments" class="btn btn-info btn-lg me-2">
                <i class="fas fa-wallet"></i> Lịch sử thanh toán
            </a>
            <a href="debug-status.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-cog"></i> Debug Status
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

