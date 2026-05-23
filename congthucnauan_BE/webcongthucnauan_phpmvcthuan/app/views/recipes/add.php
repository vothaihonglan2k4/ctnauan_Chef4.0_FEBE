<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Thêm công thức mới</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo URL_ROOT; ?>/recipes/add" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tên công thức <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo (!empty($data['title_err'])) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo $data['title']; ?>">
                            <div class="invalid-feedback"><?php echo $data['title_err']; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select <?php echo (!empty($data['category_id_err'])) ? 'is-invalid' : ''; ?>" id="category_id" name="category_id">
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach($data['categories'] as $category): ?>
                                    <option value="<?php echo $category->id; ?>" <?php echo ($data['category_id'] == $category->id) ? 'selected' : ''; ?>>
                                        <?php echo $category->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback"><?php echo $data['category_id_err']; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php echo (!empty($data['description_err'])) ? 'is-invalid' : ''; ?>" id="description" name="description" rows="3"><?php echo $data['description']; ?></textarea>
                            <div class="invalid-feedback"><?php echo $data['description_err']; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ingredients" class="form-label">Nguyên liệu <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php echo (!empty($data['ingredients_err'])) ? 'is-invalid' : ''; ?>" id="ingredients" name="ingredients" rows="5" placeholder="- 200g thịt bò &#10;- 2 quả trứng gà &#10;- 1 muỗng cà phê muối"><?php echo $data['ingredients']; ?></textarea>
                            <div class="invalid-feedback"><?php echo $data['ingredients_err']; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Cách làm <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php echo (!empty($data['instructions_err'])) ? 'is-invalid' : ''; ?>" id="instructions" name="instructions" rows="8" placeholder="Bước 1: Làm sạch thịt bò&#10;Bước 2: Ướp thịt với các gia vị&#10;..."><?php echo $data['instructions']; ?></textarea>
                            <div class="invalid-feedback"><?php echo $data['instructions_err']; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Hình ảnh</label>
                            <input class="form-control <?php echo (!empty($data['image_err'])) ? 'is-invalid' : ''; ?>" type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png">
                            <div class="form-text">Nên sử dụng hình ảnh có tỉ lệ 3:2 và dung lượng dưới 2MB. Định dạng cho phép: JPG, JPEG, PNG.</div>
                            <div class="invalid-feedback"><?php echo $data['image_err'] ?? ''; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="video_url" class="form-label">
                                <i class="fab fa-youtube text-danger me-1"></i>Link Video YouTube (Không bắt buộc)
                            </label>
                            <input type="url" 
                                   class="form-control <?php echo (!empty($data['video_url_err'])) ? 'is-invalid' : ''; ?>" 
                                   id="video_url" 
                                   name="video_url" 
                                   value="<?php echo $data['video_url'] ?? ''; ?>"
                                   placeholder="https://www.youtube.com/watch?v=...">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Nhập link video YouTube hướng dẫn nấu món ăn (ví dụ: https://www.youtube.com/watch?v=dQw4w9WgXcQ)
                            </div>
                            <div class="invalid-feedback"><?php echo $data['video_url_err'] ?? ''; ?></div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Thêm công thức</button>
                            <a href="<?php echo URL_ROOT; ?>/recipes" class="btn btn-light">Hủy bỏ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
