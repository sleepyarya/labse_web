<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: my_pengabdian.php');
    exit();
}

$id = intval($_GET['id']);
$member_id = $_SESSION['member_id'];

// Get pengabdian data and check ownership
$query = "SELECT * FROM pengabdian WHERE id = $1 AND personil_id = $2";
$result = pg_query_params($conn, $query, array($id, $member_id));
$pengabdian = pg_fetch_assoc($result);

if (!$pengabdian) {
    header('Location: my_pengabdian.php?error=notfound');
    exit();
}

// Delete
$delete_query = "DELETE FROM pengabdian WHERE id = $1 AND personil_id = $2";
$delete_result = pg_query_params($conn, $delete_query, array($id, $member_id));

if ($delete_result) {
    // Delete image file
    if ($pengabdian['gambar'] && file_exists('../public/uploads/pengabdian/' . $pengabdian['gambar'])) {
        unlink('../public/uploads/pengabdian/' . $pengabdian['gambar']);
    }
    header('Location: my_pengabdian.php?success=delete');
} else {
    header('Location: my_pengabdian.php?error=delete');
}

pg_close($conn);
exit();
?>
