<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="row mt-4">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Đổi mật khẩu</h3>
            </div>
            <div class="card-body">
                <form action="<?php echo URL_ROOT; ?>/users/changePassword" method="post">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control <?php echo (!empty($data['current_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['current_password']; ?>">
                        <div class="invalid-feedback"><?php echo $data['current_password_err']; ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control <?php echo (!empty($data['new_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['new_password']; ?>">
                        <div class="invalid-feedback"><?php echo $data['new_password_err']; ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['confirm_password']; ?>">
                        <div class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                        <a href="<?php echo URL_ROOT; ?>/users/profile" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?> 