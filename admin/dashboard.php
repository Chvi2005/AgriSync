<?php
/**
 * AgriSync — Admin Overview Dashboard Page (TASK-083 / Issue #58)
 * Platform overview, user distribution analytics, AI Broker execution audits, and quick management controls.
 */

$page_title = 'System Administration Dashboard';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../auth/auth_check.php';
checkRole(['admin']);

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
                    <h2 class="fw-bold text-dark mb-1">AgriSync System Administration</h2>
                    <p class="text-muted small mb-0">Platform overview, user activity monitoring, and automated transaction audit logs</p>
                </div>
                <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2">
                    <a href="<?= APP_URL ?>/admin/users.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-people"></i>
                        <span>Manage Users</span>
                    </a>
                    <a href="<?= APP_URL ?>/admin/orders.php" class="btn btn-outline-primary d-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-receipt"></i>
                        <span>Manage Orders</span>
                    </a>
                    <a href="<?= APP_URL ?>/admin/agent_logs.php" class="btn btn-outline-warning d-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-robot"></i>
                        <span>AI Agent Monitor</span>
                    </a>
                </div>
            </div>

            <!-- Summary Metrics Row -->
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
                                <div class="stat-card-trend trend-up"><i class="bi bi-arrow-up-right"></i> Commercial Buyers</div>
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
                                <span class="stat-card-label">Total Listed Yield</span>
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
                                <span class="stat-card-label">Settled Trade Value</span>
                                <h3 class="stat-card-value" id="metricTradeValue">Rs. 0.00</h3>
                                <div class="stat-card-trend trend-up"><i class="bi bi-arrow-up-right"></i> Completed Transactions</div>
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
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart text-primary me-2"></i>User Base Distribution</h5>
                        </div>
                        <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                            <div style="height: 240px; position: relative;">
                                <canvas id="userDistChart"></canvas>
                            </div>
                            <div class="d-flex justify-content-around mt-3 border-top pt-3 extra-small">
                                <div><span class="badge bg-primary me-1">&nbsp;</span> Farmers: <strong id="chartFarmerCount">0</strong></div>
                                <div><span class="badge bg-info me-1">&nbsp;</span> Buyers: <strong id="chartBusinessCount">0</strong></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-cpu text-warning me-2"></i>Live AI Broker & System Logs</h5>
                            <a href="<?= APP_URL ?>/admin/agent_logs.php" class="btn btn-sm btn-outline-primary rounded-pill px-3 extra-small">
                                View All Logs <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light extra-small">
                                        <tr>
                                            <th class="ps-4">Log ID</th>
                                            <th>Agent</th>
                                            <th>Step / Action</th>
                                            <th class="pe-4 text-end">Timestamp</th>
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

            <!-- Quick Management Shortcuts Row -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 p-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-grid-fill text-primary me-2"></i>Platform Administration Modules</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="<?= APP_URL ?>/admin/users.php" class="text-decoration-none">
                                    <div class="p-3 bg-light rounded-3 hover-lift border text-dark">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar rounded-circle bg-primary-subtle text-primary p-2 fs-4">
                                                <i class="bi bi-people-fill"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">User Directory</div>
                                                <div class="text-muted extra-small">Farmers, Buyers & Admins</div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= APP_URL ?>/admin/orders.php" class="text-decoration-none">
                                    <div class="p-3 bg-light rounded-3 hover-lift border text-dark">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar rounded-circle bg-success-subtle text-success p-2 fs-4">
                                                <i class="bi bi-receipt-cutoff"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">Order Management</div>
                                                <div class="text-muted extra-small">Search, Filter & Audit</div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= APP_URL ?>/admin/agent_logs.php" class="text-decoration-none">
                                    <div class="p-3 bg-light rounded-3 hover-lift border text-dark">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar rounded-circle bg-warning-subtle text-warning p-2 fs-4">
                                                <i class="bi bi-cpu-fill"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">AI Agent Monitor</div>
                                                <div class="text-muted extra-small">Gemini Logs & Reasoning</div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= APP_URL ?>/admin/sdg_impact.php" class="text-decoration-none">
                                    <div class="p-3 bg-light rounded-3 hover-lift border text-dark">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar rounded-circle bg-info-subtle text-info p-2 fs-4">
                                                <i class="bi bi-globe-americas"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">SDG Impact</div>
                                                <div class="text-muted extra-small">Food Loss & Farmer Margins</div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
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

            document.getElementById('chartFarmerCount').textContent = m.farmers_count;
            document.getElementById('chartBusinessCount').textContent = m.business_count;

            // Render User Chart (Doughnut)
            const ctx = document.getElementById('userDistChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Farmers', 'Commercial Buyers'],
                    datasets: [{
                        data: [m.farmers_count, m.business_count],
                        backgroundColor: ['#2D6A4F', '#0DCAF0'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Render Logs Table
            const logsTbody = document.getElementById('adminLogsTbody');
            const logs = result.data.recent_logs || [];

            if (logs.length === 0) {
                logsTbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted">No system logs recorded yet.</td></tr>`;
            } else {
                logsTbody.innerHTML = logs.slice(0, 7).map(log => `
                    <tr>
                        <td class="ps-4 text-muted fw-bold">#LOG-${log.id}</td>
                        <td><span class="badge bg-primary-subtle text-primary text-uppercase">${log.agent_type}</span></td>
                        <td class="text-dark fw-medium text-truncate" style="max-width: 280px;">${log.action_step}</td>
                        <td class="pe-4 text-end text-muted">${log.created_at}</td>
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
