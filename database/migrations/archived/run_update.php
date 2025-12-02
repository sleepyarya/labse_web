<?php
require_once __DIR__ . '/../core/database.php';

$sql = file_get_contents(__DIR__ . '/update_schema_dashboard.sql');

if ($sql) {
    $result = pg_query($conn, $sql);
    if ($result) {
        echo "Database schema updated successfully.\n";
    } else {
        echo "Error updating schema: " . pg_last_error($conn) . "\n";
    }
} else {
    echo "Could not read SQL file.\n";
}

pg_close($conn);
?>
