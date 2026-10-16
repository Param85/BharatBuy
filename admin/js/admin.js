/**
 * BharatBuy Admin Panel - Shared JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {

    // ── Sidebar Toggle ──
    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (sidebarToggle && sidebar) {
        // Restore saved state
        const savedState = localStorage.getItem('admin_sidebar_collapsed');
        if (savedState === 'true') {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }

        sidebarToggle.addEventListener('click', function() {
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                sidebar.classList.toggle('mobile-open');
            } else {
                sidebar.classList.toggle('collapsed');
                document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('admin_sidebar_collapsed', sidebar.classList.contains('collapsed'));
            }
        });

        // Close mobile sidebar on click outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && sidebar.classList.contains('mobile-open')) {
                if (!sidebar.contains(e.target) && e.target !== sidebarToggle) {
                    sidebar.classList.remove('mobile-open');
                }
            }
        });
    }

    // ── Table Search ──
    const tableSearchInputs = document.querySelectorAll('.admin-search-input[data-table]');
    tableSearchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const tableId = this.getAttribute('data-table');
            const table = document.getElementById(tableId);
            if (!table) return;

            const query = this.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });
});

// ── Confirmation Modal System ──
function showConfirmModal(title, message, onConfirm) {
    const overlay = document.getElementById('confirmModal');
    const titleEl = document.getElementById('confirmModalTitle');
    const messageEl = document.getElementById('confirmModalMessage');
    const confirmBtn = document.getElementById('confirmModalConfirm');
    const cancelBtn = document.getElementById('confirmModalCancel');
    const closeBtn = document.getElementById('confirmModalClose');

    if (!overlay) return;

    titleEl.textContent = title;
    messageEl.textContent = message;
    overlay.style.display = 'flex';

    // Remove previous listeners by cloning
    const newConfirm = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirm, confirmBtn);

    newConfirm.addEventListener('click', function() {
        overlay.style.display = 'none';
        if (typeof onConfirm === 'function') onConfirm();
    });

    const hideModal = () => { overlay.style.display = 'none'; };
    cancelBtn.onclick = hideModal;
    closeBtn.onclick = hideModal;
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) hideModal();
    });
}

// ── Toast Notifications ──
function showAdminToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.admin-toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = `admin-toast ${type}`;
    toast.innerHTML = `
        <i class="fa-solid ${type === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger'}"></i>
        <span class="admin-toast-message">${message}</span>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(40px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// ── AJAX Helper ──
async function adminFetch(url, data = {}) {
    const formData = new FormData();
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (metaToken && !data.csrf_token) {
        formData.append('csrf_token', metaToken);
    }
    for (const key in data) {
        formData.append(key, data[key]);
    }

    try {
        const headers = { 'X-Requested-With': 'XMLHttpRequest' };
        if (metaToken) {
            headers['X-CSRF-Token'] = metaToken;
        }
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: headers
        });
        return await response.json();
    } catch (err) {
        console.error('Admin fetch error:', err);
        return { success: false, message: 'Network error. Please try again.' };
    }
}
