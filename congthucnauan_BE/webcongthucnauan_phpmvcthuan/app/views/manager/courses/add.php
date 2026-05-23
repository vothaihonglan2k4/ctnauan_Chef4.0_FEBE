<?php require_once APPROOT . '/views/manager/includes/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>
                <i class="fas fa-plus-circle text-success me-2"></i>
                Thêm Khóa Học Mới
            </h1>
            <p class="page-description">Tạo khóa học trực tuyến mới</p>
        </div>
        <div>
            <a href="<?php echo URLROOT; ?>/manager/courses" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<!-- Form -->
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Thông tin khóa học
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo URLROOT; ?>/manager/addCourse" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-8">
                            <!-- Course Title -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-heading me-1"></i>
                                    Tiêu đề khóa học <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?php echo $data['title'] ?? ''; ?>"
                                       placeholder="Nhập tiêu đề khóa học..." required>
                            </div>
                            
                            <!-- Course Description -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-align-left me-1"></i>
                                    Mô tả khóa học <span class="text-danger">*</span>
                                </label>
                                <textarea name="description" class="form-control" rows="4" 
                                          placeholder="Mô tả chi tiết về khóa học..." required><?php echo $data['description'] ?? ''; ?></textarea>
                            </div>
                            
                            <!-- Requirements -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-list-check me-1"></i>
                                    Yêu cầu tiên quyết
                                </label>
                                <textarea name="requirements" class="form-control" rows="3" 
                                          placeholder="Những kiến thức cần có trước khi học khóa này..."><?php echo $data['requirements'] ?? ''; ?></textarea>
                            </div>
                            
                            <!-- What will learn -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-graduation-cap me-1"></i>
                                    Học viên sẽ học được gì
                                </label>
                                <textarea name="what_will_learn" class="form-control" rows="3" 
                                          placeholder="Những kỹ năng và kiến thức mà học viên đạt được..."><?php echo $data['what_will_learn'] ?? ''; ?></textarea>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="col-md-4">
                            <!-- Course Image -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-image me-1"></i>
                                    Hình ảnh khóa học
                                </label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <div class="form-text">Chọn ảnh đại diện cho khóa học (JPG, PNG, GIF)</div>
                            </div>
                            
                            <!-- Price -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-money-bill me-1"></i>
                                    Giá khóa học (VNĐ) <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="price" class="form-control" 
                                       value="<?php echo $data['price'] ?? '0'; ?>"
                                       placeholder="0" min="0" required>
                            </div>
                            
                            <!-- Duration -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-clock me-1"></i>
                                    Thời lượng (phút) <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="duration" class="form-control" 
                                       value="<?php echo $data['duration'] ?? '60'; ?>"
                                       placeholder="60" min="1" required>
                            </div>
                            
                            <!-- Level -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-layer-group me-1"></i>
                                    Cấp độ <span class="text-danger">*</span>
                                </label>
                                <select name="level" class="form-select" required>
                                    <option value="beginner" <?php echo ($data['level'] ?? '') == 'beginner' ? 'selected' : ''; ?>>
                                        Cơ bản
                                    </option>
                                    <option value="intermediate" <?php echo ($data['level'] ?? '') == 'intermediate' ? 'selected' : ''; ?>>
                                        Trung cấp
                                    </option>
                                    <option value="advanced" <?php echo ($data['level'] ?? '') == 'advanced' ? 'selected' : ''; ?>>
                                        Nâng cao
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Classroom -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-chalkboard me-1"></i>
                                    Phòng học <span class="text-danger">*</span>
                                </label>
                                <select name="classroom_id" class="form-select" required>
                                    <option value="">Chọn phòng học</option>
                                    <?php if(isset($data['classrooms'])): ?>
                                        <?php foreach($data['classrooms'] as $classroom): ?>
                                            <option value="<?php echo $classroom->id; ?>" 
                                                    <?php echo ($data['classroom_id'] ?? '') == $classroom->id ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($classroom->name); ?>
                                                (<?php echo $classroom->capacity; ?> chỗ)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Các trường có dấu <span class="text-danger">*</span> là bắt buộc
                            </span>
                        </div>
                        <div>
                            <a href="<?php echo URLROOT; ?>/manager/courses" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-1"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Tạo khóa học
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Form validation and preview -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const imageInput = document.querySelector('input[name="image"]');
    if(imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create preview if not exists
                    let preview = document.getElementById('image-preview');
                    if(!preview) {
                        preview = document.createElement('img');
                        preview.id = 'image-preview';
                        preview.className = 'img-thumbnail mt-2';
                        preview.style.maxWidth = '200px';
                        imageInput.parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const title = document.querySelector('input[name="title"]').value.trim();
        const description = document.querySelector('textarea[name="description"]').value.trim();
        const price = document.querySelector('input[name="price"]').value;
        const duration = document.querySelector('input[name="duration"]').value;
        const classroom = document.querySelector('select[name="classroom_id"]').value;
        
        if(!title || !description || !price || !duration || !classroom) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
            return false;
        }
        
        if(parseInt(price) < 0) {
            e.preventDefault();
            alert('Giá khóa học không được âm!');
            return false;
        }
        
        if(parseInt(duration) <= 0) {
            e.preventDefault();
            alert('Thời lượng khóa học phải lớn hơn 0!');
            return false;
        }
    });
});
</script>

<style>
.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-label {
    color: #2c3e50;
}

.form-control:focus,
.form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.text-danger {
    color: #dc3545 !important;
}

#image-preview {
    border: 2px dashed #dee2e6;
    padding: 10px;
    border-radius: 5px;
}
</style>

<?php require_once APPROOT . '/views/manager/includes/footer.php'; ?> 