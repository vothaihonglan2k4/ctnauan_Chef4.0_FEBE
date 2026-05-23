/**
 * Extract YouTube video ID from various YouTube URL formats
 * @param {string} url - YouTube URL
 * @returns {string|null} - Video ID or null if not found
 */
export const extractYouTubeVideoId = (url) => {
    if (!url) return null;

    const cleanUrl = url.trim();
    let videoId = '';

    // Regex patterns for YouTube video ID (11 characters)
    const patterns = [
        /youtu\.be\/([a-zA-Z0-9_-]{11})/,      // youtu.be/ID
        /[?&]v=([a-zA-Z0-9_-]{11})/,           // ?v=ID or &v=ID
        /embed\/([a-zA-Z0-9_-]{11})/           // embed/ID
    ];

    // Try regex patterns first
    for (const pattern of patterns) {
        const match = cleanUrl.match(pattern);
        if (match && match[1]) {
            videoId = match[1];
            break;
        }
    }

    // Fallback: Manual string parsing
    if (!videoId) {
        if (cleanUrl.includes('youtube.com/watch?v=')) {
            videoId = cleanUrl.split('v=')[1]?.split('&')[0];
        } else if (cleanUrl.includes('youtube.com/embed/')) {
            const parts = cleanUrl.split('embed/');
            if (parts.length > 1) {
                videoId = parts[1].split('?')[0];
            }
        } else if (cleanUrl.includes('youtu.be/')) {
            videoId = cleanUrl.split('youtu.be/')[1]?.split('?')[0];
        }
    }

    // Validation: Skip example IDs
    if (videoId && videoId.length > 3 && !videoId.match(/^example\d+$/)) {
        // Ensure ID is exactly 11 characters
        if (videoId.length > 11 && videoId.substring(0, 11).match(/^[a-zA-Z0-9_-]{11}$/)) {
            videoId = videoId.substring(0, 11);
        }
        return videoId;
    }

    return null;
};

/**
 * Get YouTube embed URL from video URL or ID
 * @param {string} urlOrId - YouTube URL or video ID
 * @returns {string|null} - Embed URL or null
 */
export const getYouTubeEmbedUrl = (urlOrId) => {
    if (!urlOrId) return null;

    // If already an embed URL, return it
    if (urlOrId.includes('youtube.com/embed/')) {
        return urlOrId;
    }

    // Extract video ID
    const videoId = extractYouTubeVideoId(urlOrId);

    if (videoId) {
        return `https://www.youtube.com/embed/${videoId}`;
    }

    return null;
};

/**
 * Extract Vimeo video ID from URL
 * @param {string} url - Vimeo URL
 * @returns {string|null} - Video ID or null
 */
export const extractVimeoVideoId = (url) => {
    if (!url) return null;

    const cleanUrl = url.trim();
    const patterns = [
        /vimeo\.com\/(\d+)/,           // vimeo.com/ID
        /player\.vimeo\.com\/video\/(\d+)/  // player.vimeo.com/video/ID
    ];

    for (const pattern of patterns) {
        const match = cleanUrl.match(pattern);
        if (match && match[1]) {
            return match[1];
        }
    }

    return null;
};

/**
 * Get Vimeo embed URL from video URL or ID
 * @param {string} urlOrId - Vimeo URL or video ID
 * @returns {string|null} - Embed URL or null
 */
export const getVimeoEmbedUrl = (urlOrId) => {
    if (!urlOrId) return null;

    // If already an embed URL, return it
    if (urlOrId.includes('player.vimeo.com/video/')) {
        return urlOrId;
    }

    // Extract video ID
    const videoId = extractVimeoVideoId(urlOrId);

    if (videoId) {
        return `https://player.vimeo.com/video/${videoId}`;
    }

    return null;
};

/**
 * Get video embed URL (supports YouTube and Vimeo)
 * @param {string} url - Video URL
 * @returns {string|null} - Embed URL or null
 */
export const getVideoEmbedUrl = (url) => {
    if (!url) return null;

    const cleanUrl = url.trim().toLowerCase();

    // Check for YouTube
    if (cleanUrl.includes('youtube') || cleanUrl.includes('youtu.be')) {
        return getYouTubeEmbedUrl(url);
    }

    // Check for Vimeo
    if (cleanUrl.includes('vimeo')) {
        return getVimeoEmbedUrl(url);
    }

    return null;
};

/**
 * Check if URL is a YouTube video
 * @param {string} url 
 * @returns {boolean}
 */
export const isYouTubeUrl = (url) => {
    if (!url) return false;
    const cleanUrl = url.trim().toLowerCase();
    return cleanUrl.includes('youtube') || cleanUrl.includes('youtu.be');
};

/**
 * Check if URL is a Vimeo video
 * @param {string} url 
 * @returns {boolean}
 */
export const isVimeoUrl = (url) => {
    if (!url) return false;
    const cleanUrl = url.trim().toLowerCase();
    return cleanUrl.includes('vimeo');
};
