<?php
// AgriSync Farmer Dashboard Page (TASK-010)
$page_title = 'Farmer Dashboard';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../auth/auth_check.php';
checkRole(['farmer']);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid px-4 py-4">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">Farmer Portal Overview</h2>
            <p class="text-muted small mb-0">Welcome back, <strong><?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></strong> (<?= htmlspecialchars($_SESSION['user_district'], ENT_QUOTES, 'UTF-8') ?> District)</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="<?= APP_URL ?>/farmer/listings.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-lg"></i>
                <span>Add New Harvest</span>
            </a>
            <a href="<?= APP_URL ?>/farmer/orders.php" class="btn btn-outline-primary d-flex align-items-center gap-2">
                <i class="bi bi-cart-check"></i>
                <span>View Order Matches</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-semibold d-block">Active Harvest Yield</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1" id="metricActiveListings">--</h3>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-semibold d-block">Total Listed Volume</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1" id="metricTotalKg">-- kg</h3>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle">
                        <i class="bi bi-tree fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-info border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-semibold d-block">Fulfilled Revenue</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1" id="metricEarnings">Rs. 0.00</h3>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-circle">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-semibold d-block">Pending Order Matches</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1" id="metricPendingMatches">--</h3>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                        <i class="bi bi-lightning-charge fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart text-primary me-2"></i>Crop Yield Distribution (kg)</h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="cropChart" style="max-height: 280px;"></canvas>
                    <div id="chartPlaceholder" class="text-center py-5 text-muted d-none">No harvest data listed yet.</div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-cpu text-primary me-2"></i>AI Market Recommendations</h5>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 bg-light-subtle rounded-3 border mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary">RAG Insights</span>
                            <span class="fw-bold text-dark small">Demand Forecast: Carrots & Potatoes</span>
                        </div>
                        <p class="text-muted extra-small mb-0">High demand anticipated in Western Province supermarkets over the next 14 days. Current recommended market price: Rs. 240/kg.</p>
                    </div>

                    <div class="p-3 bg-light-subtle rounded-3 border">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success">Direct Matching</span>
                            <span class="fw-bold text-dark small">Fastest Route Connection</span>
                        </div>
                        <p class="text-muted extra-small mb-0">List harvests 3 days prior to harvesting to ensure automated delivery logistics assignment without middleman overhead.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Harvest Listings Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Recent Harvest Listings</h5>
            <a href="<?= APP_URL ?>/farmer/listings.php" class="text-primary text-decoration-none small fw-semibold">View All <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Crop Type</th>
                            <th>Quantity</th>
                            <th>Price / kg</th>
                            <th>Harvest Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="recentListingsTbody">
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading recent listings...
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
    let cropChartInstance = null;

    try {
        const res = await fetch('<?= APP_URL ?>/api/farmer.php?action=get_dashboard');
        const result = await res.json();

        if (result.success) {
            const m = result.data.metrics;
            document.getElementById('metricActiveListings').textContent = m.active_listings_count;
            document.getElementById('metricTotalKg').textContent = m.active_volume_kg.toLocaleString() + ' kg';
            document.getElementById('metricEarnings').textContent = 'Rs. ' + m.total_earnings.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('metricPendingMatches').textContent = m.pending_matches_count;

            // Render Recent Listings Table
            const tbody = document.getElementById('recentListingsTbody');
            const listings = result.data.recent_listings || [];

            if (listings.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">No harvest listings recorded. <a href="<?= APP_URL ?>/farmer/listings.php">Create your first listing</a>.</td></tr>`;
            } else {
                tbody.innerHTML = listings.map(item => `
                    <tr>
                        <td class="ps-4 fw-bold text-dark">${item.crop_type}</td>
                        <td>${parseFloat(item.quantity_kg).toLocaleString()} kg</td>
                        <td>Rs. ${parseFloat(item.price_per_kg).toFixed(2)}</td>
                        <td>${item.harvest_date}</td>
                        <td><span class="badge ${getStatusBadgeClass(item.status)}">${item.status}</span></td>
                    </tr>
                `).join('');
            }

            // Render Crop Chart
            const cropData = result.data.crop_distribution || [];
            const ctx = document.getElementById('cropChart').getContext('2d');

            if (cropData.length > 0) {
                const labels = cropData.map(c => c.crop_type);
                const dataValues = cropData.map(c => parseFloat(c.total_qty));

                cropChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataValues,
                            backgroundColor: ['#2D6A4F', '#40916C', '#52B788', '#74C69D', '#95D5B2', '#D8F3DC']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            } else {
                document.getElementById('cropChart').classList.add('d-none');
                document.getElementById('chartPlaceholder').classList.remove('d-none');
            }
        }
    } catch (err) {
        console.error(err);
    }
});

function getStatusBadgeClass(status) {
    switch (status.toLowerCase()) {
        case 'available': return 'bg-success-subtle text-success border border-success';
        case 'matched': return 'bg-warning-subtle text-warning border border-warning';
        case 'sold': return 'bg-primary-subtle text-primary border border-primary';
        default: return 'bg-secondary-subtle text-secondary';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
