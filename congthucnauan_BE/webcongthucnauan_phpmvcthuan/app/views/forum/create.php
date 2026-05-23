<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-plus-circle me-2"></i>Tạo bài viết mới</h2>
                <a href="<?php echo URL_ROOT; ?>/forum" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Hủy
                </a>
            </div>

            <!-- Form Card -->
            <div class="card">
                <div class="card-body p-4">
                    <form action="<?php echo URL_ROOT; ?>/forum/store" method="POST" enctype="multipart/form-data">
                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label">
                                <i class="fas fa-heading me-2"></i>Tiêu đề <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   class="form-control form-control-lg <?php echo (!empty($data['title_err'])) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo $data['title'] ?? ''; ?>"
                                   placeholder="Nhập tiêu đề hấp dẫn cho bài viết..."
                                   required>
                            <div class="invalid-feedback"><?php echo $data['title_err'] ?? ''; ?></div>
                        </div>

                        <!-- Content -->
                        <div class="mb-4">
                            <label for="content" class="form-label">
                                <i class="fas fa-align-left me-2"></i>Nội dung <span class="text-danger">*</span>
                            </label>
                            <textarea name="content" 
                                      id="content"
                                      class="form-control <?php echo (!empty($data['content_err'])) ? 'is-invalid' : ''; ?>" 
                                      rows="12"
                                      placeholder="Chia sẻ kinh nghiệm, bí quyết nấu ăn của bạn..."
                                      required><?php echo $data['content'] ?? ''; ?></textarea>
                            <div class="invalid-feedback"><?php echo $data['content_err'] ?? ''; ?></div>
                            <div class="form-text">
                                <i class="fas fa-lightbulb me-1"></i>
                                Mẹo: Chia sẻ chi tiết, cụ thể sẽ giúp người đọc dễ hiểu hơn
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label for="image" class="form-label">
                                <i class="fas fa-image me-2"></i>Ảnh đính kèm
                            </label>
                            <input type="file" 
                                   name="image" 
                                   id="image"
                                   class="form-control <?php echo (!empty($data['image_err'])) ? 'is-invalid' : ''; ?>" 
                                   accept="image/*"
                                   onchange="previewImage(this)">
                            <div class="invalid-feedback"><?php echo $data['image_err'] ?? ''; ?></div>
                            <div class="form-text">Chấp nhận JPG, PNG, GIF. Tối đa 5MB</div>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="preview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        </div>

                        <!-- Video URL -->
                        <div class="mb-4">
                            <label for="video_url" class="form-label">
                                <i class="fab fa-youtube me-2 text-danger"></i>Link video YouTube (tùy chọn)
                            </label>
                            <input type="url" 
                                   name="video_url" 
                                   id="video_url"
                                   class="form-control" 
                                   value="<?php echo $data['video_url'] ?? ''; ?>"
                                   placeholder="https://www.youtube.com/watch?v=...">
                            <div class="form-text">Video hướng dẫn sẽ giúp bài viết sinh động hơn</div>
                        </div>

                        <!-- Recipe Link -->
                        <?php if(!empty($data['user_recipes'])): ?>
                            <div class="mb-4">
                                <label for="recipe_id" class="form-label">
                                    <i class="fas fa-utensils me-2"></i>Liên kết công thức (tùy chọn)
                                </label>
                                <select name="recipe_id" id="recipe_id" class="form-select">
                                    <option value="">-- Chọn công thức --</option>
                                    <?php foreach($data['user_recipes'] as $recipe): ?>
                                        <option value="<?php echo $recipe->id; ?>" 
                                                <?php echo (isset($data['recipe_id']) && $data['recipe_id'] == $recipe->id) ? 'selected' : ''; ?>>
                                            <?php echo $recipe->title; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Liên kết đến công thức bạn đã đăng trước đó</div>
                            </div>
                        <?php endif; ?>

                        <!-- Tags -->
                        <?php if(!empty($data['all_tags'])): ?>
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-tags me-2"></i>Tags (chọn từ 1-3 tags)
                                </label>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach($data['all_tags'] as $tag): ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="tags[]" 
                                                   id="tag_<?php echo $tag->id; ?>"
                                                   value="<?php echo $tag->id; ?>"
                                                   <?php echo (in_array($tag->id, $data['selected_tags'] ?? [])) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="tag_<?php echo $tag->id; ?>">
                                                <?php echo $tag->name; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="form-text">Tags giúp người khác dễ tìm thấy bài viết của bạn</div>
                            </div>
                        <?php endif; ?>

                        <hr class="my-4">

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Làm lại
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Đăng bài viết
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card mt-4 bg-light">
                <div class="card-body">
                    <h6><i class="fas fa-lightbulb text-warning me-2"></i>Mẹo viết bài hay:</h6>
                    <ul class="mb-0 small">
                        <li>Tiêu đề ngắn gọn, thu hút</li>
                        <li>Nội dung chi tiết, dễ hiểu</li>
                        <li>Thêm ảnh/video minh họa</li>
                        <li>Chia sẻ kinh nghiệm thực tế</li>
                        <li>Sử dụng tags phù hợp</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview image trước khi upload
function previewImage(input) {
    if (input.files && input.files[0]) {
        // Kiểm tra kích thước (5MB)
        if (input.files[0].size > 5242880) {
            alert('Kích thước ảnh không được vượt quá 5MB');
            input.value = '';
            document.getElementById('imagePreview').style.display = 'none';
            return;
        }

        // Kiểm tra định dạng
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(input.files[0].type)) {
            alert('Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF)');
            input.value = '';
            document.getElementById('imagePreview').style.display = 'none';
            return;
        }

        // Hiển thị preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        document.getElementById('imagePreview').style.display = 'none';
    }
}

// Giới hạn số tags được chọn (tối đa 3)
document.querySelectorAll('input[name="tags[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const checkedBoxes = document.querySelectorAll('input[name="tags[]"]:checked');
        if (checkedBoxes.length > 3) {
            this.checked = false;
            alert('Chỉ được chọn tối đa 3 tags');
        }
    });
});

// Form validation
document.querySelector('form')?.addEventListener('submit', function(e) {
    const title = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();
    
    if (title === '' || content === '') {
        e.preventDefault();
        alert('Vui lòng nhập đầy đủ tiêu đề và nội dung');
        return false;
    }
    
    if (title.length < 10) {
        e.preventDefault();
        alert('Tiêu đề phải có ít nhất 10 ký tự');
        return false;
    }
    
    if (content.length < 50) {
        e.preventDefault();
        alert('Nội dung phải có ít nhất 50 ký tự');
        return false;
    }
    
    // Confirm trước khi submit
    if (!confirm('Bạn có chắc muốn đăng bài viết này?')) {
        e.preventDefault();
        return false;
    }
});

// Auto-save draft to localStorage (tùy chọn - có thể thêm sau)
// Tự động lưu nháp mỗi 30 giây
</script>

<style>
.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25);
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

#imagePreview {
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>

