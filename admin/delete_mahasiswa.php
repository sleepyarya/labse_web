<?php
// Redirect to new view structure - delete action now handled in manage view
header('Location: views/manage_mahasiswa.php?action=delete&' . $_SERVER['QUERY_STRING']);
exit();
?>

// Check if data exists
$query = "SELECT id FROM mahasiswa WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));

if (pg_num_rows($result) == 0) {
    header('Location: manage_mahasiswa.php?error=' . urlencode('Data tidak ditemukan'));
    exit();
}

// Delete from database
$delete_query = "DELETE FROM mahasiswa WHERE id = $1";
$delete_result = pg_query_params($conn, $delete_query, array($id));

if ($delete_result) {
    header('Location: manage_mahasiswa.php?success=delete');
} else {
    header('Location: manage_mahasiswa.php?error=' . urlencode('Gagal menghapus data: ' . pg_last_error($conn)));
}

pg_close($conn);
exit();
?>
