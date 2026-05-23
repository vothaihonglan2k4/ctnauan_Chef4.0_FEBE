import { useState } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function LoginPage() {
    const [formData, setFormData] = useState({
        email: '',
        password: ''
    });
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    
    const { login } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();

    const from = location.state?.from?.pathname || '/';

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        const result = await login(formData.email, formData.password);

        if (result.success) {
            // Redirect theo role
            if (result.user.role === 'admin') {
                navigate('/admin', { replace: true });
            } else if (result.user.role === 'manager') {
                navigate('/manager', { replace: true });
            } else {
                navigate(from, { replace: true });
            }
        } else {
            setError(result.message);
        }

        setLoading(false);
    };

    const togglePasswordVisibility = () => {
        setShowPassword(!showPassword);
    };

    return (
        <div className="container mt-5">
            <div className="row justify-content-center">
                <div className="col-md-6 col-lg-5">
                    <div className="card shadow">
                        <div className="card-header bg-primary text-white">
                            <h4 className="mb-0 text-center">
                                <i className="fas fa-sign-in-alt me-2"></i>
                                Đăng nhập
                            </h4>
                        </div>
                        <div className="card-body p-4">
                            {error && (
                                <div className="alert alert-danger" role="alert">
                                    <i className="fas fa-exclamation-circle me-2"></i>
                                    {error}
                                </div>
                            )}

                            <form onSubmit={handleSubmit}>
                                <div className="mb-3">
                                    <label htmlFor="email" className="form-label">
                                        <i className="fas fa-envelope me-2"></i>
                                        Email <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        className="form-control"
                                        id="email"
                                        name="email"
                                        value={formData.email}
                                        onChange={handleChange}
                                        placeholder="Nhập email của bạn"
                                        required
                                        autoFocus
                                    />
                                </div>

                                <div className="mb-3">
                                    <label htmlFor="password" className="form-label">
                                        <i className="fas fa-lock me-2"></i>
                                        Mật khẩu <span className="text-danger">*</span>
                                    </label>
                                    <div className="position-relative">
                                        <input
                                            type={showPassword ? "text" : "password"}
                                            className="form-control"
                                            id="password"
                                            name="password"
                                            value={formData.password}
                                            onChange={handleChange}
                                            placeholder="Nhập mật khẩu"
                                            required
                                        />
                                        <i 
                                            className={`bi ${showPassword ? 'bi-eye-slash' : 'bi-eye'} position-absolute`}
                                            style={{ right: '10px', top: '50%', transform: 'translateY(-50%)', cursor: 'pointer' }}
                                            onClick={togglePasswordVisibility}
                                        ></i>
                                    </div>
                                </div>

                                <div className="mb-3 form-check">
                                    <input
                                        type="checkbox"
                                        className="form-check-input"
                                        id="remember"
                                    />
                                    <label className="form-check-label" htmlFor="remember">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>

                                <div className="d-grid gap-2">
                                    <button
                                        type="submit"
                                        className="btn btn-primary"
                                        disabled={loading}
                                    >
                                        {loading ? (
                                            <>
                                                <span className="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                                Đang đăng nhập...
                                            </>
                                        ) : (
                                            <>
                                                <i className="fas fa-sign-in-alt me-2"></i>
                                                Đăng nhập
                                            </>
                                        )}
                                    </button>
                                </div>
                            </form>

                            <hr className="my-4" />

                            <div className="text-center">
                                <p className="mb-0">
                                    Chưa có tài khoản?{' '}
                                    <Link to="/register" className="text-decoration-none">
                                        Đăng ký ngay
                                    </Link>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="text-center mt-3">
                        <Link to="/" className="text-decoration-none">
                            <i className="fas fa-arrow-left me-2"></i>
                            Quay lại trang chủ
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}