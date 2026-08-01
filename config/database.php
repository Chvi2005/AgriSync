<?php
// Database configuration file (TASK-002)
require_once __DIR__ . '/constants.php';

function getDbConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Return associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Use true prepared statements
        ];

        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Do not expose raw PHP errors to users. Log it and show generic message.
        // error_log($e->getMessage()); 
        die("Database connection failed. Please try again later.");
    }
}
