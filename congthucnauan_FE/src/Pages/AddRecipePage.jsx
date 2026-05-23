import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function AddRecipePage() {
    const [formData, setFormData] = useState({
        title: '',
        category_id: '',
        description: '',
        ingredients: '',
        instructions: '',
        video_url: ''
    });
    const [imageFile, setImageFile] = useState(null);
    const [imagePreview, setImagePreview] = useState(null);
    const [categories, setCategories] = useState([]);
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [submitMessage, setSubmitMessage] = useState('');
    
    const { token, isAuthenticated } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        // Redirect if not authenticated
        if (!isAuthenticated) {
            navigate('/login', { state: { from: { pathname: '/recipes/add' } } });
            return;
        }

        // Fetch categories
        fetch('/api/v1/categories')
            .then(res => res.json())
            .then(data => {
                setCategories(data.categories || []);
            })
            .catch(err => console.error('Error fetching categories:', err));
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

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                setErrors({
                    ...errors,
                    image: 'Chỉ chấp nhận file JPG, JPEG, PNG'
                });
                return;
            }

            // Validate file size (2MB)
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

            // Create preview
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

        // Validate YouTube URL if provided
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

        // Create FormData for file upload
        const formDataToSend = new FormData();
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
            const response = await fetch('/api/v1/recipes', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                },
                body: formDataToSend
            });

            const data = await response.json();

            if (response.ok) {
                setSubmitMessage('Công thức của bạn đã được gửi và đang chờ duyệt.');
                setTimeout(() => {
                    navigate('/recipes');
                }, 2000);
            } else {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    setErrors({ general: data.message || 'Có lỗi xảy ra khi thêm công thức' });
                }
            }
        } catch (error) {
            console.error('Error adding recipe:', error);
            setErrors({ general: 'Có lỗi xảy ra khi thêm công thức' });
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="container mt-4">
            <div className="row">
                <div className="col-md-10 mx-auto">
                    <div className="card">
                        <div className="card-header bg-primary text-white">
                            <h3 className="mb-0">
                                <i className="fas fa-plus-circle me-2"></i>
                                Thêm công thức mới
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
                                        placeholder="Ví dụ: Phở bò Hà Nội"
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
                                        placeholder="Mô tả ngắn gọn về món ăn..."
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
                                        placeholder="- 200g thịt bò&#10;- 2 quả trứng gà&#10;- 1 muỗng cà phê muối"
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
                                        placeholder="Bước 1: Làm sạch thịt bò&#10;Bước 2: Ướp thịt với các gia vị&#10;..."
                                    ></textarea>
                                    {errors.instructions && (
                                        <div className="invalid-feedback">{errors.instructions}</div>
                                    )}
                                </div>

                                {/* Image Upload */}
                                <div className="mb-3">
                                    <label htmlFor="image" className="form-label">
                                        Hình ảnh
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
                                        Nên sử dụng hình ảnh có tỉ lệ 3:2 và dung lượng dưới 2MB. Định dạng cho phép: JPG, JPEG, PNG.
                                    </div>
                                    {errors.image && (
                                        <div className="invalid-feedback d-block">{errors.image}</div>
                                    )}
                                    {imagePreview && (
                                        <div className="mt-2">
                                            <img 
                                                src={imagePreview} 
                                                alt="Preview" 
                                                className="img-thumbnail"
                                                style={{ maxWidth: '300px', maxHeight: '200px' }}
                                            />
                                        </div>
                                    )}
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
                                        Nhập link video YouTube hướng dẫn nấu món ăn
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
                                                Đang xử lý...
                                            </>
                                        ) : (
                                            <>
                                                <i className="fas fa-plus-circle me-2"></i>
                                                Thêm công thức
                                            </>
                                        )}
                                    </button>
                                    <Link to="/recipes" className="btn btn-light">
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
