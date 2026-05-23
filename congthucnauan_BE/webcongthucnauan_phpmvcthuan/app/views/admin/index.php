<?php require_once APP_ROOT . '/views/admin/includes/header.php'; ?>

<div class="page-header">
    <h1>
        <i class="fas fa-tachometer-alt text-primary me-2"></i>
        Bảng điều khiển
    </h1>
    <p class="page-description">Tổng quan về dữ liệu hệ thống</p>
</div>

<!-- Dashboard Stats -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted">
                            Người dùng
                        </div>
                        <div class="h4 mb-0 fw-bold">
                            <?php echo $data['userCount'] ?? 0; ?>
                        </div>
                        <div class="small text-success mt-1">
                            <i class="fas fa-user-plus me-1"></i>
                            <?php
                            $userModel = new User();
                            $userModel->query('SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_FORMAT(NOW() ,"%Y-%m-01")');
                            echo $userModel->single()->count;
                            ?> người dùng mới tháng này
                        </div>
                    </div>
                    <div class="p-3 rounded-circle bg-primary bg-opacity-10">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light py-2">
                <a href="<?php echo URL_ROOT; ?>/admin/users" class="text-decoration-none small">
                    <i class="fas fa-arrow-right me-1"></i> Xem chi tiết
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted">
                            Công thức
                        </div>
                        <div class="h4 mb-0 fw-bold">
                            <?php echo $data['recipe_count'] ?? 0; ?>
                        </div>
                    </div>
                    <div class="p-3 rounded-circle bg-success bg-opacity-10">
                        <i class="fas fa-utensils fa-2x text-success"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light py-2">
                <a href="<?php echo URL_ROOT; ?>/recipes" class="text-decoration-none small">
                    <i class="fas fa-arrow-right me-1"></i> Xem chi tiết
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted">
                            Khóa học
                        </div>
                        <div class="h4 mb-0 fw-bold">
                            <?php echo $data['courseCount'] ?? 0; ?>
                        </div>
                    </div>
                    <div class="p-3 rounded-circle bg-info bg-opacity-10">
                        <i class="fas fa-chalkboard-teacher fa-2x text-info"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light py-2">
                <a href="<?php echo URL_ROOT; ?>/admin/courses" class="text-decoration-none small">
                    <i class="fas fa-arrow-right me-1"></i> Xem chi tiết
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted">
                            Doanh thu
                        </div>
                        <div class="h4 mb-0 fw-bold">
                            <?php echo number_format($data['totalRevenue'] ?? 0); ?>đ
                        </div>
                    </div>
                    <div class="p-3 rounded-circle bg-warning bg-opacity-10">
                        <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light py-2">
                <a href="<?php echo URL_ROOT; ?>/admin/payments" class="text-decoration-none small">
                    <i class="fas fa-arrow-right me-1"></i> Xem chi tiết
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Users -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-users me-2"></i>
                    <span class="fw-bold">Người dùng mới</span>
                </div>
                <a href="<?php echo URL_ROOT; ?>/admin/users" class="btn btn-sm btn-primary">
                    <i class="fas fa-users me-1"></i> Tất cả người dùng
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($data['recent_users'])) : ?>
                    <div class="alert alert-info">Chưa có người dùng nào.</div>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>Email</th>
                                    <th>Ngày đăng ký</th>
                                    <th>Vai trò</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['recent_users'] as $user) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo URL_ROOT; ?>/public/img/admin-avatar.png" class="rounded-circle me-2" width="32" height="32">
                                            <?php echo $user->name; ?>
                                        </div>
                                    </td>
                                    <td><?php echo $user->email; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($user->created_at)); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($user->role == 'admin') ? 'danger' : 'secondary'; ?>">
                                            <?php echo ($user->role == 'admin') ? 'Admin' : 'Người dùng'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Recent Payments -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-money-bill-wave me-2"></i>
                    <span class="fw-bold">Thanh toán gần đây</span>
                </div>
                <a href="<?php echo URL_ROOT; ?>/admin/payments" class="btn btn-sm btn-primary">
                    <i class="fas fa-receipt me-1"></i> Tất cả thanh toán
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($data['recent_payments'])) : ?>
                    <div class="alert alert-info">Chưa có thanh toán nào.</div>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Mã</th>
                                    <th>Người dùng</th>
                                    <th>Khóa học</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['recent_payments'] as $payment) : ?>
                                <tr>
                                    <td><code>#<?php echo $payment->id; ?></code></td>
                                    <td><?php echo $payment->user_name; ?></td>
                                    <td><?php echo $payment->course_title; ?></td>
                                    <td class="fw-bold text-success"><?php echo number_format($payment->amount); ?>đ</td>
                                    <td>
                                        <span class="badge bg-<?php echo ($payment->status == 'completed') ? 'success' : (($payment->status == 'pending') ? 'warning' : 'danger'); ?>">
                                            <?php 
                                                if ($payment->status == 'completed') {
                                                    echo 'Hoàn thành';
                                                } elseif ($payment->status == 'pending') {
                                                    echo 'Đang xử lý';
                                                } else {
                                                    echo 'Thất bại';
                                                }
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Latest Courses -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    <span class="fw-bold">Khóa học mới nhất</span>
                </div>
                <a href="<?php echo URL_ROOT; ?>/admin/courses" class="btn btn-sm btn-primary">
                    <i class="fas fa-graduation-cap me-1"></i> Tất cả khóa học
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($data['recent_courses'])) : ?>
                    <div class="alert alert-info">Chưa có khóa học nào.</div>
                <?php else : ?>
                    <div class="row">
                        <?php foreach ($data['recent_courses'] as $course) : ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="<?php echo $course->image ? URL_ROOT . '/public/uploads/courses/' . $course->image : URL_ROOT . '/public/img/congthucnauan.jpg'; ?>" class="card-img-top" alt="<?php echo $course->title; ?>" style="height: 180px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $course->title; ?></h5>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-info"><?php echo $course->classroom_name; ?></span>
                                            <span class="text-success fw-bold"><?php echo number_format($course->price); ?>đ</span>
                                        </div>
                                        <p class="card-text text-muted small"><?php echo strlen($course->description) > 100 ? substr($course->description, 0, 100) . '...' : $course->description; ?></p>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($course->created_at)); ?></small>
                                            <a href="<?php echo URL_ROOT; ?>/admin/edit_course/<?php echo $course->id; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/admin/includes/footer.php'; ?>
