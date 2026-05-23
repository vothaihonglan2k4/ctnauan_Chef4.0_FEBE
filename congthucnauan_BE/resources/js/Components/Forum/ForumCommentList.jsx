import React from 'react';
import { useAuth } from '../../contexts/AuthContext';

const ForumCommentList = ({ comments, postId, onCommentDeleted }) => {
    const { user } = useAuth();

    const handleDelete = async (commentId) => {
        if (!confirm('Bạn có chắc muốn xóa bình luận này?')) return;

        try {
            const response = await fetch(`/api/v1/forum/posts/${postId}/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            if (response.ok) {
                onCommentDeleted(commentId);
            }
        } catch (err) {
            console.error('Error deleting comment:', err);
        }
    };

    if (!comments || comments.length === 0) {
        return <p className="text-muted text-center">Chưa có bình luận nào. Hãy là người đầu tiên!</p>;
    }

    return (
        <div id="commentsList">
            {comments.map(comment => {
                const avatarPath = comment.user.avatar && comment.user.avatar !== 'default-avatar.png'
                    ? `/uploads/avatars/${comment.user.avatar}`
                    : '/img/default-avatar.png';

                return (
                    <div key={comment.id} className="comment-item mb-3 p-3 bg-light rounded">
                        <div className="d-flex">
                            <img
                                src={avatarPath}
                                alt="Avatar"
                                className="rounded-circle me-3"
                                style={{ width: '40px', height: '40px', objectFit: 'cover' }}
                            />

                            <div className="flex-grow-1">
                                <div className="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{comment.user.fullname || comment.user.username}</strong>
                                        <small className="text-muted ms-2">
                                            {new Date(comment.created_at).toLocaleString('vi-VN')}
                                        </small>
                                    </div>

                                    {user && (user.id === comment.user.id || user.role === 'admin') && (
                                        <button
                                            className="btn btn-sm btn-link text-danger p-0"
                                            onClick={() => handleDelete(comment.id)}
                                        >
                                            <i className="fas fa-trash"></i>
                                        </button>
                                    )}
                                </div>
                                <p className="mb-0 mt-1" style={{ whiteSpace: 'pre-wrap' }}>{comment.content}</p>
                            </div>
                        </div>
                    </div>
                );
            })}
        </div>
    );
};

export default ForumCommentList;
