import { useState, useEffect } from 'react';
import { Outlet, Navigate, Link, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function ManagerLayout() {
    const { user, isAuthenticated, loading } = useAuth();
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const location = useLocation();

    if (loading) {
        return (
            <div className="d-flex justify-content-center align-items-center min-vh-100">
                <div className="spinner-border text-success" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    if (!isAuthenticated) {
        return <Navigate to="/login" replace />;
    }

    if (!['manager', 'admin'].includes(user?.role)) {
        return <Navigate to="/" replace />;
    }

    const isActive = (path) => location.pathname === path || location.pathname.startsWith(path + '/');

    // Helper để check permission
    const hasPermission = (permissionCode) => {
        if (!user) return false;
        if (user.role === 'admin') return true;
        return user.permissions && user.permissions.includes(permissionCode);
    };

    const navItems = [
        { path: '/manager', icon: 'fas fa-tachometer-alt', label: 'Dashboard', exact: true, permission: null },
        { path: '/manager/recipes', icon: 'fas fa-utensils', label: 'Quản lý Công thức', permission: 'recipe.view' },
        { path: '/manager/courses', icon: 'fas fa-graduation-cap', label: 'Quản lý Khóa học', permission: 'course.view' },
        { path: '/manager/contacts', icon: 'fas fa-envelope', label: 'Liên hệ', permission: 'contact.view' },
        { path: '/manager/reports', icon: 'fas fa-chart-bar', label: 'Báo cáo', permission: 'report.view' },
        { path: '/manager/users', icon: 'fas fa-users', label: 'Quản lý Người dùng', permission: 'user.view' },
        { path: '/manager/categories', icon: 'fas fa-list', label: 'Quản lý Danh mục', permission: 'category.view' },
    ];

    // Lọc menu theo quyền
    const visibleNavItems = navItems.filter(item =>
        !item.permission || hasPermission(item.permission)
    );

    return (
        <div className="manager-wrapper">
            {/* Sidebar */}
            <nav className={`manager-sidebar ${sidebarOpen ? 'show' : ''}`}>
                <button className="btn-close-sidebar" onClick={() => setSidebarOpen(false)}>
                    <i className="fas fa-times"></i>
                </button>
                
                <div className="sidebar-header">
                    <i className="fas fa-user-tie fa-2x mb-2"></i>
                    <h4>Manager Panel</h4>
                    <span className="role-badge">Quản Lý</span>
                    <div className="mt-2 small">
                        <i className="fas fa-user me-1"></i>
                        {user?.name || 'Manager'}
                    </div>
                </div>

                <ul className="sidebar-menu">
                    {visibleNavItems.map((item) => (
                        <li key={item.path}>
                            <Link
                                to={item.path}
                                className={item.exact ? (location.pathname === item.path ? 'active' : '') : (isActive(item.path) ? 'active' : '')}
                                onClick={() => setSidebarOpen(false)}
                            >
                                <i className={item.icon}></i>
                                {item.label}
                            </Link>
                        </li>
                    ))}
                    
                    <hr style={{ borderColor: 'rgba(255,255,255,0.2)', margin: '15px 0' }} />
                    
                    <li>
                        <Link to="/" onClick={() => setSidebarOpen(false)}>
                            <i className="fas fa-home"></i>
                            Về trang chủ
                        </Link>
                    </li>
                </ul>
            </nav>

            {/* Overlay */}
            <div className={`manager-overlay ${sidebarOpen ? 'show' : ''}`} onClick={() => setSidebarOpen(false)}></div>

            {/* Main Content */}
            <div className="manager-main">
                {/* Mobile Header */}
                <div className="manager-mobile-header d-lg-none">
                    <button className="btn btn-success" onClick={() => setSidebarOpen(true)}>
                        <i className="fas fa-bars"></i>
                    </button>
                    <span className="fw-bold">Manager Panel</span>
                </div>
                
                <Outlet />
            </div>


            <style>{`
                .manager-wrapper {
                    display: flex;
                    min-height: 100vh;
                    background: #f8f9fa;
                }

                .manager-sidebar {
                    background: linear-gradient(135deg, #155724, #28a745);
                    color: #fff;
                    width: 240px;
                    min-width: 240px;
                    position: fixed;
                    height: 100vh;
                    top: 0;
                    left: 0;
                    z-index: 1030;
                    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
                    transition: all 0.3s ease;
                    overflow-y: auto;
                }

                .btn-close-sidebar {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    background: rgba(255,255,255,0.2);
                    border: none;
                    border-radius: 50%;
                    width: 30px;
                    height: 30px;
                    color: white;
                    cursor: pointer;
                    display: none;
                }

                .sidebar-header {
                    padding: 20px;
                    text-align: center;
                    border-bottom: 1px solid rgba(255,255,255,0.1);
                    background: rgba(0,0,0,0.1);
                }

                .sidebar-header h4 {
                    margin: 0;
                    font-weight: 600;
                    font-size: 1.1rem;
                }

                .role-badge {
                    background: #198754;
                    color: white;
                    padding: 3px 10px;
                    border-radius: 12px;
                    font-size: 0.75rem;
                    display: inline-block;
                    margin-top: 5px;
                }

                .sidebar-menu {
                    list-style: none;
                    padding: 0;
                    margin: 15px 0 0 0;
                }

                .sidebar-menu li { margin: 0; }

                .sidebar-menu a {
                    display: flex;
                    align-items: center;
                    color: rgba(255,255,255,0.8);
                    text-decoration: none;
                    padding: 12px 20px;
                    transition: all 0.3s ease;
                    border-left: 3px solid transparent;
                }

                .sidebar-menu a:hover,
                .sidebar-menu a.active {
                    color: #fff;
                    background: rgba(255,255,255,0.1);
                    border-left-color: #fff;
                }

                .sidebar-menu i {
                    width: 20px;
                    margin-right: 10px;
                    text-align: center;
                }

                .manager-main {
                    margin-left: 240px;
                    width: calc(100% - 240px);
                    min-height: 100vh;
                    padding: 20px;
                }

                .manager-mobile-header {
                    background: white;
                    padding: 10px 15px;
                    margin: -20px -20px 20px -20px;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }

                .manager-overlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0,0,0,0.5);
                    z-index: 1020;
                }

                @media (max-width: 991.98px) {
                    .manager-sidebar {
                        left: -240px;
                    }
                    .manager-sidebar.show {
                        left: 0;
                    }
                    .manager-main {
                        margin-left: 0;
                        width: 100%;
                    }
                    .manager-overlay.show {
                        display: block;
                    }
                    .btn-close-sidebar {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                }
            `}</style>
        </div>
    );
}
