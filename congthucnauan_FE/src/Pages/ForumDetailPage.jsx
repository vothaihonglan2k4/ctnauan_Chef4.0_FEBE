import { useState, useEffect, useRef } from 'react';
import { useParams, Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import LoadingSpinner from '../Components/Common/LoadingSpinner';
import ForumCommentList from '../Components/Forum/ForumCommentList';
import ForumCommentForm from '../Components/Forum/ForumCommentForm';

export default function ForumDetailPage() {
    const { id } = useParams();
    const { user } = useAuth();
    const [post, setPost] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const fetchedIdRef = useRef(null);

    useEffect(() => {
        // Skip if already fetched for this id
        if (fetchedIdRef.current === id) {
            return;
        }
        
        const fetchPost = async () => {
            // Mark as fetching for this id
            fetchedIdRef.current = id;
            setLoading(true);
            
            try {
                const response = await fetch(`/api/v1/forum/posts/${id}`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                // Only update if still on same id
                if (fetchedIdRef.current === id) {
                    if (response.ok) {
                        setPost(data.post);
                    } else {
                        setError(data.message || 'Không tìm thấy bài viết');
                    }
                    setLoading(false);
                }
            } catch (err) {
                if (fetchedIdRef.current === id) {
                    setError('Có lỗi xảy ra khi tải bài viết');
                    setLoading(false);
                }
            }
        };
        
        fetchPost();
    }, [id]);

    const handleLike = async () => {
        if (!user) {
            alert('Vui lòng đăng nhập để thích bài viết');
            return;
        }

        try {
            const response = await fetch(`/api/v1/forum/posts/${id}/like`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (response.ok) {
                setPost(prev => ({
                    ...prev,
                    has_liked: data.liked,
                    likes_count: data.liked ? prev.likes_count + 1 : prev.likes_count - 1
                }));
            }
        } catch (err) {
            console.error('Error toggling like:', err);
        }
    };

    const handleCommentAdded = (newComment) => {
        setPost(prev => ({
            ...prev,
            comments: [newComment, ...prev.comments],
            comments_count: prev.comments_count + 1
        }));
    };

    const handleCommentDeleted = (commentId) => {
        setPost(prev => ({
            ...prev,
            comments: prev.comments.filter(c => c.id !== commentId),
            comments_count: prev.comments_count - 1
        }));
    };

    if (loading) return <LoadingSpinner />;
    if (error) return <div className="alert alert-danger m-4">{error}</div>;
    if (!post) return null;

    const avatarPath = post.user.avatar && post.user.avatar !== 'default-avatar.png'
        ? `/uploads/avatars/${post.user.avatar}`
        : '/img/default-avatar.png';

    return (
        <div className="container mt-4">
            {/* Back Button */}
            <div className="mb-3">
                <Link to="/forum" className="btn btn-outline-secondary">
                    <i className="fas fa-arrow-left me-2"></i>Quay lại diễn đàn
                </Link>
            </div>

            <div className="row">
                {/* Main Content */}
                <div className="col-md-9">
                    {/* Post Content Card */}
                    <div className="card mb-4">
                        <div className="card-body">
                            {/* Header */}
                            <div className="d-flex align-items-start mb-3">
                                <img
                                    src={avatarPath}
                                    alt="Avatar"
                                    className="rounded-circle me-3"
                                    style={{ width: '60px', height: '60px', objectFit: 'cover' }}
                                />

                                <div className="flex-grow-1">
                                    <h4 className="mb-1">{post.user.name}</h4>
                                    <div className="text-muted small">
                                        <i className="fas fa-clock me-1"></i>
                                        {new Date(post.created_at).toLocaleString('vi-VN')}
                                        <span className="mx-2">•</span>
                                        <i className="fas fa-eye me-1"></i>
                                        {post.views} lượt xem
                                    </div>
                                </div>

                                {/* Actions (if owner) */}
                                {user && user.id === post.user.id && (
                                    <div className="dropdown">
                                        <button className="btn btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                            <i className="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul className="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button className="dropdown-item text-danger">
                                                    <i className="fas fa-trash me-2"></i>Xóa bài viết
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                )}
                            </div>

                            <hr />

                            {/* Title */}
                            <h2 className="mb-3" style={{ wordBreak: 'break-word', overflowWrap: 'break-word' }}>
                                {post.title}
                            </h2>

                            {/* Tags */}
                            {post.tags && post.tags.length > 0 && (
                                <div className="mb-3">
                                    {post.tags.map(tag => (
                                        <Link
                                            key={tag.id}
                                            to={`/forum/tag/${tag.name}`}
                                            className="badge bg-primary text-decoration-none me-1"
                                        >
                                            {tag.name}
                                        </Link>
                                    ))}
                                </div>
                            )}

                            {/* Content */}
                            <div className="post-content mb-4" style={{ 
                                whiteSpace: 'pre-wrap',
                                wordBreak: 'break-word',
                                overflowWrap: 'break-word'
                            }}>
                                {post.content}
                            </div>

                            {/* Image */}
                            {post.image && (
                                <div className="mb-4">
                                    <img
                                        src={`/uploads/forum/${post.image}`}
                                        alt="Post image"
                                        className="img-fluid rounded"
                                    />
                                </div>
                            )}

                            {/* Video */}
                            {post.video_url && (
                                <div className="ratio ratio-16x9 mb-4">
                                    <iframe
                                        src={post.video_url.replace('watch?v=', 'embed/')}
                                        title="YouTube video"
                                        allowFullScreen
                                        className="rounded"
                                    ></iframe>
                                </div>
                            )}

                            <hr />

                            {/* Like & Stats */}
                            <div className="d-flex justify-content-between align-items-center">
                                <div>
                                    <button
                                        className={`btn ${post.has_liked ? 'btn-danger' : 'btn-outline-danger'}`}
                                        onClick={handleLike}
                                    >
                                        <i className="fas fa-heart me-1"></i>
                                        {post.likes_count}
                                    </button>
                                </div>
                                <div className="text-muted">
                                    <i className="fas fa-comment me-1"></i>
                                    {post.comments_count} bình luận
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Comments Section */}
                    <div className="card">
                        <div className="card-header bg-light">
                            <h5 className="mb-0">
                                <i className="fas fa-comments me-2"></i>
                                Bình luận ({post.comments.length})
                            </h5>
                        </div>
                        <div className="card-body">
                            <ForumCommentForm postId={post.id} onCommentAdded={handleCommentAdded} />
                            <hr />
                            <ForumCommentList
                                comments={post.comments}
                                postId={post.id}
                                onCommentDeleted={handleCommentDeleted}
                            />
                        </div>
                    </div>
                </div>

                {/* Sidebar */}
                <div className="col-md-3">
                    {/* Author Info */}
                    <div className="card mb-3">
                        <div className="card-header bg-light">
                            <h6 className="mb-0"><i className="fas fa-user me-2"></i>Tác giả</h6>
                        </div>
                        <div className="card-body text-center">
                            <img
                                src={avatarPath}
                                alt="Avatar"
                                className="rounded-circle mb-2"
                                style={{ width: '80px', height: '80px', objectFit: 'cover' }}
                            />
                            <h6>{post.user.name}</h6>
                        </div>
                    </div>

                    {/* Share */}
                    <div className="card">
                        <div className="card-header bg-light">
                            <h6 className="mb-0"><i className="fas fa-share-alt me-2"></i>Chia sẻ</h6>
                        </div>
                        <div className="card-body">
                            <div className="d-grid gap-2">
                                <button className="btn btn-sm btn-primary">
                                    <i className="fab fa-facebook me-2"></i>Facebook
                                </button>
                                <button
                                    className="btn btn-sm btn-secondary"
                                    onClick={() => {
                                        navigator.clipboard.writeText(window.location.href);
                                        alert('Đã copy link!');
                                    }}
                                >
                                    <i className="fas fa-link me-2"></i>Copy link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
