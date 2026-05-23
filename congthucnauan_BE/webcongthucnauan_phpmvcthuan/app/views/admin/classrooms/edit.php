<?php require_once APP_ROOT . '/views/admin/includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin">Bảng điều khiển</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/classrooms">Phòng học</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa phòng học</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <h1>Chỉnh sửa phòng học</h1>
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
    displayFlashMessage('admin_classroom_message');
    ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-edit"></i> Thông tin phòng học
                </div>
                <div class="card-body">
                    <form action="<?php echo URL_ROOT; ?>/admin/edit_classroom/<?php echo $data['id']; ?>" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên phòng học <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo !empty($data['name_err']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo $data['name']; ?>" required>
                            <div class="invalid-feedback"><?php echo isset($data['name_err']) ? $data['name_err'] : ''; ?></div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php echo !empty($data['description_err']) ? 'is-invalid' : ''; ?>" id="description" name="description" rows="4" required><?php echo $data['description']; ?></textarea>
                            <div class="invalid-feedback"><?php echo isset($data['description_err']) ? $data['description_err'] : ''; ?></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Sức chứa (học viên) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php echo !empty($data['capacity_err']) ? 'is-invalid' : ''; ?>" id="capacity" name="capacity" value="<?php echo $data['capacity']; ?>" min="1" required>
                                    <div class="invalid-feedback"><?php echo isset($data['capacity_err']) ? $data['capacity_err'] : ''; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Vị trí</label>
                                    <input type="text" class="form-control" id="location" name="location" value="<?php echo $data['location']; ?>" placeholder="Ví dụ: Trực tuyến, Tầng 2 - Phòng 201,...">
                                    <small class="form-text text-muted">Để trống nếu là phòng học trực tuyến</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="active" id="active_yes" value="1" <?php echo ($data['active'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="active_yes">Hoạt động</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="active" id="active_no" value="0" <?php echo ($data['active'] == 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="active_no">Không hoạt động</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="<?php echo URL_ROOT; ?>/admin/classrooms" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật phòng học</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle"></i> Thông tin hiện tại
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Số khóa học đang sử dụng
                            <span class="badge bg-primary rounded-pill"><?php echo isset($data['course_count']) ? $data['course_count'] : 0; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Ngày tạo
                            <span><?php echo isset($data['created_at']) ? date('d/m/Y', strtotime($data['created_at'])) : '-'; ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-exclamation-triangle"></i> Lưu ý
                </div>
                <div class="card-body">
                    <p>Nếu phòng học này đang được sử dụng bởi một hoặc nhiều khóa học, việc thay đổi trạng thái hoặc thông tin có thể ảnh hưởng đến các khóa học đó.</p>
                    
                    <p>Bạn không thể xóa phòng học này nếu có khóa học đang sử dụng.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/admin/includes/footer.php'; ?> 