import React from 'react';

const LoadingSpinner = ({ size = 'default', message = 'Đang tải...' }) => {
    const sizeClass = {
        small: 'spinner-border-sm',
        default: '',
        large: 'spinner-border-lg'
    }[size];

    const spinnerStyle = size === 'large' ? { width: '3rem', height: '3rem' } : {};

    return (
        <div className="text-center py-5">
            <div
                className={`spinner-border text-primary ${sizeClass}`}
                role="status"
                style={spinnerStyle}
            >
                <span className="visually-hidden">{message}</span>
            </div>
            {message && <p className="mt-3">{message}</p>}
        </div>
    );
};

export default LoadingSpinner;
