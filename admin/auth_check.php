<?php
// File untuk cek apakah user sudah login
// Include file ini di setiap halaman admin yang perlu proteksi

require_once __DIR__ . '/../core/session.php';

// Timeout inactivity (dalam detik) - 10 menit
define('SESSION_TIMEOUT', 600);

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Jika belum login, redirect ke halaman login
    session_unset();
    session_destroy();
    header('Location: ../login.php');
    exit();
}

// Cek timeout inactivity
if (isset($_SESSION['admin_last_activity'])) {
    $inactive_time = time() - $_SESSION['admin_last_activity'];
    
    if ($inactive_time > SESSION_TIMEOUT) {
        // Session expired karena inactivity
        session_unset();
        session_destroy();
        header('Location: ../login.php?timeout=1');
        exit();
    }
}

// Cek apakah ini direct access (bukan dari flow normal dashboard)
// Jika akses langsung ke /admin/ atau /admin/index.php tanpa melalui login
$current_page = basename($_SERVER['PHP_SELF']);
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

// Jika ini halaman index.php atau root admin
if (in_array($current_page, ['index.php', ''])) {
    // Cek apakah ada flag "fresh_login" 
    $is_fresh_login = isset($_SESSION['fresh_login']) && $_SESSION['fresh_login'] === true;
    
    // Jika bukan fresh login, berarti ini direct access atau tab baru
    if (!$is_fresh_login) {
        // Cek apakah referer dari admin internal page (navigasi dalam dashboard)
        $is_from_admin = !empty($referer) && strpos($referer, '/admin/') !== false && strpos($referer, 'login.php') === false;
        
        // Jika bukan dari internal admin page, force login ulang
        if (!$is_from_admin) {
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
$_SESSION['admin_last_activity'] = time();

// Fungsi untuk logout
function admin_logout() {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ../login.php');
    exit();
}
?>
