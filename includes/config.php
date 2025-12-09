<?php
// Database Configuration for PostgreSQL
// Check each constant individually to prevent redefinition
if (!defined('DB_HOST')) define('DB_HOST', 'db_postgres');
if (!defined('DB_PORT')) define('DB_PORT', '5432');
if (!defined('DB_NAME')) define('DB_NAME', 'labse');
if (!defined('DB_USER')) define('DB_USER', 'user');
if (!defined('DB_PASS')) define('DB_PASS', 'userpass');

// Base URL
if (!defined('BASE_URL')) define('BASE_URL', '/labse_web');

// Create connection string (di luar guard agar selalu dijalankan)
$conn_string = "host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS;

// Connect to PostgreSQL
$conn = pg_connect($conn_string);

// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Function to safely escape strings
function pg_escape($conn, $string) {
    return pg_escape_string($conn, $string);
}
?>