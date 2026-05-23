/**
 * Copy text to clipboard using modern Clipboard API or fallback method
 * @param {string} text - Text to copy
 * @param {function} onSuccess - Success callback
 * @param {function} onError - Error callback
 */
export const copyToClipboard = (text, onSuccess, onError) => {
    // Use modern Clipboard API
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text)
            .then(() => {
                if (onSuccess) onSuccess();
            })
            .catch(err => {
                console.error('Failed to copy:', err);
                fallbackCopyTextToClipboard(text, onSuccess, onError);
            });
    } else {
        fallbackCopyTextToClipboard(text, onSuccess, onError);
    }
};

/**
 * Fallback method for copying text to clipboard
 * @param {string} text - Text to copy
 * @param {function} onSuccess - Success callback  
 * @param {function} onError - Error callback
 */
const fallbackCopyTextToClipboard = (text, onSuccess, onError) => {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.top = "0";
    textArea.style.left = "0";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            if (onSuccess) onSuccess();
        } else {
            if (onError) onError(text);
        }
    } catch (err) {
        if (onError) onError(text);
    }

    document.body.removeChild(textArea);
};

/**
 * Format number to Vietnamese currency
 * @param {number} amount 
 * @returns {string}
 */
export const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
};

/**
 * Format date to Vietnamese locale
 * @param {string|Date} date 
 * @returns {string}
 */
export const formatDate = (date) => {
    return new Date(date).toLocaleDateString('vi-VN');
};
