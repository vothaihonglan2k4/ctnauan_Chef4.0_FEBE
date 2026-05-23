import React from 'react';

const SocialShareButtons = ({ url, title, onCopyLink }) => {
    const shareButtons = [
        {
            name: 'Facebook',
            icon: 'fab fa-facebook-f',
            color: 'btn-primary',
            url: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`
        },
        {
            name: 'Twitter',
            icon: 'fab fa-twitter',
            color: 'btn-info text-white',
            url: `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`
        },
        {
            name: 'WhatsApp',
            icon: 'fab fa-whatsapp',
            color: 'btn-success',
            url: `https://api.whatsapp.com/send?text=${encodeURIComponent(title + ': ' + url)}`
        },
        {
            name: 'Telegram',
            icon: 'fab fa-telegram-plane',
            color: 'btn-info',
            url: `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`
        }
    ];

    return (
        <div className="d-flex gap-2 flex-wrap align-items-center">
            <span className="text-muted me-2">
                <i className="fas fa-share-alt me-1"></i>Chia sẻ video:
            </span>

            {shareButtons.map((button) => (
                <a
                    key={button.name}
                    href={button.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className={`btn btn-sm ${button.color}`}
                >
                    <i className={`${button.icon} me-1`}></i>{button.name}
                </a>
            ))}

            <button
                type="button"
                className="btn btn-sm btn-secondary"
                onClick={() => onCopyLink(url)}
            >
                <i className="fas fa-link me-1"></i>Copy Link
            </button>
        </div>
    );
};

export default SocialShareButtons;
