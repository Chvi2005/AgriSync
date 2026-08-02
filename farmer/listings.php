<?php
// AgriSync Farmer Harvest Listings Manager (TASK-010)
$page_title = 'My Harvest Listings';
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
            <h2 class="fw-bold text-dark mb-1">Harvest Yield Manager</h2>
            <p class="text-muted small mb-0">List your upcoming crop yields for automated AI business matching</p>
        </div>
        <div class="mt-3 mt-md-0">
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addListingModal">
                <i class="bi bi-plus-lg"></i>
                <span>Add Harvest Listing</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar & Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-box-seam text-primary me-2"></i>My Listed Crops</h5>
            <div class="d-flex gap-2">
                <select id="statusFilter" class="form-select form-select-sm w-auto">
                    <option value="all">All Statuses</option>
                    <option value="available">Available</option>
                    <option value="matched">Matched</option>
                    <option value="sold">Sold / Fulfilled</option>
                </select>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Listing ID</th>
                            <th>Crop Type</th>
                            <th>Yield Quantity (kg)</th>
                            <th>Price / kg (LKR)</th>
                            <th>Expected Harvest Date</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="listingsTbody">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading harvest listings...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Harvest Listing Modal -->
<div class="modal fade" id="addListingModal" tabindex="-1" aria-labelledby="addListingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addListingModalLabel"><i class="bi bi-plus-circle text-primary me-2"></i>Add New Harvest Listing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addListingForm" method="POST">
                <div class="modal-body py-4">
                    <div id="modalAlert" class="alert alert-danger d-none mb-3"></div>

                    <div class="mb-3">
                        <label for="crop_type" class="form-label fw-medium text-secondary">Crop Type</label>
                        <select class="form-select" id="crop_type" name="crop_type" required>
                            <option value="" disabled selected>Select Crop Type</option>
                            <option value="Carrot">Carrot</option>
                            <option value="Leek">Leek</option>
                            <option value="Tomato">Tomato</option>
                            <option value="Potato">Potato</option>
                            <option value="Cabbage">Cabbage</option>
                            <option value="Onion">Onion</option>
                            <option value="Green Chilli">Green Chilli</option>
                            <option value="Paddy">Paddy / Rice</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="quantity_kg" class="form-label fw-medium text-secondary">Quantity (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="quantity_kg" name="quantity_kg" placeholder="e.g. 500" required min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="price_per_kg" class="form-label fw-medium text-secondary">Price per kg (LKR)</label>
                            <input type="number" step="0.01" class="form-control" id="price_per_kg" name="price_per_kg" placeholder="e.g. 180.00" required min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="harvest_date" class="form-label fw-medium text-secondary">Expected Harvest Date</label>
                        <input type="date" class="form-control" id="harvest_date" name="harvest_date" required min="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveListingBtn" class="btn btn-primary px-4 fw-semibold">Save Listing</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const listingsTbody = document.getElementById('listingsTbody');
    const statusFilter = document.getElementById('statusFilter');
    const addListingForm = document.getElementById('addListingForm');
    const saveListingBtn = document.getElementById('saveListingBtn');
    const modalAlert = document.getElementById('modalAlert');

    async function loadListings() {
        const status = statusFilter.value;
        try {
            const res = await fetch(`<?= APP_URL ?>/api/farmer.php?action=get_listings&status=${status}`);
            const data = await res.json();

            if (data.success) {
                const listings = data.data.listings || [];
                if (listings.length === 0) {
                    listingsTbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No harvest listings match the selected filter.</td></tr>`;
                    return;
                }

                listingsTbody.innerHTML = listings.map(item => `
                    <tr>
                        <td class="ps-4 text-muted">#HL-${item.id}</td>
                        <td class="fw-bold text-dark">${item.crop_type}</td>
                        <td>${parseFloat(item.quantity_kg).toLocaleString()} kg</td>
                        <td>Rs. ${parseFloat(item.price_per_kg).toFixed(2)}</td>
                        <td>${item.harvest_date}</td>
                        <td><span class="badge ${getStatusBadgeClass(item.status)}">${item.status}</span></td>
                        <td class="pe-4 text-end">
                            ${item.status === 'available' ? `
                                <button type="button" class="btn btn-sm btn-outline-success me-1" onclick="markAsSold(${item.id})">
                                    <i class="bi bi-check2-circle me-1"></i> Mark Sold
                                </button>
                            ` : ''}
                        </td>
                    </tr>
                `).join('');
            }
        } catch (err) {
            listingsTbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load harvest listings.</td></tr>`;
        }
    }

    statusFilter.addEventListener('change', loadListings);

    addListingForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        modalAlert.classList.add('d-none');
        saveListingBtn.disabled = true;

        try {
            const formData = new FormData(addListingForm);
            const res = await fetch('<?= APP_URL ?>/api/farmer.php?action=create_listing', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '<?= generateCSRFToken() ?>' }
            });

            const data = await res.json();
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addListingModal')).hide();
                addListingForm.reset();
                if (window.AgriSync && window.AgriSync.showToast) {
                    window.AgriSync.showToast('Harvest yield listed successfully!', 'success');
                }
                loadListings();
            } else {
                modalAlert.textContent = data.error || 'Failed to save listing.';
                modalAlert.classList.remove('d-none');
            }
        } catch (err) {
            modalAlert.textContent = 'Network error while saving listing.';
            modalAlert.classList.remove('d-none');
        } finally {
            saveListingBtn.disabled = false;
        }
    });

    window.markAsSold = async (id) => {
        if (!confirm('Mark this harvest listing as sold?')) return;
        try {
            const formData = new FormData();
            formData.append('listing_id', id);
            formData.append('status', 'sold');
            formData.append('csrf_token', '<?= generateCSRFToken() ?>');

            const res = await fetch('<?= APP_URL ?>/api/farmer.php?action=update_status', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                if (window.AgriSync && window.AgriSync.showToast) {
                    window.AgriSync.showToast('Listing marked as sold!', 'success');
                }
                loadListings();
            }
        } catch (err) {}
    };

    loadListings();
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
