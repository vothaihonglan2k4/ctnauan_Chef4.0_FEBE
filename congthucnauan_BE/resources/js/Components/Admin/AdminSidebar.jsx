import { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function AdminSidebar({ isOpen, onClose }) {
    const location = useLocation();
    const { user, logout } = useAuth();
    const [contentManagementOpen, setContentManagementOpen] = useState(false);

    const isActive = (path) => {
        return location.pathname === path || location.pathname.startsWith(path + '/');
    };

    const navItems = [
        { path: '/admin', icon: 'fas fa-tachometer-alt', label: 'Bảng điều khiển', exact: true },
        { path: '/admin/users', icon: 'fas fa-users', label: 'Quản lý người dùng' },
        { path: '/admin/categories', icon: 'fas fa-folder', label: 'Quản lý danh mục' },
        { path: '/recipes', icon: 'fas fa-utensils', label: 'Quản lý công thức' },
        { path: '/admin/courses', icon: 'fas fa-chalkboard-teacher', label: 'Quản lý khóa học' },
        { path: '/admin/classrooms', icon: 'fas fa-door-open', label: 'Quản lý phòng học' },
        { path: '/admin/payments', icon: 'fas fa-money-bill-wave', label: 'Quản lý thanh toán' },
        { path: '/admin/reports', icon: 'fas fa-chart-bar', label: 'Báo cáo & Thống kê' },
    ];

    return (
        <>
            {/* Sidebar */}
            <div className={`admin-sidebar ${isOpen ? 'active' : ''}`}>
                <button 
                    type="button" 
                    id="closeSidebar" 
                    className="btn-close-sidebar"
                    onClick={onClose}
                    aria-label="Close"
                >
                    <i className="fas fa-times"></i>
                </button>
                
                <div className="admin-logo">
                    <i className="fas fa-utensils"></i>
                    <span>ADMIN PANEL</span>
                </div>

                <div className="px-2">
                    <ul className="nav nav-pills flex-column mb-auto list-unstyled">
                        {navItems.map((item) => (
                            <li key={item.path} className="nav-item">
                                <Link
                                    to={item.path}
                                    className={`nav-link ${
                                        item.exact 
                                            ? location.pathname === item.path ? 'active' : ''
                                            : isActive(item.path) ? 'active' : ''
                                    }`}
                                    onClick={onClose}
                                >
                                    <i className={`${item.icon} me-2`}></i>
                                    {item.label}
                                </Link>
                            </li>
                        ))}

                        {/* Content Management Submenu */}
                        <li className="nav-item">
                            <a
                                className={`nav-link collapsed ${
                                    isActive('/admin/manageRecipes') || isActive('/admin/manageComments') 
                                        ? 'active' : ''
                                }`}
                                href="#"
                                onClick={(e) => {
                                    e.preventDefault();
                                    setContentManagementOpen(!contentManagementOpen);
                                }}
                            >
                                <i className="fas fa-file-alt me-2"></i>
                                <span>Quản lý Nội dung</span>
                            </a>
                            <div className={`collapse ${contentManagementOpen ? 'show' : ''}`}>
                                <div className="py-2 collapse-inner">
                                    <Link
                                        className={`collapse-item ${isActive('/admin/manageRecipes') ? 'active' : ''}`}
                                        to="/admin/manageRecipes"
                                        onClick={onClose}
                                    >
                                        Duyệt Công thức
                                    </Link>
                                    <Link
                                        className={`collapse-item ${isActive('/admin/manageComments') ? 'active' : ''}`}
                                        to="/admin/manageComments"
                                        onClick={onClose}
                                    >
                                        Quản lý Bình luận
                                    </Link>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <hr className="my-3 bg-light opacity-25" />

                    {/* User Dropdown */}
                    <div className="dropdown mb-3">
                        <a
                            href="#"
                            className="d-flex align-items-center text-decoration-none dropdown-toggle p-3"
                            id="dropdownUser1"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <img
                                src={user?.avatar && user.avatar !== 'default-avatar.png'
                                    ? `/uploads/avatars/${user.avatar}`
                                    : '/img/default-avatar.png'
                                }
                                alt=""
                                width="40"
                                height="40"
                                className="rounded-circle me-2 border border-2 border-white"
                                style={{ objectFit: 'cover' }}
                            />
                            <div>
                                <strong className="d-block">{user?.name || 'Admin'}</strong>
                                <small className="text-light opacity-75">Administrator</small>
                            </div>
                        </a>
                        <ul className="dropdown-menu dropdown-menu-dark shadow" aria-labelledby="dropdownUser1">
                            <li>
                                <Link className="dropdown-item" to="/profile" onClick={onClose}>
                                    <i className="fas fa-user me-2"></i>Hồ sơ
                                </Link>
                            </li>
                            <li>
                                <Link className="dropdown-item" to="/admin/settings" onClick={onClose}>
                                    <i className="fas fa-cog me-2"></i>Cài đặt
                                </Link>
                            </li>
                            <li><hr className="dropdown-divider" /></li>
                            <li>
                                <a className="dropdown-item" href="#" onClick={(e) => {
                                    e.preventDefault();
                                    logout();
                                }}>
                                    <i className="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {/* Overlay for mobile */}
            <div 
                className={`admin-overlay ${isOpen ? 'active' : ''}`}
                onClick={onClose}
            ></div>

            <style>{`
                .admin-sidebar {
                    background: linear-gradient(135deg, #304352, #23303f);
                    color: #fff;
                    width: 220px;
                    min-width: 220px;
                    position: fixed;
                    height: 100vh;
                    top: 0;
                    left: 0;
                    z-index: 1030;
                    box-shadow: 0 0 15px rgba(0,0,0,.1);
                    transition: all 0.3s ease;
                    overflow-y: auto;
                }

                .btn-close-sidebar {
                    position: absolute;
                    top: 20px;
                    right: 20px;
                    background: rgba(255, 255, 255, 0.2);
                    border: none;
                    border-radius: 50%;
                    width: 30px;
                    height: 30px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    z-index: 1031;
                }

                .btn-close-sidebar:hover {
                    background: rgba(255, 255, 255, 0.4);
                    transform: scale(1.1);
                }

                .admin-logo {
                    font-weight: 700;
                    font-size: 1.25rem;
                    padding: 20px 15px;
                    background: rgba(0,0,0,0.15);
                    margin-bottom: 15px;
                    text-align: center;
                    letter-spacing: 1px;
                }

                .admin-logo i {
                    margin-right: 10px;
                    font-size: 1.4rem;
                }

                .admin-sidebar .nav-link {
                    color: rgba(255, 255, 255, 0.85);
                    text-decoration: none;
                    display: block;
                    padding: 13px 20px;
                    margin: 4px 12px;
                    border-radius: 8px;
                    transition: all 0.2s ease;
                    position: relative;
                }

                .admin-sidebar .nav-link:hover,
                .admin-sidebar .nav-link.active {
                    background-color: rgba(255, 255, 255, 0.15);
                    color: #fff;
                    transform: translateY(-1px);
                }

                .admin-sidebar .nav-link.active {
                    background-color: #0d6efd;
                    box-shadow: 0 4px 10px rgba(13, 110, 253, 0.4);
                }

                .collapse-inner {
                    background-color: transparent;
                    padding-top: 0.25rem;
                    padding-bottom: 0.25rem;
                    margin-left: 15px;
                    border-left: 2px solid rgba(255, 255, 255, 0.1);
                    padding-left: 10px;
                }

                .collapse-item {
                    color: rgba(255, 255, 255, 0.8);
                    padding: 8px 15px;
                    margin: 2px 0;
                    border-radius: 8px;
                    transition: all 0.2s ease;
                    display: block;
                    font-size: 0.9rem;
                    text-decoration: none;
                }

                .collapse-item:hover,
                .collapse-item.active {
                    background-color: rgba(255, 255, 255, 0.1);
                    color: #fff;
                }

                .admin-overlay {
                    display: none;
                    position: fixed;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 998;
                    opacity: 0;
                    transition: all 0.3s ease;
                    top: 0;
                    left: 0;
                }

                @media (max-width: 991.98px) {
                    .admin-sidebar {
                        left: -220px;
                    }

                    .admin-sidebar.active {
                        left: 0;
                    }

                    .admin-overlay.active {
                        display: block;
                        opacity: 1;
                    }

                    .btn-close-sidebar {
                        display: flex;
                    }
                }

                @media (min-width: 992px) {
                    .btn-close-sidebar {
                        display: none;
                    }
                }
            `}</style>
        </>
    );
}
