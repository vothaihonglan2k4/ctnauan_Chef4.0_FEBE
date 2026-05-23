import React from 'react';
import StarRating from '../Common/StarRating';

const RatingList = ({ ratings, user, onDeleteRating }) => {
    if (ratings.length === 0) {
        return <p className="text-muted">Chưa có đánh giá nào cho công thức này.</p>;
    }

    return (
        <>
            {ratings.map(rating => (
                <div className="card mb-3" key={rating.id}>
                    <div className="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{rating.user?.name || 'Ẩn danh'}</strong>
                            {' '}
                            <StarRating rating={rating.rating} />
                        </div>
                        <small className="text-muted">
                            {new Date(rating.created_at).toLocaleDateString('vi-VN')}
                        </small>
                    </div>
                    <div className="card-body">
                        <p className="card-text">{rating.comment}</p>
                        {user && (user.id === rating.user_id || user.role === 'admin') && (
                            <button
                                onClick={() => onDeleteRating(rating.id)}
                                className="btn btn-sm btn-outline-danger"
                            >
                                <i className="fas fa-trash"></i> Xóa
                            </button>
                        )}
                    </div>
                </div>
            ))}
        </>
    );
};

export default RatingList;
