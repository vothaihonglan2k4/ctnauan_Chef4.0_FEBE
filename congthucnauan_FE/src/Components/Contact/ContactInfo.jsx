import React from 'react';

const ContactInfo = () => {
    const contactDetails = [
        {
            icon: 'fas fa-envelope',
            label: 'Email',
            value: 'volan@vothaihonglan.net',
            link: 'mailto:volan@vothaihonglan.net'
        },
        {
            icon: 'fas fa-phone',
            label: 'Điện thoại',
            value: '+84 0343968449',
            link: 'tel:+840343968449'
        },
        {
            icon: 'fas fa-map-marker-alt',
            label: 'Địa chỉ',
            value: 'Đại Phong, xã Phong Thuỷ, Huyện Lệ Thuỷ, Quảng Bình',
            link: null
        }
    ];

    return (
        <div className="card border-0 shadow-sm rounded-3 mb-4">
            <div className="card-body p-4">
                <h4 className="text-primary fw-bold mb-4">Thông tin liên hệ</h4>
                <ul className="list-unstyled mb-0">
                    {contactDetails.map((detail, index) => (
                        <li key={index} className={`d-flex align-items-center ${index < contactDetails.length - 1 ? 'mb-3' : ''}`}>
                            <div className="icon-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style={{ width: '40px', height: '40px' }}>
                                <i className={detail.icon}></i>
                            </div>
                            <div>
                                <span className="d-block text-muted small">{detail.label}</span>
                                {detail.link ? (
                                    <a href={detail.link} className="fw-medium text-decoration-none">{detail.value}</a>
                                ) : (
                                    <span className="fw-medium">{detail.value}</span>
                                )}
                            </div>
                        </li>
                    ))}
                </ul>
            </div>
        </div>
    );
};

export default ContactInfo;
