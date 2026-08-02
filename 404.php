<?php
/**
 * AgriSync — 404 Not Found Page (TASK-097)
 */

http_response_code(404);
$page_title = 'Page Not Found';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-center min-vh-100 bg-light px-3 py-5">
    <div class="card border-0 shadow-lg rounded-4 p-5 text-center" style="max-width: 540px;">
        <div class="mb-4">
            <span class="badge bg-primary-subtle text-primary fs-1 p-3 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 90px; height: 90px;">
                <i class="bi bi-compass"></i>
            </span>
        </div>
        <h1 class="display-5 fw-bold text-dark mb-2">404</h1>
        <h3 class="fw-bold text-dark mb-3">Field Not Found</h3>
        <p class="text-muted mb-4">
            The agricultural record, page, or portal route you are looking for has been moved, harvested, or does not exist.
        </p>
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
            <a href="/" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                <i class="bi bi-house-door me-1"></i> Return Home
            </a>
            <?php if (isLoggedIn()): ?>
                <?php $role = getUserRole(); ?>
                <a href="/<?= $role ?>/dashboard.php" class="btn btn-outline-primary px-4 py-2 rounded-3">
                    <i class="bi bi-speedometer2 me-1"></i> My Dashboard
                </a>
            <?php else: ?>
                <a href="/auth/login.php" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
