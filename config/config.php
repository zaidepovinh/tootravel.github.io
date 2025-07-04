<?php
// Database configuration
define('DB_HOST', 'localhost:3366'); // Port 3366 for XAMPP
define('DB_USER', 'root'); 
define('DB_PASS', '');
define('DB_NAME', 'tootravel');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}