<?php
/**
 * AgriSync — Commercial Buyer Dashboard (TASK-042 / TASK-011)
 * Procurement command center featuring order trackers, live AI match feeds, and marketplace shortcuts.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

// Strict Business Role Access Control
requireRole('business');

$page_title = 'Commercial Buyer Dashboard';
$user_id = (int) ($_SESSION['user_id'] ?? 0);
$user_name = $_SESSION['user_name'] ?? 'Buyer';

$stats = [
    'active_orders' => 0,
    'ai_matches' => 0,
    'completed_deliveries' => 0,
    'total_kg_sourced' => 0
];
$recent_orders = [];
$recent_matches = [];

try {
    $db = getDbConnection();

    // Summary Metrics
    $oStat = $db->prepare("
        SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN status IN ('pending', 'matching', 'matched') THEN 1 ELSE 0 END) as active_orders,
            SUM(CASE WHEN status = 'fulfilled' THEN 1 ELSE 0 END) as completed_deliveries,
            COALESCE(SUM(CASE WHEN status = 'fulfilled' THEN quantity_kg ELSE 0 END), 0) as total_kg
        FROM order_requests
        WHERE business_id = :business_id
    ");
    $oStat->execute([':business_id' => $user_id]);
    $oRow = $oStat->fetch(PDO::FETCH_ASSOC);
    if ($oRow) {
        $stats['active_orders'] = (int) ($oRow['active_orders'] ?? 0);
        $stats['completed_deliveries'] = (int) ($oRow['completed_deliveries'] ?? 0);
        $stats['total_kg_sourced'] = (float) ($oRow['total_kg'] ?? 0);
    }

    // AI Matches Count
    $mStat = $db->prepare("
        SELECT COUNT(*) as total_matches 
        FROM order_matches 
        WHERE business_id = :business_id AND status IN ('proposed', 'accepted')
    ");
    $mStat->execute([':business_id' => $user_id]);
    $stats['ai_matches'] = (int) ($mStat->fetchColumn() ?? 0);

    // Recent Orders
    $oRecent = $db->prepare("
        SELECT id, crop_type, quantity_kg, max_price, delivery_date, urgency, status, created_at
        FROM order_requests
        WHERE business_id = :business_id
        ORDER BY id DESC LIMIT 5
    ");
    $oRecent->execute([':business_id' => $user_id]);
    $recent_orders = $oRecent->fetchAll(PDO::FETCH_ASSOC);

    // Recent AI Matches
    $mRecent = $db->prepare("
        SELECT 
            m.id as match_id, m.order_id, m.matched_price, m.confidence_score, m.agent_reasoning, m.status as match_status, m.created_at,
            o.crop_type, o.quantity_kg,
            u.name as farmer_name, u.district as farmer_district
        FROM order_matches m
        JOIN order_requests o ON m.order_id = o.id
        JOIN users u ON m.farmer_id = u.id
        WHERE m.business_id = :business_id
        ORDER BY m.id DESC LIMIT 4
    ");
    $mRecent->execute([':business_id' => $user_id]);
    $recent_matches = $mRecent->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    error_log("Business Dashboard Error: " . $e->getMessage());
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="d-flex" style="min-height: 100vh;">
    <!-- Role-based Sidebar Navigation -->
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-grow-1 bg-light p-4 overflow-auto">
        <div class="container-fluid max-w-7xl">
            
            <!-- Dashboard Welcome Header -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        Commercial Procurement Hub
                    </h1>
                    <p class="text-muted small mb-0">
                        Welcome back, <strong><?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?></strong>. Track direct farm procurement, AI matchmaking, and bulk deliveries.
                    </p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="browse.php" class="btn btn-outline-secondary rounded-3 d-flex align-items-center">
                        <i class="bi bi-search me-1"></i> Browse Produce
                    </a>
                    <a href="place_order.php" class="btn btn-primary rounded-3 d-flex align-items-center shadow-sm">
                        <i class="bi bi-cart-plus-fill me-1"></i> Place Commercial Order
                    </a>
                </div>
            </div>

            <!-- Key Metric Cards -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold">Active Orders</span>
                                <h3 class="fw-bold mb-0 text-dark mt-1"><?= number_format($stats['active_orders']) ?></h3>
                            </div>
                            <div class="p-3 rounded-3 bg-primary-subtle text-primary">
                                <i class="bi bi-cart-check fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold">AI Matches Found</span>
                                <h3 class="fw-bold mb-0 text-success mt-1"><?= number_format($stats['ai_matches']) ?></h3>
                            </div>
                            <div class="p-3 rounded-3 bg-success-subtle text-success">
                                <i class="bi bi-robot fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold">Completed Orders</span>
                                <h3 class="fw-bold mb-0 text-info mt-1"><?= number_format($stats['completed_deliveries']) ?></h3>
                            </div>
                            <div class="p-3 rounded-3 bg-info-subtle text-info">
                                <i class="bi bi-truck fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold">Volume Sourced</span>
                                <h3 class="fw-bold mb-0 text-dark mt-1"><?= number_format($stats['total_kg_sourced'], 1) ?> <span class="fs-6 text-muted">kg</span></h3>
                            </div>
                            <div class="p-3 rounded-3 bg-warning-subtle text-warning">
                                <i class="bi bi-box-seam fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <!-- Recent Orders Table -->
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0">Active Procurement Orders</h5>
                            <a href="orders.php" class="text-success small fw-semibold text-decoration-none">View All Orders &rarr;</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Order</th>
                                            <th>Produce</th>
                                            <th>Volume</th>
                                            <th>Max Budget</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recent_orders)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted small">
                                                    No commercial orders placed yet. 
                                                    <div class="mt-2">
                                                        <a href="place_order.php" class="btn btn-sm btn-primary rounded-3">Place First Order</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <?php
                                                    $st = $order['status'];
                                                    $badge = 'bg-secondary-subtle text-secondary';
                                                    if ($st === 'matching') $badge = 'bg-warning-subtle text-warning';
                                                    if ($st === 'matched') $badge = 'bg-success-subtle text-success';
                                                    if ($st === 'fulfilled') $badge = 'bg-info-subtle text-info';
                                                ?>
                                                <tr>
                                                    <td class="fw-semibold small">#ORD-<?= (int)$order['id'] ?></td>
                                                    <td class="fw-bold text-dark"><?= htmlspecialchars($order['crop_type'], ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td class="small"><?= number_format($order['quantity_kg'], 1) ?> kg</td>
                                                    <td class="small">Rs. <?= number_format($order['max_price'], 2) ?>/kg</td>
                                                    <td>
                                                        <span class="badge rounded-pill <?= $badge ?> px-2 py-1 text-capitalize">
                                                            <?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="matches.php?order_id=<?= (int)$order['id'] ?>" class="btn btn-sm btn-outline-success rounded-3 px-2 py-1" title="View AI Match">
                                                            <i class="bi bi-stars"></i> Matches
                                                        </a>
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

                <!-- Live AI Match Recommendations -->
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="bi bi-robot text-success me-1"></i> AI Broker Matches
                            </h5>
                            <a href="matches.php" class="text-success small fw-semibold text-decoration-none">Explore &rarr;</a>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <?php if (empty($recent_matches)): ?>
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-stars fs-2 d-block mb-2 text-warning"></i>
                                    Place an order to trigger our autonomous AI Broker matchmaker.
                                </div>
                            <?php else: ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($recent_matches as $m): ?>
                                        <div class="p-3 bg-light rounded-3 border">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold text-dark"><?= htmlspecialchars($m['crop_type'], ENT_QUOTES, 'UTF-8') ?></span>
                                                <span class="badge bg-success rounded-pill px-2 py-1">
                                                    <?= (int)$m['confidence_score'] ?>% Match
                                                </span>
                                            </div>
                                            <div class="small text-muted mb-2">
                                                <span><i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($m['farmer_name'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($m['farmer_district'], ENT_QUOTES, 'UTF-8') ?>)</span> &bull; 
                                                <strong class="text-success">Rs. <?= number_format($m['matched_price'], 2) ?>/kg</strong>
                                            </div>
                                            <p class="small text-dark fst-italic mb-2 bg-white p-2 rounded border">
                                                "<?= htmlspecialchars(mb_strimwidth($m['agent_reasoning'], 0, 130, '...'), ENT_QUOTES, 'UTF-8') ?>"
                                            </p>
                                            <div class="text-end">
                                                <a href="matches.php?order_id=<?= (int)$m['order_id'] ?>" class="btn btn-sm btn-primary rounded-3 px-3 py-1">
                                                    Review Deal <i class="bi bi-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
