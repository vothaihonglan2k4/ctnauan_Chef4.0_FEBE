import { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import LoadingSpinner from '../Components/Common/LoadingSpinner';
import StarRating from '../Components/Common/StarRating';
import YouTubePlayer from '../Components/Recipe/YouTubePlayer';
import SocialShareButtons from '../Components/Common/SocialShareButtons';
import RatingForm from '../Components/Recipe/RatingForm';
import RatingList from '../Components/Recipe/RatingList';
import CategorySidebar from '../Components/Recipe/CategorySidebar';
import RelatedRecipes from '../Components/Recipe/RelatedRecipes';
import { copyToClipboard, formatDate } from '../utils/helpers';

export default function RecipeDetailPage() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [recipe, setRecipe] = useState(null);
    const [ratings, setRatings] = useState([]);
    const [avgRating, setAvgRating] = useState(0);
    const [loading, setLoading] = useState(true);
    const [categories, setCategories] = useState([]);
    const [relatedRecipes, setRelatedRecipes] = useState([]);
    const [userRated, setUserRated] = useState(false);
    const [newRating, setNewRating] = useState({
        rating: 3,
        comment: ''
    });

    const user = JSON.parse(localStorage.getItem('user') || 'null');

    useEffect(() => {
        fetchRecipeDetail();
        fetchCategories();
    }, [id]);

    const fetchRecipeDetail = async () => {
        try {
            const response = await fetch(`/api/v1/recipes/${id}`);
            const data = await response.json();

            if (data.recipe) {
                setRecipe(data.recipe);
                fetchRatings();
                if (data.recipe.category_id) {
                    fetchRelatedRecipes(data.recipe.category_id);
                }
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchRatings = async () => {
        try {
            const response = await fetch(`/api/v1/recipes/${id}/ratings`);
            const data = await response.json();

            const ratings = data.ratings || [];
            setRatings(ratings);

            if (ratings.length > 0) {
                const avg = ratings.reduce((sum, r) => sum + r.rating, 0) / ratings.length;
                setAvgRating(avg);
            }

            if (user && ratings.length > 0) {
                const hasRated = ratings.some(r => r.user_id === user.id);
                setUserRated(hasRated);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    };

    const fetchCategories = async () => {
        try {
            const response = await fetch('/api/v1/categories');
            const data = await response.json();
            setCategories(data.categories || []);
        } catch (error) {
            console.error('Error:', error);
        }
    };

    const fetchRelatedRecipes = async (categoryId) => {
        try {
            const response = await fetch(`/api/v1/recipes?category_id=${categoryId}&limit=3`);
            const data = await response.json();
            const related = (data.recipes || []).filter(r => r.id !== parseInt(id));
            setRelatedRecipes(related.slice(0, 3));
        } catch (error) {
            console.error('Error:', error);
        }
    };

    const handleRatingSubmit = async (e) => {
        e.preventDefault();

        if (!user) {
            alert('Vui lòng đăng nhập để đánh giá');
            navigate('/login');
            return;
        }

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`/api/v1/recipes/${id}/ratings`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    rating: newRating.rating,
                    comment: newRating.comment
                })
            });

            const data = await response.json();

            if (response.ok) {
                alert('Đánh giá thành công!');
                fetchRatings();
                setNewRating({ rating: 3, comment: '' });
                setUserRated(true);
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi gửi đánh giá');
        }
    };

    const handleDeleteRating = async (ratingId) => {
        if (!confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) return;

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`/api/v1/recipes/${id}/ratings/${ratingId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await response.json();

            if (response.ok) {
                alert('Xóa đánh giá thành công!');
                fetchRatings();
                setUserRated(false);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    };

    const handleCopyLink = (url) => {
        copyToClipboard(
            url,
            () => alert('Đã copy link video!'),
            (text) => alert('Không thể copy link. Vui lòng copy thủ công: ' + text)
        );
    };

    if (loading) {
        return <LoadingSpinner size="large" message="Đang tải công thức..." />;
    }

    if (!recipe) {
        return (
            <div className="container mt-5">
                <div className="alert alert-danger">Không tìm thấy công thức này!</div>
            </div>
        );
    }

    return (
        <div className="container mt-4">
            <div className="row">
                {/* Main Content */}
                <div className="col-md-8">
                    {/* Recipe Card */}
                    <div className="card mb-4">
                        <img
                            src={`/uploads/${recipe.image}`}
                            className="card-img-top recipe-full-img"
                            alt={recipe.title}
                            onError={(e) => e.target.src = 'https://via.placeholder.com/800x400?text=No+Image'}
                        />
                        <div className="card-body">
                            <h1 className="card-title">{recipe.title}</h1>

                            <div className="mb-3">
                                <StarRating
                                    rating={avgRating}
                                    showCount={true}
                                    count={ratings.length}
                                />
                            </div>

                            <p className="text-muted">
                                <i className="fas fa-user"></i> Đăng bởi: {recipe.user?.name || 'Ẩn danh'} |
                                <i className="fas fa-calendar ms-2"></i> Ngày đăng: {formatDate(recipe.created_at)} |
                                <i className="fas fa-folder ms-2"></i> Danh mục: {recipe.category?.name || 'Chưa phân loại'}
                            </p>

                            <div className="mb-4">
                                <h5>Mô tả</h5>
                                <p>{recipe.description}</p>
                            </div>

                            {recipe.video_url && (
                                <div className="mb-4">
                                    <h5><i className="fab fa-youtube text-danger me-2"></i>Video hướng dẫn</h5>
                                    <YouTubePlayer
                                        videoUrl={recipe.video_url}
                                        title={recipe.title}
                                    />
                                    <SocialShareButtons
                                        url={recipe.video_url}
                                        title={`Xem video nấu ${recipe.title}`}
                                        onCopyLink={handleCopyLink}
                                    />
                                </div>
                            )}

                            <div className="row mb-4">
                                <div className="col-md-6">
                                    <div className="card">
                                        <div className="card-header bg-light">
                                            <h5 className="mb-0"><i className="fas fa-list me-2"></i>Nguyên liệu</h5>
                                        </div>
                                        <div className="card-body">
                                            <div style={{ whiteSpace: 'pre-line' }}>{recipe.ingredients}</div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-6">
                                    <div className="card">
                                        <div className="card-header bg-light">
                                            <h5 className="mb-0"><i className="fas fa-utensils me-2"></i>Cách làm</h5>
                                        </div>
                                        <div className="card-body">
                                            <div style={{ whiteSpace: 'pre-line' }}>{recipe.instructions}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {user && (user.id === recipe.user_id || user.role === 'admin') && (
                                <div className="mt-3">
                                    <Link to={`/recipes/edit/${recipe.id}`} className="btn btn-outline-primary">
                                        <i className="fas fa-edit"></i> Sửa
                                    </Link>
                                    <button className="btn btn-outline-danger ms-2">
                                        <i className="fas fa-trash"></i> Xóa
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Ratings Section */}
                    <div className="card mb-4">
                        <div className="card-header bg-light">
                            <h4 className="mb-0">Đánh giá và nhận xét</h4>
                        </div>
                        <div className="card-body">
                            {user && !userRated ? (
                                <>
                                    <RatingForm
                                        newRating={newRating}
                                        onRatingChange={setNewRating}
                                        onSubmit={handleRatingSubmit}
                                    />
                                    <hr />
                                </>
                            ) : !user ? (
                                <div className="alert alert-info">
                                    <Link to="/login">Đăng nhập</Link> để đánh giá công thức này.
                                </div>
                            ) : null}

                            <RatingList
                                ratings={ratings}
                                user={user}
                                onDeleteRating={handleDeleteRating}
                            />
                        </div>
                    </div>
                </div>

                {/* Sidebar */}
                <div className="col-md-4">
                    <CategorySidebar categories={categories} />
                    <RelatedRecipes recipes={relatedRecipes} />
                </div>
            </div>
        </div>
    );
}