import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function RegisterPage() {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    
    const { register } = useAuth();
    const navigate = useNavigate();

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
        // Clear error for this field
        if (errors[e.target.name]) {
            setErrors({
                ...errors,
                [e.target.name]: ''
            });
        }
    };

    const validateForm = () => {
        const newErrors = {};

        if (!formData.name.trim()) {
            newErrors.name = 'Vui lòng nhập họ tên';
        }

        if (!formData.email.trim()) {
            newErrors.email = 'Vui lòng nhập email';
        } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
            newErrors.email = 'Email không hợp lệ';
        }

        if (!formData.password) {
            newErrors.password = 'Vui lòng nhập mật khẩu';
        } else if (formData.password.length < 6) {
            newErrors.password = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        if (!formData.password_confirmation) {
            newErrors.password_confirmation = 'Vui lòng xác nhận mật khẩu';
        } else if (formData.password !== formData.password_confirmation) {
            newErrors.password_confirmation = 'Mật khẩu xác nhận không khớp';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        setLoading(true);

        const result = await register(
            formData.name,
            formData.email,
            formData.password,
            formData.password_confirmation
        );

        if (result.success) {
            navigate('/', { replace: true });
        } else {
            setErrors({ general: result.message });
        }

        setLoading(false);
    };

    return (
        <div className="container mt-5">
            <div className="row justify-content-center">
                <div className="col-md-6 col-lg-5">
                    <div className="card shadow">
                        <div className="card-header bg-success text-white">
                            <h4 className="mb-0 text-center">
                                <i className="fas fa-user-plus me-2"></i>
                                Đăng ký tài khoản
                            </h4>
                        </div>
                        <div className="card-body p-4">
                            {errors.general && (
                                <div className="alert alert-danger" role="alert">
                                    <i className="fas fa-exclamation-circle me-2"></i>
                                    {errors.general}
                                </div>
                            )}

                            <form onSubmit={handleSubmit}>
                                <div className="mb-3">
                                    <label htmlFor="name" className="form-label">
                                        <i className="fas fa-user me-2"></i>
                                        Họ và tên <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                        id="name"
                                        name="name"
                                        value={formData.name}
                                        onChange={handleChange}
                                        placeholder="Nhập họ và tên"
                                        autoFocus
                                    />
                                    {errors.name && (
                                        <div className="invalid-feedback">{errors.name}</div>
                                    )}
                                </div>

                                <div className="mb-3">
                                    <label htmlFor="email" className="form-label">
                                        <i className="fas fa-envelope me-2"></i>
                                        Email <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                                        id="email"
                                        name="email"
                                        value={formData.email}
                                        onChange={handleChange}
                                        placeholder="Nhập địa chỉ email"
                                    />
                                    {errors.email && (
                                        <div className="invalid-feedback">{errors.email}</div>
                                    )}
                                </div>

                                <div className="mb-3">
                                    <label htmlFor="password" className="form-label">
                                        <i className="fas fa-lock me-2"></i>
                                        Mật khẩu <span className="text-danger">*</span>
                                    </label>
                                    <div className="input-group">
                                        <input
                                            type={showPassword ? "text" : "password"}
                                            className={`form-control ${errors.password ? 'is-invalid' : ''}`}
                                            id="password"
                                            name="password"
                                            value={formData.password}
                                            onChange={handleChange}
                                            placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)"
                                        />
                                        <span className="input-group-text" style={{ cursor: 'pointer' }} onClick={() => setShowPassword(!showPassword)}>
                                            <i className={`bi ${showPassword ? 'bi-eye-slash' : 'bi-eye'}`}></i>
                                        </span>
                                        {errors.password && (
                                            <div className="invalid-feedback">{errors.password}</div>
                                        )}
                                    </div>
                                </div>

                                <div className="mb-3">
                                    <label htmlFor="password_confirmation" className="form-label">
                                        <i className="fas fa-lock me-2"></i>
                                        Xác nhận mật khẩu <span className="text-danger">*</span>
                                    </label>
                                    <div className="input-group">
                                        <input
                                            type={showConfirmPassword ? "text" : "password"}
                                            className={`form-control ${errors.password_confirmation ? 'is-invalid' : ''}`}
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            value={formData.password_confirmation}
                                            onChange={handleChange}
                                            placeholder="Nhập lại mật khẩu"
                                        />
                                        <span className="input-group-text" style={{ cursor: 'pointer' }} onClick={() => setShowConfirmPassword(!showConfirmPassword)}>
                                            <i className={`bi ${showConfirmPassword ? 'bi-eye-slash' : 'bi-eye'}`}></i>
                                        </span>
                                        {errors.password_confirmation && (
                                            <div className="invalid-feedback">{errors.password_confirmation}</div>
                                        )}
                                    </div>
                                </div>

                                <div className="mb-3 form-check">
                                    <input
                                        type="checkbox"
                                        className="form-check-input"
                                        id="terms"
                                        required
                                    />
                                    <label className="form-check-label" htmlFor="terms">
                                        Tôi đồng ý với{' '}
                                        <Link to="/terms" className="text-decoration-none">
                                            Điều khoản sử dụng
                                        </Link>
                                    </label>
                                </div>

                                <div className="d-grid gap-2">
                                    <button
                                        type="submit"
                                        className="btn btn-success"
                                        disabled={loading}
                                    >
                                        {loading ? (
                                            <>
                                                <span className="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                                Đang đăng ký...
                                            </>
                                        ) : (
                                            <>
                                                <i className="fas fa-user-plus me-2"></i>
                                                Đăng ký
                                            </>
                                        )}
                                    </button>
                                </div>
                            </form>

                            <hr className="my-4" />

                            <div className="text-center">
                                <p className="mb-0">
                                    Đã có tài khoản?{' '}
                                    <Link to="/login" className="text-decoration-none">
                                        Đăng nhập ngay
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