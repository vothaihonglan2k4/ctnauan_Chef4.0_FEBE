<?php require_once APP_ROOT . '/views/admin/includes/header.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>
            <i class="fas fa-graduation-cap text-primary me-2"></i>
            Quản lý khóa học online
        </h1>
        <p class="page-description">Tạo và quản lý các khóa học trực tuyến của bạn</p>
    </div>
    <div>
        <a href="<?php echo URL_ROOT; ?>/admin/add_course" class="btn btn-success">
            <i class="fas fa-plus-circle me-2"></i> Thêm khóa học mới
        </a>
    </div>
</div>

<?php 
// Hàm flash tạm thời
function displayFlashMessage($name) {
    if(isset($_SESSION[$name])) {
        $message = $_SESSION[$name];
        $class = isset($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : 'alert alert-success';
        echo '<div class="'.$class.' alert-dismissible fade show" role="alert">';
        echo '<i class="fas fa-check-circle me-2"></i>' . $message;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION[$name]);
        unset($_SESSION[$name . '_class']);
    }
}

// Hiển thị flash message
displayFlashMessage('admin_course_message');
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-list me-2"></i>
            <span class="fw-bold">Danh sách khóa học</span>
        </div>
        <div>
            <span class="badge bg-info"><?php echo count($data['courses'] ?? []); ?> khóa học</span>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($data['courses'])) : ?>
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle fs-4 me-3"></i>
                <div>
                    <h5 class="mb-1">Chưa có khóa học nào</h5>
                    <p class="mb-0">Hãy thêm khóa học đầu tiên để bắt đầu!</p>
                </div>
            </div>
            <div class="text-center mt-4">
                <img src="<?php echo URL_ROOT; ?>/public/img/congthucnauan.jpg" alt="No Courses" style="max-width: 200px; opacity: 0.5;" class="mb-3 rounded">
                <p>Bắt đầu tạo khóa học đầu tiên của bạn ngay bây giờ</p>
                <a href="<?php echo URL_ROOT; ?>/admin/add_course" class="btn btn-primary mt-2">
                    <i class="fas fa-plus-circle me-2"></i> Tạo khóa học
                </a>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">ID</th>
                            <th width="100">Hình ảnh</th>
                            <th>Tên khóa học</th>
                            <th>Phòng học</th>
                            <th>Giá (VNĐ)</th>
                            <th>Thời lượng</th>
                            <th class="text-center">Học viên</th>
                            <th>Ngày tạo</th>
                            <th width="120" class="text-center">Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['courses'] as $course) : ?>
                            <tr>
                                <td class="text-center fw-bold"><?php echo $course->id; ?></td>
                                <td>
                                    <?php if ($course->image) : ?>
                                        <img src="<?php echo URL_ROOT; ?>/public/uploads/courses/<?php echo $course->image; ?>" alt="<?php echo $course->title; ?>" class="img-thumbnail" width="80" height="80" style="object-fit: cover;">
                                    <?php else : ?>
                                        <img src="<?php echo URL_ROOT; ?>/public/img/congthucnauan.jpg" alt="No Image" class="img-thumbnail" width="80" height="80" style="object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo $course->title; ?></div>
                                    <span class="badge bg-<?php echo $course->status == 'published' ? 'success' : ($course->status == 'draft' ? 'warning' : 'secondary'); ?>">
                                        <?php echo $course->status == 'published' ? 'Đã xuất bản' : ($course->status == 'draft' ? 'Bản nháp' : 'Đã lưu trữ'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <i class="fas fa-door-open me-1"></i>
                                        <?php echo $course->classroom_name; ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-success"><?php echo number_format($course->price); ?> ₫</td>
                                <td><i class="far fa-clock me-1"></i> <?php echo $course->duration; ?> phút</td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill fs-6">
                                        <?php echo $course->student_count ?? 0; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($course->created_at)); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo URL_ROOT; ?>/admin/lessons/<?php echo $course->id; ?>" class="btn btn-sm btn-info text-white" title="Quản lý bài học" data-bs-toggle="tooltip">
                                            <i class="fas fa-book"></i>
                                        </a>
                                        <a href="<?php echo URL_ROOT; ?>/admin/edit_course/<?php echo $course->id; ?>" class="btn btn-sm btn-primary" title="Sửa" data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo URL_ROOT; ?>/admin/delete_course/<?php echo $course->id; ?>" class="btn btn-sm btn-danger btn-delete" title="Xóa" data-bs-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_ROOT . '/views/admin/includes/footer.php'; ?> 