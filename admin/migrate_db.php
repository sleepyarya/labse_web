<?php
require_once __DIR__ . '/../includes/config.php';

$query = "ALTER TABLE lab_profile ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
$result = pg_query($conn, $query);

if ($result) {
    echo "Successfully added updated_at column to lab_profile.";
} else {
    echo "Error: " . pg_last_error($conn);
}
?>
