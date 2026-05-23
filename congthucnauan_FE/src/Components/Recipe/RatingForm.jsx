import React from 'react';

const RatingForm = ({ newRating, onRatingChange, onSubmit }) => {
    return (
        <form onSubmit={onSubmit}>
            <div className="mb-3">
                <label className="form-label">Đánh giá của bạn</label>
                <div>
                    {[1, 2, 3, 4, 5].map(star => (
                        <div className="form-check form-check-inline" key={star}>
                            <input
                                className="form-check-input"
                                type="radio"
                                name="rating"
                                id={`rating${star}`}
                                value={star}
                                checked={newRating.rating === star}
                                onChange={(e) => onRatingChange({
                                    ...newRating,
                                    rating: parseInt(e.target.value)
                                })}
                            />
                            <label className="form-check-label" htmlFor={`rating${star}`}>
                                {star} <i className="far fa-star text-warning"></i>
                            </label>
                        </div>
                    ))}
                </div>
            </div>
            <div className="mb-3">
                <label htmlFor="comment" className="form-label">Nhận xét của bạn</label>
                <textarea
                    className="form-control"
                    id="comment"
                    rows="3"
                    value={newRating.comment}
                    onChange={(e) => onRatingChange({
                        ...newRating,
                        comment: e.target.value
                    })}
                ></textarea>
            </div>
            <button type="submit" className="btn btn-primary">Gửi đánh giá</button>
        </form>
    );
};

export default RatingForm;
