<?php
// AgriSync Login Page - Milestone 3 (M3)
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Sign In';
$error_message = '';

// If user is already logged in, redirect immediately to their role dashboard
if (isLoggedIn()) {
    $role = getUserRole();
    $app_url = defined('APP_URL') ? APP_URL : '';
    $target = match ($role) {
        'farmer'   => $app_url . '/farmer/dashboard.php',
        'business' => $app_url . '/business/dashboard.php',
        'admin'    => $app_url . '/admin/dashboard.php',
        default    => $app_url . '/index.php',
    };
    header("Location: " . $target);
    exit;
}

// Handle Direct Server-Side POST Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!validateCSRFToken($csrf_token)) {
        $error_message = 'CSRF security token validation failed. Please refresh and try again.';
    } else {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = trim($_POST['password'] ?? '');

        if (!$email || empty($password)) {
            $error_message = 'Invalid email or password';
        } else {
            try {
                $db = getDbConnection();
                $stmt = $db->prepare("SELECT id, name, email, password_hash, role, phone, district, is_active FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    if ((int)$user['is_active'] !== 1) {
                        $error_message = 'Your account has been deactivated. Please contact support.';
                    } else {
                        // Set session variables
                        $_SESSION['user_id']       = (int)$user['id'];
                        $_SESSION['user_name']     = sanitize($user['name']);
                        $_SESSION['user_email']    = sanitize($user['email']);
                        $_SESSION['user_role']     = sanitize($user['role']);
                        $_SESSION['user_district'] = sanitize($user['district']);

                        $app_url = defined('APP_URL') ? APP_URL : '';
                        $redirect_target = match ($user['role']) {
                            'farmer'   => $app_url . '/farmer/dashboard.php',
                            'business' => $app_url . '/business/dashboard.php',
                            'admin'    => $app_url . '/admin/dashboard.php',
                            default    => $app_url . '/index.php',
                        };

                        header("Location: " . $redirect_target);
                        exit;
                    }
                } else {
                    $error_message = 'Invalid email or password';
                }
            } catch (PDOException $e) {
                $error_message = 'A database error occurred. Please try again later.';
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid p-0 min-vh-100 d-flex flex-column justify-content-center bg-light">
    <div class="row g-0 min-vh-100">
        <!-- Left Column: Branding & Platform Feature Showcase (Split-Screen) -->
        <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between p-5 text-white" style="background: linear-gradient(135deg, #1B4332 0%, #2D6A4F 60%, #40916C 100%);">
            <div>
                <a href="<?= APP_URL ?>" class="d-inline-flex align-items-center gap-3 text-decoration-none text-white mb-4">
                    <span class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bi bi-sprout fs-3"></i>
                    </span>
                    <span class="fs-2 fw-bold tracking-tight">AgriSync</span>
                </a>
            </div>

            <div class="my-auto py-5">
                <span class="badge bg-accent-light text-dark fw-bold px-3 py-2 mb-3 rounded-pill text-uppercase extra-small">AI-Powered B2B Marketplace</span>
                <h1 class="display-5 fw-bold mb-3">Connecting Sri Lankan Agriculture Directly</h1>
                <p class="lead text-white-50 mb-4" style="max-width: 520px;">
                    Empowering farmers with automated yield matching and direct access to supermarkets, restaurants, and commercial grocers.
                </p>

                <div class="row g-3 mt-4">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10">
                            <i class="bi bi-graph-up-arrow fs-4 mb-2 text-accent"></i>
                            <h6 class="fw-bold text-white mb-1">Fair Trade Pricing</h6>
                            <p class="small text-white-50 mb-0">Eliminate middleman margins and maximize farm profits.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10">
                            <i class="bi bi-cpu fs-4 mb-2 text-accent"></i>
                            <h6 class="fw-bold text-white mb-1">Automated Matching</h6>
                            <p class="small text-white-50 mb-0">Direct buyer order matching based on proximity & demand.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between text-white-50 extra-small pt-3 border-top border-white border-opacity-10">
                <span>&copy; <?= date('Y') ?> AgriSync Platform. All rights reserved.</span>
                <span>AIESEC Idealize 2026</span>
            </div>
        </div>

        <!-- Right Column: Login Form Interface -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-sm-5 bg-white">
            <div class="w-100" style="max-width: 440px;">
                <!-- Mobile Logo Header -->
                <div class="text-center mb-4 d-lg-none">
                    <a href="<?= APP_URL ?>" class="text-decoration-none d-inline-flex align-items-center gap-2">
                        <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-sprout fs-3"></i>
                        </span>
                        <span class="h3 fw-bold text-dark mb-0">AgriSync</span>
                    </a>
                </div>

                <div class="mb-4">
                    <h2 class="fw-bold text-dark mb-1">Welcome Back</h2>
                    <p class="text-muted small">Sign in with your registered email and password to access your portal</p>
                </div>

                <!-- Server-Side Error Alert -->
                <?php if (!empty($error_message)): ?>
                    <div id="loginAlert" class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                <?php else: ?>
                    <div id="loginAlert" class="alert alert-danger d-none rounded-3 mb-4" role="alert"></div>
                <?php endif; ?>

                <form id="loginForm" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold text-dark small">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" placeholder="name@domain.com" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label fw-semibold text-dark small mb-0">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" id="loginBtn" class="btn btn-primary w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                        <span>Sign In</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <hr class="my-4 text-muted opacity-25">

                <div class="text-center">
                    <p class="text-muted small mb-0">Don't have an AgriSync account? 
                        <a href="<?= APP_URL ?>/auth/register.php" class="text-primary fw-bold text-decoration-none">Create Account</a>
                    </p>
                </div>

                <!-- Demo Account Credentials Helper -->
                <div class="mt-4 p-3 bg-light rounded-3 text-center border">
                    <p class="text-muted extra-small fw-semibold mb-2"><i class="bi bi-key-fill me-1 text-primary"></i> Demo Login Accounts (Password: <code>password123</code>)</p>
                    <div class="d-flex justify-content-center gap-1.5 flex-wrap extra-small">
                        <span class="badge bg-white text-dark border me-1">Farmer: farmer@agrisync.lk</span>
                        <span class="badge bg-white text-dark border me-1">Business: buyer@agrisync.lk</span>
                        <span class="badge bg-white text-dark border">Admin: admin@agrisync.lk</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginAlert = document.getElementById('loginAlert');

    loginForm.addEventListener('submit', async (e) => {
        // Provide seamless AJAX authentication experience
        e.preventDefault();
        loginAlert.classList.add('d-none');
        
        const originalBtnContent = loginBtn.innerHTML;
        loginBtn.disabled = true;
        loginBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Authenticating...`;

        try {
            const formData = new FormData(loginForm);
            const response = await fetch('<?= APP_URL ?>/api/auth.php?action=login', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '<?= generateCSRFToken() ?>'
                }
            });

            const result = await response.json();

            if (result.success) {
                if (window.AgriSync && window.AgriSync.showToast) {
                    window.AgriSync.showToast('Login successful! Redirecting...', 'success');
                }
                setTimeout(() => {
                    window.location.href = result.data.redirect;
                }, 400);
            } else {
                loginAlert.textContent = result.error || 'Invalid email or password';
                loginAlert.classList.remove('d-none');
                loginBtn.disabled = false;
                loginBtn.innerHTML = originalBtnContent;
            }
        } catch (error) {
            // Fallback to standard form submit if fetch API fails
            loginForm.submit();
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
