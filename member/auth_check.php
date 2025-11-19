<?php
// File: member/auth_check.php
// Proteksi halaman member - harus login sebagai member

require_once __DIR__ . '/../core/session.php';

// Session timeout (30 menit)
$timeout_duration = 1800;

// Cek apakah sudah login
if (!isset($_SESSION['member_logged_in']) || $_SESSION['member_logged_in'] !== true) {
    // Jika belum login, redirect ke login page
    header('Location: login.php');
    exit();
}

// Cek session timeout
if (isset($_SESSION['member_last_activity'])) {
    $elapsed_time = time() - $_SESSION['member_last_activity'];
    
    if ($elapsed_time > $timeout_duration) {
        // Session timeout - destroy dan redirect
        session_unset();
        session_destroy();
        header('Location: login.php?timeout=1');
        exit();
    }
}

// Update last activity time
$_SESSION['member_last_activity'] = time();

// Validasi session token (security check)
if (!isset($_SESSION['member_session_token']) || empty($_SESSION['member_session_token'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

// Clear fresh login flag setelah first page load
if (isset($_SESSION['fresh_login'])) {
    unset($_SESSION['fresh_login']);
}
?>
