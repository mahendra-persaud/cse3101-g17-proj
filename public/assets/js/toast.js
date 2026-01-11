/**
 * Toast Notification System
 * Shows non-blocking success, error, info, and warning messages
 */

// Initialize toast container when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
});

/**
 * Show a toast notification
 * @param {string} message - The message to display
 * @param {string} type - Type: 'success', 'error', 'info', 'warning'
 * @param {number} duration - Duration in milliseconds (default: 4000)
 */
function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toast-container');
    if (!container) {
        console.error('Toast container not found');
        return;
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    // Icon based on type
    const icons = {
        success: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>`,
        error: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>`,
        warning: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>`,
        info: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>`
    };

    toast.innerHTML = `
        <div class="toast-icon">${icons[type] || icons.info}</div>
        <div class="toast-message">${message}</div>
        <button class="toast-close" onclick="closeToast(this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    `;

    container.appendChild(toast);

    // Trigger animation
    setTimeout(() => toast.classList.add('toast-show'), 10);

    // Auto remove after duration
    const timeoutId = setTimeout(() => {
        removeToast(toast);
    }, duration);

    // Store timeout ID for manual close
    toast.dataset.timeoutId = timeoutId;
}

/**
 * Close a toast notification
 * @param {HTMLElement} button - The close button element
 */
function closeToast(button) {
    const toast = button.closest('.toast');
    if (toast) {
        clearTimeout(toast.dataset.timeoutId);
        removeToast(toast);
    }
}

/**
 * Remove toast with animation
 * @param {HTMLElement} toast - The toast element to remove
 */
function removeToast(toast) {
    toast.classList.remove('toast-show');
    toast.classList.add('toast-hide');
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

/**
 * Convenience functions for specific toast types
 */
function showSuccessToast(message, duration) {
    showToast(message, 'success', duration);
}

function showErrorToast(message, duration) {
    showToast(message, 'error', duration);
}

function showWarningToast(message, duration) {
    showToast(message, 'warning', duration);
}

function showInfoToast(message, duration) {
    showToast(message, 'info', duration);
}

// Check for URL parameters to show toasts (for redirects after form submissions)
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('success')) {
        showSuccessToast(decodeURIComponent(urlParams.get('success')));
        // Clean URL
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }

    if (urlParams.has('error')) {
        showErrorToast(decodeURIComponent(urlParams.get('error')));
        // Clean URL
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
});
