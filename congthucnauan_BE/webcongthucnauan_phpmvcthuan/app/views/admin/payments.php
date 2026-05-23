<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h1>Quản lý thanh toán</h1>
        </div>
        <div class="col-auto">
            <a href="<?php echo URL_ROOT; ?>/admin" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Danh sách thanh toán</h5>
                </div>
                <div class="col-auto">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="#">Tất cả thanh toán</a></li>
                            <li><a class="dropdown-item" href="#">Thanh toán thành công</a></li>
                            <li><a class="dropdown-item" href="#">Thanh toán đang xử lý</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th style="width: 120px;">Người dùng</th>
                            <th style="width: 100px;">Số tiền</th>
                            <th style="width: 100px;">Phương thức</th>
                            <th style="width: 100px;">Trạng thái</th>
                            <th style="width: 150px;">Mã giao dịch</th>
                            <th style="width: 130px;">Ngày thanh toán</th>
                            <th style="width: 180px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['payments'] as $payment): ?>
                            <tr>
                                <td><?php echo $payment->id; ?></td>
                                <td><?php echo $payment->user_name; ?></td>
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
                                <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo $payment->transaction_id; ?>">
                                    <?php echo $payment->transaction_id; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($payment->created_at)); ?></td>
                                <td style="white-space: nowrap;">
                                    <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#paymentDetailModal<?php echo $payment->id; ?>" style="display: block; width: 100%;">
                                        <i class="fas fa-eye"></i> Chi tiết
                                    </button>
                                    <a href="<?php echo URL_ROOT; ?>/admin/invoice/<?php echo $payment->id; ?>" class="btn btn-sm btn-success" target="_blank" style="display: block; width: 100%;">
                                        <i class="fas fa-print"></i> In hóa đơn
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Payment Detail Modals - Moved outside of the table structure -->
    <?php foreach($data['payments'] as $payment): ?>
        <div class="modal fade" id="paymentDetailModal<?php echo $payment->id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chi tiết thanh toán #<?php echo $payment->id; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <tr>
                                <th>ID:</th>
                                <td><?php echo $payment->id; ?></td>
                            </tr>
                            <tr>
                                <th>Người dùng:</th>
                                <td><?php echo $payment->user_name; ?></td>
                            </tr>
                            <tr>
                                <th>Số tiền:</th>
                                <td><?php echo number_format($payment->amount); ?>₫</td>
                            </tr>
                            <tr>
                                <th>Phương thức:</th>
                                <td>
                                    <?php 
                                    switch($payment->payment_method) {
                                        case 'credit_card':
                                            echo 'Thẻ tín dụng';
                                            break;
                                        case 'bank_transfer':
                                            echo 'Chuyển khoản';
                                            break;
                                        case 'momo':
                                            echo 'MoMo';
                                            break;
                                        default:
                                            echo $payment->payment_method;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td>
                                    <?php 
                                    if($payment->status == 'completed') {
                                        echo 'Hoàn thành';
                                    } elseif($payment->status == 'pending') {
                                        echo 'Đang xử lý';
                                    } else {
                                        echo $payment->status;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Mã giao dịch:</th>
                                <td><?php echo $payment->transaction_id; ?></td>
                            </tr>
                            <tr>
                                <th>Ngày thanh toán:</th>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($payment->created_at)); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <?php if($payment->status != 'completed'): ?>
                            <form action="#" method="post">
                                <input type="hidden" name="payment_id" value="<?php echo $payment->id; ?>">
                                <button type="submit" class="btn btn-success">Xác nhận thanh toán</button>
                            </form>
                        <?php endif; ?>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
