<?php
require_once __DIR__ . '/../includes/config.php';

$query = "SELECT key_name, content_value FROM landing_page_content WHERE section_name = 'footer' AND key_name LIKE 'social_%'";
$result = pg_query($conn, $query);

echo "<pre>";
while ($row = pg_fetch_assoc($result)) {
    echo $row['key_name'] . ": [" . $row['content_value'] . "]\n";
}
echo "</pre>";
?>
