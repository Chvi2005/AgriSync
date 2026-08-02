/**
 * AgriSync — Global JavaScript Application Helpers (TASK-008)
 * Vanilla JavaScript (ES6+) — No external library dependencies except Bootstrap
 */

/**
 * Fetch API wrapper with CSRF injection and JSON parsing
 * 
 * @param {string} url 
 * @param {object} options 
 * @returns {Promise<object>}
 */
async function fetchAPI(url, options = {}) {
    const defaultHeaders = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };

    // Extract CSRF token from meta tag if available
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta && csrfMeta.content) {
        defaultHeaders['X-CSRF-Token'] = csrfMeta.content;
    }

    // Merge headers
    const headers = { ...defaultHeaders, ...(options.headers || {}) };

    // Format body if passing JSON object (and not FormData)
    let body = options.body;
    if (body && typeof body === 'object' && !(body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
        body = JSON.stringify(body);
    }

    try {
        const response = await fetch(url, {
            ...options,
            headers,
            body
        });

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('FetchAPI Error:', error);
        return {
            success: false,
            data: null,
            error: error.message || 'Network communication error'
        };
    }
}

/**
 * Display dynamic Bootstrap toast notification
 * 
 * @param {string} message 
 * @param {'success'|'danger'|'warning'|'info'} type 
 * @param {number} duration 
 */
function showToast(message, type = 'info', duration = 4500) {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1090';
        document.body.appendChild(container);
    }

    // Normalize type
    const toastType = type === 'error' ? 'danger' : type;

    // Icon mapping
    const icons = {
        success: 'bi-check-circle-fill text-success',
        danger: 'bi-exclamation-triangle-fill text-danger',
        warning: 'bi-exclamation-circle-fill text-warning',
        info: 'bi-info-circle-fill text-info'
    };
    const iconClass = icons[toastType] || icons.info;

    const toastId = 'toast_' + Math.random().toString(36).substring(2, 9);
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center border-0 shadow-sm mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi ${iconClass} fs-5"></i>
                    <span class="text-dark">${escapeHtml(message)}</span>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.getElementById(toastId);
    if (window.bootstrap && window.bootstrap.Toast) {
        const toast = new bootstrap.Toast(toastEl, { delay: duration });
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        toast.show();
    } else {
        setTimeout(() => toastEl.remove(), duration);
    }
}

/**
 * Show loading spinner on a button or container
 * 
 * @param {string} elementId 
 * @param {string} text 
 */
function showLoading(elementId, text = 'Processing...') {
    const el = document.getElementById(elementId);
    if (!el) return;

    if (!el.dataset.originalHtml) {
        el.dataset.originalHtml = el.innerHTML;
    }
    el.disabled = true;
    el.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${escapeHtml(text)}`;
}

/**
 * Hide loading spinner and restore original content
 * 
 * @param {string} elementId 
 */
function hideLoading(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return;

    if (el.dataset.originalHtml) {
        el.innerHTML = el.dataset.originalHtml;
        delete el.dataset.originalHtml;
    }
    el.disabled = false;
}

/**
 * Prompt user confirmation using the global modal
 * 
 * @param {string} message 
 * @param {Function} onConfirm 
 * @param {string} title 
 */
function confirmAction(message, onConfirm, title = 'Confirm Action') {
    const modalEl = document.getElementById('globalConfirmModal');
    if (!modalEl || !window.bootstrap) {
        if (confirm(message)) {
            onConfirm();
        }
        return;
    }

    const modalTitle = document.getElementById('globalConfirmModalLabel');
    const modalBody = document.getElementById('globalConfirmModalBody');
    const confirmBtn = document.getElementById('globalConfirmModalBtn');

    if (modalTitle) modalTitle.innerHTML = `<i class="bi bi-question-circle text-primary me-2"></i>${escapeHtml(title)}`;
    if (modalBody) modalBody.textContent = message;

    const modal = new bootstrap.Modal(modalEl);

    const handleConfirm = () => {
        confirmBtn.removeEventListener('click', handleConfirm);
        modal.hide();
        onConfirm();
    };

    confirmBtn.replaceWith(confirmBtn.cloneNode(true));
    document.getElementById('globalConfirmModalBtn').addEventListener('click', handleConfirm);

    modal.show();
}

/**
 * Format date string into human-readable format
 * 
 * @param {string} dateString 
 * @returns {string}
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Format number into Sri Lankan Rupees (LKR)
 * 
 * @param {number|string} amount 
 * @returns {string}
 */
function formatCurrency(amount) {
    const num = parseFloat(amount);
    if (isNaN(num)) return 'Rs. 0.00';
    return 'Rs. ' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/**
 * Escape HTML to prevent XSS in dynamic inserts
 * 
 * @param {string} str 
 * @returns {string}
 */
function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

/**
 * Polling helper for background notifications
 * 
 * @param {string} endpoint 
 * @param {Function} onReceive 
 * @param {number} intervalMs 
 */
function pollNotifications(endpoint = '/api/notifications.php', onReceive = null, intervalMs = 30000) {
    setInterval(async () => {
        const res = await fetchAPI(endpoint);
        if (res.success && typeof onReceive === 'function') {
            onReceive(res.data);
        }
    }, intervalMs);
}

/**
 * Render reusable empty state HTML string for client-side dynamic lists
 * 
 * @param {string} title 
 * @param {string} description 
 * @param {string} icon 
 * @param {string|null} btnText 
 * @param {string|null} btnTarget Modal ID starting with '#' or link URL
 * @returns {string}
 */
function renderEmptyStateHTML(title, description = '', icon = 'bi-inbox', btnText = null, btnTarget = null) {
    let btnHtml = '';
    if (btnText && btnTarget) {
        const isModal = btnTarget.startsWith('#');
        if (isModal) {
            btnHtml = `<div class="empty-state-action"><button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="${escapeHtml(btnTarget)}"><i class="bi bi-plus-lg me-1"></i>${escapeHtml(btnText)}</button></div>`;
        } else {
            btnHtml = `<div class="empty-state-action"><a href="${escapeHtml(btnTarget)}" class="btn btn-primary shadow-sm"><i class="bi bi-plus-lg me-1"></i>${escapeHtml(btnText)}</a></div>`;
        }
    }

    const descHtml = description ? `<p class="empty-state-description mb-3">${escapeHtml(description)}</p>` : '';

    return `
        <div class="empty-state py-5 text-center">
            <div class="empty-state-icon-wrapper mx-auto">
                <i class="bi ${escapeHtml(icon)}"></i>
            </div>
            <h5 class="empty-state-title">${escapeHtml(title)}</h5>
            ${descHtml}
            ${btnHtml}
        </div>
    `;
}

// Auto-initialize Bootstrap components on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    // Tooltips
    if (window.bootstrap && window.bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
    }
});
