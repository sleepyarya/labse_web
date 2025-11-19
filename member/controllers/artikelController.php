<?php
// Controller: Member Artikel Controller
// Description: Handles CRUD operations for member artikel management

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';

class MemberArtikelController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Add new artikel by member
    public function add() {
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $member_id = $_SESSION['member_id'];
            $member_nama = $_SESSION['member_nama'];
            
            $judul = trim($_POST['judul']);
            $isi = trim($_POST['isi']);
            $penulis = $member_nama; // Otomatis dari session
            
            if (empty($judul) || empty($isi)) {
                $error = 'Judul dan isi artikel harus diisi!';
            } else {
                // Handle gambar upload
                $gambar = null;
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = uniqid() . '.' . $ext;
                        $upload_dir = '../public/uploads/artikel/';
                        
                        // Create directory if not exists
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                            $gambar = $new_filename;
                        }
                    }
                }
                
                // Insert artikel dengan personil_id
                $query = "INSERT INTO artikel (judul, isi, penulis, gambar, personil_id) 
                          VALUES ($1, $2, $3, $4, $5)";
                $result = pg_query_params($this->conn, $query, array($judul, $isi, $penulis, $gambar, $member_id));
                
                if ($result) {
                    header('Location: ../member/views/my_articles.php?success=add');
                    exit();
                } else {
                    $error = 'Gagal menambahkan artikel. Silakan coba lagi.';
                }
            }
        }
        
        return ['error' => $error, 'success' => $success];
    }
    
    // Edit artikel by member
    public function edit($id) {
        $error = '';
        $success = '';
        $artikel = null;
        $member_id = $_SESSION['member_id'];
        
        // Get artikel data and verify ownership
        if ($id) {
            $query = "SELECT * FROM artikel WHERE id = $1 AND personil_id = $2";
            $result = pg_query_params($this->conn, $query, array($id, $member_id));
            $artikel = pg_fetch_assoc($result);
            
            if (!$artikel) {
                $error = 'Artikel tidak ditemukan atau Anda tidak memiliki akses untuk mengedit artikel ini!';
                return ['error' => $error, 'artikel' => null];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = trim($_POST['judul']);
            $isi = trim($_POST['isi']);
            
            if (empty($judul) || empty($isi)) {
                $error = 'Judul dan isi artikel harus diisi!';
            } else {
                // Handle gambar upload
                $gambar = $artikel['gambar']; // Keep existing image by default
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = uniqid() . '.' . $ext;
                        $upload_dir = '../public/uploads/artikel/';
                        
                        // Create directory if not exists
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                            // Delete old image
                            if ($artikel['gambar'] && file_exists($upload_dir . $artikel['gambar'])) {
                                unlink($upload_dir . $artikel['gambar']);
                            }
                            $gambar = $new_filename;
                        }
                    }
                }
                
                // Update artikel
                $query = "UPDATE artikel SET judul = $1, isi = $2, gambar = $3, updated_at = NOW() 
                          WHERE id = $4 AND personil_id = $5";
                $result = pg_query_params($this->conn, $query, array($judul, $isi, $gambar, $id, $member_id));
                
                if ($result) {
                    header('Location: ../member/views/my_articles.php?success=edit');
                    exit();
                } else {
                    $error = 'Gagal mengupdate artikel. Silakan coba lagi.';
                }
            }
        }
        
        return ['error' => $error, 'success' => $success, 'artikel' => $artikel];
    }
    
    // Delete artikel by member
    public function delete($id) {
        $member_id = $_SESSION['member_id'];
        
        if ($id) {
            // Get artikel data first to verify ownership and delete image
            $query = "SELECT gambar FROM artikel WHERE id = $1 AND personil_id = $2";
            $result = pg_query_params($this->conn, $query, array($id, $member_id));
            $artikel = pg_fetch_assoc($result);
            
            if ($artikel) {
                // Delete from database
                $delete_query = "DELETE FROM artikel WHERE id = $1 AND personil_id = $2";
                $delete_result = pg_query_params($this->conn, $delete_query, array($id, $member_id));
                
                if ($delete_result) {
                    // Delete image file
                    if ($artikel['gambar'] && file_exists('../public/uploads/artikel/' . $artikel['gambar'])) {
                        unlink('../public/uploads/artikel/' . $artikel['gambar']);
                    }
                    header('Location: ../member/views/my_articles.php?success=delete');
                } else {
                    header('Location: ../member/views/my_articles.php?error=delete');
                }
            } else {
                header('Location: ../member/views/my_articles.php?error=notfound');
            }
        } else {
            header('Location: ../member/views/my_articles.php?error=invalid');
        }
        exit();
    }
    
    // Get member's articles with pagination
    public function getMyArticles($page = 1, $limit = 10, $search = '') {
        $member_id = $_SESSION['member_id'];
        $offset = ($page - 1) * $limit;
        
        // Search functionality
        $where = "WHERE personil_id = $member_id";
        if ($search) {
            $where .= " AND (judul ILIKE '%$search%' OR isi ILIKE '%$search%')";
        }
        
        // Get total records
        $count_query = "SELECT COUNT(*) as total FROM artikel $where";
        $count_result = pg_query($this->conn, $count_query);
        $total_records = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_records / $limit);
        
        // Get artikel data
        $query = "SELECT * FROM artikel $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $result = pg_query($this->conn, $query);
        
        $articles = [];
        while ($row = pg_fetch_assoc($result)) {
            $articles[] = $row;
        }
        
        return [
            'articles' => $articles,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
    
    // Get recent articles for dashboard
    public function getRecentArticles($limit = 5) {
        $member_id = $_SESSION['member_id'];
        
        $query = "SELECT id, judul, created_at FROM artikel 
                  WHERE personil_id = $1 
                  ORDER BY created_at DESC LIMIT $2";
        $result = pg_query_params($this->conn, $query, array($member_id, $limit));
        
        $articles = [];
        while ($row = pg_fetch_assoc($result)) {
            $articles[] = $row;
        }
        
        return $articles;
    }
    
    // Get total articles count for member
    public function getTotalCount() {
        $member_id = $_SESSION['member_id'];
        
        $query = "SELECT COUNT(*) as total FROM artikel WHERE personil_id = $1";
        $result = pg_query_params($this->conn, $query, array($member_id));
        
        return pg_fetch_assoc($result)['total'];
    }
}
?>
