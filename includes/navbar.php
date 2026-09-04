<?php
// AgriSync Shared Top Navigation Bar (TASK-013)
// Include this component on all dashboard pages

if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/../config/session.php';
}
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/../includes/functions.php';
}

$user_role = getUserRole();
$user_name = $_SESSION['user_name'] ?? 'User';
$user_district = $_SESSION['user_district'] ?? '';
$app_url = defined('APP_URL') ? APP_URL : '';

// Active page detection
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container-fluid px-3 px-lg-4">
        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-4" href="<?= $app_url ?>/index.php">
            <span class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                <i class="bi bi-flower1"></i>
            </span>
            <span>AgriSync</span>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#agriSyncNav" aria-controls="agriSyncNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="agriSyncNav">
            <!-- Navigation Links by Role -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <?php if ($user_role === 'farmer'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active fw-semibold' : '' ?>" href="<?= $app_url ?>/farmer/dashboard.php">
                            <i class="bi bi-grid-1x2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'listings.php' ? 'active fw-semibold' : '' ?>" href="<?= $app_url ?>/farmer/listings.php">
                            <i class="bi bi-box-seam me-1"></i> My Harvest Listings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'orders.php' ? 'active fw-semibold' : '' ?>" href="<?= $app_url ?>/farmer/orders.php">
                            <i class="bi bi-cart-check me-1"></i> Order Matches
                        </a>
                    </li>
                <?php elseif ($user_role === 'business'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active fw-semibold' : '' ?>" href="<?= $app_url ?>/business/dashboard.php">
                            <i class="bi bi-grid-1x2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'requests.php' ? 'active fw-semibold' : '' ?>" href="<?= $app_url ?>/business/requests.php">
                            <i class="bi bi-bag-plus me-1"></i> Pre-Order Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'matches.php' ? 'active fw-semibold' : '' ?>" href="<?= $app_url ?>/business/matches.php">
                            <i class="bi bi-shop-window me-1"></i> Matched Produce
                        </a>
                    </li>
                <?php elseif ($user_role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active fw-semibold' : '' ?>" href="<?= $app_url ?>/admin/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i> Admin Overview
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'users.php' ? 'active fw-semibold' : '' ?>" href="<?= $app_url ?>/admin/users.php">
                            <i class="bi bi-people me-1"></i> User Directory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'listings.php' ? 'active fw-semibold' : '' ?>" href="<?= $app_url ?>/admin/listings.php">
                            <i class="bi bi-boxes me-1"></i> Listings Audit
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'orders.php' ? 'active fw-semibold' : '' ?>" href="<?= $app_url ?>/admin/orders.php">
                            <i class="bi bi-receipt me-1"></i> Orders Audit
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Right Nav Controls: Notifications & User Profile -->
            <div class="d-flex align-items-center gap-3">
                <?php if (isLoggedIn()): ?>
                    <!-- Notifications Dropdown -->
                    <div class="dropdown" id="notificationDropdownContainer">
                        <button class="btn btn-outline-light position-relative border-0 rounded-circle p-2" type="button" id="notifDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                            <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                                0
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 rounded-3 mt-2" style="width: 320px; max-height: 400px; overflow-y: auto;" aria-labelledby="notifDropdownBtn">
                            <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold mb-0 text-dark">Notifications</h6>
                                <button type="button" id="markAllReadBtn" class="btn btn-link p-0 text-decoration-none extra-small text-primary">Mark all as read</button>
                            </div>
                            <div id="notifList" class="list-group list-group-flush extra-small">
                                <div class="text-center py-4 text-muted">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span> Loading updates...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light bg-white border-0 dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1.5 shadow-sm" type="button" id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar-circle bg-primary-subtle text-primary fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                <?= strtoupper(substr($user_name, 0, 1)) ?>
                            </span>
                            <span class="fw-semibold text-dark small d-none d-sm-inline"><?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="badge bg-secondary text-capitalize extra-small ms-1"><?= htmlspecialchars($user_role, ENT_QUOTES, 'UTF-8') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2" aria-labelledby="userProfileDropdown">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark small"><?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="text-muted extra-small"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($user_district, ENT_QUOTES, 'UTF-8') ?></div>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="<?= $app_url ?>/auth/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= $app_url ?>/auth/login.php" class="btn btn-outline-light rounded-2 px-3">Sign In</a>
                    <a href="<?= $app_url ?>/auth/register.php" class="btn btn-light rounded-2 px-3 fw-semibold">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const notifBadge = document.getElementById('notifBadge');
    const notifList = document.getElementById('notifList');
    const markAllReadBtn = document.getElementById('markAllReadBtn');

    async function loadNotifications() {
        if (!notifList) return;
        try {
            const res = await fetch('<?= $app_url ?>/api/notifications.php?action=list');
            const data = await res.json();
            if (data.success) {
                const notifications = data.data.notifications || [];
                const unreadCount = data.data.unread_count || 0;

                if (unreadCount > 0) {
                    notifBadge.textContent = unreadCount;
                    notifBadge.classList.remove('d-none');
                } else {
                    notifBadge.classList.add('d-none');
                }

                if (notifications.length === 0) {
                    notifList.innerHTML = `<div class="p-3 text-center text-muted">No notifications yet.</div>`;
                    return;
                }

                notifList.innerHTML = notifications.map(n => `
                    <div class="list-group-item list-group-item-action p-3 ${n.is_read ? 'bg-white' : 'bg-light'}">
                        <div class="d-flex justify-content-between align-items-start">
                            <p class="mb-1 text-dark">${n.message}</p>
                            ${!n.is_read ? '<span class="badge bg-primary rounded-pill p-1 ms-2"></span>' : ''}
                        </div>
                        <small class="text-muted extra-small">${n.time_ago}</small>
                    </div>
                `).join('');
            }
        } catch (err) {
            if (notifList) notifList.innerHTML = `<div class="p-3 text-center text-muted">Failed to load updates.</div>`;
        }
    }

    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', async () => {
            try {
                const formData = new FormData();
                formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');
                await fetch('<?= $app_url ?>/api/notifications.php?action=read_all', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '<?= $_SESSION['csrf_token'] ?? '' ?>' }
                });
                loadNotifications();
            } catch (err) {}
        });
    }

    loadNotifications();
});
</script>
