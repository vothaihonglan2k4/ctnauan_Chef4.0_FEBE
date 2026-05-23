import React from 'react';
import { getVideoEmbedUrl } from '../../utils/videoUtils';

const VideoPlayer = ({ videoUrl, title, fallbackImage = null }) => {
    if (!videoUrl && !fallbackImage) {
        return (
            <div className="alert alert-info">
                Bài học này không có video.
            </div>
        );
    }

    // Try to get video embed URL
    const embedUrl = videoUrl ? getVideoEmbedUrl(videoUrl) : null;

    // If video URL is provided but can't be converted to embed URL
    if (videoUrl && !embedUrl) {
        return (
            <div className="alert alert-warning mb-4">
                <i className="fas fa-exclamation-triangle me-2"></i>
                Video không khả dụng. URL video có thể không hợp lệ hoặc video đã bị xóa.
            </div>
        );
    }

    // Show video player if embed URL is available
    if (embedUrl) {
        return (
            <div className="ratio ratio-16x9 mb-4">
                <iframe
                    src={embedUrl}
                    title={title}
                    allowFullScreen
                    className="rounded"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                ></iframe>
            </div>
        );
    }

    // Fallback to image if no video
    if (fallbackImage) {
        return (
            <img
                src={fallbackImage}
                className="img-fluid rounded mb-4 w-100"
                alt={title}
            />
        );
    }

    return null;
};

export default VideoPlayer;
