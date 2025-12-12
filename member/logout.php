<?php
session_start();

// Log activity: Logout (before destroying session)
if (isset($_SESSION['member_logged_in']) && $_SESSION['member_logged_in'] === true 
    && isset($_SESSION['member_id']) && isset($_SESSION['member_nama'])) {
    require_once '../includes/config.php';
    require_once '../includes/activity_logger.php';
    
    log_activity($conn, $_SESSION['member_id'], $_SESSION['member_nama'], 
        'LOGOUT', 'Logout dari dashboard', null, null);
    
    pg_close($conn);
}

session_unset();
session_destroy();

// Redirect ke login page
header('Location: ../login.php?logout=success');
exit();
?>

