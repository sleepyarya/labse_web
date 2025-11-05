<?php
// Start session
session_start();

// Clear preview mode flag
unset($_SESSION['viewing_from_admin']);

// Return success
http_response_code(200);
echo json_encode(['success' => true]);
?>
