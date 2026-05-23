<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Đăng ký tài khoản</h2>
            <p>Vui lòng điền thông tin để đăng ký</p>
            <form action="<?php echo URL_ROOT; ?>/users/register" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['name']; ?>">
                    <div class="invalid-feedback"><?php echo $data['name_err']; ?></div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
                    <div class="invalid-feedback"><?php echo $data['email_err']; ?></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
                        <span class="input-group-text">
                            <i class="bi bi-eye-slash" id="togglePassword" style="cursor: pointer"></i>
                        </span>
                        <div class="invalid-feedback"><?php echo $data['password_err']; ?></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['confirm_password']; ?>">
                        <span class="input-group-text">
                            <i class="bi bi-eye-slash" id="toggleConfirmPassword" style="cursor: pointer"></i>
                        </span>
                        <div class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <button type="submit" class="btn btn-primary btn-block">Đăng ký</button>
                    </div>
                    <div class="col">
                        <a href="<?php echo URL_ROOT; ?>/users/login" class="btn btn-light btn-block">Đã có tài khoản? Đăng nhập</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script for toggle password visibility -->
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle the eye icon
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmPasswordInput = document.getElementById('confirm_password');
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        
        // Toggle the eye icon
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
</script>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
