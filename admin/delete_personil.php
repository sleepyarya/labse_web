<?php
// Proteksi halaman - harus login dulu
require_once 'auth_check.php';
require_once '../includes/config.php';

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: manage_personil.php?error=' . urlencode('ID tidak valid'));
    exit();
}

// Get data untuk hapus foto
$query = "SELECT foto FROM personil WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));

if (pg_num_rows($result) == 0) {
    header('Location: manage_personil.php?error=' . urlencode('Data tidak ditemukan'));
    exit();
}

$data = pg_fetch_assoc($result);

// Delete from database
$delete_query = "DELETE FROM personil WHERE id = $1";
$delete_result = pg_query_params($conn, $delete_query, array($id));

if ($delete_result) {
    // Delete photo file if exists
    if ($data['foto']) {
        $foto_path = '../uploads/personil/' . $data['foto'];
        if (file_exists($foto_path)) {
            unlink($foto_path);
        }
    }
    
    header('Location: manage_personil.php?success=delete');
} else {
    header('Location: manage_personil.php?error=' . urlencode('Gagal menghapus data: ' . pg_last_error($conn)));
}

pg_close($conn);
exit();
?>
