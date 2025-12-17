<?php
// Admin Dashboard - Main routing
require_once 'auth_check.php';

// Redirect to dashboard view
header('Location: views/dashboard.php');
exit();
?>