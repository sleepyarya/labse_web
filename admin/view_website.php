<?php
// Proteksi halaman - harus login dulu
require_once 'auth_check.php';
require_once '../includes/config.php';

// Set session flag bahwa admin sedang preview website
$_SESSION['viewing_from_admin'] = true;

// Redirect ke homepage
header('Location: ' . BASE_URL . '/');
exit();
?>
