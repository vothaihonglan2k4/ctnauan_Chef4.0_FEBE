<?php require_once APP_ROOT . '/views/admin/includes/header.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>
            <i class="fas fa-door-open text-primary me-2"></i>
            Quản lý phòng học
        </h1>
        <p class="page-description">Quản lý các phòng học cho các khóa học trực tuyến của bạn</p>
    </div>
    <div>
        <a href="<?php echo URL_ROOT; ?>/admin/add_classroom" class="btn btn-success">
            <i class="fas fa-plus-circle me-2"></i> Thêm phòng học mới
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
displayFlashMessage('admin_classroom_message');
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-list me-2"></i>
            <span class="fw-bold">Danh sách phòng học</span>
        </div>
        <div>
            <span class="badge bg-info"><?php echo count($data['classrooms'] ?? []); ?> phòng học</span>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($data['classrooms'])) : ?>
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle fs-4 me-3"></i>
                <div>
                    <h5 class="mb-1">Chưa có phòng học nào</h5>
                    <p class="mb-0">Bạn cần thêm phòng học để có thể tạo các khóa học</p>
                </div>
            </div>
            <div class="text-center mt-4">
                <img src="<?php echo URL_ROOT; ?>/public/img/congthucnauan.jpg" alt="No Classrooms" style="max-width: 200px; opacity: 0.5;" class="mb-3 rounded">
                <p>Bắt đầu tạo phòng học đầu tiên của bạn ngay bây giờ</p>
                <a href="<?php echo URL_ROOT; ?>/admin/add_classroom" class="btn btn-primary mt-2">
                    <i class="fas fa-plus-circle me-2"></i> Tạo phòng học
                </a>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">ID</th>
                            <th>Tên phòng học</th>
                            <th>Mô tả</th>
                            <th>Sức chứa</th>
                            <th>Vị trí</th>
                            <th class="text-center">Số khóa học</th>
                            <th width="120">Trạng thái</th>
                            <th width="120" class="text-center">Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['classrooms'] as $classroom) : ?>
                            <tr>
                                <td class="text-center fw-bold"><?php echo $classroom->id; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo $classroom->name; ?></div>
                                </td>
                                <td><?php echo substr($classroom->description, 0, 50) . (strlen($classroom->description) > 50 ? '...' : ''); ?></td>
                                <td><?php echo $classroom->capacity; ?> người</td>
                                <td><?php echo $classroom->location; ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill">
                                        <?php echo $classroom->course_count ?? 0; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $classroom->active == 1 ? 'success' : 'danger'; ?>">
                                        <?php echo $classroom->active == 1 ? 'Hoạt động' : 'Không hoạt động'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo URL_ROOT; ?>/admin/edit_classroom/<?php echo $classroom->id; ?>" class="btn btn-sm btn-primary" title="Sửa" data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?php echo URL_ROOT; ?>/admin/delete_classroom/<?php echo $classroom->id; ?>" style="display: inline;">
                                            <button type="submit" class="btn btn-sm btn-danger btn-delete-form" title="Xóa" data-bs-toggle="tooltip" data-classroom-name="<?php echo htmlspecialchars($classroom->name); ?>" data-course-count="<?php echo $classroom->course_count ?? 0; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-footer bg-light d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted">Tổng số phòng học: <strong><?php echo count($data['classrooms'] ?? []); ?></strong></span>
        </div>
        <div>
            <a href="<?php echo URL_ROOT; ?>/admin/courses" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-chalkboard-teacher me-1"></i> Quản lý khóa học
            </a>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/admin/includes/footer.php'; ?> 