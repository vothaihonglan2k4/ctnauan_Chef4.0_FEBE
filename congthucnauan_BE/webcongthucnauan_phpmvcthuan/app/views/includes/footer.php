    </div>
    
    <footer class="bg-dark py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h4 class="text-white fw-bold mb-4 position-relative">
                            <span class="position-relative pb-2 d-inline-block">Công Thức Nấu Ăn
                                <span class="position-absolute start-0 bottom-0 bg-primary" style="height: 2px; width: 100%;"></span>
                            </span>
                        </h4>
                        <p class="text-white-50 mb-4">Khám phá tinh hoa ẩm thực Việt Nam và thế giới qua hàng ngàn công thức được chia sẻ bởi cộng đồng đầu bếp chuyên nghiệp.</p>
                        <div class="footer-newsletter">
                            <form action="#">
                                <div class="input-group">
                                    <input type="email" class="form-control border-0" placeholder="Email của bạn" required>
                                    <button class="btn btn-primary">Đăng ký</button>
                                </div>
                            </form>
                            <small class="text-white-50 mt-2 d-block">Đăng ký nhận thông báo về công thức mới</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget ps-lg-5">
                        <h4 class="text-white fw-bold mb-4 position-relative">
                            <span class="position-relative pb-2 d-inline-block">Liên kết nhanh
                                <span class="position-absolute start-0 bottom-0 bg-primary" style="height: 2px; width: 100%;"></span>
                            </span>
                        </h4>
                        <ul class="list-unstyled footer-links">
                            <li class="mb-2"><a href="<?php echo URL_ROOT; ?>" class="text-white-50 text-decoration-none footer-link"><i class="fas fa-angle-right me-2"></i>Trang chủ</a></li>
                            <li class="mb-2"><a href="<?php echo URL_ROOT; ?>/recipes" class="text-white-50 text-decoration-none footer-link"><i class="fas fa-angle-right me-2"></i>Công thức</a></li>
                            <li class="mb-2"><a href="<?php echo URL_ROOT; ?>/recipes/categories" class="text-white-50 text-decoration-none footer-link"><i class="fas fa-angle-right me-2"></i>Danh mục món ăn</a></li>
                            <li class="mb-2"><a href="<?php echo URL_ROOT; ?>/home/about" class="text-white-50 text-decoration-none footer-link"><i class="fas fa-angle-right me-2"></i>Giới thiệu</a></li>
                            <li><a href="<?php echo URL_ROOT; ?>/home/contact" class="text-white-50 text-decoration-none footer-link"><i class="fas fa-angle-right me-2"></i>Liên hệ</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h4 class="text-white fw-bold mb-4 position-relative">
                            <span class="position-relative pb-2 d-inline-block">Học nấu ăn
                                <span class="position-absolute start-0 bottom-0 bg-primary" style="height: 2px; width: 100%;"></span>
                            </span>
                        </h4>
                        <ul class="list-unstyled footer-links">
                            <li class="mb-2"><a href="<?php echo URL_ROOT; ?>/courses" class="text-white-50 text-decoration-none footer-link"><i class="fas fa-angle-right me-2"></i>Khóa học nấu ăn</a></li>
                            <?php if(isLoggedIn()): ?>
                                <li class="mb-2"><a href="<?php echo URL_ROOT; ?>/courses/my_courses" class="text-white-50 text-decoration-none footer-link"><i class="fas fa-angle-right me-2"></i>Khóa học của tôi</a></li>
                                <li class="mb-2"><a href="<?php echo URL_ROOT; ?>/courses/schedule" class="text-white-50 text-decoration-none footer-link"><i class="fas fa-angle-right me-2"></i>Lịch học offline</a></li>
                                <li><a href="<?php echo URL_ROOT; ?>/users/profile" class="text-white-50 text-decoration-none footer-link"><i class="fas fa-angle-right me-2"></i>Hồ sơ của tôi</a></li>
                            <?php else: ?>
                                <li class="mb-2"><a href="<?php echo URL_ROOT; ?>/users/login" class="text-white-50 text-decoration-none footer-link"><i class="fas fa-angle-right me-2"></i>Đăng nhập để học</a></li>
                                <li><a href="<?php echo URL_ROOT; ?>/users/register" class="text-white-50 text-decoration-none footer-link"><i class="fas fa-angle-right me-2"></i>Đăng ký tài khoản</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h4 class="text-white fw-bold mb-4 position-relative">
                            <span class="position-relative pb-2 d-inline-block">Kết nối với chúng tôi
                                <span class="position-absolute start-0 bottom-0 bg-primary" style="height: 2px; width: 100%;"></span>
                            </span>
                        </h4>
                        <p class="text-white-50 mb-3">Theo dõi chúng tôi trên các nền tảng mạng xã hội để cập nhật công thức và tin tức mới nhất</p>
                        <div class="social-links mb-4">
                            <a href="https://www.facebook.com/vothaihonglan" class="social-icon-link d-inline-flex align-items-center justify-content-center rounded-circle me-2" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="social-icon-link d-inline-flex align-items-center justify-content-center rounded-circle me-2" title="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="#" class="social-icon-link d-inline-flex align-items-center justify-content-center rounded-circle me-2" title="YouTube">
                                <i class="bi bi-youtube"></i>
                            </a>
                            <a href="#" class="social-icon-link d-inline-flex align-items-center justify-content-center rounded-circle" title="TikTok">
                                <i class="bi bi-tiktok"></i>
                            </a>
                        </div>
                                        <p class="text-white-50 mb-1"><i class="fas fa-phone me-2"></i> +84 0343968449</p>
                <p class="text-white-50"><i class="fas fa-envelope me-2"></i> volan@vothaihonglan.net</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom py-3 mt-5" style="background-color: rgba(0,0,0,0.2);">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-white-50 mb-md-0">&copy; <?php echo date('Y'); ?> Công Thức Nấu Ăn. Tất cả các quyền được bảo lưu.</p>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-inline mb-0 text-md-end">
                            <li class="list-inline-item"><a href="<?php echo URL_ROOT; ?>/home/privacy" class="text-white-50 text-decoration-none footer-link small">Chính sách bảo mật</a></li>
                            <li class="list-inline-item"><span class="text-white-50 mx-2">|</span></li>
                            <li class="list-inline-item"><a href="<?php echo URL_ROOT; ?>/home/terms" class="text-white-50 text-decoration-none footer-link small">Điều khoản sử dụng</a></li>
                            <li class="list-inline-item"><span class="text-white-50 mx-2">|</span></li>
                            <li class="list-inline-item"><a href="<?php echo URL_ROOT; ?>/home/faq" class="text-white-50 text-decoration-none footer-link small">FAQ</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <style>
    .footer-link {
        transition: all 0.3s ease;
    }
    .footer-link:hover {
        color: white !important;
        padding-left: 5px;
    }
    .social-icon-link {
        width: 36px;
        height: 36px;
        background-color: rgba(255,255,255,0.1);
        color: white;
        transition: all 0.3s ease;
    }
    .social-icon-link:hover {
        background-color: var(--bs-primary);
        color: white;
        transform: translateY(-3px);
    }
    .footer-newsletter .form-control {
        background-color: rgba(255,255,255,0.1);
        color: white;
    }
    .footer-newsletter .form-control::placeholder {
        color: rgba(255,255,255,0.5);
    }
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo URL_ROOT; ?>/public/js/main.js"></script>
    
    <!-- Cloudflare Scripts will be injected here -->
</body>
</html>
