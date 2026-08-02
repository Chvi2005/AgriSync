<?php
// AgriSync Shared Dashboard Sidebar Component (TASK-013 / M3 Layout Pattern)
// Usage: Included within standard grid <div class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebarMenu">

if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/../config/session.php';
}
if (!function_exists('getUserRole')) {
    require_once __DIR__ . '/../includes/functions.php';
}

$user_role = getUserRole();
$app_url = defined('APP_URL') ? APP_URL : '';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse shadow-xs">
    <div class="position-sticky pt-3">
        <div class="px-3 mb-3 text-muted extra-small fw-bold text-uppercase tracking-wider">
            Navigation Menu
        </div>
        <ul class="nav flex-column px-2">
            <?php if ($user_role === 'farmer'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="<?= $app_url ?>/farmer/dashboard.php">
                        <i class="bi bi-grid-1x2 fs-5"></i>
                        <span>Overview</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'listings.php' ? 'active' : '' ?>" href="<?= $app_url ?>/farmer/listings.php">
                        <i class="bi bi-box-seam fs-5"></i>
                        <span>Harvest Listings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'orders.php' ? 'active' : '' ?>" href="<?= $app_url ?>/farmer/orders.php">
                        <i class="bi bi-cart-check fs-5"></i>
                        <span>Order Matches</span>
                    </a>
                </li>
            <?php elseif ($user_role === 'business'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="<?= $app_url ?>/business/dashboard.php">
                        <i class="bi bi-grid-1x2 fs-5"></i>
                        <span>Overview</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'requests.php' ? 'active' : '' ?>" href="<?= $app_url ?>/business/requests.php">
                        <i class="bi bi-bag-plus fs-5"></i>
                        <span>Pre-Orders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'matches.php' ? 'active' : '' ?>" href="<?= $app_url ?>/business/matches.php">
                        <i class="bi bi-shop-window fs-5"></i>
                        <span>Matched Produce</span>
                    </a>
                </li>
            <?php elseif ($user_role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="<?= $app_url ?>/admin/dashboard.php">
                        <i class="bi bi-speedometer2 fs-5"></i>
                        <span>Admin Overview</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'users.php' ? 'active' : '' ?>" href="<?= $app_url ?>/admin/users.php">
                        <i class="bi bi-people fs-5"></i>
                        <span>User Directory</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'listings.php' ? 'active' : '' ?>" href="<?= $app_url ?>/admin/listings.php">
                        <i class="bi bi-boxes fs-5"></i>
                        <span>Listings Audit</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'orders.php' ? 'active' : '' ?>" href="<?= $app_url ?>/admin/orders.php">
                        <i class="bi bi-receipt fs-5"></i>
                        <span>Orders Audit</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <hr class="mx-3 my-3 text-muted opacity-25">

        <div class="px-3">
            <div class="p-3 bg-light rounded-3 text-center border">
                <div class="fw-bold text-dark extra-small mb-1"><i class="bi bi-shield-check text-primary me-1"></i>AgriSync System</div>
                <div class="text-muted extra-small">Sri Lanka B2B Supply Network</div>
            </div>
        </div>
    </div>
</nav>
