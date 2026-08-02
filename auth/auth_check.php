<?php
// AgriSync User Authentication & Role Protection (TASK-009)
// Safe to require_once on protected pages and API endpoints

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Global Authentication Check
if (!isLoggedIn()) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (str_contains($request_uri, '/api/')) {
        jsonResponse(false, [], 'Unauthorized access. Please log in.', 401);
    }
    $login_path = defined('APP_URL') ? APP_URL . '/auth/login.php' : '/auth/login.php';
    redirect($login_path);
}

/**
 * Enforce role-based access control for specific routes
 * 
 * @param array $allowed_roles Array of permitted roles (e.g. ['farmer', 'admin'])
 * @return void
 */
function checkRole(array $allowed_roles): void {
    $current_role = getUserRole();
    if (!$current_role || !in_array($current_role, $allowed_roles, true)) {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        if (str_contains($request_uri, '/api/')) {
            jsonResponse(false, [], 'Forbidden: Insufficient permissions for this action.', 403);
        }
        
        // Redirect to user's appropriate dashboard if logged in, or login page
        $app_base = defined('APP_URL') ? APP_URL : '';
        $target = match ($current_role) {
            'farmer'   => $app_base . '/farmer/dashboard.php',
            'business' => $app_base . '/business/dashboard.php',
            'admin'    => $app_base . '/admin/dashboard.php',
            default    => $app_base . '/auth/login.php',
        };
        redirect($target);
    }
}
