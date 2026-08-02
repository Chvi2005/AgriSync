<?php
// AgriSync Admin Listings Audit Page (TASK-012)
$page_title = 'Listings Audit';
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
            <h2 class="fw-bold text-dark mb-1">System-Wide Harvest Listings Audit</h2>
            <p class="text-muted small mb-0">Audit all farmer crop yield listings across all Sri Lankan districts</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-boxes text-primary me-2"></i>All Registered Harvest Listings</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Listing ID</th>
                            <th>Farmer & District</th>
                            <th>Crop Type</th>
                            <th>Yield Volume (kg)</th>
                            <th>Price / kg</th>
                            <th>Harvest Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="adminListingsTbody">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading platform listings...
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
    const tbody = document.getElementById('adminListingsTbody');
    try {
        const res = await fetch('<?= APP_URL ?>/api/admin.php?action=get_all_listings');
        const data = await res.json();

        if (data.success) {
            const listings = data.data.listings || [];
            if (listings.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No harvest listings recorded in the system yet.</td></tr>`;
                return;
            }

            tbody.innerHTML = listings.map(item => `
                <tr>
                    <td class="ps-4 text-muted">#HL-${item.id}</td>
                    <td>
                        <div class="fw-bold text-dark">${item.farmer_name}</div>
                        <div class="text-muted extra-small"><i class="bi bi-geo-alt me-1"></i>${item.farmer_district || 'N/A'}</div>
                    </td>
                    <td class="fw-bold text-dark">${item.crop_type}</td>
                    <td>${parseFloat(item.quantity_kg).toLocaleString()} kg</td>
                    <td>Rs. ${parseFloat(item.price_per_kg).toFixed(2)}</td>
                    <td>${item.harvest_date}</td>
                    <td><span class="badge ${getStatusBadgeClass(item.status)}">${item.status}</span></td>
                </tr>
            `).join('');
        }
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load platform listings.</td></tr>`;
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
