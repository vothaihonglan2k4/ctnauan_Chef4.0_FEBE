import React from 'react';

const SocialLinks = () => {
    const socialPlatforms = [
        { name: 'Facebook', icon: 'fab fa-facebook-f', url: '#' },
        { name: 'Instagram', icon: 'fab fa-instagram', url: '#' },
        { name: 'Twitter', icon: 'fab fa-twitter', url: '#' },
        { name: 'YouTube', icon: 'fab fa-youtube', url: '#' }
    ];

    return (
        <div className="card border-0 shadow-sm rounded-3">
            <div className="card-body p-4">
                <h4 className="text-primary fw-bold mb-4">Theo dõi chúng tôi</h4>
                <div className="d-flex gap-2">
                    {socialPlatforms.map((platform, index) => (
                        <a
                            key={index}
                            href={platform.url}
                            className="btn btn-light rounded-circle"
                            style={{ width: '40px', height: '40px', padding: 0, lineHeight: '40px' }}
                            title={platform.name}
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <i className={platform.icon}></i>
                        </a>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default SocialLinks;
