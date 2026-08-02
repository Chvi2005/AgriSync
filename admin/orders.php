<?php
// AgriSync Admin Orders Audit Page (TASK-012)
$page_title = 'Orders Audit';
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
            <h2 class="fw-bold text-dark mb-1">System-Wide Pre-Orders Audit</h2>
            <p class="text-muted small mb-0">Audit all commercial buyer pre-order requests across all Sri Lankan districts</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-receipt text-primary me-2"></i>All Registered Pre-Orders</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Request ID</th>
                            <th>Buyer & District</th>
                            <th>Crop Type</th>
                            <th>Yield Volume (kg)</th>
                            <th>Max Price / kg</th>
                            <th>Target Date</th>
                            <th>Urgency</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="adminOrdersTbody">
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading platform pre-orders...
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
    const tbody = document.getElementById('adminOrdersTbody');
    try {
        const res = await fetch('<?= APP_URL ?>/api/admin.php?action=get_all_orders');
        const data = await res.json();

        if (data.success) {
            const orders = data.data.orders || [];
            if (orders.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">No pre-orders recorded in the system yet.</td></tr>`;
                return;
            }

            tbody.innerHTML = orders.map(req => `
                <tr>
                    <td class="ps-4 text-muted">#OR-${req.id}</td>
                    <td>
                        <div class="fw-bold text-dark">${req.business_name}</div>
                        <div class="text-muted extra-small"><i class="bi bi-geo-alt me-1"></i>${req.business_district || 'N/A'}</div>
                    </td>
                    <td class="fw-bold text-dark">${req.crop_type}</td>
                    <td>${parseFloat(req.quantity_kg).toLocaleString()} kg</td>
                    <td>Rs. ${parseFloat(req.max_price).toFixed(2)}</td>
                    <td>${req.delivery_date}</td>
                    <td><span class="badge bg-light text-dark border text-capitalize extra-small">${req.urgency}</span></td>
                    <td><span class="badge ${getStatusBadgeClass(req.status)}">${req.status}</span></td>
                </tr>
            `).join('');
        }
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Failed to load platform pre-orders.</td></tr>`;
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
