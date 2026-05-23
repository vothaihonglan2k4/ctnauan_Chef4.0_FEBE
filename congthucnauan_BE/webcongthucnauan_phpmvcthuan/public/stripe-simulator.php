<?php
/**
 * Stripe Payment Simulator
 * Test Stripe checkout flow mà không cần API keys thật
 */

session_start();

// Get base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$projectPath = str_replace('/public/stripe-simulator.php', '', $_SERVER['PHP_SELF']);
$baseUrl = $protocol . "://" . $host . $projectPath;

// Get session info from query string
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : 'cs_test_' . time();
$amount = isset($_GET['amount']) ? $_GET['amount'] : 50000;
$description = isset($_GET['description']) ? $_GET['description'] : 'Test Payment';

// Handle payment action
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'pay') {
        // Redirect to success URL
        header('Location: ' . $baseUrl . '/payments/stripe_success?session_id=' . $session_id);
        exit;
    } elseif ($_POST['action'] === 'cancel') {
        // Redirect to cancel URL
        header('Location: ' . $baseUrl . '/payments/stripe_cancel');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Checkout - Test Mode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #635bff 0%, #4f46ba 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .stripe-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }
        .stripe-header {
            background: #635bff;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .stripe-body {
            padding: 40px;
        }
        .test-badge {
            background: #ffd700;
            color: #000;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 15px;
        }
        .amount-display {
            font-size: 48px;
            font-weight: bold;
            color: #635bff;
            text-align: center;
            margin: 20px 0;
        }
        .test-cards {
            background: #f7f9fc;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .card-number {
            background: white;
            border: 2px solid #e3e8ee;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .card-number:hover {
            border-color: #635bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 91, 255, 0.2);
        }
        .card-number.success {
            border-color: #00d924;
        }
        .card-number.declined {
            border-color: #ff4444;
        }
        .btn-stripe {
            background: #635bff;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-stripe:hover {
            background: #4f46ba;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 91, 255, 0.4);
        }
        .btn-cancel {
            background: transparent;
            color: #635bff;
            border: 2px solid #635bff;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s;
        }
        .btn-cancel:hover {
            background: #f7f9fc;
        }
        .secure-badge {
            text-align: center;
            color: #8a94a6;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="stripe-container">
        <div class="stripe-header">
            <div class="test-badge">
                <i class="fas fa-vial"></i> TEST MODE
            </div>
            <h2 class="mb-0"><i class="fab fa-cc-stripe"></i> Stripe Checkout</h2>
        </div>
        
        <div class="stripe-body">
            <div class="text-center mb-4">
                <h5><?php echo htmlspecialchars($description); ?></h5>
                <div class="amount-display">
                    <?php echo number_format($amount); ?>₫
                </div>
            </div>

            <div class="test-cards">
                <h6 class="text-center mb-3"><i class="fas fa-credit-card"></i> Chọn thẻ test</h6>
                
                <div class="card-number success" onclick="selectCard(this)">
                    <div><strong>✅ Thanh toán thành công</strong></div>
                    <div class="mt-2">4242 4242 4242 4242</div>
                    <small class="text-muted">Exp: 12/34 | CVC: 123</small>
                </div>

                <div class="card-number declined" onclick="alert('⚠️ Test thẻ bị từ chối!\n\nTrong môi trường test thật, thẻ này sẽ bị từ chối.')">
                    <div><strong>❌ Thẻ bị từ chối</strong></div>
                    <div class="mt-2">4000 0000 0000 0002</div>
                    <small class="text-muted">Test declined card</small>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="pay">
                <button type="submit" class="btn-stripe">
                    <i class="fas fa-lock"></i> Thanh toán <?php echo number_format($amount); ?>₫
                </button>
            </form>

            <form method="POST">
                <input type="hidden" name="action" value="cancel">
                <button type="submit" class="btn-cancel">
                    <i class="fas fa-arrow-left"></i> Hủy thanh toán
                </button>
            </form>

            <div class="secure-badge">
                <i class="fas fa-shield-alt"></i> Bảo mật bởi Stripe (Simulator)
            </div>

            <div class="alert alert-info mt-3">
                <small>
                    <i class="fas fa-info-circle"></i> 
                    <strong>Simulator Mode:</strong> Đây là trang giả lập Stripe Checkout. 
                    Để test với Stripe thật, vui lòng đăng ký tại 
                    <a href="https://dashboard.stripe.com/register" target="_blank">Stripe Dashboard</a>
                </small>
            </div>
        </div>
    </div>

    <script>
        function selectCard(element) {
            // Remove selection from all cards
            document.querySelectorAll('.card-number').forEach(card => {
                card.style.borderWidth = '2px';
            });
            
            // Highlight selected card
            element.style.borderWidth = '4px';
            
            // Auto submit after 1 second
            setTimeout(() => {
                document.querySelector('form').submit();
            }, 500);
        }
    </script>
</body>
</html>

