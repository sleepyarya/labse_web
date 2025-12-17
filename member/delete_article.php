<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

$member_id = $_SESSION['member_id'];
$artikel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verify artikel exists and belongs to member
$query_check = "SELECT judul, gambar FROM artikel WHERE id = $1 AND personil_id = $2";
$result_check = pg_query_params($conn, $query_check, array($artikel_id, $member_id));

if ($result_check && pg_num_rows($result_check) > 0) {
    $artikel = pg_fetch_assoc($result_check);
    
    // Log activity: Delete Article (before actual deletion)
    require_once '../includes/activity_logger.php';
    log_activity($conn, $member_id, $_SESSION['member_nama'], 'DELETE_ARTICLE', 
        "Menghapus artikel: {$artikel['judul']}", 'artikel', $artikel_id);
    
    // Delete image file if exists
    if (!empty($artikel['gambar'])) {
        $image_path = '../uploads/artikel/' . $artikel['gambar'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete artikel from database
    $query_delete = "DELETE FROM artikel WHERE id = $1 AND personil_id = $2";
    pg_query_params($conn, $query_delete, array($artikel_id, $member_id));
    
    header('Location: my_articles.php?success=delete');
} else {
    header('Location: my_articles.php');
}

pg_close($conn);
exit();
?>
