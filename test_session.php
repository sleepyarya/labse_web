<?php
// Test session untuk debug
session_start();

echo "<h2>Session Debug Information</h2>";
echo "<pre>";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? "ACTIVE" : "INACTIVE") . "\n\n";
echo "Session ID: " . session_id() . "\n\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "</pre>";

echo "<hr>";

// Check admin login status
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo "<p style='color: green; font-weight: bold;'>✓ Admin is LOGGED IN</p>";
    echo "<p>Username: " . ($_SESSION['admin_username'] ?? 'N/A') . "</p>";
    echo "<p>Nama: " . ($_SESSION['admin_nama'] ?? 'N/A') . "</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ Admin is NOT logged in</p>";
}

echo "<hr>";

// Check preview mode status
if (isset($_SESSION['viewing_from_admin']) && $_SESSION['viewing_from_admin'] === true) {
    echo "<p style='color: blue; font-weight: bold;'>👁️ PREVIEW MODE: ACTIVE</p>";
    echo "<p>Floating buttons WILL appear on public pages</p>";
    echo "<p><a href='admin/close_preview.php' onclick='setTimeout(() => location.reload(), 100); return false;'>Close Preview Mode</a></p>";
} else {
    echo "<p style='color: orange; font-weight: bold;'>👁️ PREVIEW MODE: INACTIVE</p>";
    echo "<p>Floating buttons will NOT appear</p>";
}

echo "<hr>";
echo "<h3>Actions:</h3>";
echo "<p><a href='admin/index.php'>Go to Admin Dashboard</a></p>";
echo "<p><a href='admin/view_website.php'>Enable Preview Mode (from Dashboard)</a></p>";
echo "<p><a href='index.php'>Go to Homepage</a></p>";
?>
