<?php
// Core: Database Connection Handler
// Description: Handles PostgreSQL database connections and configuration

// Database configuration - adjust these values according to your setup
define('DB_HOST', 'localhost');
define('DB_PORT', '5433');
define('DB_NAME', 'labse');
define('DB_USER', 'postgres');
define('DB_PASS', '12345678');

// Base URL configuration
define('BASE_URL', 'http://localhost/labse_web');

// Create database connection
function getConnection() {
    $conn_string = "host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS;
    $conn = pg_connect($conn_string);
    
    if (!$conn) {
        die("Database connection failed: " . pg_last_error());
    }
    
    return $conn;
}

// Global connection variable
$conn = getConnection();

// Set timezone
date_default_timezone_set('Asia/Jakarta');
?>
