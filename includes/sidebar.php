<?php
/**
 * AgriSync — Unified Role-Based Sidebar Navigation Component
 * Provides responsive sidebar navigation for Admin, Farmer, and Business portals.
 */

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/constants.php';
}

$current_role = $_SESSION['user_role'] ?? 'guest';
$user_name = $_SESSION['user_name'] ?? 'User';
$current_page = basename($_SERVER['PHP_SELF']);
$app_url = defined('APP_URL') ? APP_URL : '';

$nav_items = [];

if ($current_role === 'admin') {
    $nav_items = [
        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'url' => $app_url . '/admin/dashboard.php', 'file' => 'dashboard.php'],
        ['label' => 'User Management', 'icon' => 'bi-people-fill', 'url' => $app_url . '/admin/users.php', 'file' => 'users.php'],
        ['label' => 'Order Management', 'icon' => 'bi-bag-check-fill', 'url' => $app_url . '/admin/orders.php', 'file' => 'orders.php'],
        ['label' => 'AI Agent Monitor', 'icon' => 'bi-cpu-fill', 'url' => $app_url . '/admin/agent_logs.php', 'file' => 'agent_logs.php', 'badge' => 'Live AI'],
        ['label' => 'SDG Impact', 'icon' => 'bi-globe-americas', 'url' => $app_url . '/admin/sdg_impact.php', 'file' => 'sdg_impact.php'],
        ['label' => 'Settings', 'icon' => 'bi-gear-fill', 'url' => $app_url . '/admin/settings.php', 'file' => 'settings.php'],
    ];
} elseif ($current_role === 'farmer') {
    $nav_items = [
        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'url' => $app_url . '/farmer/dashboard.php', 'file' => 'dashboard.php'],
        ['label' => 'My Harvests', 'icon' => 'bi-flower1', 'url' => $app_url . '/farmer/harvests.php', 'file' => 'harvests.php'],
        ['label' => 'AI Insights & Demand', 'icon' => 'bi-graph-up-arrow', 'url' => $app_url . '/farmer/ai_insights.php', 'file' => 'ai_insights.php', 'badge' => 'AI'],
        ['label' => 'Buyer Offers', 'icon' => 'bi-receipt-cutoff', 'url' => $app_url . '/farmer/offers.php', 'file' => 'offers.php'],
        ['label' => 'My Profile', 'icon' => 'bi-person-circle', 'url' => $app_url . '/farmer/profile.php', 'file' => 'profile.php'],
    ];
} elseif ($current_role === 'business') {
    $nav_items = [
        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'url' => $app_url . '/business/dashboard.php', 'file' => 'dashboard.php'],
        ['label' => 'My Orders', 'icon' => 'bi-cart-check-fill', 'url' => $app_url . '/business/orders.php', 'file' => 'orders.php'],
        ['label' => 'Produce Market', 'icon' => 'bi-shop', 'url' => $app_url . '/business/discover.php', 'file' => 'discover.php'],
        ['label' => 'AI Matches', 'icon' => 'bi-magic', 'url' => $app_url . '/business/matches.php', 'file' => 'matches.php', 'badge' => 'AI'],
        ['label' => 'Company Profile', 'icon' => 'bi-building', 'url' => $app_url . '/business/profile.php', 'file' => 'profile.php'],
    ];
}
?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-white border-end shadow-sm" style="width: 260px; min-height: 100vh;">
    <!-- Brand Header -->
    <a href="<?= $app_url ?>/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none px-2">
        <span class="fs-4 fw-bold text-primary d-flex align-items-center">
            <i class="bi bi-tree-fill me-2 fs-3 text-success"></i> <?= APP_NAME ?>
        </span>
    </a>
    
    <hr class="my-3 text-muted">

    <!-- User Profile Summary -->
    <div class="px-2 py-2 mb-3 rounded-3 bg-light d-flex align-items-center">
        <div class="avatar rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 38px; height: 38px;">
            <?= strtoupper(substr($user_name, 0, 1)) ?>
        </div>
        <div class="overflow-hidden">
            <div class="fw-semibold text-truncate small"><?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?></div>
            <span class="badge bg-secondary text-capitalize" style="font-size: 0.7rem;"><?= htmlspecialchars($current_role, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>

    <!-- Navigation List -->
    <ul class="nav nav-pills flex-column mb-auto gap-1">
        <?php foreach ($nav_items as $item): ?>
            <?php $is_active = ($current_page === $item['file']); ?>
            <li class="nav-item">
                <a href="<?= $item['url'] ?>" class="nav-link d-flex align-items-center justify-content-between py-2 px-3 rounded-3 <?= $is_active ? 'active bg-primary text-white shadow-sm' : 'text-dark hover-light' ?>">
                    <div class="d-flex align-items-center">
                        <i class="bi <?= $item['icon'] ?> me-2 fs-5"></i>
                        <span><?= $item['label'] ?></span>
                    </div>
                    <?php if (!empty($item['badge'])): ?>
                        <span class="badge rounded-pill <?= $is_active ? 'bg-light text-primary' : 'bg-success-subtle text-success' ?> small" style="font-size: 0.65rem;">
                            <?= $item['badge'] ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <hr class="my-3 text-muted">

    <!-- Bottom Actions -->
    <div class="dropdown px-2">
        <a href="<?= $app_url ?>/auth/logout.php" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center py-2 rounded-3">
            <i class="bi bi-box-arrow-right me-2"></i> Sign Out
        </a>
    </div>
</div>
