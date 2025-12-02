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
        header('Location: ../login.php?timeout=1');
        exit();
    }
}

// Cek apakah ini direct access (bukan dari flow normal dashboard)
// Jika akses langsung ke /member/ atau /member/index.php tanpa melalui login
$current_page = basename($_SERVER['PHP_SELF']);
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

// Jika ini halaman index.php atau root member
if (in_array($current_page, ['index.php', ''])) {
    // Cek apakah ada flag "fresh_login" 
    $is_fresh_login = isset($_SESSION['fresh_login']) && $_SESSION['fresh_login'] === true;
    
    // Jika bukan fresh login, berarti ini direct access atau tab baru
    if (!$is_fresh_login) {
        // Cek apakah referer dari member internal page (navigasi dalam dashboard)
        $is_from_member = !empty($referer) && strpos($referer, '/member/') !== false && strpos($referer, 'login.php') === false;
        
        // Jika bukan dari internal member page, force login ulang
        if (!$is_from_member) {
            // Direct access atau tab baru detected - clear session
            session_unset();
            session_destroy();
            header('Location: ../login.php?direct=1');
            exit();
        }
    } else {
        // Clear fresh_login flag setelah digunakan
        $_SESSION['fresh_login'] = false;
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
