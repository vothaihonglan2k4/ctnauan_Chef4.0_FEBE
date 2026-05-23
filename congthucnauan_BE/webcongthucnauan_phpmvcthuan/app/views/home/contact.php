<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h2 class="text-primary fw-bold mb-4">Liên hệ với chúng tôi</h2>
                    <p class="text-muted mb-4">Chúng tôi rất mong nhận được phản hồi và góp ý từ bạn để cải thiện chất lượng dịch vụ. Vui lòng điền thông tin vào mẫu dưới đây để liên hệ với chúng tôi.</p>
                    
                    <?php if(isset($data['success_message']) && !empty($data['success_message'])) : ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?php echo $data['success_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($data['error_message']) && !empty($data['error_message'])) : ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $data['error_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Debug information for development only -->
                    <?php if(defined('DEBUG_MODE') && DEBUG_MODE === true): ?>
                        <div class="alert alert-warning">
                            <p><strong>Debug Mode:</strong> Check the logs for detailed information.</p>
                            <?php if(file_exists(APP_ROOT . '/logs/debug.log')): ?>
                                <p>Log file exists at: <?php echo APP_ROOT . '/logs/debug.log'; ?></p>
                            <?php else: ?>
                                <p>No log file found. Make sure the logs directory is writable.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo URL_ROOT; ?>/home/contact" method="post" class="needs-validation">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label small text-muted">Họ tên <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-user text-primary"></i></span>
                                    <input type="text" class="form-control border-start-0 <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo $data['name']; ?>" placeholder="Nhập họ tên của bạn">
                                </div>
                                <?php if(!empty($data['name_err'])): ?>
                                    <div class="invalid-feedback d-block"><?php echo $data['name_err']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label small text-muted">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-envelope text-primary"></i></span>
                                    <input type="email" class="form-control border-start-0 <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo $data['email']; ?>" placeholder="Nhập địa chỉ email">
                                </div>
                                <?php if(!empty($data['email_err'])): ?>
                                    <div class="invalid-feedback d-block"><?php echo $data['email_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label small text-muted">Tiêu đề <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-heading text-primary"></i></span>
                                <input type="text" class="form-control border-start-0 <?php echo (!empty($data['subject_err'])) ? 'is-invalid' : ''; ?>" id="subject" name="subject" value="<?php echo $data['subject']; ?>" placeholder="Nhập tiêu đề liên hệ">
                            </div>
                            <?php if(!empty($data['subject_err'])): ?>
                                <div class="invalid-feedback d-block"><?php echo $data['subject_err']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-4">
                            <label for="message" class="form-label small text-muted">Nội dung <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-comment-alt text-primary"></i></span>
                                <textarea class="form-control border-start-0 <?php echo (!empty($data['message_err'])) ? 'is-invalid' : ''; ?>" id="message" name="message" rows="6" placeholder="Nhập nội dung tin nhắn của bạn"><?php echo $data['message']; ?></textarea>
                            </div>
                            <?php if(!empty($data['message_err'])): ?>
                                <div class="invalid-feedback d-block"><?php echo $data['message_err']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-paper-plane me-2"></i>Gửi tin nhắn</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h4 class="text-primary fw-bold mb-4">Thông tin liên hệ</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <span class="d-block text-muted small">Email</span>
                                <span class="fw-medium">volan@vothaihonglan.net</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <span class="d-block text-muted small">Điện thoại</span>
                                <span class="fw-medium">+84 0343968449</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <div class="icon-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <span class="d-block text-muted small">Địa chỉ</span>
                                <span class="fw-medium">Đại Phong, xã Phong Thuỷ, Huyện Lệ Thuỷ, Quảng Bình</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h4 class="text-primary fw-bold mb-4">Giờ làm việc</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Thứ Hai - Thứ Sáu:</span>
                            <span class="fw-medium">8:00 - 17:30</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Thứ Bảy:</span>
                            <span class="fw-medium">8:00 - 12:00</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">Chủ Nhật:</span>
                            <span class="fw-medium text-danger">Đóng cửa</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h4 class="text-primary fw-bold mb-4">Theo dõi chúng tôi</h4>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; padding: 0; line-height: 40px;" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; padding: 0; line-height: 40px;" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; padding: 0; line-height: 40px;" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; padding: 0; line-height: 40px;" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="ratio ratio-21x9">
                        <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1917.2555175659147!2d108.2416236384948!3d16.03894959615902!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314217056218e8f3%3A0xee870d6af1db6de8!2zMjA5IE5nxakgSMOgbmggU8ahbiwgQuG6r2MgTeG7uSBQaMO6LCBOZ8WpIEjDoG5oIFPGoW4sIMSQw6AgTuG6tW5nLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1757313960072!5m2!1svi!2s" 
                        width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    transition: all 0.3s ease;
}
.icon-circle:hover {
    transform: scale(1.1);
}
.form-control:focus {
    border-color: #ced4da;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
}
.btn-primary {
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
}
</style>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
