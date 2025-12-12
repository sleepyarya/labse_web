<?php
require_once __DIR__ . '/../includes/config.php';

// Update social links to be empty string where they are currently '#'
$query = "UPDATE landing_page_content SET content_value = '' WHERE section_name = 'footer' AND key_name LIKE 'social_%' AND content_value = '#'";
$result = pg_query($conn, $query);

if ($result) {
    echo "Successfully cleared default social links.";
} else {
    echo "Error: " . pg_last_error($conn);
}
?>
