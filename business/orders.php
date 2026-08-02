<?php
/**
 * AgriSync — Commercial Buyer Order Tracking (TASK-046 / Issue #33)
 * Full lifecycle tracking for wholesale orders, match statuses, and scheduled deliveries.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

// Strict Business Access Control
requireRole('business');

$page_title = 'Order Tracking';
$user_id = (int) ($_SESSION['user_id'] ?? 0);
$status_filter = sanitize($_GET['status'] ?? 'all');

$orders = [];

try {
    $db = getDbConnection();

    $sql = "
        SELECT 
            o.id, o.crop_type, o.quantity_kg, o.max_price, o.delivery_date, o.urgency, o.status, o.notes, o.created_at,
            m.id as match_id, m.matched_price, m.confidence_score, m.status as match_status,
            u.name as farmer_name, u.district as farmer_district
        FROM order_requests o
        LEFT JOIN order_matches m ON o.id = m.order_id
        LEFT JOIN users u ON m.farmer_id = u.id
        WHERE o.business_id = :business_id
    ";
    $params = [':business_id' => $user_id];

    if ($status_filter !== 'all' && in_array($status_filter, ['pending', 'matching', 'matched', 'fulfilled', 'cancelled'])) {
        $sql .= " AND o.status = :status";
        $params[':status'] = $status_filter;
    }

    $sql .= " ORDER BY o.id DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    error_log("Orders Page Error: " . $e->getMessage());
    $error_message = "Unable to fetch orders.";
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex" style="min-height: 100vh;">
    <!-- Role-based Sidebar Navigation -->
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-grow-1 bg-light p-4 overflow-auto">
        <div class="container-fluid max-w-7xl">
            
            <!-- Header -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-card-checklist text-success me-2"></i> Order Tracking & History
                    </h1>
                    <p class="text-muted small mb-0">
                        Monitor active procurement orders, AI match status, and fulfillment updates.
                    </p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="place_order.php" class="btn btn-primary rounded-3 d-flex align-items-center shadow-sm">
                        <i class="bi bi-cart-plus-fill me-1"></i> Place New Order
                    </a>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="card border-0 shadow-sm rounded-4 p-2 mb-4 bg-white">
                <ul class="nav nav-pills nav-fill small fw-semibold">
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter === 'all' ? 'active bg-primary' : 'text-dark' ?>" href="orders.php?status=all">All Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter === 'pending' ? 'active bg-primary' : 'text-dark' ?>" href="orders.php?status=pending">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter === 'matched' ? 'active bg-primary' : 'text-dark' ?>" href="orders.php?status=matched">AI Matched</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter === 'fulfilled' ? 'active bg-primary' : 'text-dark' ?>" href="orders.php?status=fulfilled">Fulfilled</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter === 'cancelled' ? 'active bg-primary' : 'text-dark' ?>" href="orders.php?status=cancelled">Cancelled</a>
                    </li>
                </ul>
            </div>

            <!-- Orders Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Order ID</th>
                                <th>Produce</th>
                                <th>Volume (kg)</th>
                                <th>Price Cap / Matched</th>
                                <th>Delivery Target</th>
                                <th>Status</th>
                                <th>AI Matched Producer</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-box2 fs-1 text-muted d-block mb-2"></i>
                                        No commercial orders found for the selected filter.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $o): ?>
                                    <?php
                                        $st = $o['status'];
                                        $badge = 'bg-secondary-subtle text-secondary';
                                        if ($st === 'matching') $badge = 'bg-warning-subtle text-warning';
                                        if ($st === 'matched') $badge = 'bg-success-subtle text-success';
                                        if ($st === 'fulfilled') $badge = 'bg-info-subtle text-info';
                                        if ($st === 'cancelled') $badge = 'bg-danger-subtle text-danger';
                                    ?>
                                    <tr>
                                        <td class="fw-semibold text-muted">#ORD-<?= (int)$o['id'] ?></td>
                                        <td>
                                            <span class="fw-bold text-dark"><?= htmlspecialchars($o['crop_type'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php if (!empty($o['notes'])): ?>
                                                <small class="text-muted d-block" style="font-size: 0.75rem;"><?= htmlspecialchars($o['notes'], ENT_QUOTES, 'UTF-8') ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?= number_format($o['quantity_kg'], 1) ?></strong> kg</td>
                                        <td>
                                            <?php if (!empty($o['matched_price'])): ?>
                                                <span class="text-success fw-bold">Rs. <?= number_format($o['matched_price'], 2) ?></span>
                                                <small class="text-muted d-block">(Cap: <?= number_format($o['max_price'], 2) ?>)</small>
                                            <?php else: ?>
                                                <span>Rs. <?= number_format($o['max_price'], 2) ?> cap</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted">
                                            <i class="bi bi-calendar3 me-1"></i> <?= htmlspecialchars($o['delivery_date'], ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill <?= $badge ?> px-2 py-1 text-capitalize">
                                                <?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($o['farmer_name'])): ?>
                                                <span class="fw-semibold text-dark small d-block"><?= htmlspecialchars($o['farmer_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                                <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($o['farmer_district'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic">Awaiting match</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?php if (!empty($o['match_id'])): ?>
                                                <a href="matches.php?order_id=<?= (int)$o['id'] ?>" class="btn btn-sm btn-outline-success rounded-3 px-2 py-1">
                                                    <i class="bi bi-robot"></i> Review Match
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted border">In Queue</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
