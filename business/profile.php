<?php
/**
 * AgriSync — Business Profile Management (TASK-047)
 * Allows commercial buyers to manage company profile, logistics hubs, and procurement preferences.
 */

$page_title = 'Business Profile';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../auth/auth_check.php';
checkRole(['business', 'admin']);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$user_id = (int)$_SESSION['user_id'];
$db = getDbConnection();
$message = '';
$message_type = '';

// Handle Profile Update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($csrf_token)) {
        $message = 'Invalid security token. Please refresh and try again.';
        $message_type = 'danger';
    } else {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $district = trim($_POST['district'] ?? '');

        if (empty($name) || empty($district)) {
            $message = 'Company name and primary district are required fields.';
            $message_type = 'warning';
        } else {
            try {
                $stmt = $db->prepare("
                    UPDATE users
                    SET name = ?, phone = ?, district = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$name, $phone, $district, $user_id]);
                
                $_SESSION['user_name'] = $name;
                $message = 'Business profile details updated successfully!';
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = 'An error occurred while updating profile.';
                $message_type = 'danger';
            }
        }
    }
}

// Fetch user data
try {
    $stmt = $db->prepare("
        SELECT id, name, email, phone, district, role, created_at, updated_at
        FROM users
        WHERE id = ? LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Order statistics
    $stmt_stats = $db->prepare("
        SELECT 
            COUNT(*) as total_orders,
            COALESCE(SUM(CASE WHEN status = 'fulfilled' THEN quantity_kg ELSE 0 END), 0) as fulfilled_kg,
            COALESCE(SUM(CASE WHEN status IN ('pending', 'matching', 'matched') THEN 1 ELSE 0 END), 0) as active_orders
        FROM order_requests
        WHERE business_id = ?
    ");
    $stmt_stats->execute([$user_id]);
    $stats = $stmt_stats->fetch();

} catch (PDOException $e) {
    $user = [];
    $stats = ['total_orders' => 0, 'fulfilled_kg' => 0, 'active_orders' => 0];
}

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
                    <h2 class="fw-bold text-dark mb-1">Company Profile</h2>
                    <p class="text-muted small mb-0">Manage procurement organization details, supply chain contacts, and regional operations.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">
                        <i class="bi bi-shield-check me-1"></i> Verified Commercial Buyer
                    </span>
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= htmlspecialchars($message_type, ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi <?= $message_type === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger' ?> me-2"></i>
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Profile Card & Statistics -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center mb-4">
                        <div class="avatar-circle mx-auto mb-3 bg-primary-subtle text-primary fw-bold fs-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-building"></i>
                        </div>
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($user['name'] ?? 'Commercial Buyer', ENT_QUOTES, 'UTF-8') ?></h4>
                        <span class="badge bg-light text-dark border mx-auto mb-3 px-3 py-1">
                            <i class="bi bi-geo-alt text-danger me-1"></i> <?= htmlspecialchars($user['district'] ?? 'Sri Lanka', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <div class="border-top pt-3 text-start small text-muted">
                            <p class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i> <?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i> <?= htmlspecialchars($user['phone'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i> Partner since <?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?></p>
                        </div>
                    </div>

                    <!-- Supply Chain Overview Widget -->
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Procurement Activity</h5>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">Total Orders Placed:</span>
                            <span class="fw-bold"><?= number_format((int)($stats['total_orders'] ?? 0)) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">Active In-Flight:</span>
                            <span class="badge bg-warning-subtle text-warning fw-bold"><?= number_format((int)($stats['active_orders'] ?? 0)) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted">Fulfilled Volume:</span>
                            <span class="fw-bold text-success"><?= number_format((float)($stats['fulfilled_kg'] ?? 0), 1) ?> kg</span>
                        </div>
                    </div>
                </div>

                <!-- Profile Edit Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Organization Profile</h4>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_profile">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Company / Enterprise Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Primary Contact Phone</label>
                                    <input type="text" name="phone" class="form-control rounded-3 py-2" placeholder="011-XXXXXXX" value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Central Logistics District <span class="text-danger">*</span></label>
                                    <select name="district" class="form-select rounded-3 py-2" required>
                                        <?php
                                        $districts = ['Colombo', 'Gampaha', 'Kalutara', 'Kandy', 'Matale', 'Nuwara Eliya', 'Galle', 'Matara', 'Hambantota', 'Jaffna', 'Kilinochchi', 'Mannar', 'Vavuniya', 'Mullaitivu', 'Batticaloa', 'Ampara', 'Trincomalee', 'Kurunegala', 'Puttalam', 'Anuradhapura', 'Polonnaruwa', 'Badulla', 'Monaragala', 'Ratnapura', 'Kegalle'];
                                        foreach ($districts as $d):
                                            $sel = ($user['district'] ?? '') === $d ? 'selected' : '';
                                        ?>
                                            <option value="<?= $d ?>" <?= $sel ?>><?= $d ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Official Email Address</label>
                                    <input type="email" class="form-control rounded-3 py-2 bg-light text-muted" value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly disabled>
                                    <small class="text-muted">To modify your corporate authentication email, please contact platform administrators.</small>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                <a href="dashboard.php" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">
                                    <i class="bi bi-check2-circle me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
