<?php
require_once 'auth_check.php';
require_once 'controllers/produkController.php';
require_once '../includes/config.php';

$controller = new MemberProdukController();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: my_produk.php?error=invalid');
    exit();
}

$id = intval($_GET['id']);
$controller->delete($id);
?>
