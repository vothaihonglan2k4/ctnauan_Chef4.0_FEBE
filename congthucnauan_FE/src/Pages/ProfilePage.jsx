import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function ProfilePage() {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        address: ''
    });
    const [avatarFile, setAvatarFile] = useState(null);
    const [avatarPreview, setAvatarPreview] = useState(null);
    const [userRecipes, setUserRecipes] = useState([]);
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');
    
    const { user, token, isAuthenticated, updateUser } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login');
            return;
        }

        // Load user data
        if (user) {
            setFormData({
                name: user.name || '',
                email: user.email || '',
                phone: user.phone || '',
                address: user.address || ''
            });
            
            // Set avatar preview
            if (user.avatar && user.avatar !== 'default-avatar.png') {
                setAvatarPreview(`/uploads/avatars/${user.avatar}`);
            } else {
                setAvatarPreview('/img/default-avatar.png');
            }
        }

        // Fetch user's recipes
        fetchUserRecipes();
    }, [isAuthenticated, navigate, user]);

    const fetchUserRecipes = async () => {
        try {
            const response = await fetch('/api/v1/my-recipes', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (response.ok) {
                setUserRecipes(data.recipes || []);
            }
        } catch (error) {
            console.error('Error fetching recipes:', error);
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        
        // Auto format phone number
        if (name === 'phone') {
            const phoneValue = value.replace(/[^0-9]/g, '').slice(0, 11);
            setFormData({ ...formData, [name]: phoneValue });
        } else {
            setFormData({ ...formData, [name]: value });
        }

        // Clear error
        if (errors[name]) {
            setErrors({ ...errors, [name]: '' });
        }
    };

    const handleAvatarChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                setErrors({
                    ...errors,
                    avatar: 'Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF)'
                });
                e.target.value = '';
                return;
            }

            // Validate file size (5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                setErrors({
                    ...errors,
                    avatar: 'Kích thước ảnh không được vượt quá 5MB'
                });
                e.target.value = '';
                return;
            }

            setAvatarFile(file);
            setErrors({ ...errors, avatar: '' });

            // Create preview
            const reader = new FileReader();
            reader.onloadend = () => {
                setAvatarPreview(reader.result);
            };
            reader.readAsDataURL(file);
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

        if (formData.phone && !/^[0-9]{10,11}$/.test(formData.phone)) {
            newErrors.phone = 'Số điện thoại không hợp lệ (10-11 số)';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        if (!confirm('Bạn có chắc chắn muốn cập nhật thông tin?')) {
            return;
        }

        // Debug: Check token
        console.log('Token:', token);
        console.log('User:', user);

        setLoading(true);
        setSuccessMessage('');

        // Create FormData for file upload
        const formDataToSend = new FormData();
        formDataToSend.append('_method', 'PUT'); // Laravel method spoofing
        formDataToSend.append('name', formData.name);
        formDataToSend.append('email', formData.email);
        formDataToSend.append('phone', formData.phone || '');
        formDataToSend.append('address', formData.address || '');
        
        if (avatarFile) {
            formDataToSend.append('avatar', avatarFile);
        }

        try {
            const response = await fetch('/api/v1/profile', {
                method: 'POST', // Use POST with _method=PUT for FormData
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                },
                body: formDataToSend
            });

            const data = await response.json();

            if (response.ok) {
                setSuccessMessage('Cập nhật thông tin thành công!');
                // Update user in context
                updateUser(data.user);
                // Clear avatar file
                setAvatarFile(null);
            } else {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    setErrors({ general: data.message || 'Có lỗi xảy ra' });
                }
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            setErrors({ general: 'Có lỗi xảy ra khi cập nhật thông tin' });
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteRecipe = async (recipeId) => {
        if (!confirm('Bạn có chắc chắn muốn xóa công thức này?')) {
            return;
        }

        try {
            const response = await fetch(`/api/v1/recipes/${recipeId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok) {
                alert('Xóa công thức thành công!');
                fetchUserRecipes();
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Error deleting recipe:', error);
            alert('Có lỗi xảy ra khi xóa công thức');
        }
    };

    return (
        <div className="row mt-4">
            <div className="col-md-8 mx-auto">
                <div className="card">
                    <div className="card-header bg-primary text-white">
                        <h3 className="mb-0">
                            <i className="fas fa-user-circle me-2"></i>
                            Thông tin cá nhân
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
                            {/* Avatar Section */}
                            <div className="text-center mb-4">
                                <div className="avatar-preview mb-3">
                                    <img 
                                        id="avatarPreview"
                                        src={avatarPreview}
                                        alt="Avatar"
                                        className="rounded-circle border border-3 border-primary"
                                        style={{ 
                                            width: '150px', 
                                            height: '150px', 
                                            objectFit: 'cover',
                                            boxShadow: '0 4px 8px rgba(0,0,0,0.1)',
                                            transition: 'transform 0.3s ease'
                                        }}
                                        onMouseOver={(e) => e.target.style.transform = 'scale(1.05)'}
                                        onMouseOut={(e) => e.target.style.transform = 'scale(1)'}
                                    />
                                </div>
                                <div className="mb-3">
                                    <label htmlFor="avatar" className="form-label">
                                        Thay đổi ảnh đại diện
                                    </label>
                                    <input
                                        type="file"
                                        name="avatar"
                                        id="avatar"
                                        className={`form-control ${errors.avatar ? 'is-invalid' : ''}`}
                                        accept="image/jpeg,image/jpg,image/png,image/gif"
                                        onChange={handleAvatarChange}
                                    />
                                    <div className="form-text">
                                        Chỉ chấp nhận JPG, JPEG, PNG, GIF. Dung lượng dưới 5MB
                                    </div>
                                    {errors.avatar && (
                                        <div className="invalid-feedback d-block">{errors.avatar}</div>
                                    )}
                                </div>
                            </div>

                            <hr className="my-4" />

                            {/* Basic Info */}
                            <div className="row mb-3">
                                <div className="col-md-6">
                                    <label htmlFor="name" className="form-label">
                                        <i className="fas fa-user me-1"></i>
                                        Họ tên <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="name"
                                        className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                        value={formData.name}
                                        onChange={handleChange}
                                        required
                                    />
                                    {errors.name && (
                                        <div className="invalid-feedback">{errors.name}</div>
                                    )}
                                </div>
                                <div className="col-md-6">
                                    <label htmlFor="email" className="form-label">
                                        <i className="fas fa-envelope me-1"></i>
                                        Email <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        name="email"
                                        className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                                        value={formData.email}
                                        onChange={handleChange}
                                        required
                                    />
                                    {errors.email && (
                                        <div className="invalid-feedback">{errors.email}</div>
                                    )}
                                </div>
                            </div>

                            {/* Contact Info */}
                            <div className="mb-3">
                                <label htmlFor="phone" className="form-label">
                                    <i className="fas fa-phone me-1"></i>
                                    Số điện thoại
                                </label>
                                <input
                                    type="tel"
                                    name="phone"
                                    className={`form-control ${errors.phone ? 'is-invalid' : ''}`}
                                    value={formData.phone}
                                    onChange={handleChange}
                                    placeholder="Ví dụ: 0901234567"
                                />
                                {errors.phone && (
                                    <div className="invalid-feedback">{errors.phone}</div>
                                )}
                            </div>

                            {/* Address */}
                            <div className="mb-3">
                                <label htmlFor="address" className="form-label">
                                    <i className="fas fa-map-marker-alt me-1"></i>
                                    Địa chỉ
                                </label>
                                <textarea
                                    name="address"
                                    className="form-control"
                                    rows="3"
                                    value={formData.address}
                                    onChange={handleChange}
                                    placeholder="Nhập địa chỉ của bạn"
                                ></textarea>
                            </div>

                            <hr className="my-4" />

                            {/* Action Buttons */}
                            <div className="d-grid gap-2">
                                <button
                                    type="submit"
                                    className="btn btn-primary btn-lg"
                                    disabled={loading}
                                >
                                    {loading ? (
                                        <>
                                            <span className="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            Đang cập nhật...
                                        </>
                                    ) : (
                                        <>
                                            <i className="fas fa-save me-2"></i>
                                            Cập nhật thông tin
                                        </>
                                    )}
                                </button>
                                <Link to="/change-password" className="btn btn-warning">
                                    <i className="fas fa-key me-2"></i>
                                    Đổi mật khẩu
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>

                {/* User Recipes */}
                <div className="card mt-4">
                    <div className="card-header bg-light">
                        <h4 className="mb-0">Công thức của tôi</h4>
                    </div>
                    <div className="card-body">
                        {userRecipes.length > 0 ? (
                            <div className="list-group">
                                {userRecipes.map(recipe => (
                                    <div key={recipe.id} className="list-group-item list-group-item-action">
                                        <div className="d-flex w-100 justify-content-between">
                                            <h5 className="mb-1">
                                                <Link to={`/recipes/${recipe.id}`} className="text-decoration-none">
                                                    {recipe.title}
                                                </Link>
                                            </h5>
                                            <small className="text-muted">
                                                {new Date(recipe.created_at).toLocaleDateString('vi-VN')}
                                            </small>
                                        </div>
                                        <p className="mb-1">
                                            {recipe.description.substring(0, 100)}...
                                        </p>
                                        <div className="mt-2">
                                            <Link 
                                                to={`/recipes/edit/${recipe.id}`}
                                                className="btn btn-sm btn-outline-primary">
                                                <i className="fas fa-edit me-1"></i>
                                                Sửa
                                            </Link>
                                            <button
                                                onClick={() => handleDeleteRecipe(recipe.id)}
                                                className="btn btn-sm btn-outline-danger ms-2">
                                                <i className="fas fa-trash me-1"></i>
                                                Xóa
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div>
                                <p className="text-muted">Bạn chưa chia sẻ công thức nào.</p>
                                <Link to="/recipes/add" className="btn btn-success">
                                    <i className="fas fa-plus me-2"></i>
                                    Thêm công thức mới
                                </Link>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
