<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * --- Database Configuration ---
 * Replace these values with your actual database credentials.
 */
define('DB_HOST', 'localhost');      // Your database host, usually 'localhost'
define('DB_NAME', 'lms');     // The name of the database you created with the SQL code
define('DB_USER', 'root');    // Your database username (e.g., 'root')
define('DB_PASS', ''); // Your database password

/**
 * --- Site Configuration ---
 */
$config = [
    'site_name' => 'EduLearn',
];

/**
 * --- PDO Database Connection ---
 * This creates a reusable database connection object ($pdo).
 */
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Fetch results as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,            // Use real prepared statements
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // For development, we show the error. In production, you'd log this 
    // and show a generic error page to the user.
    die("Database connection failed: " . $e->getMessage());
}

?>