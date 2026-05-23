<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #<?php echo $data['payment']->id; ?> - Công Thức Nấu Ăn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 15px;
            }
        }
        
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .invoice-header {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-info h1 {
            color: #0d6efd;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-info p {
            color: #666;
            margin: 2px 0;
        }
        
        .invoice-title {
            text-align: right;
        }
        
        .invoice-title h2 {
            font-size: 36px;
            color: #333;
            margin: 0;
        }
        
        .invoice-title p {
            color: #666;
            margin: 5px 0;
        }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .customer-info, .payment-info {
            flex: 1;
        }
        
        .customer-info h5, .payment-info h5 {
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
            display: inline-block;
            width: 150px;
        }
        
        .info-value {
            color: #333;
        }
        
        .invoice-table {
            margin-bottom: 30px;
        }
        
        .invoice-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .invoice-table th {
            background: #0d6efd;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        .invoice-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .invoice-table tr:hover {
            background: #f8f9fa;
        }
        
        .invoice-total {
            text-align: right;
            margin-top: 20px;
        }
        
        .total-row {
            padding: 10px 0;
            font-size: 16px;
        }
        
        .total-row.grand-total {
            border-top: 2px solid #0d6efd;
            padding-top: 15px;
            margin-top: 10px;
        }
        
        .total-row .label {
            display: inline-block;
            width: 200px;
            font-weight: 600;
        }
        
        .total-row .amount {
            display: inline-block;
            width: 150px;
            text-align: right;
        }
        
        .grand-total .amount {
            font-size: 24px;
            color: #0d6efd;
            font-weight: bold;
        }
        
        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            color: #666;
        }
        
        .badge-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-failed {
            background: #f8d7da;
            color: #721c24;
        }
        
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary btn-print">
            <i class="fas fa-print"></i> In hóa đơn
        </button>
        <a href="<?php echo URL_ROOT; ?>/admin/payments" class="btn btn-secondary" style="position: fixed; top: 20px; right: 160px;">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Invoice Container -->
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header row">
            <div class="col-md-6 company-info">
                <h1><i class="fas fa-utensils"></i> Công Thức Nấu Ăn</h1>
                <p><i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận XYZ, TP. Đà Nẵng</p>
                <p><i class="fas fa-phone"></i> (0236) 123-4567</p>
                <p><i class="fas fa-envelope"></i> info@congthucnauan.com</p>
                <p><i class="fas fa-globe"></i> www.congthucnauan.com</p>
            </div>
            <div class="col-md-6 invoice-title">
                <h2>HÓA ĐƠN</h2>
                <p><strong>Số hóa đơn:</strong> #<?php echo str_pad($data['payment']->id, 6, '0', STR_PAD_LEFT); ?></p>
                <p><strong>Ngày:</strong> <?php echo date('d/m/Y', strtotime($data['payment']->created_at)); ?></p>
                <p>
                    <strong>Trạng thái:</strong> 
                    <?php if($data['payment']->status == 'completed'): ?>
                        <span class="badge-status badge-completed">Đã thanh toán</span>
                    <?php elseif($data['payment']->status == 'pending'): ?>
                        <span class="badge-status badge-pending">Đang xử lý</span>
                    <?php else: ?>
                        <span class="badge-status badge-failed">Thất bại</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="customer-info">
                <h5><i class="fas fa-user"></i> Thông tin khách hàng</h5>
                <div class="info-row">
                    <span class="info-label">Họ tên:</span>
                    <span class="info-value"><?php echo $data['payment']->user_name; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo $data['payment']->user_email; ?></span>
                </div>
            </div>
            
            <div class="payment-info">
                <h5><i class="fas fa-credit-card"></i> Thông tin thanh toán</h5>
                <div class="info-row">
                    <span class="info-label">Phương thức:</span>
                    <span class="info-value">
                        <?php 
                        switch($data['payment']->payment_method) {
                            case 'credit_card':
                                echo 'Thẻ tín dụng';
                                break;
                            case 'bank_transfer':
                                echo 'Chuyển khoản ngân hàng';
                                break;
                            case 'momo':
                                echo 'Ví MoMo';
                                break;
                            case 'vnpay':
                                echo 'VNPay';
                                break;
                            case 'stripe':
                                echo 'Stripe';
                                break;
                            case 'demo':
                                echo 'Demo (Thanh toán thử)';
                                break;
                            default:
                                echo ucfirst($data['payment']->payment_method);
                        }
                        ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mã giao dịch:</span>
                    <span class="info-value"><?php echo $data['payment']->transaction_id ?? 'N/A'; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngày thanh toán:</span>
                    <span class="info-value"><?php echo date('d/m/Y H:i:s', strtotime($data['payment']->created_at)); ?></span>
                </div>
            </div>
        </div>

        <!-- Invoice Table -->
        <div class="invoice-table">
            <table>
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Khóa học</th>
                        <th width="150" class="text-end">Ngày đăng ký</th>
                        <th width="150" class="text-end">Giá</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['payment']->courses)): ?>
                        <?php $stt = 1; foreach($data['payment']->courses as $course): ?>
                            <tr>
                                <td><?php echo $stt++; ?></td>
                                <td><?php echo $course->title; ?></td>
                                <td class="text-end"><?php echo date('d/m/Y', strtotime($course->enrollment_date)); ?></td>
                                <td class="text-end"><?php echo number_format($course->price); ?>₫</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Không có thông tin khóa học</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Total -->
        <div class="invoice-total">
            <div class="total-row grand-total">
                <span class="label">Tổng cộng:</span>
                <span class="amount"><?php echo number_format($data['payment']->amount); ?>₫</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <p><strong>Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!</strong></p>
            <p>Hóa đơn này được tạo tự động và có giá trị mà không cần chữ ký.</p>
            <p style="margin-top: 20px; font-size: 12px;">
                <i class="fas fa-question-circle"></i> Nếu có thắc mắc, vui lòng liên hệ: 
                <strong>support@congthucnauan.com</strong> hoặc <strong>(0236) 123-4567</strong>
            </p>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

