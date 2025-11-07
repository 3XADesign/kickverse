/**
 * Kickverse Admin - Core JavaScript
 * Global utilities and helpers
 */

// Global Admin namespace
window.AdminApp = window.AdminApp || {};

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

/**
 * Format currency (EUR)
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

/**
 * Format date
 */
function formatDate(dateString, format = 'short') {
    const date = new Date(dateString);

    if (format === 'short') {
        return date.toLocaleDateString('es-ES');
    } else if (format === 'long') {
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } else if (format === 'datetime') {
        return date.toLocaleString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    return date.toLocaleDateString('es-ES');
}

/**
 * Format time ago
 */
function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    const intervals = {
        año: 31536000,
        mes: 2592000,
        semana: 604800,
        día: 86400,
        hora: 3600,
        minuto: 60,
        segundo: 1
    };

    for (const [name, count] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / count);
        if (interval >= 1) {
            return `Hace ${interval} ${name}${interval > 1 ? (name === 'mes' ? 'es' : 's') : ''}`;
        }
    }

    return 'Hace un momento';
}

/**
 * Show loading overlay
 */
function showLoading(message = 'Cargando...') {
    const overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.className = 'loading-overlay';
    overlay.innerHTML = `
        <div class="loading-spinner"></div>
        <div style="color: white; margin-top: 20px;">${escapeHtml(message)}</div>
    `;
    document.body.appendChild(overlay);
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.remove();
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info', duration = 3000) {
    // Create toast container if it doesn't exist
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 10000; display: flex; flex-direction: column; gap: 10px;';
        document.body.appendChild(container);
    }

    // Create toast
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        min-width: 300px;
        padding: 16px 20px;
        border-radius: 8px;
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideIn 0.3s ease;
    `;

    const icons = {
        success: '<i class="fas fa-check-circle" style="color: #10b981; font-size: 20px;"></i>',
        error: '<i class="fas fa-exclamation-circle" style="color: #ef4444; font-size: 20px;"></i>',
        warning: '<i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 20px;"></i>',
        info: '<i class="fas fa-info-circle" style="color: #3b82f6; font-size: 20px;"></i>'
    };

    toast.innerHTML = `
        ${icons[type] || icons.info}
        <div style="flex: 1; color: #1f2937;">${escapeHtml(message)}</div>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 20px;">×</button>
    `;

    container.appendChild(toast);

    // Auto remove
    if (duration > 0) {
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
}

// Add animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

/**
 * Validate email
 */
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Get URL parameter
 */
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

/**
 * Set URL parameter without reload
 */
function setUrlParameter(name, value) {
    const url = new URL(window.location);
    if (value === null || value === undefined || value === '') {
        url.searchParams.delete(name);
    } else {
        url.searchParams.set(name, value);
    }
    window.history.pushState({}, '', url);
}

/**
 * Remove URL parameter without reload
 */
function removeUrlParameter(name) {
    const url = new URL(window.location);
    url.searchParams.delete(name);
    window.history.replaceState({}, '', url);
}

/**
 * Confirm dialog
 */
async function confirmDialog(message, title = '¿Estás seguro?') {
    return new Promise((resolve) => {
        if (window.ModalManager) {
            window.ModalManager.confirm(message, title)
                .then(() => resolve(true))
                .catch(() => resolve(false));
        } else {
            resolve(confirm(message));
        }
    });
}

/**
 * Copy to clipboard
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showToast('Copiado al portapapeles', 'success');
        return true;
    } catch (err) {
        showToast('Error al copiar', 'error');
        return false;
    }
}

/**
 * Download file from data
 */
function downloadFile(data, filename, mimeType = 'text/plain') {
    const blob = new Blob([data], { type: mimeType });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

/**
 * Format phone number
 */
function formatPhone(phone) {
    if (!phone) return '';
    // Remove all non-digits
    const cleaned = phone.replace(/\D/g, '');
    // Format as +XX XXX XXX XXX
    if (cleaned.length === 11 && cleaned.startsWith('34')) {
        return `+${cleaned.slice(0, 2)} ${cleaned.slice(2, 5)} ${cleaned.slice(5, 8)} ${cleaned.slice(8)}`;
    }
    return phone;
}

/**
 * Truncate text
 */
function truncate(text, length = 50) {
    if (!text) return '';
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Export to global scope
window.AdminApp.utils = {
    escapeHtml,
    formatCurrency,
    formatDate,
    timeAgo,
    showLoading,
    hideLoading,
    showToast,
    isValidEmail,
    debounce,
    getUrlParameter,
    setUrlParameter,
    removeUrlParameter,
    confirmDialog,
    copyToClipboard,
    downloadFile,
    formatPhone,
    truncate,
    formatFileSize
};
