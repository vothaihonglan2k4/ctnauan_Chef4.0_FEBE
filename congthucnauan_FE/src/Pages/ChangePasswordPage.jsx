import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function ChangePasswordPage() {
    const [formData, setFormData] = useState({
        current_password: '',
        password: '',
        password_confirmation: ''
    });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');
    const [showCurrentPassword, setShowCurrentPassword] = useState(false);
    const [showNewPassword, setShowNewPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    
    const { token, isAuthenticated } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login');
        }
    }, [isAuthenticated, navigate]);

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

        if (!formData.current_password) {
            newErrors.current_password = 'Vui lòng nhập mật khẩu hiện tại';
        }

        if (!formData.password) {
            newErrors.password = 'Vui lòng nhập mật khẩu mới';
        } else if (formData.password.length < 6) {
            newErrors.password = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        }

        if (!formData.password_confirmation) {
            newErrors.password_confirmation = 'Vui lòng xác nhận mật khẩu mới';
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
        setSuccessMessage('');

        try {
            const response = await fetch('/api/v1/change-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok) {
                setSuccessMessage('Đổi mật khẩu thành công!');
                setFormData({
                    current_password: '',
                    password: '',
                    password_confirmation: ''
                });
                
                // Redirect to profile after 2 seconds
                setTimeout(() => {
                    navigate('/profile');
                }, 2000);
            } else {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    setErrors({ general: data.message || 'Có lỗi xảy ra' });
                }
            }
        } catch (error) {
            console.error('Error changing password:', error);
            setErrors({ general: 'Có lỗi xảy ra khi đổi mật khẩu' });
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="row mt-4">
            <div className="col-md-6 mx-auto">
                <div className="card">
                    <div className="card-header bg-primary text-white">
                        <h3 className="mb-0">
                            <i className="fas fa-key me-2"></i>
                            Đổi mật khẩu
                        </h3>
                    </div>
                    <div className="card-body">
                        {errors.general && (
                            <div className="alert alert-danger" role="alert">
                                <i className="fas fa-exclamation-circle me-2"></i>
                                {errors.general}
                            </div>
                        )}

                        {successMessage && (
                            <div className="alert alert-success" role="alert">
                                <i className="fas fa-check-circle me-2"></i>
                                {successMessage}
                            </div>
                        )}

                        <form onSubmit={handleSubmit}>
                            {/* Current Password */}
                            <div className="mb-3">
                                <label htmlFor="current_password" className="form-label">
                                    Mật khẩu hiện tại <span className="text-danger">*</span>
                                </label>
                                <div className="input-group">
                                    <input
                                        type={showCurrentPassword ? "text" : "password"}
                                        className={`form-control ${errors.current_password ? 'is-invalid' : ''}`}
                                        id="current_password"
                                        name="current_password"
                                        value={formData.current_password}
                                        onChange={handleChange}
                                        placeholder="Nhập mật khẩu hiện tại"
                                    />
                                    <span 
                                        className="input-group-text" 
                                        style={{ cursor: 'pointer' }}
                                        onClick={() => setShowCurrentPassword(!showCurrentPassword)}>
                                        <i className={`bi ${showCurrentPassword ? 'bi-eye-slash' : 'bi-eye'}`}></i>
                                    </span>
                                    {errors.current_password && (
                                        <div className="invalid-feedback">{errors.current_password}</div>
                                    )}
                                </div>
                            </div>

                            {/* New Password */}
                            <div className="mb-3">
                                <label htmlFor="password" className="form-label">
                                    Mật khẩu mới <span className="text-danger">*</span>
                                </label>
                                <div className="input-group">
                                    <input
                                        type={showNewPassword ? "text" : "password"}
                                        className={`form-control ${errors.password ? 'is-invalid' : ''}`}
                                        id="password"
                                        name="password"
                                        value={formData.password}
                                        onChange={handleChange}
                                        placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                                    />
                                    <span 
                                        className="input-group-text" 
                                        style={{ cursor: 'pointer' }}
                                        onClick={() => setShowNewPassword(!showNewPassword)}>
                                        <i className={`bi ${showNewPassword ? 'bi-eye-slash' : 'bi-eye'}`}></i>
                                    </span>
                                    {errors.password && (
                                        <div className="invalid-feedback">{errors.password}</div>
                                    )}
                                </div>
                            </div>

                            {/* Confirm Password */}
                            <div className="mb-3">
                                <label htmlFor="password_confirmation" className="form-label">
                                    Xác nhận mật khẩu mới <span className="text-danger">*</span>
                                </label>
                                <div className="input-group">
                                    <input
                                        type={showConfirmPassword ? "text" : "password"}
                                        className={`form-control ${errors.password_confirmation ? 'is-invalid' : ''}`}
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        value={formData.password_confirmation}
                                        onChange={handleChange}
                                        placeholder="Nhập lại mật khẩu mới"
                                    />
                                    <span 
                                        className="input-group-text" 
                                        style={{ cursor: 'pointer' }}
                                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}>
                                        <i className={`bi ${showConfirmPassword ? 'bi-eye-slash' : 'bi-eye'}`}></i>
                                    </span>
                                    {errors.password_confirmation && (
                                        <div className="invalid-feedback">{errors.password_confirmation}</div>
                                    )}
                                </div>
                            </div>

                            {/* Password Requirements */}
                            <div className="alert alert-info mb-3">
                                <small>
                                    <i className="fas fa-info-circle me-1"></i>
                                    <strong>Yêu cầu mật khẩu:</strong>
                                    <ul className="mb-0 mt-2">
                                        <li>Tối thiểu 6 ký tự</li>
                                        <li>Nên kết hợp chữ hoa, chữ thường và số</li>
                                        <li>Không sử dụng mật khẩu quá đơn giản</li>
                                    </ul>
                                </small>
                            </div>

                            {/* Submit Buttons */}
                            <div className="d-grid gap-2">
                                <button
                                    type="submit"
                                    className="btn btn-primary"
                                    disabled={loading}
                                >
                                    {loading ? (
                                        <>
                                            <span className="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            Đang xử lý...
                                        </>
                                    ) : (
                                        <>
                                            <i className="fas fa-key me-2"></i>
                                            Đổi mật khẩu
                                        </>
                                    )}
                                </button>
                                <Link to="/profile" className="btn btn-secondary">
                                    <i className="fas fa-arrow-left me-2"></i>
                                    Quay lại
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Security Tips */}
                <div className="card mt-3">
                    <div className="card-body">
                        <h6 className="card-title">
                            <i className="fas fa-shield-alt text-success me-2"></i>
                            Mẹo bảo mật
                        </h6>
                        <ul className="small text-muted mb-0">
                            <li>Thay đổi mật khẩu định kỳ (3-6 tháng)</li>
                            <li>Không chia sẻ mật khẩu với người khác</li>
                            <li>Sử dụng mật khẩu khác nhau cho các tài khoản</li>
                            <li>Đăng xuất sau khi sử dụng trên máy chung</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    );
}
