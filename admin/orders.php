<?php
// AgriSync Admin Orders Audit & Management Page (M3 Task)
$page_title = 'System Orders Management';
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
            <!-- Header Banner -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="fw-bold text-dark mb-1">System-Wide Pre-Orders Audit</h2>
                    <p class="text-muted small mb-0">Audit commercial buyer pre-orders, update order statuses, and inspect AI broker logs.</p>
                </div>
                <div class="mt-3 mt-md-0 d-flex gap-2">
                    <a href="<?= APP_URL ?>/admin/agent_logs.php" class="btn btn-outline-primary shadow-sm">
                        <i class="bi bi-cpu"></i>
                        <span>AI Agent Logs</span>
                    </a>
                </div>
            </div>

            <!-- Filters & Search Bar Card -->
            <div class="card border-0 shadow-sm p-3 mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="searchInput" class="form-label extra-small text-muted fw-bold text-uppercase">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search crop, buyer, district, or ID...">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="filterStatus" class="form-label extra-small text-muted fw-bold text-uppercase">Status</label>
                        <select id="filterStatus" class="form-select">
                            <option value="all">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="matching">Matching</option>
                            <option value="matched">Matched</option>
                            <option value="fulfilled">Fulfilled</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filterCrop" class="form-label extra-small text-muted fw-bold text-uppercase">Crop Type</label>
                        <select id="filterCrop" class="form-select">
                            <option value="all">All Crop Types</option>
                            <option value="Carrot">Carrot</option>
                            <option value="Potato">Potato</option>
                            <option value="Leek">Leek</option>
                            <option value="Cabbage">Cabbage</option>
                            <option value="Tomato">Tomato</option>
                            <option value="Onion">Onion</option>
                            <option value="Chilli">Chilli</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="filterDate" class="form-label extra-small text-muted fw-bold text-uppercase">Target Date</label>
                        <input type="date" id="filterDate" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-receipt text-primary me-2"></i>All Platform Orders</h5>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5" id="orderCountBadge">0 Orders</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Order ID</th>
                                    <th>Business Buyer</th>
                                    <th>Crop Type</th>
                                    <th>Volume</th>
                                    <th>Max Price</th>
                                    <th>Target Date</th>
                                    <th>Status</th>
                                    <th>AI Agent Logs</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="adminOrdersTbody">
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading platform pre-orders...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Admin Update Order Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="updateStatusModalLabel"><i class="bi bi-pencil-square me-2"></i>Update Order Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm">
                <div class="modal-body p-4">
                    <input type="hidden" id="modalOrderId" name="order_id">
                    <input type="hidden" id="modalOrderType" name="type" value="request">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Order Reference</label>
                        <input type="text" id="modalOrderRef" class="form-control bg-light" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="modalNewStatus" class="form-label fw-semibold small">Select New Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="modalNewStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="matching">Matching</option>
                            <option value="matched">Matched</option>
                            <option value="fulfilled">Fulfilled</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSaveStatus" class="btn btn-primary shadow-sm">Save Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('adminOrdersTbody');
    const orderCountBadge = document.getElementById('orderCountBadge');
    const searchInput = document.getElementById('searchInput');
    const filterStatus = document.getElementById('filterStatus');
    const filterCrop = document.getElementById('filterCrop');
    const filterDate = document.getElementById('filterDate');

    const updateStatusModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
    const updateStatusForm = document.getElementById('updateStatusForm');

    function getStatusBadgeClass(status) {
        switch(status.toLowerCase()) {
            case 'pending': return 'badge badge-pending';
            case 'matching': return 'badge badge-matching';
            case 'matched': return 'badge badge-matched';
            case 'accepted': return 'badge badge-accepted';
            case 'fulfilled': return 'badge badge-delivered';
            case 'completed': return 'badge badge-delivered';
            case 'cancelled': return 'badge badge-cancelled';
            case 'rejected': return 'badge badge-cancelled';
            default: return 'badge badge-status-secondary';
        }
    }

    async function loadAdminOrders() {
        const query = new URLSearchParams({
            action: 'get_all_orders',
            search: searchInput.value.trim(),
            status: filterStatus.value,
            crop: filterCrop.value,
            date: filterDate.value
        });

        try {
            const res = await fetch(`<?= APP_URL ?>/api/admin.php?${query.toString()}`);
            const data = await res.json();

            if (data.success) {
                const orders = data.data.orders || [];
                orderCountBadge.textContent = `${orders.length} Order${orders.length === 1 ? '' : 's'}`;

                if (orders.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="9" class="p-0">${renderEmptyStateHTML('No orders found', 'Try adjusting your search criteria or filters.', 'bi-receipt')}</td></tr>`;
                    return;
                }

                tbody.innerHTML = orders.map(req => `
                    <tr>
                        <td class="ps-4 text-muted fw-bold">#OR-${req.id}</td>
                        <td>
                            <div class="fw-bold text-dark">${req.business_name}</div>
                            <div class="text-muted extra-small"><i class="bi bi-geo-alt me-1"></i>${req.business_district || 'N/A'}</div>
                        </td>
                        <td class="fw-bold text-dark">${req.crop_type}</td>
                        <td>${parseFloat(req.quantity_kg).toLocaleString()} kg</td>
                        <td>Rs. ${parseFloat(req.max_price).toFixed(2)}</td>
                        <td>${req.delivery_date}</td>
                        <td><span class="${getStatusBadgeClass(req.status)}">${req.status}</span></td>
                        <td>
                            <a href="<?= APP_URL ?>/admin/agent_logs.php?q=${req.id}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5">
                                <i class="bi bi-cpu me-1"></i> Agent Logs
                            </a>
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-primary rounded-2 btn-open-status-modal" 
                                    data-id="${req.id}" 
                                    data-status="${req.status}">
                                <i class="bi bi-pencil-square me-1"></i> Status
                            </button>
                        </td>
                    </tr>
                `).join('');

                // Bind Status Modal Buttons
                document.querySelectorAll('.btn-open-status-modal').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-id');
                        const status = btn.getAttribute('data-status');
                        document.getElementById('modalOrderId').value = id;
                        document.getElementById('modalOrderRef').value = `#OR-${id}`;
                        document.getElementById('modalNewStatus').value = status;
                        updateStatusModal.show();
                    });
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-danger">Failed to load platform pre-orders.</td></tr>`;
            }
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-danger">Error connecting to server.</td></tr>`;
        }
    }

    // Debounced Search & Instant Filtering
    let searchTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadAdminOrders, 300);
    });

    filterStatus.addEventListener('change', loadAdminOrders);
    filterCrop.addEventListener('change', loadAdminOrders);
    filterDate.addEventListener('change', loadAdminOrders);

    // Handle Admin Status Form Submission
    updateStatusForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btnSave = document.getElementById('btnSaveStatus');
        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const formData = new FormData(updateStatusForm);
            formData.append('action', 'update_status');

            const res = await fetch('<?= APP_URL ?>/api/orders.php?action=update_status', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': formData.get('csrf_token') }
            });
            const result = await res.json();

            if (result.success) {
                showToast(result.error || 'Order status updated successfully!', 'success');
                updateStatusModal.hide();
                loadAdminOrders();
            } else {
                showToast(result.error || 'Failed to update order status.', 'error');
            }
        } catch (err) {
            showToast('Server error while updating status.', 'error');
        } finally {
            btnSave.disabled = false;
            btnSave.innerHTML = 'Save Status';
        }
    });

    loadAdminOrders();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
