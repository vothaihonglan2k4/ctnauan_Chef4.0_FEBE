import { useState, useEffect } from 'react';
import { Outlet, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import AdminSidebar from '../Admin/AdminSidebar';

export default function AdminLayout() {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const { isAuthenticated, isAdmin, loading } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();

    const getyearNamNay = () => new Date().getFullYear();

    useEffect(() => {
        if (loading) return;

        if (!isAuthenticated) {
            navigate('/login', { state: { from: location }, replace: true });
            return;
        }

        if (!isAdmin) {
            alert('Bạn không có quyền truy cập trang này');
            navigate('/', { replace: true });
            return;
        }
    }, [isAuthenticated, isAdmin, loading, navigate, location]);

    const toggleSidebar = () => {
        setSidebarOpen(!sidebarOpen);
    };

    const closeSidebar = () => {
        setSidebarOpen(false);
    };

    if (loading) {
        return (
            <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '100vh' }}>
                <div className="spinner-border text-primary" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    if (!isAuthenticated || !isAdmin) {
        return null;
    }

    return (
        <div className="admin-wrapper">
            {/* Sidebar toggle button for mobile */}
            <button 
                type="button" 
                id="sidebarCollapse" 
                className="btn-sidebar-toggle"
                onClick={toggleSidebar}
            >
                <i className="fas fa-bars"></i>
            </button>

            {/* Sidebar */}
            <AdminSidebar isOpen={sidebarOpen} onClose={closeSidebar} />

            {/* Main content wrapper */}
            <div className="admin-main-content-wrapper">
                <div className="admin-main-content">
                    <Outlet />
                </div>

                {/* Footer */}
                <footer className="admin-footer">
                    <div className="d-flex justify-content-between align-items-center">
                        <span>© {getyearNamNay()} Công thức nấu ăn. All rights reserved.</span>
                        <span>Version 1.0.0</span>
                    </div>
                </footer>
            </div>

            <style>{`
                .admin-wrapper {
                    display: flex;
                    width: 100%;
                    min-height: 100vh;
                    position: relative;
                    background-color: #f5f7fa;
                }

                .btn-sidebar-toggle {
                    background: #304352;
                    color: white;
                    border: none;
                    position: fixed;
                    top: 15px;
                    left: 15px;
                    z-index: 1050;
                    display: none;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    box-shadow: 0 0 10px rgba(0,0,0,0.2);
                }

                .admin-main-content-wrapper {
                    width: 100%;
                    min-height: 100vh;
                    margin-left: 220px;
                    display: flex;
                    flex-direction: column;
                    flex: 1;
                    transition: all 0.3s ease;
                    background-color: #f5f7fa;
                    position: relative;
                    overflow-x: hidden;
                }

                .admin-main-content {
                    flex: 1;
                    padding: 30px;
                    width: 100%;
                    min-height: calc(100vh - 60px);
                }

                .admin-footer {
                    padding: 20px 30px;
                    color: #718096;
                    border-top: 1px solid rgba(0,0,0,0.05);
                    background-color: #fff;
                    font-size: 0.9rem;
                    width: 100%;
                }

                @media (max-width: 991.98px) {
                    .admin-main-content-wrapper {
                        margin-left: 0;
                        width: 100%;
                    }

                    .btn-sidebar-toggle {
                        display: block;
                    }

                    .admin-main-content {
                        padding: 20px 15px;
                        padding-top: 70px;
                    }
                }

                @media (max-width: 767.98px) {
                    .admin-main-content {
                        padding: 15px 10px;
                        padding-top: 70px;
                    }
                }
            `}</style>
        </div>
    );
}
