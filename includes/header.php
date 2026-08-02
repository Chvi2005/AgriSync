<?php
// AgriSync Global Header Template (TASK-006)
// Safe to include on any user-facing page

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/constants.php';
}
if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/../config/session.php';
}

$page_title_display = isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') . ' | ' . APP_NAME : APP_NAME . ' — AI-Powered Agricultural Supply Chain';
$csrf_token = $_SESSION['csrf_token'] ?? '';
$app_url = defined('APP_URL') ? APP_URL : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="AgriSync connects Sri Lankan farmers directly with commercial buyers through AI-driven demand forecasting and automated supply matching.">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="theme-color" content="#2D6A4F">

    <title><?= $page_title_display ?></title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
          crossorigin="anonymous">

    <!-- Bootstrap Icons 1.11.3 -->
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom AgriSync Design System Stylesheet -->
    <link rel="stylesheet" href="<?= $app_url ?>/assets/css/style.css">
</head>
<body class="bg-light">
