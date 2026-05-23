import React from 'react';
import { Link } from 'react-router-dom';

const FeaturesSection = ({ isLoggedIn }) => {
    const features = [
        {
            icon: 'fa-search',
            title: 'Dễ Dàng Tìm Kiếm',
            description: 'Tìm công thức theo từ khóa, nguyên liệu hoặc danh mục một cách nhanh chóng.'
        },
        {
            icon: 'fa-users',
            title: 'Cộng Đồng Đánh Giá',
            description: 'Xem đánh giá và nhận xét từ người dùng khác để chọn công thức phù hợp.'
        },
        {
            icon: 'fa-share-alt',
            title: 'Chia Sẻ Công Thức',
            description: 'Chia sẻ công thức nấu ăn yêu thích của bạn với cộng đồng.'
        }
    ];

    return (
        <>
            {/* Features Heading */}
            <div className="row mt-5">
                <div className="col-12 text-center">
                    <h3>Tại sao chọn Công Thức Nấu Ăn?</h3>
                </div>
            </div>

            {/* Features Grid */}
            <div className="row mt-3">
                {features.map((feature, index) => (
                    <div key={index} className="col-md-4">
                        <div className="text-center">
                            <i className={`fas ${feature.icon} fa-3x mb-3 text-primary`}></i>
                            <h4>{feature.title}</h4>
                            <p>{feature.description}</p>
                        </div>
                    </div>
                ))}
            </div>

            {/* CTA Section */}
            <div className="row mt-5 mb-4">
                <div className="col-12 text-center">
                    <p className="lead mb-4">Hãy tham gia cộng đồng của chúng tôi ngay hôm nay!</p>
                    {!isLoggedIn && (
                        <Link to="/register" className="btn btn-primary btn-lg">
                            Đăng ký ngay
                        </Link>
                    )}
                </div>
            </div>
        </>
    );
};

export default FeaturesSection;
