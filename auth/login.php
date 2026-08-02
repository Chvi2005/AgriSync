<?php
/**
 * AgriSync — User Login Page (TASK-016)
 * Secure authentication gateway for Farmers, Commercial Buyers, and Admins.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already authenticated
if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_role'])) {
    $role = $_SESSION['user_role'];
    $app_url = defined('APP_URL') ? APP_URL : '';
    $dest = match($role) {
        'farmer' => $app_url . '/farmer/dashboard.php',
        'business' => $app_url . '/business/dashboard.php',
        'admin' => $app_url . '/admin/dashboard.php',
        default => $app_url . '/index.php'
    };
    redirect($dest);
}

$page_title = 'Login';
$error = '';
$email_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_val = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf)) {
        $error = 'Security validation failed. Please try again.';
    } elseif (empty($email_val) || empty($password)) {
        $error = 'Please enter both your email and password.';
    } else {
        try {
            $db = getDbConnection();
            $stmt = $db->prepare("SELECT id, name, email, password_hash, role, district, phone FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email_val]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Set session data
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_district'] = $user['district'] ?? 'Dambulla';
                $_SESSION['user_phone'] = $user['phone'] ?? '';

                $app_url = defined('APP_URL') ? APP_URL : '';
                $target = match($user['role']) {
                    'farmer' => $app_url . '/farmer/dashboard.php',
                    'business' => $app_url . '/business/dashboard.php',
                    'admin' => $app_url . '/admin/dashboard.php',
                    default => $app_url . '/index.php'
                };
                redirect($target);
            } else {
                $error = 'Invalid email address or password.';
            }
        } catch (Throwable $e) {
            error_log("Login Error: " . $e->getMessage());
            $error = 'Authentication system temporarily unavailable.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            
            <div class="text-center mb-4">
                <a href="../index.php" class="d-inline-flex align-items-center text-decoration-none mb-2">
                    <span class="fs-2">🌾</span>
                    <span class="fs-3 fw-bold text-success ms-2"><?= APP_NAME ?></span>
                </a>
                <h4 class="fw-bold text-dark mb-1">Sign in to your account</h4>
                <p class="text-muted small">Enter your credentials to access the agricultural marketplace</p>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger rounded-3 py-2 px-3 small d-flex align-items-center mb-3">
                        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                        <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success rounded-3 py-2 px-3 small d-flex align-items-center mb-3">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>Registration successful! You can now log in.</div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['logged_out'])): ?>
                    <div class="alert alert-info rounded-3 py-2 px-3 small d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div>You have been safely logged out.</div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                    <div class="mb-3">
                        <label for="emailInput" class="form-label small fw-semibold text-muted">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="emailInput" class="form-control rounded-end-3 border-start-0 ps-0" placeholder="name@domain.lk" value="<?= htmlspecialchars($email_val, ENT_QUOTES, 'UTF-8') ?>" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="passwordInput" class="form-label small fw-semibold text-muted mb-0">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="passwordInput" class="form-control rounded-end-3 border-start-0 ps-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-3 mb-3 shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                    </button>
                </form>

                <!-- Demo Credentials Helper -->
                <div class="border-top pt-3 mt-2">
                    <span class="text-muted extra-small d-block text-center mb-2 fw-semibold text-uppercase" style="font-size: 0.75rem;">Quick Demo Logins</span>
                    <div class="d-grid gap-1">
                        <button type="button" class="btn btn-light btn-sm text-start py-1 px-2 border small" onclick="fillCreds('sunil@agrisync.lk', 'Farmer@123')">
                            🌱 <strong>Farmer:</strong> sunil@agrisync.lk <small class="text-muted">(Farmer@123)</small>
                        </button>
                        <button type="button" class="btn btn-light btn-sm text-start py-1 px-2 border small" onclick="fillCreds('procurement@keells.lk', 'Business@123')">
                            🛒 <strong>Buyer:</strong> procurement@keells.lk <small class="text-muted">(Business@123)</small>
                        </button>
                        <button type="button" class="btn btn-light btn-sm text-start py-1 px-2 border small" onclick="fillCreds('admin@agrisync.lk', 'Admin@123')">
                            🛡️ <strong>Admin:</strong> admin@agrisync.lk <small class="text-muted">(Admin@123)</small>
                        </button>
                    </div>
                </div>

            </div>

            <div class="text-center mt-3 text-muted small">
                Don't have an account? <a href="register.php" class="text-success fw-semibold text-decoration-none">Create one now</a>
            </div>

        </div>
    </div>
</div>

<script>
function fillCreds(email, pass) {
    document.getElementById('emailInput').value = email;
    document.getElementById('passwordInput').value = pass;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
