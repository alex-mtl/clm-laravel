// Toast function
window.showToast = function(message, type = 'info') {
    // Create toast container if it doesn't exist
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    // Add progress bar
    const progress = document.createElement('div');
    progress.className = 'toast-progress';

    // Add message
    const messageElement = document.createElement('div');
    messageElement.textContent = message;

    toast.appendChild(messageElement);
    toast.appendChild(progress);
    container.appendChild(toast);

    // Remove toast after 5 seconds
    setTimeout(() => {
        toast.classList.add('fade-out');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 5000);

    return toast;
}

// Optional: Different toast types
window.showSuccessToast = function (message) {
    return showToast(message, 'success');
}

window.showErrorToast = function (message) {
    return showToast(message, 'error');
}

window.showWarningToast = function(message) {
    return showToast(message, 'warning');
}

document.addEventListener('DOMContentLoaded', function() {
    if(typeof toasts !== 'undefined') {
        toasts.forEach(toast => {
            showToast(toast.message, toast.type);
        });
    }
});
