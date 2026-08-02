<?php
/**
 * AgriSync — Farmer Edit Harvest Listing (TASK-035)
 * Update produce availability, pricing, and harvest dates.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

// Strict Farmer Access Control
requireRole('farmer');

$page_title = 'Edit Harvest Listing';
$user_id = (int) ($_SESSION['user_id'] ?? 0);
$listing_id = (int) ($_GET['id'] ?? 0);

$error = '';
$listing = null;

try {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT * FROM harvest_listings WHERE id = :id AND farmer_id = :farmer_id LIMIT 1");
    $stmt->execute([':id' => $listing_id, ':farmer_id' => $user_id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        $_SESSION['flash_error'] = 'Listing not found or access denied.';
        $app_url = defined('APP_URL') ? APP_URL : '';
        redirect($app_url . '/farmer/listings.php');
    }
} catch (Throwable $e) {
    error_log("Edit Listing Fetch Error: " . $e->getMessage());
    $error = 'Database error fetching listing details.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity_kg = (float) ($_POST['quantity_kg'] ?? 0);
    $price_per_kg = (float) ($_POST['price_per_kg'] ?? 0);
    $harvest_date = trim($_POST['harvest_date'] ?? '');
    $status = trim($_POST['status'] ?? 'available');
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf)) {
        $error = 'Invalid security token. Please refresh and try again.';
    } elseif ($quantity_kg <= 0) {
        $error = 'Quantity must be greater than 0 kg.';
    } elseif ($price_per_kg <= 0) {
        $error = 'Price per kg must be a positive value.';
    } elseif (empty($harvest_date)) {
        $error = 'Please provide a valid harvest date.';
    } elseif (!in_array($status, ['available', 'matched', 'sold'], true)) {
        $error = 'Invalid status selected.';
    } else {
        try {
            $uStmt = $db->prepare("
                UPDATE harvest_listings 
                SET quantity_kg = :quantity_kg,
                    price_per_kg = :price_per_kg,
                    harvest_date = :harvest_date,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id AND farmer_id = :farmer_id
            ");
            $uStmt->execute([
                ':quantity_kg' => $quantity_kg,
                ':price_per_kg' => $price_per_kg,
                ':harvest_date' => $harvest_date,
                ':status' => $status,
                ':id' => $listing_id,
                ':farmer_id' => $user_id
            ]);

            $app_url = defined('APP_URL') ? APP_URL : '';
            redirect($app_url . '/farmer/listings.php?updated=1');
        } catch (Throwable $e) {
            error_log("Update Listing Error: " . $e->getMessage());
            $error = 'Unable to update listing. Please try again.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex" style="min-height: 100vh;">
    <!-- Role-based Sidebar Navigation -->
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-grow-1 bg-light p-4 overflow-auto">
        <div class="container-fluid max-w-4xl">
            
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="dashboard.php" class="text-success text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="listings.php" class="text-success text-decoration-none">My Harvests</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Listing #<?= (int)$listing['id'] ?></li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                    <div class="p-3 rounded-3 bg-primary-subtle text-primary me-3">
                        <i class="bi bi-pencil-square fs-3"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-1">Edit Harvest: <?= htmlspecialchars($listing['crop_type'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="text-muted small mb-0">Modify stock quantity, asking price, or listing status.</p>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger rounded-3 d-flex align-items-center py-2 px-3 small mb-4">
                        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                        <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="edit_listing.php?id=<?= (int)$listing['id'] ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-muted">Crop / Produce Type</label>
                            <input type="text" class="form-control rounded-3 bg-light" value="<?= htmlspecialchars($listing['crop_type'], ENT_QUOTES, 'UTF-8') ?>" disabled>
                            <small class="text-muted">Crop type cannot be changed once listed.</small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="statusSelect" class="form-label small fw-semibold text-muted">Listing Status</label>
                            <select name="status" id="statusSelect" class="form-select rounded-3">
                                <option value="available" <?= $listing['status'] === 'available' ? 'selected' : '' ?>>Available (Active in Market)</option>
                                <option value="matched" <?= $listing['status'] === 'matched' ? 'selected' : '' ?>>Matched (Under Deal)</option>
                                <option value="sold" <?= $listing['status'] === 'sold' ? 'selected' : '' ?>>Sold / Off Market</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="qtyInput" class="form-label small fw-semibold text-muted">Quantity Available (kg)</label>
                            <div class="input-group">
                                <input type="number" step="10" min="1" name="quantity_kg" id="qtyInput" class="form-control rounded-start-3" value="<?= (float)$listing['quantity_kg'] ?>" required>
                                <span class="input-group-text bg-light rounded-end-3">kg</span>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="priceInput" class="form-label small fw-semibold text-muted">Price per kg (LKR)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light rounded-start-3">Rs.</span>
                                <input type="number" step="1" min="1" name="price_per_kg" id="priceInput" class="form-control rounded-end-3" value="<?= (float)$listing['price_per_kg'] ?>" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="harvestDateInput" class="form-label small fw-semibold text-muted">Harvest Date</label>
                            <input type="date" name="harvest_date" id="harvestDateInput" class="form-control rounded-3" value="<?= htmlspecialchars($listing['harvest_date'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="listings.php" class="btn btn-light rounded-3 px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
