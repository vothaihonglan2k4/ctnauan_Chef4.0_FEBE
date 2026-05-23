import React from 'react';

const StarRating = ({ rating, showCount = false, count = 0 }) => {
    const stars = [];
    for (let i = 1; i <= 5; i++) {
        stars.push(
            <i
                key={i}
                className={i <= Math.round(rating) ? "fas fa-star text-warning" : "far fa-star text-warning"}
            ></i>
        );
    }

    return (
        <div className="d-inline-flex align-items-center">
            {stars}
            {showCount && (
                <span className="ms-1">
                    ({rating.toFixed(1)}/5 - {count} đánh giá)
                </span>
            )}
        </div>
    );
};

export default StarRating;
