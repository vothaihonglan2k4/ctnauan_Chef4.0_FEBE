<?php require_once APP_ROOT . '/views/admin/includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin">Bảng điều khiển</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/courses">Khóa học</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm khóa học mới</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <h1>Thêm khóa học mới</h1>
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
    displayFlashMessage('admin_course_message');
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-plus-circle"></i> Thông tin khóa học
                </div>
                <div class="card-body">
                    <form action="<?php echo URL_ROOT; ?>/admin/add_course" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Tên khóa học <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($data['title_err']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo isset($data['title']) ? $data['title'] : ''; ?>" required>
                                    <div class="invalid-feedback"><?php echo isset($data['title_err']) ? $data['title_err'] : ''; ?></div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                                    <textarea class="form-control <?php echo isset($data['description_err']) ? 'is-invalid' : ''; ?>" id="description" name="description" rows="6" required><?php echo isset($data['description']) ? $data['description'] : ''; ?></textarea>
                                    <div class="invalid-feedback"><?php echo isset($data['description_err']) ? $data['description_err'] : ''; ?></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control <?php echo isset($data['price_err']) ? 'is-invalid' : ''; ?>" id="price" name="price" value="<?php echo isset($data['price']) ? $data['price'] : '0'; ?>" min="0" required>
                                            <div class="invalid-feedback"><?php echo isset($data['price_err']) ? $data['price_err'] : ''; ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Thời lượng (phút) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control <?php echo isset($data['duration_err']) ? 'is-invalid' : ''; ?>" id="duration" name="duration" value="<?php echo isset($data['duration']) ? $data['duration'] : '0'; ?>" min="0" required>
                                            <div class="invalid-feedback"><?php echo isset($data['duration_err']) ? $data['duration_err'] : ''; ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="level" class="form-label">Cấp độ <span class="text-danger">*</span></label>
                                            <select class="form-select <?php echo isset($data['level_err']) ? 'is-invalid' : ''; ?>" id="level" name="level" required>
                                                <option value="beginner" <?php echo (isset($data['level']) && $data['level'] == 'beginner') ? 'selected' : ''; ?>>Cơ bản</option>
                                                <option value="intermediate" <?php echo (isset($data['level']) && $data['level'] == 'intermediate') ? 'selected' : ''; ?>>Trung cấp</option>
                                                <option value="advanced" <?php echo (isset($data['level']) && $data['level'] == 'advanced') ? 'selected' : ''; ?>>Nâng cao</option>
                                            </select>
                                            <div class="invalid-feedback"><?php echo isset($data['level_err']) ? $data['level_err'] : ''; ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="classroom_id" class="form-label">Phòng học <span class="text-danger">*</span></label>
                                            <select class="form-select <?php echo isset($data['classroom_id_err']) ? 'is-invalid' : ''; ?>" id="classroom_id" name="classroom_id" required>
                                                <option value="">-- Chọn phòng học --</option>
                                                <?php foreach($data['classrooms'] as $classroom) : ?>
                                                    <option value="<?php echo $classroom->id; ?>" <?php echo (isset($data['classroom_id']) && $data['classroom_id'] == $classroom->id) ? 'selected' : ''; ?>>
                                                        <?php echo $classroom->name; ?> (Sức chứa: <?php echo $classroom->capacity; ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback"><?php echo isset($data['classroom_id_err']) ? $data['classroom_id_err'] : ''; ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="requirements" class="form-label">Yêu cầu trước khóa học</label>
                                    <textarea class="form-control" id="requirements" name="requirements" rows="3"><?php echo isset($data['requirements']) ? $data['requirements'] : ''; ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="what_will_learn" class="form-label">Học viên sẽ học được gì</label>
                                    <textarea class="form-control" id="what_will_learn" name="what_will_learn" rows="3"><?php echo isset($data['what_will_learn']) ? $data['what_will_learn'] : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-select <?php echo isset($data['status_err']) ? 'is-invalid' : ''; ?>" id="status" name="status" required>
                                        <option value="draft" <?php echo (isset($data['status']) && $data['status'] == 'draft') ? 'selected' : ''; ?>>Nháp</option>
                                        <option value="published" <?php echo (isset($data['status']) && $data['status'] == 'published') ? 'selected' : ''; ?>>Xuất bản</option>
                                        <option value="archived" <?php echo (isset($data['status']) && $data['status'] == 'archived') ? 'selected' : ''; ?>>Lưu trữ</option>
                                    </select>
                                    <div class="invalid-feedback"><?php echo isset($data['status_err']) ? $data['status_err'] : ''; ?></div>
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">Hình ảnh khóa học</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <small class="form-text text-muted">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, GIF</small>
                                </div>

                                <div class="mb-3 text-center">
                                    <div class="border p-3 rounded">
                                        <p><strong>Xem trước hình ảnh</strong></p>
                                        <img id="image-preview" src="<?php echo URL_ROOT; ?>/public/img/no-image.jpg" alt="Course Image" class="img-fluid mb-2" style="max-height: 200px;">
                                        <p id="image-status" class="text-muted mt-2">Trạng thái: <span class="fst-italic">chưa đính kèm</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <a href="<?php echo URL_ROOT; ?>/admin/courses" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu khóa học</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Xem trước hình ảnh khi chọn file
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('image-preview').src = event.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?php require_once APP_ROOT . '/views/admin/includes/footer.php'; ?> 