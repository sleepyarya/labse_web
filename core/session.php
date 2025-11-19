<?php
// Core: Session Management
// Description: Handles session initialization and security

// Start session with secure settings
if (session_status() == PHP_SESSION_NONE) {
    // Set session cookie parameters for security
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set to true if using HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    
    session_start();
}

// Regenerate session ID periodically for security
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Function to check if user is logged in as admin
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_nama']);
}

// Function to check if user is logged in as member
function isMemberLoggedIn() {
    return isset($_SESSION['member_id']) && isset($_SESSION['member_nama']);
}

// Function to redirect if not logged in
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Alias function for checkAdminSession (compatibility)
function checkAdminSession() {
    if (!isAdminLoggedIn()) {
        header('Location: ../../login.php');
        exit();
    }
}

// Function to redirect if not logged in as member
function requireMemberLogin() {
    if (!isMemberLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Function to logout admin
function logoutAdmin() {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_nama']);
    unset($_SESSION['admin_email']);
    session_destroy();
}

// Function to logout member
function logoutMember() {
    unset($_SESSION['member_id']);
    unset($_SESSION['member_nama']);
    unset($_SESSION['member_email']);
    unset($_SESSION['member_jabatan']);
    unset($_SESSION['member_foto']);
    session_destroy();
}
?>
