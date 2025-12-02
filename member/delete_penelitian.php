<?php
require_once 'auth_check.php';
require_once 'controllers/penelitianController.php';
require_once '../includes/config.php';

// Validate ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: my_penelitian.php?error=invalid');
    exit();
}

$id = intval($_GET['id']);
$controller = new MemberPenelitianController();

// Delete with ownership validation
$controller->delete($id);
?>
