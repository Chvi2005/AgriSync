<?php
// AgriSync Session Management & CSRF Initialization (TASK-004)
// Safe to require_once anywhere in backend scripts

if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session cookie parameters
    $cookie_lifetime = 86400; // 24 hours
    $cookie_path = '/';
    $cookie_domain = '';
    $is_secure = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1');
    $http_only = true;
    $same_site = 'Lax';

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');

    session_set_cookie_params([
        'lifetime' => $cookie_lifetime,
        'path'     => $cookie_path,
        'domain'   => $cookie_domain,
        'secure'   => $is_secure,
        'httponly' => $http_only,
        'samesite' => $same_site
    ]);

    session_start();
}

// Generate CSRF token if not already present in the active session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
