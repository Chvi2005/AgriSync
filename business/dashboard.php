<?php
// AgriSync Business Buyer Dashboard Page (TASK-011)
$page_title = 'Business Dashboard';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../auth/auth_check.php';
checkRole(['business']);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid px-4 py-4">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">Business Buyer Portal</h2>
            <p class="text-muted small mb-0">Welcome back, <strong><?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></strong> (<?= htmlspecialchars($_SESSION['user_district'], ENT_QUOTES, 'UTF-8') ?> Region)</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="<?= APP_URL ?>/business/requests.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-bag-plus"></i>
                <span>Place Pre-Order Request</span>
            </a>
            <a href="<?= APP_URL ?>/business/matches.php" class="btn btn-outline-primary d-flex align-items-center gap-2">
                <i class="bi bi-shop-window"></i>
                <span>View Matched Produce</span>
            </a>
        </div>
    </div>

    <!-- Metrics Cards (4 per row desktop, 2 tablet, 1 mobile) -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-card-label">Active Pre-Orders</span>
                        <h3 class="stat-card-value" id="metricActiveRequests">--</h3>
                        <div class="stat-card-trend trend-up"><i class="bi bi-arrow-up-right"></i> Active Demand</div>
                    </div>
                    <div class="stat-card-icon-wrapper">
                        <i class="bi bi-bag-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-success">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-card-label">Target Procurement (kg)</span>
                        <h3 class="stat-card-value" id="metricTotalKg">-- kg</h3>
                        <div class="stat-card-trend trend-up"><i class="bi bi-arrow-up-right"></i> Direct Farm</div>
                    </div>
                    <div class="stat-card-icon-wrapper">
                        <i class="bi bi-boxes"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-info">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-card-label">Total Fulfilled Spend</span>
                        <h3 class="stat-card-value" id="metricTotalSpend">Rs. 0.00</h3>
                        <div class="stat-card-trend trend-up"><i class="bi bi-arrow-up-right"></i> Verified</div>
                    </div>
                    <div class="stat-card-icon-wrapper">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-card-label">Confirmed Farmer Matches</span>
                        <h3 class="stat-card-value" id="metricAcceptedMatches">--</h3>
                        <div class="stat-card-trend trend-up"><i class="bi bi-arrow-up-right"></i> AI Matched</div>
                    </div>
                    <div class="stat-card-icon-wrapper">
                        <i class="bi bi-hand-thumbs-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Marketplace Available Crops Preview -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shop text-primary me-2"></i>Live Farmer Harvest Availability</h5>
                <small class="text-muted extra-small">Direct farm listings from Nuwara Eliya, Dambulla & surrounding agricultural hubs</small>
            </div>
            <a href="<?= APP_URL ?>/business/requests.php" class="btn btn-sm btn-outline-primary fw-semibold">Submit Pre-Order</a>
        </div>
        <div class="card-body p-4">
            <div id="marketPreviewRow" class="row g-3">
                <div class="col-12 text-center py-4 text-muted">
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span> Searching live farmer listings...
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Pre-Order Requests -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Recent Pre-Order Requests</h5>
            <a href="<?= APP_URL ?>/business/requests.php" class="text-primary text-decoration-none small fw-semibold">Manage Requests <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Request ID</th>
                            <th>Crop Type</th>
                            <th>Quantity (kg)</th>
                            <th>Max Price / kg</th>
                            <th>Delivery Date</th>
                            <th>Urgency</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="recentRequestsTbody">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading request history...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const res = await fetch('<?= APP_URL ?>/api/business.php?action=get_dashboard');
        const result = await res.json();

        if (result.success) {
            const m = result.data.metrics;
            document.getElementById('metricActiveRequests').textContent = m.active_requests_count;
            document.getElementById('metricTotalKg').textContent = m.active_volume_kg.toLocaleString() + ' kg';
            document.getElementById('metricTotalSpend').textContent = 'Rs. ' + m.total_spend.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('metricAcceptedMatches').textContent = m.accepted_matches;

            // Render Marketplace Grid
            const marketRow = document.getElementById('marketPreviewRow');
            const marketItems = result.data.market_preview || [];

            if (marketItems.length === 0) {
                marketRow.innerHTML = `<div class="col-12 text-center py-3 text-muted">No active farmer harvest listings available right now.</div>`;
            } else {
                marketRow.innerHTML = marketItems.map(item => `
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border border-light-subtle shadow-xs rounded-3 p-3 bg-white">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold text-dark mb-0">${item.crop_type}</h6>
                                <span class="badge bg-success-subtle text-success border border-success extra-small">Available</span>
                            </div>
                            <div class="extra-small text-muted mb-2">
                                <i class="bi bi-person me-1"></i>${item.farmer_name} (<i class="bi bi-geo-alt"></i> ${item.farmer_district})
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                <div>
                                    <span class="text-muted extra-small d-block">Yield Volume</span>
                                    <span class="fw-bold text-dark small">${parseFloat(item.quantity_kg).toLocaleString()} kg</span>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted extra-small d-block">Price / kg</span>
                                    <span class="fw-bold text-success small">Rs. ${parseFloat(item.price_per_kg).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            // Render Recent Requests Table
            const tbody = document.getElementById('recentRequestsTbody');
            const requests = result.data.recent_requests || [];

            if (requests.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No pre-order requests submitted yet. <a href="<?= APP_URL ?>/business/requests.php">Create your first request</a>.</td></tr>`;
            } else {
                tbody.innerHTML = requests.map(req => `
                    <tr>
                        <td class="ps-4 text-muted">#OR-${req.id}</td>
                        <td class="fw-bold text-dark">${req.crop_type}</td>
                        <td>${parseFloat(req.quantity_kg).toLocaleString()} kg</td>
                        <td>Rs. ${parseFloat(req.max_price).toFixed(2)}</td>
                        <td>${req.delivery_date}</td>
                        <td><span class="badge bg-light text-dark border text-capitalize extra-small">${req.urgency}</span></td>
                        <td><span class="badge ${getStatusBadgeClass(req.status)}">${req.status}</span></td>
                    </tr>
                `).join('');
            }
        }
    } catch (err) {
        console.error(err);
    }
});

function getStatusBadgeClass(status) {
    switch (status.toLowerCase()) {
        case 'pending': return 'bg-secondary-subtle text-secondary';
        case 'matching': return 'bg-warning-subtle text-warning border border-warning';
        case 'matched': return 'bg-info-subtle text-info border border-info';
        case 'fulfilled': return 'bg-success-subtle text-success border border-success';
        case 'cancelled': return 'bg-danger-subtle text-danger';
        default: return 'bg-light text-dark';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
