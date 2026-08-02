<?php
// AgriSync Application Constants Configuration Template
// Copy this file to config/constants.php and configure your environment

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'agrisync');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Google Gemini AI Configuration
define('GEMINI_API_KEY', 'your-gemini-api-key-here');
define('GEMINI_MODEL', 'gemini-1.5-flash');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/');

// Application Settings
define('APP_NAME', 'AgriSync');
define('APP_URL', 'http://localhost/agrisync');
define('FAIR_TRADE_MIN_MULTIPLIER', 1.2); // Minimum 20% margin above base cost
define('APP_ENV', 'development'); // 'development' or 'production'
