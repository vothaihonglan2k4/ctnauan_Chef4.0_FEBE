import React from 'react';
import { Link } from 'react-router-dom';

const ForumPostCard = ({ post }) => {
    const avatarPath = post.user?.avatar && post.user.avatar !== 'default-avatar.png'
        ? `/uploads/avatars/${post.user.avatar}`
        : '/img/default-avatar.png';

    return (
        <div className="card mb-3 forum-post-card">
            <div className="card-body">
                <div className="d-flex">
                    {/* Avatar */}
                    <div className="me-3">
                        <img
                            src={avatarPath}
                            alt="Avatar"
                            className="rounded-circle"
                            style={{ width: '50px', height: '50px', objectFit: 'cover' }}
                        />
                    </div>

                    {/* Content */}
                    <div className="flex-grow-1">
                        {/* Title */}
                        <h5 className="mb-2" style={{ 
                            wordBreak: 'break-word', 
                            overflowWrap: 'break-word' 
                        }}>
                            {post.is_pinned && (
                                <span className="badge bg-danger me-2">📌 Ghim</span>
                            )}
                            <Link
                                to={`/forum/${post.id}`}
                                className="text-decoration-none text-dark"
                            >
                                {post.title}
                            </Link>
                        </h5>

                        {/* Author & Time */}
                        <div className="text-muted small mb-2">
                            <i className="fas fa-user me-1"></i>
                            <strong>{post.user?.fullname || post.user?.username}</strong>
                            <span className="mx-2">•</span>
                            <i className="fas fa-clock me-1"></i>
                            {new Date(post.created_at).toLocaleString('vi-VN')}
                        </div>

                        {/* Excerpt */}
                        <p className="mb-2" style={{ 
                            wordBreak: 'break-word', 
                            overflowWrap: 'break-word',
                            maxWidth: '100%'
                        }}>
                            {post.content.replace(/<[^>]*>/g, '').substring(0, 200)}...
                        </p>

                        {/* Stats */}
                        <div className="d-flex align-items-center">
                            <span className="badge bg-light text-dark me-2">
                                <i className="fas fa-heart text-danger"></i> {post.likes_count || 0}
                            </span>
                            <span className="badge bg-light text-dark me-2">
                                <i className="fas fa-comment text-primary"></i> {post.comments_count || 0}
                            </span>
                            <span className="badge bg-light text-dark me-2">
                                <i className="fas fa-eye text-secondary"></i> {post.views || 0}
                            </span>

                            {post.recipe_id && (
                                <span className="badge bg-success">
                                    <i className="fas fa-utensils"></i> Có công thức
                                </span>
                            )}
                        </div>
                    </div>

                    {/* Thumbnail */}
                    {post.image && (
                        <div className="ms-3">
                            <img
                                src={`/uploads/forum/${post.image}`}
                                alt="Thumbnail"
                                className="rounded"
                                style={{ width: '120px', height: '80px', objectFit: 'cover' }}
                            />
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ForumPostCard;
