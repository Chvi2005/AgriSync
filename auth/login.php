<?php
// AgriSync Login Page (TASK-009)
$page_title = 'Sign In';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// If user is already logged in, redirect to their role dashboard
if (isLoggedIn()) {
    $role = getUserRole();
    $app_url = defined('APP_URL') ? APP_URL : '';
    $target = match ($role) {
        'farmer'   => $app_url . '/farmer/dashboard.php',
        'business' => $app_url . '/business/dashboard.php',
        'admin'    => $app_url . '/admin/dashboard.php',
        default    => $app_url . '/index.php',
    };
    redirect($target);
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-4">
                <a href="<?= APP_URL ?>" class="text-decoration-none">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-2" style="width: 56px; height: 56px;">
                        <i class="bi bi-sprout fs-2"></i>
                    </span>
                    <h2 class="fw-bold text-dark mb-0">AgriSync</h2>
                </a>
                <p class="text-muted">Empowering Sri Lankan Agriculture with AI Intelligence</p>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4 p-md-5">
                    <h4 class="fw-bold text-dark mb-1">Welcome Back</h4>
                    <p class="text-muted small mb-4">Sign in to access your dashboard</p>

                    <div id="loginAlert" class="alert alert-danger d-none mb-4" role="alert"></div>

                    <form id="loginForm" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium text-secondary">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="name@domain.com" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium text-secondary">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" id="loginBtn" class="btn btn-primary w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2">
                            <span>Sign In</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>

                    <hr class="my-4 text-muted opacity-25">

                    <div class="text-center">
                        <p class="text-muted small mb-0">Don't have an AgriSync account? 
                            <a href="<?= APP_URL ?>/auth/register.php" class="text-primary fw-semibold text-decoration-none">Create Account</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Role Quick Demo Credentials Hint -->
            <div class="mt-4 p-3 bg-white rounded-3 shadow-sm text-center border">
                <p class="text-muted small fw-semibold mb-1"><i class="bi bi-info-circle me-1 text-primary"></i> Demo Account Credentials</p>
                <div class="d-flex justify-content-center gap-2 flex-wrap text-muted extra-small">
                    <span class="badge bg-light text-dark border">Farmer: farmer@agrisync.lk</span>
                    <span class="badge bg-light text-dark border">Business: buyer@agrisync.lk</span>
                    <span class="badge bg-light text-dark border">Admin: admin@agrisync.lk</span>
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
                }, 500);
            } else {
                loginAlert.textContent = result.error || 'Authentication failed. Please check your credentials.';
                loginAlert.classList.remove('d-none');
                loginBtn.disabled = false;
                loginBtn.innerHTML = originalBtnContent;
            }
        } catch (error) {
            loginAlert.textContent = 'A network error occurred. Please check your connection and try again.';
            loginAlert.classList.remove('d-none');
            loginBtn.disabled = false;
            loginBtn.innerHTML = originalBtnContent;
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
