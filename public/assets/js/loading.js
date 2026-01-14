// making things spin when it loads
// basically shows the user something is happening

// Global loading state
const LoadingState = {
    // shows the big loading screen covering everything
    showFullPage: function(message = 'Loading...') {
        let overlay = document.getElementById('loading-overlay');

        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'loading-overlay';
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="loading-content">
                    <div class="spinner-large"></div>
                    <p class="loading-message">${message}</p>
                </div>
            `;
            document.body.appendChild(overlay);
        } else {
            overlay.querySelector('.loading-message').textContent = message;
        }

        overlay.classList.add('loading-show');
    },

    // hides the big loading screen
    hideFullPage: function() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.classList.remove('loading-show');
        }
    },

    // Show loading spinner in a specific element
    showInElement: function(element, size = 'medium') {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }

        if (!element) return;

        // Store original content
        element.dataset.originalContent = element.innerHTML;
        element.classList.add('loading-container');

        const spinner = document.createElement('div');
        spinner.className = `spinner spinner-${size}`;

        element.innerHTML = '';
        element.appendChild(spinner);
    },

    // Hide loading spinner and restore content
    hideInElement: function(element) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }

        if (!element) return;

        if (element.dataset.originalContent) {
            element.innerHTML = element.dataset.originalContent;
            delete element.dataset.originalContent;
        }

        element.classList.remove('loading-container');
    },

    // puts a spinner on the button so they don't click it twice
    showOnButton: function(button, text = 'Loading...') {
        if (typeof button === 'string') {
            button = document.querySelector(button);
        }

        if (!button) return;

        button.disabled = true;
        button.dataset.originalText = button.textContent;
        button.classList.add('btn-loading');

        button.innerHTML = `
            <div class="spinner spinner-small"></div>
            <span>${text}</span>
        `;
    },

    // Restore button to original state
    hideOnButton: function(button) {
        if (typeof button === 'string') {
            button = document.querySelector(button);
        }

        if (!button) return;

        button.disabled = false;
        button.classList.remove('btn-loading');

        if (button.dataset.originalText) {
            button.textContent = button.dataset.originalText;
            delete button.dataset.originalText;
        }
    }
};

// Make available globally
window.LoadingState = LoadingState;

// Auto-show loading for forms
document.addEventListener('DOMContentLoaded', function() {
    // find all forms and make them show loading when submitted
    const forms = document.querySelectorAll('form[data-loading]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                LoadingState.showOnButton(submitBtn, 'Submitting...');
            }
        });
    });

    // Add loading state to links with data-loading attribute
    const links = document.querySelectorAll('a[data-loading]');

    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const message = link.dataset.loadingMessage || 'Loading...';
            LoadingState.showFullPage(message);
        });
    });
});

// Example usage with fetch
async function fetchWithLoading(url, options = {}) {
    LoadingState.showFullPage('Fetching data...');

    try {
        const response = await fetch(url, options);
        const data = await response.json();
        LoadingState.hideFullPage();
        return data;
    } catch (error) {
        LoadingState.hideFullPage();
        showErrorToast('Failed to fetch data: ' + error.message);
        throw error;
    }
}

// Make available globally
window.fetchWithLoading = fetchWithLoading;
