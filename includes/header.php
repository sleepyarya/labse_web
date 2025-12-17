<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch Landing Page Content
$landing_content = [];
if (isset($conn)) {
    $query = "SELECT * FROM landing_page_content";
    $result = pg_query($conn, $query);
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $landing_content[$row['section_name']][$row['key_name']] = $row['content_value'];
        }
    }
}

function get_content($section, $key, $default = '') {
    global $landing_content;
    return isset($landing_content[$section][$key]) ? $landing_content[$section][$key] : $default;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Lab Software Engineering</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/style.css">
</head>
<body>