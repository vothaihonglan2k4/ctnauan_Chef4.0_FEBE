<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1>Lịch sử thanh toán</h1>
        </div>
        <div class="col-auto">
            <a href="<?php echo URL_ROOT; ?>/payments/checkout" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thanh toán mới
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Thanh toán của bạn</h5>
        </div>
        <div class="card-body">
            <?php if(!empty($data['payments'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mã thanh toán</th>
                                <th>Số tiền</th>
                                <th>Phương thức</th>
                                <th>Trạng thái</th>
                                <th>Mã giao dịch</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['payments'] as $payment): ?>
                                <tr>
                                    <td>#<?php echo $payment->id; ?></td>
                                    <td><?php echo number_format($payment->amount); ?>₫</td>
                                    <td>
                                        <?php 
                                        switch($payment->payment_method) {
                                            case 'credit_card':
                                                echo '<span class="badge bg-info">Thẻ tín dụng</span>';
                                                break;
                                            case 'bank_transfer':
                                                echo '<span class="badge bg-primary">Chuyển khoản</span>';
                                                break;
                                            case 'momo':
                                                echo '<span class="badge bg-danger">MoMo</span>';
                                                break;
                                            default:
                                                echo $payment->payment_method;
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if($payment->status == 'completed'): ?>
                                            <span class="badge bg-success">Hoàn thành</span>
                                        <?php elseif($payment->status == 'pending'): ?>
                                            <span class="badge bg-warning">Đang xử lý</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo $payment->status; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $payment->transaction_id; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($payment->created_at)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center p-5">
                    <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                    <p class="lead">Bạn chưa có thanh toán nào.</p>
                    <a href="<?php echo URL_ROOT; ?>/payments/checkout" class="btn btn-primary">Thanh toán mới</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin thanh toán</h5>
                </div>
                <div class="card-body">
                    <p>Chúng tôi cung cấp nhiều phương thức thanh toán để bạn có thể ủng hộ website và mở khóa các tính năng cao cấp.</p>
                    <h6>Quyền lợi thành viên trả phí:</h6>
                    <ul>
                        <li>Truy cập các công thức đặc biệt</li>
                        <li>Tạo sách công thức cá nhân</li>
                        <li>Lưu trữ công thức không giới hạn</li>
                        <li>Không hiển thị quảng cáo</li>
                    </ul>
                    <a href="<?php echo URL_ROOT; ?>/payments/checkout" class="btn btn-primary">Đăng ký gói thành viên</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
