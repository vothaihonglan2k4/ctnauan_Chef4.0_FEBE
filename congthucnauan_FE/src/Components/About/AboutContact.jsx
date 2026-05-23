import React from 'react';
import { Link } from 'react-router-dom';

const AboutContact = () => {
    return (
        <div className="card shadow-sm">
            <div className="card-body">
                <h4 className="mb-3">Liên hệ</h4>
                <p>Bạn có câu hỏi hoặc đề xuất? Hãy liên hệ với chúng tôi:</p>
                <ul className="list-unstyled">
                    <li className="mb-2">
                        <i className="fas fa-envelope me-2 text-primary"></i>
                        Email: <a href="mailto:volan@vothaihonglan.net">volan@vothaihonglan.net</a>
                    </li>
                    <li className="mb-2">
                        <i className="fas fa-phone me-2 text-primary"></i>
                        Điện thoại: <a href="tel:+840343968449">+84 0343968449</a>
                    </li>
                    <li className="mb-2">
                        <i className="fas fa-map-marker-alt me-2 text-primary"></i>
                        Địa chỉ: Đại Phong, xã Phong Thuỷ, Huyện Lệ Thuỷ, Quảng Bình
                    </li>
                </ul>

                <p className="mt-3">
                    Hoặc sử dụng <Link to="/contact" className="text-primary">biểu mẫu liên hệ</Link> của chúng tôi.
                </p>
            </div>
        </div>
    );
};

export default AboutContact;
