/**
 * Custom Modal System
 * Replaces browser confirm() with beautiful custom modals
 */

// Initialize modal container when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('modal-overlay')) {
        const overlay = document.createElement('div');
        overlay.id = 'modal-overlay';
        overlay.className = 'modal-overlay';
        overlay.innerHTML = `
            <div class="modal-container">
                <div class="modal-content">
                    <div class="modal-icon"></div>
                    <h3 class="modal-title"></h3>
                    <p class="modal-message"></p>
                    <div class="modal-actions">
                        <button class="modal-btn modal-btn-cancel">Cancel</button>
                        <button class="modal-btn modal-btn-confirm">Confirm</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    }
});

/**
 * Show a confirmation modal
 * @param {Object} options - Modal options
 * @param {string} options.title - Modal title
 * @param {string} options.message - Modal message
 * @param {string} options.confirmText - Confirm button text (default: "Confirm")
 * @param {string} options.cancelText - Cancel button text (default: "Cancel")
 * @param {string} options.type - Modal type: 'danger', 'warning', 'info' (default: 'danger')
 * @param {Function} options.onConfirm - Callback when confirmed
 * @param {Function} options.onCancel - Callback when cancelled
 */
function showConfirmModal(options) {
    const {
        title = 'Confirm Action',
        message = 'Are you sure you want to proceed?',
        confirmText = 'Confirm',
        cancelText = 'Cancel',
        type = 'danger',
        onConfirm = () => {},
        onCancel = () => {}
    } = options;

    const overlay = document.getElementById('modal-overlay');
    const modalContent = overlay.querySelector('.modal-content');
    const modalIcon = overlay.querySelector('.modal-icon');
    const modalTitle = overlay.querySelector('.modal-title');
    const modalMessage = overlay.querySelector('.modal-message');
    const confirmBtn = overlay.querySelector('.modal-btn-confirm');
    const cancelBtn = overlay.querySelector('.modal-btn-cancel');

    // Set content
    modalTitle.textContent = title;
    modalMessage.textContent = message;
    confirmBtn.textContent = confirmText;
    cancelBtn.textContent = cancelText;

    // Set icon based on type
    const icons = {
        danger: `<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>`,
        warning: `<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>`,
        info: `<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>`
    };

    modalIcon.innerHTML = icons[type] || icons.danger;
    modalIcon.className = `modal-icon modal-icon-${type}`;
    confirmBtn.className = `modal-btn modal-btn-confirm modal-btn-${type}`;

    // Show modal
    overlay.classList.add('modal-show');
    modalContent.classList.add('modal-content-show');

    // Handle confirm
    const handleConfirm = () => {
        closeModal();
        onConfirm();
    };

    // Handle cancel
    const handleCancel = () => {
        closeModal();
        onCancel();
    };

    // Remove old listeners and add new ones
    const newConfirmBtn = confirmBtn.cloneNode(true);
    const newCancelBtn = cancelBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

    newConfirmBtn.addEventListener('click', handleConfirm);
    newCancelBtn.addEventListener('click', handleCancel);

    // Close on overlay click
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            handleCancel();
        }
    });

    // Close on Escape key
    const escapeHandler = function(e) {
        if (e.key === 'Escape') {
            handleCancel();
            document.removeEventListener('keydown', escapeHandler);
        }
    };
    document.addEventListener('keydown', escapeHandler);
}

/**
 * Close the modal
 */
function closeModal() {
    const overlay = document.getElementById('modal-overlay');
    const modalContent = overlay.querySelector('.modal-content');

    modalContent.classList.remove('modal-content-show');
    setTimeout(() => {
        overlay.classList.remove('modal-show');
    }, 200);
}

/**
 * Convenience function for delete confirmation
 * @param {string} itemName - Name of item being deleted
 * @param {Function} onConfirm - Callback when confirmed
 */
function confirmDelete(itemName, onConfirm) {
    showConfirmModal({
        title: 'Delete ' + itemName + '?',
        message: 'This action cannot be undone. Are you sure you want to delete this ' + itemName.toLowerCase() + '?',
        confirmText: 'Delete',
        cancelText: 'Cancel',
        type: 'danger',
        onConfirm: onConfirm
    });
}

/**
 * Replace all onclick="return confirm(...)" with custom modals
 */
document.addEventListener('DOMContentLoaded', function() {
    // Find all delete buttons with confirm
    const deleteButtons = document.querySelectorAll('.delete-btn, [onclick*="confirm"]');

    deleteButtons.forEach(button => {
        const onclickAttr = button.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes('confirm(')) {
            // Extract the URL if it's a link
            const href = button.getAttribute('href');

            // Remove the onclick attribute
            button.removeAttribute('onclick');

            // Add new click handler
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Determine what's being deleted
                const row = button.closest('tr');
                let itemType = 'item';
                let itemName = 'Unknown';

                // Try to determine item type from context
                if (window.location.pathname.includes('student')) {
                    itemType = 'student';
                    if (row) {
                        const nameCell = row.querySelector('td:nth-child(3)');
                        if (nameCell) itemName = nameCell.textContent.trim();
                    }
                } else if (window.location.pathname.includes('user')) {
                    itemType = 'user';
                    if (row) {
                        const nameCell = row.querySelector('td:nth-child(3)');
                        if (nameCell) itemName = nameCell.textContent.trim();
                    }
                } else if (window.location.pathname.includes('subject')) {
                    itemType = 'subject';
                    if (row) {
                        const nameCell = row.querySelector('td:nth-child(3)');
                        if (nameCell) itemName = nameCell.textContent.trim();
                    }
                } else if (window.location.pathname.includes('class')) {
                    itemType = 'class';
                    if (row) {
                        const nameCell = row.querySelector('td:nth-child(2)');
                        if (nameCell) itemName = nameCell.textContent.trim();
                    }
                }

                showConfirmModal({
                    title: `Delete ${itemType}?`,
                    message: `Are you sure you want to delete "${itemName}"? This action cannot be undone.`,
                    confirmText: 'Delete',
                    cancelText: 'Cancel',
                    type: 'danger',
                    onConfirm: () => {
                        // If it's a link with href, navigate to it
                        if (href && href !== '#') {
                            window.location.href = href;
                        } else {
                            // Otherwise show success toast
                            showSuccessToast(`${itemType} deleted successfully`);
                            // Remove the row from the table
                            if (row) {
                                row.remove();
                            }
                        }
                    }
                });
            });
        }
    });
});
