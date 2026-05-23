<?php require_once APPROOT . '/views/manager/includes/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>
                <i class="fas fa-chart-bar text-primary me-2"></i>
                Báo Cáo & Thống Kê
            </h1>
            <p class="page-description">Tổng quan về hoạt động và hiệu suất hệ thống</p>
        </div>
        <div>
            <a href="<?php echo URLROOT; ?>/manager" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Về Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Overview Stats -->
<div class="row mb-4">
    <!-- Recipes Stats -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-utensils me-2"></i>
                    Thống Kê Công Thức
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="h4 text-success mb-0"><?php echo $data['stats']['recipes']['total'] ?? 0; ?></div>
                        <div class="small text-muted">Tổng số</div>
                    </div>
                    <div class="col-3">
                        <div class="h4 text-info mb-0"><?php echo $data['stats']['recipes']['approved'] ?? 0; ?></div>
                        <div class="small text-muted">Đã duyệt</div>
                    </div>
                    <div class="col-3">
                        <div class="h4 text-warning mb-0"><?php echo $data['stats']['recipes']['pending'] ?? 0; ?></div>
                        <div class="small text-muted">Chờ duyệt</div>
                    </div>
                    <div class="col-3">
                        <div class="h4 text-danger mb-0"><?php echo $data['stats']['recipes']['rejected'] ?? 0; ?></div>
                        <div class="small text-muted">Từ chối</div>
                    </div>
                </div>
                
                <hr>
                
                <div class="progress" style="height: 10px;">
                    <?php 
                    $total = $data['stats']['recipes']['total'] ?? 1;
                    $approved = ($data['stats']['recipes']['approved'] ?? 0) / $total * 100;
                    $pending = ($data['stats']['recipes']['pending'] ?? 0) / $total * 100;
                    $rejected = ($data['stats']['recipes']['rejected'] ?? 0) / $total * 100;
                    ?>
                    <div class="progress-bar bg-info" style="width: <?php echo $approved; ?>%"></div>
                    <div class="progress-bar bg-warning" style="width: <?php echo $pending; ?>%"></div>
                    <div class="progress-bar bg-danger" style="width: <?php echo $rejected; ?>%"></div>
                </div>
                <div class="text-center mt-2">
                    <small class="text-muted">
                        <span class="text-info">■</span> Đã duyệt 
                        <span class="text-warning">■</span> Chờ duyệt 
                        <span class="text-danger">■</span> Từ chối
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Courses Stats -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Thống Kê Khóa Học
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="h4 text-info mb-0"><?php echo $data['stats']['courses']['total'] ?? 0; ?></div>
                        <div class="small text-muted">Tổng số</div>
                    </div>
                    <div class="col-4">
                        <div class="h4 text-success mb-0"><?php echo $data['stats']['courses']['published'] ?? 0; ?></div>
                        <div class="small text-muted">Hoạt động</div>
                    </div>
                    <div class="col-4">
                        <div class="h4 text-warning mb-0"><?php echo $data['stats']['courses']['draft'] ?? 0; ?></div>
                        <div class="small text-muted">Bản nháp</div>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h5 text-primary mb-0"><?php echo $data['stats']['users']['total'] ?? 0; ?></div>
                        <div class="small text-muted">Tổng học viên</div>
                    </div>
                    <div class="text-end">
                        <div class="h5 text-success mb-0">+<?php echo $data['stats']['users']['this_month'] ?? 0; ?></div>
                        <div class="small text-muted">Mới tháng này</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Stats -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Doanh Thu
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-success text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-coins fa-2x"></i>
                                </div>
                                <div>
                                    <div class="h4 mb-0 text-success">
                                        <?php echo number_format($data['stats']['revenue']['total'] ?? 0, 0, ',', '.'); ?>₫
                                    </div>
                                    <div class="text-muted">Tổng doanh thu</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-info text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                </div>
                                <div>
                                    <div class="h4 mb-0 text-info">
                                        <?php echo number_format($data['stats']['revenue']['this_month'] ?? 0, 0, ',', '.'); ?>₫
                                    </div>
                                    <div class="text-muted">Tháng <?php echo date('m/Y'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Lưu ý:</strong> Manager chỉ có thể xem báo cáo doanh thu tổng quan. 
                    Để xem chi tiết, vui lòng liên hệ Admin.
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-tasks me-2"></i>
                    Thao Tác Nhanh
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo URLROOT; ?>/manager/recipes" class="btn btn-outline-success">
                        <i class="fas fa-utensils me-2"></i>
                        Duyệt công thức
                        <?php if(($data['stats']['recipes']['pending'] ?? 0) > 0): ?>
                            <span class="badge bg-warning"><?php echo $data['stats']['recipes']['pending']; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <a href="<?php echo URLROOT; ?>/manager/courses" class="btn btn-outline-info">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Quản lý khóa học
                    </a>
                    
                    <a href="<?php echo URLROOT; ?>/manager/addCourse" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>
                        Tạo khóa học mới
                    </a>
                    
                    <a href="<?php echo URLROOT; ?>/manager/contacts" class="btn btn-outline-warning">
                        <i class="fas fa-envelope me-2"></i>
                        Xem liên hệ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Performance -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Thông Tin Hệ Thống
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h5 mb-0">v1.0.0</div>
                            <div class="text-muted small">Phiên bản</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h5 mb-0 text-success">
                                <i class="fas fa-circle"></i> Online
                            </div>
                            <div class="text-muted small">Trạng thái</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h5 mb-0"><?php echo date('d/m/Y'); ?></div>
                            <div class="text-muted small">Cập nhật lần cuối</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h5 mb-0 text-info">Manager</div>
                            <div class="text-muted small">Quyền truy cập</div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Manager Permissions Summary -->
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            Quyền được cấp:
                        </h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Duyệt/từ chối công thức</li>
                            <li><i class="fas fa-check text-success me-2"></i>Quản lý khóa học</li>
                            <li><i class="fas fa-check text-success me-2"></i>Xem liên hệ khách hàng</li>
                            <li><i class="fas fa-check text-success me-2"></i>Xem báo cáo tổng quan</li>
                            <li><i class="fas fa-check text-success me-2"></i>Tạo nội dung mới</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">
                            <i class="fas fa-times-circle me-1"></i>
                            Quyền hạn chế:
                        </h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-times text-muted me-2"></i>Quản lý người dùng</li>
                            <li><i class="fas fa-times text-muted me-2"></i>Xem chi tiết thanh toán</li>
                            <li><i class="fas fa-times text-muted me-2"></i>Cài đặt hệ thống</li>
                            <li><i class="fas fa-times text-muted me-2"></i>Xóa dữ liệu quan trọng</li>
                            <li><i class="fas fa-times text-muted me-2"></i>Phân quyền người dùng</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for future charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.progress {
    background-color: #e9ecef;
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.bg-light {
    background-color: #f8f9fa !important;
}

.rounded-circle {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.badge {
    position: relative;
    top: -2px;
}

.alert-info {
    background-color: rgba(13, 202, 240, 0.1);
    border-color: rgba(13, 202, 240, 0.2);
}
</style>

<?php require_once APPROOT . '/views/manager/includes/footer.php'; ?> 