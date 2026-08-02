<?php
// AgriSync Session Logout Controller (TASK-009)
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Unset all session variables
$_SESSION = [];

// Destroy session cookie if set
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy active session
session_destroy();

// Redirect to login page
$login_url = defined('APP_URL') ? APP_URL . '/auth/login.php' : '/auth/login.php';
redirect($login_url);
