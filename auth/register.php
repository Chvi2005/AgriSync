<?php
// AgriSync User Registration Page (TASK-009)
$page_title = 'Create Account';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

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

$districts = [
    'Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 
    'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara', 
    'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar', 
    'Matale', 'Matara', 'Moneragala', 'Mullaitivu', 'Nuwara Eliya', 
    'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'
];
?>

<div class="container py-5">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center mb-4">
                <a href="<?= APP_URL ?>" class="text-decoration-none">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-2" style="width: 56px; height: 56px;">
                        <i class="bi bi-sprout fs-2"></i>
                    </span>
                    <h2 class="fw-bold text-dark mb-0">AgriSync</h2>
                </a>
                <p class="text-muted">Join Sri Lanka's AI-Powered Agricultural Marketplace</p>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4 p-md-5">
                    <h4 class="fw-bold text-dark mb-1">Create Your Account</h4>
                    <p class="text-muted small mb-4">Select your role and enter your details to get started</p>

                    <div id="registerAlert" class="alert alert-danger d-none mb-4" role="alert"></div>

                    <form id="registerForm" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                        <!-- Account Role Selector Cards -->
                        <div class="mb-4">
                            <label class="form-label fw-medium text-secondary d-block">Account Type</label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="role" id="roleFarmer" value="farmer" checked>
                                    <label class="btn btn-outline-primary w-100 p-3 text-start rounded-3 h-100" for="roleFarmer">
                                        <i class="bi bi-person-workspace fs-3 d-block mb-1 text-primary"></i>
                                        <span class="fw-bold d-block text-dark">Farmer</span>
                                        <small class="text-muted extra-small">List yields & sell directly</small>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="role" id="roleBusiness" value="business">
                                    <label class="btn btn-outline-primary w-100 p-3 text-start rounded-3 h-100" for="roleBusiness">
                                        <i class="bi bi-shop fs-3 d-block mb-1 text-primary"></i>
                                        <span class="fw-bold d-block text-dark">Business / Buyer</span>
                                        <small class="text-muted extra-small">Pre-order bulk produce</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-medium text-secondary">Full Name / Organization</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Bandara Herath" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-medium text-secondary">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="07X XXX XXXX" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium text-secondary">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="district" class="form-label fw-medium text-secondary">District Location</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-geo-alt"></i></span>
                                    <select class="form-select" id="district" name="district" required>
                                        <option value="" disabled selected>Select District</option>
                                        <?php foreach ($districts as $dist): ?>
                                            <option value="<?= htmlspecialchars($dist, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($dist, ENT_QUOTES, 'UTF-8') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-medium text-secondary">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="At least 6 chars" required minlength="6">
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="registerBtn" class="btn btn-primary w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2">
                            <span>Complete Registration</span>
                            <i class="bi bi-check-circle"></i>
                        </button>
                    </form>

                    <hr class="my-4 text-muted opacity-25">

                    <div class="text-center">
                        <p class="text-muted small mb-0">Already have an AgriSync account? 
                            <a href="<?= APP_URL ?>/auth/login.php" class="text-primary fw-semibold text-decoration-none">Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    const registerBtn = document.getElementById('registerBtn');
    const registerAlert = document.getElementById('registerAlert');

    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        registerAlert.classList.add('d-none');

        const originalBtnContent = registerBtn.innerHTML;
        registerBtn.disabled = true;
        registerBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Creating Account...`;

        try {
            const formData = new FormData(registerForm);
            const response = await fetch('<?= APP_URL ?>/api/auth.php?action=register', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '<?= generateCSRFToken() ?>'
                }
            });

            const result = await response.json();

            if (result.success) {
                if (window.AgriSync && window.AgriSync.showToast) {
                    window.AgriSync.showToast('Account created successfully! Logging you in...', 'success');
                }
                setTimeout(() => {
                    window.location.href = result.data.redirect;
                }, 600);
            } else {
                registerAlert.textContent = result.error || 'Registration failed. Please review your input.';
                registerAlert.classList.remove('d-none');
                registerBtn.disabled = false;
                registerBtn.innerHTML = originalBtnContent;
            }
        } catch (error) {
            registerAlert.textContent = 'A network error occurred. Please check your connection and try again.';
            registerAlert.classList.remove('d-none');
            registerBtn.disabled = false;
            registerBtn.innerHTML = originalBtnContent;
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
