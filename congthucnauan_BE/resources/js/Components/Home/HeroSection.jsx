import React from 'react';
import { Link } from 'react-router-dom';

const HeroSection = ({ isLoggedIn }) => {
    return (
        <div className="jumbotron p-5 mb-4 bg-light rounded-3">
            <div className="container">
                <h1 className="display-4">Khám Phá Công Thức Nấu Ăn</h1>
                <p className="lead">
                    Tìm kiếm, chia sẻ, và đánh giá các công thức nấu ăn tuyệt vời từ khắp nơi trên thế giới.
                </p>
                <Link to="/recipes" className="btn btn-primary btn-lg">
                    Xem tất cả công thức
                </Link>
                {!isLoggedIn && (
                    <Link to="/register" className="btn btn-outline-primary btn-lg ms-2">
                        Đăng ký ngay
                    </Link>
                )}
            </div>
        </div>
    );
};

export default HeroSection;
