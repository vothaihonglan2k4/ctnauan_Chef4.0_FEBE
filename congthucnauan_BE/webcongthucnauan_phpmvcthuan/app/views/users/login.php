<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Đăng nhập</h2>
            <p>Vui lòng điền thông tin để đăng nhập</p>
            <form action="<?php echo URL_ROOT; ?>/users/login" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
                    <div class="invalid-feedback"><?php echo $data['email_err']; ?></div>
                </div>
                <div class="mb-3 position-relative">
                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
                    <i class="toggle-password bi bi-eye position-absolute" style="right: 10px; top: 40px; cursor: pointer;"></i>
                    <div class="invalid-feedback"><?php echo $data['password_err']; ?></div>
                </div>
                <div class="row">
                    <div class="col">
                        <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
                    </div>
                    <div class="col">
                        <a href="<?php echo URL_ROOT; ?>/users/register" class="btn btn-light btn-block">Chưa có tài khoản? Đăng ký</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('#password');
    
    togglePassword.addEventListener('click', function() {
        // Toggle type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle icon
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
});
</script>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
