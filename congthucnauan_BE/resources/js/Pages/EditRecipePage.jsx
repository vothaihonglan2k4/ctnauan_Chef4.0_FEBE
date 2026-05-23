import { useState, useEffect } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function EditRecipePage() {
    const { id } = useParams();
    const [formData, setFormData] = useState({
        title: '',
        category_id: '',
        description: '',
        ingredients: '',
        instructions: '',
        video_url: ''
    });
    const [currentImage, setCurrentImage] = useState(null);
    const [imageFile, setImageFile] = useState(null);
    const [imagePreview, setImagePreview] = useState(null);
    const [categories, setCategories] = useState([]);
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [pageLoading, setPageLoading] = useState(true);
    const [submitMessage, setSubmitMessage] = useState('');
    
    const { token, isAuthenticated, user, isAdmin } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login', { state: { from: { pathname: `/recipes/edit/${id}` } } });
            return;
        }

        fetchRecipe();
        fetchCategories();
    }, [isAuthenticated, navigate, id]);

    const fetchRecipe = async () => {
        try {
            const response = await fetch(`/api/v1/recipes/${id}`);
            const data = await response.json();

            if (data.recipe) {
                const recipe = data.recipe;
                
                // Check permission: only owner or admin can edit
                if (recipe.user_id !== user?.id && !isAdmin) {
                    alert('Bạn không có quyền chỉnh sửa công thức này');
                    navigate(`/recipes/${id}`);
                    return;
                }

                setFormData({
                    title: recipe.title || '',
                    category_id: recipe.category_id || '',
                    description: recipe.description || '',
                    ingredients: recipe.ingredients || '',
                    instructions: recipe.instructions || '',
                    video_url: recipe.video_url || ''
                });
                setCurrentImage(recipe.image);
            } else {
                alert('Không tìm thấy công thức');
                navigate('/recipes');
            }
        } catch (error) {
            console.error('Error fetching recipe:', error);
            alert('Có lỗi xảy ra khi tải công thức');
        } finally {
            setPageLoading(false);
        }
    };

    const fetchCategories = async () => {
        try {
            const response = await fetch('/api/v1/categories');
            const data = await response.json();
            setCategories(data.categories || []);
        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    };

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
        if (errors[e.target.name]) {
            setErrors({
                ...errors,
                [e.target.name]: ''
            });
        }
    };

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                setErrors({
                    ...errors,
                    image: 'Chỉ chấp nhận file JPG, JPEG, PNG'
                });
                return;
            }

            const maxSize = 2 * 1024 * 1024;
            if (file.size > maxSize) {
                setErrors({
                    ...errors,
                    image: 'Dung lượng file phải dưới 2MB'
                });
                return;
            }

            setImageFile(file);
            setErrors({ ...errors, image: '' });

            const reader = new FileReader();
            reader.onloadend = () => {
                setImagePreview(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    const validateForm = () => {
        const newErrors = {};

        if (!formData.title.trim()) {
            newErrors.title = 'Vui lòng nhập tên công thức';
        }

        if (!formData.category_id) {
            newErrors.category_id = 'Vui lòng chọn danh mục';
        }

        if (!formData.description.trim()) {
            newErrors.description = 'Vui lòng nhập mô tả';
        }

        if (!formData.ingredients.trim()) {
            newErrors.ingredients = 'Vui lòng nhập nguyên liệu';
        }

        if (!formData.instructions.trim()) {
            newErrors.instructions = 'Vui lòng nhập cách làm';
        }

        if (formData.video_url && formData.video_url.trim()) {
            const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+/;
            if (!youtubeRegex.test(formData.video_url)) {
                newErrors.video_url = 'Link YouTube không hợp lệ';
            }
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
        setSubmitMessage('');

        const formDataToSend = new FormData();
        formDataToSend.append('_method', 'PUT');
        formDataToSend.append('title', formData.title);
        formDataToSend.append('category_id', formData.category_id);
        formDataToSend.append('description', formData.description);
        formDataToSend.append('ingredients', formData.ingredients);
        formDataToSend.append('instructions', formData.instructions);
        
        if (formData.video_url) {
            formDataToSend.append('video_url', formData.video_url);
        }
        
        if (imageFile) {
            formDataToSend.append('image', imageFile);
        }

        try {
            const response = await fetch(`/api/v1/recipes/${id}`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                },
                body: formDataToSend
            });

            const data = await response.json();

            if (response.ok) {
                setSubmitMessage('Cập nhật công thức thành công!');
                setTimeout(() => {
                    navigate(`/recipes/${id}`);
                }, 1500);
            } else {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    setErrors({ general: data.message || 'Có lỗi xảy ra khi cập nhật công thức' });
                }
            }
        } catch (error) {
            console.error('Error updating recipe:', error);
            setErrors({ general: 'Có lỗi xảy ra khi cập nhật công thức' });
        } finally {
            setLoading(false);
        }
    };

    if (pageLoading) {
        return (
            <div className="container mt-4">
                <div className="text-center py-5">
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">Loading...</span>
                    </div>
                    <p className="mt-2">Đang tải công thức...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="container mt-4">
            <div className="row">
                <div className="col-md-10 mx-auto">
                    <div className="card">
                        <div className="card-header bg-primary text-white">
                            <h3 className="mb-0">
                                <i className="fas fa-edit me-2"></i>
                                Chỉnh sửa công thức
                            </h3>
                        </div>
                        <div className="card-body">
                            {errors.general && (
                                <div className="alert alert-danger" role="alert">
                                    <i className="fas fa-exclamation-circle me-2"></i>
                                    {errors.general}
                                </div>
                            )}

                            {submitMessage && (
                                <div className="alert alert-success" role="alert">
                                    <i className="fas fa-check-circle me-2"></i>
                                    {submitMessage}
                                </div>
                            )}

                            <form onSubmit={handleSubmit}>
                                {/* Title */}
                                <div className="mb-3">
                                    <label htmlFor="title" className="form-label">
                                        Tên công thức <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.title ? 'is-invalid' : ''}`}
                                        id="title"
                                        name="title"
                                        value={formData.title}
                                        onChange={handleChange}
                                    />
                                    {errors.title && (
                                        <div className="invalid-feedback">{errors.title}</div>
                                    )}
                                </div>

                                {/* Category */}
                                <div className="mb-3">
                                    <label htmlFor="category_id" className="form-label">
                                        Danh mục <span className="text-danger">*</span>
                                    </label>
                                    <select
                                        className={`form-select ${errors.category_id ? 'is-invalid' : ''}`}
                                        id="category_id"
                                        name="category_id"
                                        value={formData.category_id}
                                        onChange={handleChange}
                                    >
                                        <option value="">-- Chọn danh mục --</option>
                                        {categories.map(category => (
                                            <option key={category.id} value={category.id}>
                                                {category.name}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.category_id && (
                                        <div className="invalid-feedback">{errors.category_id}</div>
                                    )}
                                </div>

                                {/* Description */}
                                <div className="mb-3">
                                    <label htmlFor="description" className="form-label">
                                        Mô tả <span className="text-danger">*</span>
                                    </label>
                                    <textarea
                                        className={`form-control ${errors.description ? 'is-invalid' : ''}`}
                                        id="description"
                                        name="description"
                                        rows="3"
                                        value={formData.description}
                                        onChange={handleChange}
                                    ></textarea>
                                    {errors.description && (
                                        <div className="invalid-feedback">{errors.description}</div>
                                    )}
                                </div>

                                {/* Ingredients */}
                                <div className="mb-3">
                                    <label htmlFor="ingredients" className="form-label">
                                        Nguyên liệu <span className="text-danger">*</span>
                                    </label>
                                    <textarea
                                        className={`form-control ${errors.ingredients ? 'is-invalid' : ''}`}
                                        id="ingredients"
                                        name="ingredients"
                                        rows="5"
                                        value={formData.ingredients}
                                        onChange={handleChange}
                                    ></textarea>
                                    {errors.ingredients && (
                                        <div className="invalid-feedback">{errors.ingredients}</div>
                                    )}
                                </div>

                                {/* Instructions */}
                                <div className="mb-3">
                                    <label htmlFor="instructions" className="form-label">
                                        Cách làm <span className="text-danger">*</span>
                                    </label>
                                    <textarea
                                        className={`form-control ${errors.instructions ? 'is-invalid' : ''}`}
                                        id="instructions"
                                        name="instructions"
                                        rows="8"
                                        value={formData.instructions}
                                        onChange={handleChange}
                                    ></textarea>
                                    {errors.instructions && (
                                        <div className="invalid-feedback">{errors.instructions}</div>
                                    )}
                                </div>

                                {/* Image Upload */}
                                <div className="row mb-3">
                                    <div className="col-md-8">
                                        <label htmlFor="image" className="form-label">
                                            Thay đổi hình ảnh
                                        </label>
                                        <input
                                            className={`form-control ${errors.image ? 'is-invalid' : ''}`}
                                            type="file"
                                            id="image"
                                            name="image"
                                            accept="image/jpeg,image/jpg,image/png"
                                            onChange={handleImageChange}
                                        />
                                        <div className="form-text">
                                            Để trống nếu không muốn thay đổi hình ảnh. Định dạng: JPG, JPEG, PNG. Dung lượng dưới 2MB.
                                        </div>
                                        {errors.image && (
                                            <div className="invalid-feedback d-block">{errors.image}</div>
                                        )}
                                        {imagePreview && (
                                            <div className="mt-2">
                                                <p className="text-muted mb-1">Hình ảnh mới:</p>
                                                <img 
                                                    src={imagePreview} 
                                                    alt="Preview" 
                                                    className="img-thumbnail"
                                                    style={{ maxWidth: '200px', maxHeight: '150px' }}
                                                />
                                            </div>
                                        )}
                                    </div>
                                    <div className="col-md-4">
                                        <label className="form-label">Hình ảnh hiện tại</label>
                                        {currentImage ? (
                                            <img 
                                                src={`/uploads/${currentImage}`}
                                                className="img-thumbnail w-100"
                                                alt="Current"
                                                onError={(e) => e.target.src = 'https://via.placeholder.com/200x150?text=No+Image'}
                                            />
                                        ) : (
                                            <div className="text-muted">Chưa có hình ảnh</div>
                                        )}
                                    </div>
                                </div>

                                {/* Video URL */}
                                <div className="mb-3">
                                    <label htmlFor="video_url" className="form-label">
                                        <i className="fab fa-youtube text-danger me-1"></i>
                                        Link Video YouTube (Không bắt buộc)
                                    </label>
                                    <input
                                        type="url"
                                        className={`form-control ${errors.video_url ? 'is-invalid' : ''}`}
                                        id="video_url"
                                        name="video_url"
                                        value={formData.video_url}
                                        onChange={handleChange}
                                        placeholder="https://www.youtube.com/watch?v=..."
                                    />
                                    <div className="form-text">
                                        <i className="fas fa-info-circle me-1"></i>
                                        Để trống nếu không muốn thêm/thay đổi video.
                                    </div>
                                    {errors.video_url && (
                                        <div className="invalid-feedback">{errors.video_url}</div>
                                    )}
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
                                                Đang cập nhật...
                                            </>
                                        ) : (
                                            <>
                                                <i className="fas fa-save me-2"></i>
                                                Cập nhật công thức
                                            </>
                                        )}
                                    </button>
                                    <Link to={`/recipes/${id}`} className="btn btn-light">
                                        <i className="fas fa-times me-2"></i>
                                        Hủy bỏ
                                    </Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
