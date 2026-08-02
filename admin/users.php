<?php
/**
 * AgriSync — Admin User Management Directory Page (TASK-084 / Issue #59)
 * Manage registered farmers, commercial buyers, and system administrator accounts.
 */

$page_title = 'User Directory & Account Management';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../auth/auth_check.php';
checkRole(['admin']);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$csrf_token = generateCSRFToken();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid dashboard-wrapper">
    <div class="row">
        <!-- Sidebar Navigation -->
        <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

        <!-- Main Content Area -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            
            <!-- Page Header Banner -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="fw-bold text-dark mb-1">User Account Directory</h2>
                    <p class="text-muted small mb-0">Manage registered farmers, business buyers, and system administrative accounts across Sri Lanka</p>
                </div>
                <div class="mt-3 mt-md-0 d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary shadow-sm" id="btnRefreshUsers">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh List
                    </button>
                </div>
            </div>

            <!-- Metric Summary Row -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm p-3 bg-light-subtle">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted extra-small text-uppercase fw-semibold">Total Accounts</span>
                                <h4 class="fw-bold text-dark mb-0 mt-1" id="statTotalUsers">0</h4>
                            </div>
                            <div class="avatar rounded-circle bg-primary-subtle text-primary p-2 fs-4">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm p-3 bg-light-subtle">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted extra-small text-uppercase fw-semibold">Farmers</span>
                                <h4 class="fw-bold text-success mb-0 mt-1" id="statFarmers">0</h4>
                            </div>
                            <div class="avatar rounded-circle bg-success-subtle text-success p-2 fs-4">
                                <i class="bi bi-person-workspace"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm p-3 bg-light-subtle">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted extra-small text-uppercase fw-semibold">Business Buyers</span>
                                <h4 class="fw-bold text-info mb-0 mt-1" id="statBuyers">0</h4>
                            </div>
                            <div class="avatar rounded-circle bg-info-subtle text-info p-2 fs-4">
                                <i class="bi bi-shop"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm p-3 bg-light-subtle">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted extra-small text-uppercase fw-semibold">Active Status</span>
                                <h4 class="fw-bold text-primary mb-0 mt-1" id="statActiveRatio">100%</h4>
                            </div>
                            <div class="avatar rounded-circle bg-primary-subtle text-primary p-2 fs-4">
                                <i class="bi bi-patch-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Table Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Platform Accounts</h5>
                        <span class="badge bg-light text-dark border ms-2" id="userCountBadge">0 Users</span>
                    </div>
                    
                    <div class="d-flex gap-2 flex-grow-1 flex-md-grow-0">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search name, email or district...">
                        </div>
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
                            <thead class="table-light extra-small">
                                <tr>
                                    <th class="ps-4">User ID</th>
                                    <th>Name / Enterprise</th>
                                    <th>Email Address</th>
                                    <th>Role</th>
                                    <th>District</th>
                                    <th>Phone</th>
                                    <th>Account Status</th>
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

        </main>
    </div>
</div>

<!-- Toggle Status Confirmation Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="statusModalLabel">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p id="statusModalText" class="text-secondary mb-0"></p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnConfirmStatusChange">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = '<?= $csrf_token ?>';
    const usersTbody = document.getElementById('usersTbody');
    const roleFilter = document.getElementById('roleFilter');
    const searchInput = document.getElementById('searchInput');
    const btnRefreshUsers = document.getElementById('btnRefreshUsers');
    const userCountBadge = document.getElementById('userCountBadge');

    const statTotalUsers = document.getElementById('statTotalUsers');
    const statFarmers = document.getElementById('statFarmers');
    const statBuyers = document.getElementById('statBuyers');
    const statActiveRatio = document.getElementById('statActiveRatio');

    const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    const btnConfirmStatusChange = document.getElementById('btnConfirmStatusChange');

    let pendingUserId = null;
    let pendingAction = null; // 'activate' or 'deactivate'

    async function loadUsers() {
        const role = roleFilter.value;
        const search = encodeURIComponent(searchInput.value.trim());

        try {
            const res = await fetch(`<?= APP_URL ?>/api/admin.php?action=get_users&role=${role}&search=${search}`);
            const data = await res.json();

            if (data.success) {
                const users = data.data.users || [];
                renderUsersTable(users);
            } else {
                usersTbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">${data.error || 'Failed to load user directory.'}</td></tr>`;
            }
        } catch (err) {
            usersTbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Error connecting to server.</td></tr>`;
        }
    }

    function renderUsersTable(users) {
        userCountBadge.textContent = `${users.length} User${users.length === 1 ? '' : 's'}`;

        let total = users.length;
        let farmers = 0;
        let buyers = 0;
        let active = 0;

        users.forEach(u => {
            if (u.role === 'farmer') farmers++;
            if (u.role === 'business') buyers++;
            if (parseInt(u.is_active) === 1) active++;
        });

        statTotalUsers.textContent = total;
        statFarmers.textContent = farmers;
        statBuyers.textContent = buyers;
        statActiveRatio.textContent = total > 0 ? Math.round((active / total) * 100) + '%' : '100%';

        if (users.length === 0) {
            usersTbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-person-x fs-2 d-block mb-2 text-muted"></i>No user accounts found matching your search.</td></tr>`;
            return;
        }

        usersTbody.innerHTML = users.map(u => {
            const isActive = parseInt(u.is_active) === 1;
            return `
                <tr>
                    <td class="ps-4 text-muted fw-bold">#USR-${u.id}</td>
                    <td>
                        <div class="fw-bold text-dark">${u.name}</div>
                        <div class="text-muted extra-small">Joined ${u.created_at ? u.created_at.split(' ')[0] : 'N/A'}</div>
                    </td>
                    <td><a href="mailto:${u.email}" class="text-decoration-none text-dark">${u.email}</a></td>
                    <td><span class="badge ${getRoleBadgeClass(u.role)} text-capitalize extra-small">${u.role}</span></td>
                    <td><i class="bi bi-geo-alt text-muted me-1"></i>${u.district || 'N/A'}</td>
                    <td>${u.phone || 'N/A'}</td>
                    <td>
                        <span class="badge ${isActive ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger'} extra-small">
                            <i class="bi ${isActive ? 'bi-check-circle-fill' : 'bi-slash-circle'} me-1"></i>
                            ${isActive ? 'Active' : 'Deactivated'}
                        </span>
                    </td>
                    <td class="pe-4 text-end">
                        <button type="button" class="btn btn-sm ${isActive ? 'btn-outline-danger' : 'btn-outline-success'} btn-toggle-status" 
                                data-id="${u.id}" data-name="${u.name}" data-action="${isActive ? 'deactivate' : 'activate'}">
                            ${isActive ? '<i class="bi bi-slash-circle me-1"></i> Deactivate' : '<i class="bi bi-check-circle me-1"></i> Activate'}
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        // Bind status toggle buttons
        document.querySelectorAll('.btn-toggle-status').forEach(btn => {
            btn.addEventListener('click', () => {
                pendingUserId = btn.getAttribute('data-id');
                pendingAction = btn.getAttribute('data-action');
                const userName = btn.getAttribute('data-name');

                document.getElementById('statusModalText').innerHTML = `Are you sure you want to <strong>${pendingAction}</strong> the account for <strong>${userName}</strong>?`;
                btnConfirmStatusChange.className = pendingAction === 'activate' ? 'btn btn-success' : 'btn btn-danger';
                btnConfirmStatusChange.textContent = pendingAction === 'activate' ? 'Activate Account' : 'Deactivate Account';
                
                statusModal.show();
            });
        });
    }

    btnConfirmStatusChange.addEventListener('click', async () => {
        if (!pendingUserId) return;

        btnConfirmStatusChange.disabled = true;
        btnConfirmStatusChange.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

        try {
            const formData = new FormData();
            formData.append('user_id', pendingUserId);
            formData.append('csrf_token', csrfToken);

            const res = await fetch('<?= APP_URL ?>/api/admin.php?action=toggle_user_status', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });

            const data = await res.json();
            if (data.success) {
                showToast(data.error || 'User account status updated!', 'success');
                statusModal.hide();
                loadUsers();
            } else {
                showToast(data.error || 'Failed to update user status.', 'error');
            }
        } catch (err) {
            showToast('Server error while updating user status.', 'error');
        } finally {
            btnConfirmStatusChange.disabled = false;
        }
    });

    roleFilter.addEventListener('change', loadUsers);
    btnRefreshUsers.addEventListener('click', loadUsers);
    
    let timer;
    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(loadUsers, 300);
    });

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
