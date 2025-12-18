<?php
require_once 'auth_check.php';
require_once 'controllers/penelitianController.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $controller = new PenelitianController();
    $controller->delete($id);
} else {
    header('Location: manage_penelitian.php');
}