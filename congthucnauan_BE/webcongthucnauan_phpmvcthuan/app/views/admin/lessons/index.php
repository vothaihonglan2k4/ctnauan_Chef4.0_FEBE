<?php require_once APP_ROOT . '/views/admin/includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin">Bảng điều khiển</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/courses">Khóa học</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Quản lý bài học</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <div class="d-flex align-items-center">
                <h1 class="me-3">Quản lý bài học</h1>
                <span class="badge bg-primary"><?php echo $data['course']->title; ?></span>
            </div>
        </div>
        <div class="col-auto">
            <a href="<?php echo URL_ROOT; ?>/admin/edit_course/<?php echo $data['course']->id; ?>" class="btn btn-info me-2">
                <i class="fas fa-edit"></i> Chỉnh sửa khóa học
            </a>
            <a href="<?php echo URL_ROOT; ?>/admin/add_lesson/<?php echo $data['course']->id; ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> Thêm bài học mới
            </a>
        </div>
    </div>

    <?php 
    // Hàm flash tạm thời
    function displayFlashMessage($name) {
        if(isset($_SESSION[$name])) {
            $message = $_SESSION[$name];
            $class = isset($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : 'alert alert-success';
            echo '<div class="'.$class.'" role="alert">';
            echo $message;
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
    
    // Hiển thị flash message
    displayFlashMessage('admin_lesson_message');
    ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-book"></i> Danh sách bài học
                </div>
                <div class="card-body">
                    <?php if (empty($data['lessons'])) : ?>
                        <div class="alert alert-info">
                            <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Chưa có bài học nào!</h5>
                            <p>Khóa học này chưa có bài học nào. Vui lòng thêm bài học đầu tiên để học viên có thể học.</p>
                            <a href="<?php echo URL_ROOT; ?>/admin/add_lesson/<?php echo $data['course']->id; ?>" class="btn btn-primary">Thêm bài học đầu tiên</a>
                        </div>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="50">STT</th>
                                        <th>Tên bài học</th>
                                        <th width="100">Thời lượng</th>
                                        <th width="80">Miễn phí</th>
                                        <th width="140">Chức năng</th>
                                    </tr>
                                </thead>
                                <tbody class="sortable-lessons">
                                    <?php foreach ($data['lessons'] as $lesson) : ?>
                                        <tr data-lesson-id="<?php echo $lesson->id; ?>">
                                            <td class="text-center">
                                                <span class="sort-handle badge bg-secondary"><i class="fas fa-arrows-alt"></i></span>
                                                <span><?php echo $lesson->sort_order; ?></span>
                                            </td>
                                            <td>
                                                <strong><?php echo $lesson->title; ?></strong>
                                                <div class="small text-muted"><?php echo mb_substr(strip_tags($lesson->content), 0, 60) . (strlen($lesson->content) > 60 ? '...' : ''); ?></div>
                                            </td>
                                            <td><?php echo $lesson->duration_minutes; ?> phút</td>
                                            <td class="text-center">
                                                <?php if ($lesson->is_free) : ?>
                                                    <span class="badge bg-success"><i class="fas fa-check"></i></span>
                                                <?php else : ?>
                                                    <span class="badge bg-secondary"><i class="fas fa-times"></i></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo URL_ROOT; ?>/admin/edit_lesson/<?php echo $lesson->id; ?>" class="btn btn-primary" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo URL_ROOT; ?>/admin/delete_lesson/<?php echo $lesson->id; ?>" class="btn btn-danger btn-delete" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa bài học này?');">
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
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle"></i> Thông tin khóa học
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tên khóa học
                            <span class="text-primary"><?php echo $data['course']->title; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Phòng học
                            <span><?php echo $data['course']->classroom_name; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Cấp độ
                            <span>
                                <?php if ($data['course']->level == 'beginner') : ?>
                                    <span class="badge bg-success">Cơ bản</span>
                                <?php elseif ($data['course']->level == 'intermediate') : ?>
                                    <span class="badge bg-warning text-dark">Trung cấp</span>
                                <?php else : ?>
                                    <span class="badge bg-danger">Nâng cao</span>
                                <?php endif; ?>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Giá
                            <span><?php echo number_format($data['course']->price); ?>đ</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Số bài học
                            <span class="badge bg-primary rounded-pill"><?php echo count($data['lessons']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Học viên đã đăng ký
                            <span class="badge bg-info rounded-pill"><?php echo $data['course']->student_count ?? 0; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Trạng thái
                            <span>
                                <?php if ($data['course']->status == 'published') : ?>
                                    <span class="badge bg-success">Đã xuất bản</span>
                                <?php elseif ($data['course']->status == 'draft') : ?>
                                    <span class="badge bg-secondary">Bản nháp</span>
                                <?php else : ?>
                                    <span class="badge bg-dark">Đã lưu trữ</span>
                                <?php endif; ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-lightbulb"></i> Mẹo quản lý bài học
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Bạn có thể kéo thả để sắp xếp lại thứ tự bài học.</li>
                        <li>Đánh dấu "Miễn phí" cho các bài học giới thiệu để thu hút học viên.</li>
                        <li>Mỗi bài học nên có thời lượng từ 10-20 phút để duy trì sự tập trung của học viên.</li>
                        <li>Đảm bảo đủ nội dung và video cho mỗi bài học.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/admin/includes/footer.php'; ?> 