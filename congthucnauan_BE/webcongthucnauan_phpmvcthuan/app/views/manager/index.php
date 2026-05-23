<?php require_once APPROOT . '/views/manager/includes/header.php'; ?>

<!-- Flash Messages -->
<?php if(isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <h1>
        <i class="fas fa-tachometer-alt text-success me-2"></i>
        Manager Dashboard
    </h1>
    <p class="page-description">Tổng quan quản lý nội dung và khóa học</p>
</div>

<!-- Dashboard Stats -->
<div class="row">
    <!-- Total Recipes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100 stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted fw-bold">
                            Tổng Công Thức
                        </div>
                        <div class="h4 mb-0 fw-bold">
                            <?php echo $data['total_recipes'] ?? 0; ?>
                        </div>
                        <div class="small text-success mt-1">
                            <i class="fas fa-utensils me-1"></i>
                            Đã phê duyệt
                        </div>
                    </div>
                    <div class="p-3 rounded-circle bg-success bg-opacity-10 stat-icon">
                        <i class="fas fa-utensils fa-2x text-success"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light py-2">
                <a href="<?php echo URLROOT; ?>/manager/recipes" class="text-decoration-none small text-success fw-bold">
                    <i class="fas fa-arrow-right me-1"></i> Quản lý công thức
                </a>
            </div>
        </div>
    </div>

    <!-- Pending Recipes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100 stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted fw-bold">
                            Chờ Duyệt
                        </div>
                        <div class="h4 mb-0 fw-bold">
                            <?php echo $data['pending_recipes'] ?? 0; ?>
                        </div>
                        <div class="small text-warning mt-1">
                            <i class="fas fa-clock me-1"></i>
                            Cần xem xét
                        </div>
                    </div>
                    <div class="p-3 rounded-circle bg-warning bg-opacity-10 stat-icon">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light py-2">
                <a href="<?php echo URLROOT; ?>/manager/recipes" class="text-decoration-none small text-warning fw-bold">
                    <i class="fas fa-arrow-right me-1"></i> Duyệt công thức
                </a>
            </div>
        </div>
    </div>

    <!-- Total Courses -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100 stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted fw-bold">
                            Khóa Học
                        </div>
                        <div class="h4 mb-0 fw-bold">
                            <?php echo $data['total_courses'] ?? 0; ?>
                        </div>
                        <div class="small text-info mt-1">
                            <i class="fas fa-graduation-cap me-1"></i>
                            Đang hoạt động
                        </div>
                    </div>
                    <div class="p-3 rounded-circle bg-info bg-opacity-10 stat-icon">
                        <i class="fas fa-graduation-cap fa-2x text-info"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light py-2">
                <a href="<?php echo URLROOT; ?>/manager/courses" class="text-decoration-none small text-info fw-bold">
                    <i class="fas fa-arrow-right me-1"></i> Quản lý khóa học
                </a>
            </div>
        </div>
    </div>

    <!-- Total Students -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted fw-bold">
                            Học Viên
                        </div>
                        <div class="h4 mb-0 fw-bold">
                            <?php echo $data['total_students'] ?? 0; ?>
                        </div>
                        <div class="small text-primary mt-1">
                            <i class="fas fa-users me-1"></i>
                            Đang học
                        </div>
                    </div>
                    <div class="p-3 rounded-circle bg-primary bg-opacity-10 stat-icon">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light py-2">
                <a href="<?php echo URLROOT; ?>/manager/reports" class="text-decoration-none small text-primary fw-bold">
                    <i class="fas fa-arrow-right me-1"></i> Xem báo cáo
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Thao Tác Nhanh
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="d-grid">
                            <a href="<?php echo URLROOT; ?>/manager/recipes" class="btn btn-outline-success quick-action">
                                <i class="fas fa-utensils me-2"></i>
                                Duyệt Công Thức
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="d-grid">
                            <a href="<?php echo URLROOT; ?>/manager/courses" class="btn btn-outline-info quick-action">
                                <i class="fas fa-graduation-cap me-2"></i>
                                Quản Lý Khóa Học
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="d-grid">
                            <a href="<?php echo URLROOT; ?>/manager/contacts" class="btn btn-outline-warning quick-action">
                                <i class="fas fa-envelope me-2"></i>
                                Xem Liên Hệ
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="d-grid">
                            <a href="<?php echo URLROOT; ?>/manager/reports" class="btn btn-outline-primary quick-action">
                                <i class="fas fa-chart-bar me-2"></i>
                                Báo Cáo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Area -->
<div class="row">
    <!-- Recent Contacts -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-envelope me-2"></i>
                    Liên Hệ Gần Đây
                </h5>
                <span class="badge bg-warning"><?php echo count($data['recent_contacts'] ?? []); ?> mới</span>
            </div>
            <div class="card-body p-0">
                <?php if(isset($data['recent_contacts']) && !empty($data['recent_contacts'])): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach($data['recent_contacts'] as $contact): ?>
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold"><?php echo htmlspecialchars($contact->name); ?></div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($contact->subject); ?></div>
                                    <div class="text-muted small">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($contact->created_at)); ?>
                                    </div>
                                </div>
                                <span class="badge bg-<?php echo $contact->status == 'new' ? 'danger' : 'success'; ?> rounded-pill">
                                    <?php echo $contact->status == 'new' ? 'Mới' : 'Đã đọc'; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?php echo URLROOT; ?>/manager/contacts" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> Xem Tất Cả
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có liên hệ mới</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Manager Info & Permissions -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-shield me-2"></i>
                    Thông Tin Quản Lý
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-muted small">Tên:</div>
                        <div class="fw-bold"><?php echo $_SESSION['user_name'] ?? 'Manager'; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Role:</div>
                        <div class="fw-bold text-success">
                            <i class="fas fa-user-tie me-1"></i>Manager
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-muted small">Email:</div>
                        <div class="fw-bold"><?php echo $_SESSION['user_email'] ?? 'manager@domain.com'; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Trạng thái:</div>
                        <div class="fw-bold text-success">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>Đang hoạt động
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <div class="text-muted small mb-2">Quyền truy cập:</div>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-success small">
                                <i class="fas fa-check me-1"></i>Quản lý công thức
                            </div>
                            <div class="text-success small">
                                <i class="fas fa-check me-1"></i>Quản lý khóa học
                            </div>
                            <div class="text-success small">
                                <i class="fas fa-check me-1"></i>Xem liên hệ
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">
                                <i class="fas fa-times me-1"></i>Quản lý user
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-times me-1"></i>Cài đặt hệ thống
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-times me-1"></i>Xóa dữ liệu
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="<?php echo URLROOT; ?>/users/profile" class="btn btn-sm btn-outline-success me-2">
                        <i class="fas fa-user-edit me-1"></i>Cập nhật profile
                    </a>
                    <a href="<?php echo URLROOT; ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-home me-1"></i>Về trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/manager/includes/footer.php'; ?> 