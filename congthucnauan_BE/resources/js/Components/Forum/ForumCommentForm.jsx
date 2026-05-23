import React, { useState } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import { Link } from 'react-router-dom';

const ForumCommentForm = ({ postId, onCommentAdded }) => {
    const { user } = useAuth();
    const [content, setContent] = useState('');
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!content.trim()) return;

        setSubmitting(true);
        try {
            const response = await fetch(`/api/v1/forum/posts/${postId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content })
            });
            const data = await response.json();
            if (response.ok) {
                setContent('');
                onCommentAdded(data.comment);
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        } catch (err) {
            console.error('Error adding comment:', err);
        } finally {
            setSubmitting(false);
        }
    };

    if (!user) {
        return (
            <div className="alert alert-info">
                <Link to="/login">Đăng nhập</Link> để bình luận
            </div>
        );
    }

    return (
        <form onSubmit={handleSubmit} className="mb-4">
            <div className="mb-3">
                <textarea
                    className="form-control"
                    rows="3"
                    placeholder="Viết bình luận của bạn..."
                    value={content}
                    onChange={(e) => setContent(e.target.value)}
                    required
                ></textarea>
            </div>
            <button type="submit" className="btn btn-primary" disabled={submitting}>
                {submitting ? 'Đang gửi...' : (
                    <>
                        <i className="fas fa-paper-plane me-2"></i>Gửi bình luận
                    </>
                )}
            </button>
        </form>
    );
};

export default ForumCommentForm;
