import React from 'react';

const YouTubePlayer = ({ videoUrl, title, onCopyLink }) => {
    const getYouTubeVideoId = (url) => {
        if (!url) return null;

        const patterns = [
            /youtube\.com\/watch\?v=([^&?\/]+)/,
            /youtube\.com\/embed\/([^&?\/]+)/,
            /youtu\.be\/([^&?\/]+)/
        ];

        for (const pattern of patterns) {
            const match = url.match(pattern);
            if (match) return match[1];
        }

        return null;
    };

    const videoId = getYouTubeVideoId(videoUrl);

    if (!videoId) return null;

    return (
        <div className="ratio ratio-16x9 mb-3">
            <iframe
                src={`https://www.youtube.com/embed/${videoId}`}
                title={title}
                frameBorder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowFullScreen
                className="rounded"
                style={{ boxShadow: '0 4px 8px rgba(0,0,0,0.1)' }}
            ></iframe>
        </div>
    );
};

export default YouTubePlayer;
