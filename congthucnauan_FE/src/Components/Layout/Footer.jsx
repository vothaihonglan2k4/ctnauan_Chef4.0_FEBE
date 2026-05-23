import React from 'react';
import { Link } from 'react-router-dom';

const Footer = () => {
    const currentYear = new Date().getFullYear();
    const isLoggedIn = localStorage.getItem('token') !== null;

    return (
        <footer className="bg-dark py-5">
            <div className="container">
                <div className="row g-4">
                    {/* About Section */}
                    <div className="col-lg-3 col-md-6">
                        <div className="footer-widget">
                            <h4 className="text-white fw-bold mb-4 position-relative">
                                <span className="position-relative pb-2 d-inline-block">
                                    Công Thức Nấu Ăn
                                    <span className="position-absolute start-0 bottom-0 bg-primary" style={{height: '2px', width: '100%'}}></span>
                                </span>
                            </h4>
                            <p className="text-white-50 mb-4">
                                Khám phá tinh hoa ẩm thực Việt Nam và thế giới qua hàng ngàn công thức được chia sẻ bởi cộng đồng đầu bếp chuyên nghiệp.
                            </p>
                            <div className="footer-newsletter">
                                <form onSubmit={(e) => e.preventDefault()}>
                                    <div className="input-group">
                                        <input 
                                            type="email" 
                                            className="form-control border-0" 
                                            placeholder="Email của bạn" 
                                            required 
                                        />
                                        <button className="btn btn-primary">Đăng ký</button>
                                    </div>
                                </form>
                                <small className="text-white-50 mt-2 d-block">Đăng ký nhận thông báo về công thức mới</small>
                            </div>
                        </div>
                    </div>
                    
                    {/* Quick Links */}
                    <div className="col-lg-3 col-md-6">
                        <div className="footer-widget ps-lg-5">
                            <h4 className="text-white fw-bold mb-4 position-relative">
                                <span className="position-relative pb-2 d-inline-block">
                                    Liên kết nhanh
                                    <span className="position-absolute start-0 bottom-0 bg-primary" style={{height: '2px', width: '100%'}}></span>
                                </span>
                            </h4>
                            <ul className="list-unstyled footer-links">
                                <li className="mb-2">
                                    <Link to="/" className="text-white-50 text-decoration-none footer-link">
                                        <i className="fas fa-angle-right me-2"></i>Trang chủ
                                    </Link>
                                </li>
                                <li className="mb-2">
                                    <Link to="/recipes" className="text-white-50 text-decoration-none footer-link">
                                        <i className="fas fa-angle-right me-2"></i>Công thức
                                    </Link>
                                </li>
                                <li className="mb-2">
                                    <Link to="/recipes/categories" className="text-white-50 text-decoration-none footer-link">
                                        <i className="fas fa-angle-right me-2"></i>Danh mục món ăn
                                    </Link>
                                </li>
                                <li className="mb-2">
                                    <Link to="/about" className="text-white-50 text-decoration-none footer-link">
                                        <i className="fas fa-angle-right me-2"></i>Giới thiệu
                                    </Link>
                                </li>
                                <li>
                                    <Link to="/contact" className="text-white-50 text-decoration-none footer-link">
                                        <i className="fas fa-angle-right me-2"></i>Liên hệ
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    {/* Learning Section */}
                    <div className="col-lg-3 col-md-6">
                        <div className="footer-widget">
                            <h4 className="text-white fw-bold mb-4 position-relative">
                                <span className="position-relative pb-2 d-inline-block">
                                    Học nấu ăn
                                    <span className="position-absolute start-0 bottom-0 bg-primary" style={{height: '2px', width: '100%'}}></span>
                                </span>
                            </h4>
                            <ul className="list-unstyled footer-links">
                                <li className="mb-2">
                                    <Link to="/courses" className="text-white-50 text-decoration-none footer-link">
                                        <i className="fas fa-angle-right me-2"></i>Khóa học nấu ăn
                                    </Link>
                                </li>
                                {isLoggedIn ? (
                                    <>
                                        <li className="mb-2">
                                            <Link to="/courses/my-courses" className="text-white-50 text-decoration-none footer-link">
                                                <i className="fas fa-angle-right me-2"></i>Khóa học của tôi
                                            </Link>
                                        </li>
                                        <li className="mb-2">
                                            <Link to="/courses/schedule" className="text-white-50 text-decoration-none footer-link">
                                                <i className="fas fa-angle-right me-2"></i>Lịch học offline
                                            </Link>
                                        </li>
                                        <li>
                                            <Link to="/profile" className="text-white-50 text-decoration-none footer-link">
                                                <i className="fas fa-angle-right me-2"></i>Hồ sơ của tôi
                                            </Link>
                                        </li>
                                    </>
                                ) : (
                                    <>
                                        <li className="mb-2">
                                            <Link to="/login" className="text-white-50 text-decoration-none footer-link">
                                                <i className="fas fa-angle-right me-2"></i>Đăng nhập để học
                                            </Link>
                                        </li>
                                        <li>
                                            <Link to="/register" className="text-white-50 text-decoration-none footer-link">
                                                <i className="fas fa-angle-right me-2"></i>Đăng ký tài khoản
                                            </Link>
                                        </li>
                                    </>
                                )}
                            </ul>
                        </div>
                    </div>
                    
                    {/* Social & Contact */}
                    <div className="col-lg-3 col-md-6">
                        <div className="footer-widget">
                            <h4 className="text-white fw-bold mb-4 position-relative">
                                <span className="position-relative pb-2 d-inline-block">
                                    Kết nối với chúng tôi
                                    <span className="position-absolute start-0 bottom-0 bg-primary" style={{height: '2px', width: '100%'}}></span>
                                </span>
                            </h4>
                            <p className="text-white-50 mb-3">
                                Theo dõi chúng tôi trên các nền tảng mạng xã hội để cập nhật công thức và tin tức mới nhất
                            </p>
                            <div className="social-links mb-4">
                                <a 
                                    href="https://www.facebook.com/vothaihonglan" 
                                    className="social-icon-link d-inline-flex align-items-center justify-content-center rounded-circle me-2" 
                                    title="Facebook"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <i className="bi bi-facebook"></i>
                                </a>
                                <a 
                                    href="#" 
                                    className="social-icon-link d-inline-flex align-items-center justify-content-center rounded-circle me-2" 
                                    title="Instagram"
                                >
                                    <i className="bi bi-instagram"></i>
                                </a>
                                <a 
                                    href="#" 
                                    className="social-icon-link d-inline-flex align-items-center justify-content-center rounded-circle me-2" 
                                    title="YouTube"
                                >
                                    <i className="bi bi-youtube"></i>
                                </a>
                                <a 
                                    href="#" 
                                    className="social-icon-link d-inline-flex align-items-center justify-content-center rounded-circle" 
                                    title="TikTok"
                                >
                                    <i className="bi bi-tiktok"></i>
                                </a>
                            </div>
                            <p className="text-white-50 mb-1 footer-contact-item">
                                <i className="fas fa-phone footer-contact-icon" aria-hidden="true"></i>
                                <span>+84 0343968449</span>
                            </p>
                            <p className="text-white-50 footer-contact-item">
                                <i className="fas fa-envelope footer-contact-icon" aria-hidden="true"></i>
                                <span>volan@vothaihonglan.net</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            {/* Footer Bottom */}
            <div className="footer-bottom py-3 mt-5" style={{backgroundColor: 'rgba(0,0,0,0.2)'}}>
                <div className="container">
                    <div className="row">
                        <div className="col-md-6">
                            <p className="text-white-50 mb-md-0">
                                &copy; {currentYear} Công Thức Nấu Ăn. Tất cả các quyền được bảo lưu.
                            </p>
                        </div>
                        <div className="col-md-6">
                            <ul className="list-inline mb-0 text-md-end">
                                <li className="list-inline-item">
                                    <Link to="/privacy" className="text-white-50 text-decoration-none footer-link small">
                                        Chính sách bảo mật
                                    </Link>
                                </li>
                                <li className="list-inline-item">
                                    <span className="text-white-50 mx-2">|</span>
                                </li>
                                <li className="list-inline-item">
                                    <Link to="/terms" className="text-white-50 text-decoration-none footer-link small">
                                        Điều khoản sử dụng
                                    </Link>
                                </li>
                                <li className="list-inline-item">
                                    <span className="text-white-50 mx-2">|</span>
                                </li>
                                <li className="list-inline-item">
                                    <Link to="/faq" className="text-white-50 text-decoration-none footer-link small">
                                        FAQ
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </footer>
    );
};

export default Footer;
