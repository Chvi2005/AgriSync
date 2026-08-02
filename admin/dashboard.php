<?php
// AgriSync Admin Overview Dashboard Page (TASK-012)
$page_title = 'Admin Overview';
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
            <h2 class="fw-bold text-dark mb-1">AgriSync System Administration</h2>
            <p class="text-muted small mb-0">Platform overview, user activity monitoring, and automated transaction audit logs</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="<?= APP_URL ?>/admin/users.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-people"></i>
                <span>Manage Users</span>
            </a>
            <a href="<?= APP_URL ?>/admin/listings.php" class="btn btn-outline-primary d-flex align-items-center gap-2">
                <i class="bi bi-boxes"></i>
                <span>Audit Listings</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Row (4 per row desktop, 2 tablet, 1 mobile) -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-card-label">Registered Farmers</span>
                        <h3 class="stat-card-value" id="metricFarmers">--</h3>
                        <div class="stat-card-trend trend-up"><i class="bi bi-arrow-up-right"></i> Active Accounts</div>
                    </div>
                    <div class="stat-card-icon-wrapper">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-success">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-card-label">Business Buyers</span>
                        <h3 class="stat-card-value" id="metricBusiness">--</h3>
                        <div class="stat-card-trend trend-up"><i class="bi bi-arrow-up-right"></i> Active Accounts</div>
                    </div>
                    <div class="stat-card-icon-wrapper">
                        <i class="bi bi-shop"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-info">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-card-label">Total Listed Yield (kg)</span>
                        <h3 class="stat-card-value" id="metricVolumeKg">-- kg</h3>
                        <div class="stat-card-trend trend-up"><i class="bi bi-arrow-up-right"></i> Platform Capacity</div>
                    </div>
                    <div class="stat-card-icon-wrapper">
                        <i class="bi bi-bar-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-card-label">Fulfilled Trade Value</span>
                        <h3 class="stat-card-value" id="metricTradeValue">Rs. 0.00</h3>
                        <div class="stat-card-trend trend-up"><i class="bi bi-arrow-up-right"></i> Settled Trade</div>
                    </div>
                    <div class="stat-card-icon-wrapper">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics & System Logs Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart text-primary me-2"></i>User Base Distribution</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <canvas id="userDistChart" style="max-height: 260px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-terminal text-primary me-2"></i>System Transaction & Agent Logs</h5>
                    <span class="badge bg-success-subtle text-success border border-success extra-small">Live Sync</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light extra-small">
                                <tr>
                                    <th class="ps-4">Log ID</th>
                                    <th>Agent Module</th>
                                    <th>Action Summary</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody id="adminLogsTbody" class="extra-small">
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading system audit logs...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const res = await fetch('<?= APP_URL ?>/api/admin.php?action=get_metrics');
        const result = await res.json();

        if (result.success) {
            const m = result.data.metrics;
            document.getElementById('metricFarmers').textContent = m.farmers_count;
            document.getElementById('metricBusiness').textContent = m.business_count;
            document.getElementById('metricVolumeKg').textContent = m.total_volume_kg.toLocaleString() + ' kg';
            document.getElementById('metricTradeValue').textContent = 'Rs. ' + m.fulfilled_value.toLocaleString('en-US', {minimumFractionDigits: 2});

            // Render User Chart
            const ctx = document.getElementById('userDistChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Farmers', 'Business Buyers'],
                    datasets: [{
                        data: [m.farmers_count, m.business_count],
                        backgroundColor: ['#2D6A4F', '#0DCAF0']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            // Render Logs Table
            const logsTbody = document.getElementById('adminLogsTbody');
            const logs = result.data.recent_logs || [];

            if (logs.length === 0) {
                logsTbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted">No system logs recorded yet.</td></tr>`;
            } else {
                logsTbody.innerHTML = logs.map(log => `
                    <tr>
                        <td class="ps-4 text-muted">#LOG-${log.id}</td>
                        <td><span class="badge bg-primary-subtle text-primary text-uppercase">${log.agent_type}</span></td>
                        <td class="text-dark fw-medium">${log.action_step}</td>
                        <td class="text-muted">${log.created_at}</td>
                    </tr>
                `).join('');
            }
        }
    } catch (err) {
        console.error(err);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
