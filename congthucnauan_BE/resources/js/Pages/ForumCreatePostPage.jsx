import React, { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import LoadingSpinner from '../Components/Common/LoadingSpinner';

export default function ForumCreatePostPage() {
    const navigate = useNavigate();
    const { user } = useAuth();
    const [formData, setFormData] = useState({
        title: '',
        content: '',
        video_url: '',
        recipe_id: '',
        tags: []
    });
    const [image, setImage] = useState(null);
    const [imagePreview, setImagePreview] = useState(null);
    const [allTags, setAllTags] = useState([]);
    const [userRecipes, setUserRecipes] = useState([]);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [errors, setErrors] = useState({});

    useEffect(() => {
        fetchData();
    }, []);

    const fetchData = async () => {
        try {
            // Fetch tags
            const tagsResponse = await fetch('/api/v1/forum/tags');
            const tagsData = await tagsResponse.json();
            setAllTags(tagsData.tags || []);

            // Fetch user recipes
            if (user) {
                const recipesResponse = await fetch('/api/v1/my-recipes', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });
                const recipesData = await recipesResponse.json();
                setUserRecipes(recipesData.recipes || []);
            }
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Kích thước ảnh không được vượt quá 5MB');
                return;
            }
            setImage(file);
            const reader = new FileReader();
            reader.onloadend = () => {
                setImagePreview(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleTagChange = (tagName) => {
        setFormData(prev => {
            const newTags = prev.tags.includes(tagName)
                ? prev.tags.filter(t => t !== tagName)
                : [...prev.tags, tagName];

            if (newTags.length > 3) {
                alert('Chỉ được chọn tối đa 3 tags');
                return prev;
            }
            return { ...prev, tags: newTags };
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors({});
        setSubmitting(true);

        // Validation
        const newErrors = {};
        if (!formData.title.trim()) newErrors.title = 'Vui lòng nhập tiêu đề';
        else if (formData.title.length < 10) newErrors.title = 'Tiêu đề phải có ít nhất 10 ký tự';

        if (!formData.content.trim()) newErrors.content = 'Vui lòng nhập nội dung';
        else if (formData.content.length < 50) newErrors.content = 'Nội dung phải có ít nhất 50 ký tự';

        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
            setSubmitting(false);
            return;
        }

        try {
            const submitData = new FormData();
            submitData.append('title', formData.title);
            submitData.append('content', formData.content);
            if (formData.video_url) submitData.append('video_url', formData.video_url);
            if (formData.recipe_id) submitData.append('recipe_id', formData.recipe_id);
            if (image) submitData.append('image', image);

            formData.tags.forEach(tag => {
                submitData.append('tags[]', tag);
            });

            const response = await fetch('/api/v1/forum/posts', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                },
                body: submitData
            });

            const data = await response.json();

            if (response.ok) {
                alert('Đăng bài thành công!');
                navigate(`/forum/${data.post.id}`);
            } else {
                setErrors({ submit: data.message || 'Có lỗi xảy ra' });
            }
        } catch (error) {
            console.error('Error submitting post:', error);
            setErrors({ submit: 'Có lỗi xảy ra khi gửi dữ liệu' });
        } finally {
            setSubmitting(false);
        }
    };

    if (loading) return <LoadingSpinner />;

    return (
        <div className="container mt-4">
            <div className="row justify-content-center">
                <div className="col-md-10">
                    {/* Header */}
                    <div className="d-flex justify-content-between align-items-center mb-4">
                        <h2><i className="fas fa-plus-circle me-2"></i>Tạo bài viết mới</h2>
                        <Link to="/forum" className="btn btn-outline-secondary">
                            <i className="fas fa-arrow-left me-2"></i>Hủy
                        </Link>
                    </div>

                    {/* Form Card */}
                    <div className="card">
                        <div className="card-body p-4">
                            {errors.submit && (
                                <div className="alert alert-danger mb-4">{errors.submit}</div>
                            )}

                            <form onSubmit={handleSubmit}>
                                {/* Title */}
                                <div className="mb-4">
                                    <label htmlFor="title" className="form-label">
                                        <i className="fas fa-heading me-2"></i>Tiêu đề <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="title"
                                        className={`form-control form-control-lg ${errors.title ? 'is-invalid' : ''}`}
                                        value={formData.title}
                                        onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                                        placeholder="Nhập tiêu đề hấp dẫn cho bài viết..."
                                        required
                                    />
                                    {errors.title && <div className="invalid-feedback">{errors.title}</div>}
                                </div>

                                {/* Content */}
                                <div className="mb-4">
                                    <label htmlFor="content" className="form-label">
                                        <i className="fas fa-align-left me-2"></i>Nội dung <span className="text-danger">*</span>
                                    </label>
                                    <textarea
                                        id="content"
                                        className={`form-control ${errors.content ? 'is-invalid' : ''}`}
                                        rows="12"
                                        value={formData.content}
                                        onChange={(e) => setFormData({ ...formData, content: e.target.value })}
                                        placeholder="Chia sẻ kinh nghiệm, bí quyết nấu ăn của bạn..."
                                        required
                                    ></textarea>
                                    {errors.content && <div className="invalid-feedback">{errors.content}</div>}
                                    <div className="form-text">
                                        <i className="fas fa-lightbulb me-1"></i>
                                        Mẹo: Chia sẻ chi tiết, cụ thể sẽ giúp người đọc dễ hiểu hơn
                                    </div>
                                </div>

                                <hr className="my-4" />

                                {/* Image Upload */}
                                <div className="mb-4">
                                    <label htmlFor="image" className="form-label">
                                        <i className="fas fa-image me-2"></i>Ảnh đính kèm
                                    </label>
                                    <input
                                        type="file"
                                        id="image"
                                        className="form-control"
                                        accept="image/*"
                                        onChange={handleImageChange}
                                    />
                                    <div className="form-text">Chấp nhận JPG, PNG, GIF. Tối đa 5MB</div>

                                    {imagePreview && (
                                        <div className="mt-3">
                                            <img
                                                src={imagePreview}
                                                alt="Preview"
                                                className="img-fluid rounded"
                                                style={{ maxHeight: '300px' }}
                                            />
                                        </div>
                                    )}
                                </div>

                                {/* Video URL */}
                                <div className="mb-4">
                                    <label htmlFor="video_url" className="form-label">
                                        <i className="fab fa-youtube me-2 text-danger"></i>Link video YouTube (tùy chọn)
                                    </label>
                                    <input
                                        type="url"
                                        id="video_url"
                                        className="form-control"
                                        value={formData.video_url}
                                        onChange={(e) => setFormData({ ...formData, video_url: e.target.value })}
                                        placeholder="https://www.youtube.com/watch?v=..."
                                    />
                                    <div className="form-text">Video hướng dẫn sẽ giúp bài viết sinh động hơn</div>
                                </div>

                                {/* Recipe Link */}
                                {userRecipes.length > 0 && (
                                    <div className="mb-4">
                                        <label htmlFor="recipe_id" className="form-label">
                                            <i className="fas fa-utensils me-2"></i>Liên kết công thức (tùy chọn)
                                        </label>
                                        <select
                                            id="recipe_id"
                                            className="form-select"
                                            value={formData.recipe_id}
                                            onChange={(e) => setFormData({ ...formData, recipe_id: e.target.value })}
                                        >
                                            <option value="">-- Chọn công thức --</option>
                                            {userRecipes.map(recipe => (
                                                <option key={recipe.id} value={recipe.id}>
                                                    {recipe.title}
                                                </option>
                                            ))}
                                        </select>
                                        <div className="form-text">Liên kết đến công thức bạn đã đăng trước đó</div>
                                    </div>
                                )}

                                {/* Tags */}
                                {allTags.length > 0 && (
                                    <div className="mb-4">
                                        <label className="form-label">
                                            <i className="fas fa-tags me-2"></i>Tags (chọn từ 1-3 tags)
                                        </label>
                                        <div className="d-flex flex-wrap gap-2">
                                            {allTags.map(tag => (
                                                <div key={tag.id} className="form-check form-check-inline">
                                                    <input
                                                        className="form-check-input"
                                                        type="checkbox"
                                                        id={`tag_${tag.id}`}
                                                        checked={formData.tags.includes(tag.name)}
                                                        onChange={() => handleTagChange(tag.name)}
                                                    />
                                                    <label className="form-check-label" htmlFor={`tag_${tag.id}`}>
                                                        {tag.name}
                                                    </label>
                                                </div>
                                            ))}
                                        </div>
                                        <div className="form-text">Tags giúp người khác dễ tìm thấy bài viết của bạn</div>
                                    </div>
                                )}

                                <hr className="my-4" />

                                {/* Submit Buttons */}
                                <div className="d-flex justify-content-between">
                                    <button
                                        type="button"
                                        className="btn btn-outline-secondary"
                                        onClick={() => {
                                            setFormData({
                                                title: '',
                                                content: '',
                                                video_url: '',
                                                recipe_id: '',
                                                tags: []
                                            });
                                            setImage(null);
                                            setImagePreview(null);
                                        }}
                                    >
                                        <i className="fas fa-redo me-2"></i>Làm lại
                                    </button>
                                    <button type="submit" className="btn btn-primary btn-lg" disabled={submitting}>
                                        {submitting ? 'Đang đăng...' : (
                                            <>
                                                <i className="fas fa-paper-plane me-2"></i>Đăng bài viết
                                            </>
                                        )}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {/* Tips Card */}
                    <div className="card mt-4 bg-light">
                        <div className="card-body">
                            <h6><i className="fas fa-lightbulb text-warning me-2"></i>Mẹo viết bài hay:</h6>
                            <ul className="mb-0 small">
                                <li>Tiêu đề ngắn gọn, thu hút</li>
                                <li>Nội dung chi tiết, dễ hiểu</li>
                                <li>Thêm ảnh/video minh họa</li>
                                <li>Chia sẻ kinh nghiệm thực tế</li>
                                <li>Sử dụng tags phù hợp</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
