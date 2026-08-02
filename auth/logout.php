<?php
/**
 * AgriSync — User Logout Handler (TASK-020)
 * Safely destroys session data and redirects to login.
 */

require_once __DIR__ . '/../config/session.php';
if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/constants.php';
}
require_once __DIR__ . '/../includes/functions.php';

// Unset all session variables
$_SESSION = [];

// Destroy session cookie if present
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy session
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

$app_url = defined('APP_URL') ? APP_URL : '';
redirect($app_url . '/auth/login.php?logged_out=1');
