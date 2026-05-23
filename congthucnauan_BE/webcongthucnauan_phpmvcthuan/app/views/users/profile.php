<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="row mt-4">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-user-circle me-2"></i>Thông tin cá nhân</h3>
            </div>
            <div class="card-body">
                <form action="<?php echo URL_ROOT; ?>/users/profile" method="post" enctype="multipart/form-data">
                    
                    <!-- Avatar Section -->
                    <div class="text-center mb-4">
                        <div class="avatar-preview mb-3">
                            <?php 
                            $avatarPath = !empty($data['avatar']) && $data['avatar'] != 'default-avatar.png' 
                                ? URL_ROOT . '/public/uploads/avatars/' . $data['avatar']
                                : URL_ROOT . '/public/img/default-avatar.png';
                            ?>
                            <img id="avatarPreview" src="<?php echo $avatarPath; ?>" alt="Avatar" class="rounded-circle border border-3 border-primary" style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Thay đổi ảnh đại diện</label>
                            <input type="file" name="avatar" id="avatar" class="form-control <?php echo (!empty($data['avatar_err'])) ? 'is-invalid' : ''; ?>" accept="image/jpeg,image/jpg,image/png" onchange="previewAvatar(this)">
                            <div class="form-text">Chỉ chấp nhận JPG, JPEG, PNG. Dung lượng dưới 2MB</div>
                            <div class="invalid-feedback"><?php echo $data['avatar_err'] ?? ''; ?></div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Basic Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label"><i class="fas fa-user me-1"></i>Họ tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['name']; ?>" required>
                            <div class="invalid-feedback"><?php echo $data['name_err']; ?></div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>" required>
                            <div class="invalid-feedback"><?php echo $data['email_err']; ?></div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="mb-3">
                        <label for="phone" class="form-label"><i class="fas fa-phone me-1"></i>Số điện thoại</label>
                        <input type="tel" name="phone" class="form-control <?php echo (!empty($data['phone_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['phone'] ?? ''; ?>" placeholder="Ví dụ: 0901234567">
                        <div class="invalid-feedback"><?php echo $data['phone_err'] ?? ''; ?></div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Địa chỉ</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Nhập địa chỉ của bạn"><?php echo $data['address'] ?? ''; ?></textarea>
                    </div>

                    <hr class="my-4">

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Cập nhật thông tin
                        </button>
                        <a href="<?php echo URL_ROOT; ?>/users/changePassword" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Đổi mật khẩu
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h4 class="mb-0">Công thức của tôi</h4>
            </div>
            <div class="card-body">
                <?php
                if(isset($_SESSION['user_id'])) {
                    if(!empty($data['user_recipes'])):
                ?>
                    <div class="list-group">
                        <?php foreach($data['user_recipes'] as $recipe): ?>
                        <a href="<?php echo URL_ROOT; ?>/recipes/show/<?php echo $recipe->id; ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><?php echo $recipe->title; ?></h5>
                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($recipe->created_at)); ?></small>
                            </div>
                            <p class="mb-1"><?php echo substr($recipe->description, 0, 100); ?>...</p>
                            <div>
                                <a href="<?php echo URL_ROOT; ?>/recipes/edit/<?php echo $recipe->id; ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                                <form class="d-inline" action="<?php echo URL_ROOT; ?>/recipes/delete/<?php echo $recipe->id; ?>" method="post">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa công thức này?');">Xóa</button>
                                </form>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Bạn chưa chia sẻ công thức nào.</p>
                    <a href="<?php echo URL_ROOT; ?>/recipes/add" class="btn btn-success">Thêm công thức mới</a>
                <?php 
                    endif;
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-preview img {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.avatar-preview img:hover {
    transform: scale(1.05);
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-control:focus, .form-control:hover {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25);
}
</style>

<script>
// Preview avatar trước khi upload
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        // Kiểm tra kích thước file (max 5MB)
        if (input.files[0].size > 5242880) {
            alert('Kích thước ảnh không được vượt quá 5MB');
            input.value = '';
            return;
        }

        // Kiểm tra định dạng file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(input.files[0].type)) {
            alert('Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF)');
            input.value = '';
            return;
        }

        // Hiển thị preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto format phone number
document.querySelector('input[name="phone"]')?.addEventListener('input', function(e) {
    // Chỉ cho phép nhập số
    this.value = this.value.replace(/[^0-9]/g, '');
    
    // Giới hạn 11 số
    if (this.value.length > 11) {
        this.value = this.value.slice(0, 11);
    }
});

// Form validation
document.querySelector('form')?.addEventListener('submit', function(e) {
    const name = document.querySelector('input[name="name"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    
    if (name === '') {
        e.preventDefault();
        alert('Vui lòng nhập họ tên');
        return false;
    }
    
    if (email === '') {
        e.preventDefault();
        alert('Vui lòng nhập email');
        return false;
    }
    
    // Confirm trước khi submit
    if (!confirm('Bạn có chắc chắn muốn cập nhật thông tin?')) {
        e.preventDefault();
        return false;
    }
});
</script>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
