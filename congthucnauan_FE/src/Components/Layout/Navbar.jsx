import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function Navbar() {
    const [searchTerm, setSearchTerm] = useState('');
    const navigate = useNavigate();
    const { user, logout, isAuthenticated } = useAuth();

    const handleSearch = (e) => {
        e.preventDefault();
        if (searchTerm.trim()) {
            navigate(`/recipes/search?term=${encodeURIComponent(searchTerm)}`);
        } else {
            navigate('/recipes');
        }
    };

    const handleLogout = () => {
        logout();
    };

    return (
        <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
            <div className="container">
                <Link className="navbar-brand" to="/">
                    <i className="fas fa-utensils me-2"></i>Công Thức Nấu Ăn
                </Link>
                <button 
                    className="navbar-toggler" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarMain"
                    aria-controls="navbarMain"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span className="navbar-toggler-icon"></span>
                </button>
                <div className="collapse navbar-collapse" id="navbarMain">
                    <ul className="navbar-nav me-auto mb-2 mb-lg-0">
                        <li className="nav-item">
                            <Link className="nav-link" to="/">Trang chủ</Link>
                        </li>
                        <li className="nav-item">
                            <Link className="nav-link" to="/recipes">Công thức</Link>
                        </li>
                        <li className="nav-item">
                            <Link className="nav-link" to="/courses">Học nấu ăn online</Link>
                        </li>
                        <li className="nav-item">
                            <Link className="nav-link" to="/about">Giới thiệu</Link>
                        </li>
                        <li className="nav-item">
                            <Link className="nav-link" to="/contact">Liên hệ</Link>
                        </li>
                        <li className="nav-item dropdown">
                            <a 
                                className="nav-link dropdown-toggle" 
                                href="#" 
                                id="moreDropdown" 
                                role="button" 
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                Xem thêm
                            </a>
                            <ul className="dropdown-menu" aria-labelledby="moreDropdown">
                                <li><Link className="dropdown-item" to="/forum">Diễn đàn</Link></li>
                            </ul>
                        </li>
                    </ul>
                    
                    <form className="d-flex me-2" onSubmit={handleSearch}>
                        <input 
                            className="form-control me-2" 
                            type="search" 
                            placeholder="Tìm công thức"
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            aria-label="Tìm kiếm"
                        />
                        <button className="btn btn-outline-light" type="submit">Tìm</button>
                    </form>
                    
                    <ul className="navbar-nav">
                        {isAuthenticated ? (
                            <>
                                {user?.role === 'admin' && (
                                    <li className="nav-item">
                                        <Link className="nav-link" to="/admin">
                                            <i className="fas fa-cog me-1"></i>Quản trị
                                        </Link>
                                    </li>
                                )}
                                {user?.role === 'manager' && (
                                    <li className="nav-item">
                                        <Link className="nav-link" to="/manager">
                                            <i className="fas fa-user-tie me-1"></i>Quản lý
                                        </Link>
                                    </li>
                                )}
                                <li className="nav-item dropdown">
                                    <a 
                                        className="nav-link dropdown-toggle" 
                                        href="#" 
                                        id="userDropdown" 
                                        role="button" 
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        <i className="fas fa-user me-1"></i>{user?.name}
                                    </a>
                                    <ul className="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <li><Link className="dropdown-item" to="/profile">Hồ sơ</Link></li>
                                        <li><Link className="dropdown-item" to="/recipes/add">Thêm công thức</Link></li>
                                        <li><Link className="dropdown-item" to="/payments">Thanh toán</Link></li>
                                        <li><hr className="dropdown-divider" /></li>
                                        <li>
                                            <a 
                                                className="dropdown-item" 
                                                href="#" 
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    handleLogout();
                                                }}
                                            >
                                                Đăng xuất
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </>
                        ) : (
                            <>
                                <li className="nav-item">
                                    <Link className="nav-link" to="/login">Đăng nhập</Link>
                                </li>
                                <li className="nav-item">
                                    <Link className="nav-link" to="/register">Đăng ký</Link>
                                </li>
                            </>
                        )}
                    </ul>
                </div>
            </div>
        </nav>
    );
}