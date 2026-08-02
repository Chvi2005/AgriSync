<?php
// AgriSync Shared Dashboard Sidebar Component (TASK-013 / M3 Role-Based Navigation)
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

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse shadow-sm">
    <div class="position-sticky pt-3">
        <div class="px-3 mb-2 sidebar-header text-uppercase">
            <?= htmlspecialchars(ucfirst((string)$user_role), ENT_QUOTES, 'UTF-8') ?> Portal
        </div>
        
        <ul class="nav flex-column px-2">
            <?php if ($user_role === 'farmer'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="<?= $app_url ?>/farmer/dashboard.php">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'listings.php' ? 'active' : '' ?>" href="<?= $app_url ?>/farmer/listings.php">
                        <i class="bi bi-box-seam"></i>
                        <span>My Listings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'orders.php' ? 'active' : '' ?>" href="<?= $app_url ?>/farmer/orders.php">
                        <i class="bi bi-tags"></i>
                        <span>Incoming Offers</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'orders.php' ? 'active' : '' ?>" href="<?= $app_url ?>/farmer/orders.php">
                        <i class="bi bi-cart-check"></i>
                        <span>My Orders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $app_url ?>/farmer/dashboard.php#ai-insights">
                        <i class="bi bi-lightbulb"></i>
                        <span>AI Insights</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-target="#notifDropdownBtn">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-target="#userProfileDropdown">
                        <i class="bi bi-person-circle"></i>
                        <span>Profile</span>
                    </a>
                </li>

            <?php elseif ($user_role === 'business'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="<?= $app_url ?>/business/dashboard.php">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'requests.php' ? 'active' : '' ?>" href="<?= $app_url ?>/business/requests.php">
                        <i class="bi bi-search"></i>
                        <span>Browse Produce</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'matches.php' ? 'active' : '' ?>" href="<?= $app_url ?>/business/matches.php">
                        <i class="bi bi-bag-check"></i>
                        <span>My Orders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'matches.php' ? 'active' : '' ?>" href="<?= $app_url ?>/business/matches.php">
                        <i class="bi bi-cpu"></i>
                        <span>AI Matches</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-target="#notifDropdownBtn">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-target="#userProfileDropdown">
                        <i class="bi bi-person-circle"></i>
                        <span>Profile</span>
                    </a>
                </li>

            <?php elseif ($user_role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="<?= $app_url ?>/admin/dashboard.php">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'users.php' ? 'active' : '' ?>" href="<?= $app_url ?>/admin/users.php">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'orders.php' ? 'active' : '' ?>" href="<?= $app_url ?>/admin/orders.php">
                        <i class="bi bi-receipt"></i>
                        <span>Orders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $app_url ?>/admin/dashboard.php#agent-monitor">
                        <i class="bi bi-robot"></i>
                        <span>AI Agent Monitor</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $app_url ?>/admin/dashboard.php#analytics">
                        <i class="bi bi-graph-up"></i>
                        <span>Analytics</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $app_url ?>/admin/dashboard.php#settings">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <hr class="mx-3 my-3 text-white-50 opacity-25">

        <div class="px-3 pb-3">
            <div class="p-3 bg-black bg-opacity-25 rounded-3 text-center border border-white-50 border-opacity-10">
                <div class="fw-bold text-white extra-small mb-1"><i class="bi bi-sprout text-accent me-1"></i>AgriSync Sri Lanka</div>
                <div class="text-white-50 extra-small">Smart B2B Agritech</div>
            </div>
        </div>
    </div>
</nav>
