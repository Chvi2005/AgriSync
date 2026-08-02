<?php
// AgriSync Admin User Management Directory Page (TASK-012)
$page_title = 'User Directory';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../auth/auth_check.php';
checkRole(['admin']);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid px-4 py-4">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">User Account Directory</h2>
            <p class="text-muted small mb-0">Manage registered farmers, business buyers, and system administrative accounts</p>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people text-primary me-2"></i>Platform Registered Users</h5>
            
            <div class="d-flex gap-2 flex-grow-1 flex-md-grow-0">
                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search name, email or district...">
                <select id="roleFilter" class="form-select form-select-sm w-auto">
                    <option value="all">All Roles</option>
                    <option value="farmer">Farmers</option>
                    <option value="business">Business Buyers</option>
                    <option value="admin">Administrators</option>
                </select>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">User ID</th>
                            <th>Name / Company</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>District</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="usersTbody">
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading users directory...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const usersTbody = document.getElementById('usersTbody');
    const roleFilter = document.getElementById('roleFilter');
    const searchInput = document.getElementById('searchInput');

    async function loadUsers() {
        const role = roleFilter.value;
        const search = encodeURIComponent(searchInput.value.trim());

        try {
            const res = await fetch(`<?= APP_URL ?>/api/admin.php?action=get_users&role=${role}&search=${search}`);
            const data = await res.json();

            if (data.success) {
                const users = data.data.users || [];
                if (users.length === 0) {
                    usersTbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">No user accounts found matching your search.</td></tr>`;
                    return;
                }

                usersTbody.innerHTML = users.map(u => `
                    <tr>
                        <td class="ps-4 text-muted">#USR-${u.id}</td>
                        <td class="fw-bold text-dark">${u.name}</td>
                        <td>${u.email}</td>
                        <td><span class="badge ${getRoleBadgeClass(u.role)} text-capitalize">${u.role}</span></td>
                        <td><i class="bi bi-geo-alt text-muted me-1"></i>${u.district || 'N/A'}</td>
                        <td>${u.phone || 'N/A'}</td>
                        <td>
                            <span class="badge ${parseInt(u.is_active) === 1 ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger'}">
                                ${parseInt(u.is_active) === 1 ? 'Active' : 'Deactivated'}
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <button type="button" class="btn btn-sm ${parseInt(u.is_active) === 1 ? 'btn-outline-danger' : 'btn-outline-success'}" onclick="toggleUserStatus(${u.id})">
                                ${parseInt(u.is_active) === 1 ? '<i class="bi bi-slash-circle me-1"></i> Deactivate' : '<i class="bi bi-check-circle me-1"></i> Activate'}
                            </button>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (err) {
            usersTbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Failed to load user directory.</td></tr>`;
        }
    }

    roleFilter.addEventListener('change', loadUsers);
    
    let timer;
    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(loadUsers, 300);
    });

    window.toggleUserStatus = async (userId) => {
        if (!confirm('Change status for this user account?')) return;
        try {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('csrf_token', '<?= generateCSRFToken() ?>');

            const res = await fetch('<?= APP_URL ?>/api/admin.php?action=toggle_user_status', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '<?= generateCSRFToken() ?>' }
            });

            const data = await res.json();
            if (data.success) {
                if (window.AgriSync && window.AgriSync.showToast) {
                    window.AgriSync.showToast('User status updated!', 'success');
                }
                loadUsers();
            } else {
                alert(data.error || 'Failed to update user status.');
            }
        } catch (err) {}
    };

    loadUsers();
});

function getRoleBadgeClass(role) {
    switch (role.toLowerCase()) {
        case 'farmer': return 'bg-success-subtle text-success border border-success';
        case 'business': return 'bg-info-subtle text-info border border-info';
        case 'admin': return 'bg-danger-subtle text-danger border border-danger';
        default: return 'bg-secondary-subtle text-secondary';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
