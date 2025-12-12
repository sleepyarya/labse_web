<?php
// Controller: Artikel Controller
// Description: Handles CRUD operations for artikel management

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../core/session.php';

class ArtikelController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Get list of personil for dropdown
    public function getPersonilList() {
        $query = "SELECT id, nama FROM personil ORDER BY nama ASC";
        $result = pg_query($this->conn, $query);
        $personil_list = [];
        while ($row = pg_fetch_assoc($result)) {
            $personil_list[] = $row;
        }
        return $personil_list;
    }
    
    // Get list of kategori for dropdown
    public function getKategoriList() {
        $query = "SELECT id, nama_kategori, warna FROM kategori_artikel WHERE is_active = TRUE ORDER BY nama_kategori ASC";
        $result = pg_query($this->conn, $query);
        $kategori_list = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $kategori_list[] = $row;
            }
        }
        return $kategori_list;
    }

    // Add new artikel
    public function add() {
        $error = '';
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = pg_escape_string($this->conn, trim($_POST['judul']));
            $isi = pg_escape_string($this->conn, trim($_POST['isi']));
            
            // Handle personil selection
            $personil_id = !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : 'NULL';
            
            // Handle kategori selection
            $kategori_id = !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            
            // If personil selected, use their name. Otherwise use manual input
            if ($personil_id !== 'NULL') {
                $query_personil = "SELECT nama FROM personil WHERE id = $1";
                $result_personil = pg_query_params($this->conn, $query_personil, array($personil_id));
                $personil_data = pg_fetch_assoc($result_personil);
                $penulis = pg_escape_string($this->conn, $personil_data['nama']);
            } else {
                $penulis = pg_escape_string($this->conn, trim($_POST['penulis']));
            }
            
            // Validation
            if (empty($judul) || empty($isi) || empty($penulis)) {
                $error = 'Judul, isi, dan penulis wajib diisi!';
            } else {
                // Handle file upload
                $gambar = '';
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'artikel_' . time() . '_' . uniqid() . '.' . $ext;
                        // FIX: Use correct absolute path
                        $upload_dir = __DIR__ . '/../../public/uploads/artikel/';
                        
                        // Create directory if not exists
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                            $gambar = $new_filename;
                        }
                    } else {
                        $error = 'Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
                    }
                }
                
                // Insert to database
                if (empty($error)) {
                    $query = "INSERT INTO artikel (judul, isi, penulis, gambar, personil_id, kategori_id) 
                              VALUES ($1, $2, $3, $4, $5, $6)";
                    
                    // Handle NULL for personil_id correctly in pg_query_params
                    $params = array($judul, $isi, $penulis, $gambar);
                    if ($personil_id === 'NULL') {
                        $params[] = null;
                    } else {
                        $params[] = $personil_id;
                    }
                    $params[] = $kategori_id;
                    
                    $result = pg_query_params($this->conn, $query, $params);
                    
                    if ($result) {
                        header('Location: ../views/manage_artikel.php?success=add');
                        exit();
                    } else {
                        $error = 'Gagal menambahkan artikel: ' . pg_last_error($this->conn);
                    }
                }
            }
        }
        
        return ['error' => $error, 'success' => $success];
    }
    
    // Edit artikel
    public function edit($id) {
        $error = '';
        $success = false;
        $artikel = null;
        
        // Get artikel data
        if ($id) {
            $query = "SELECT * FROM artikel WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $artikel = pg_fetch_assoc($result);
            
            if (!$artikel) {
                $error = 'Artikel tidak ditemukan!';
                return ['error' => $error, 'artikel' => null];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = pg_escape_string($this->conn, trim($_POST['judul']));
            $isi = pg_escape_string($this->conn, trim($_POST['isi']));
            
            // Handle personil selection
            $personil_id = !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : 'NULL';
            
            // Handle kategori selection
            $kategori_id = !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            
            if ($personil_id !== 'NULL') {
                $query_personil = "SELECT nama FROM personil WHERE id = $1";
                $result_personil = pg_query_params($this->conn, $query_personil, array($personil_id));
                $personil_data = pg_fetch_assoc($result_personil);
                $penulis = pg_escape_string($this->conn, $personil_data['nama']);
            } else {
                $penulis = pg_escape_string($this->conn, trim($_POST['penulis']));
            }
            
            // Validation
            if (empty($judul) || empty($isi) || empty($penulis)) {
                $error = 'Judul, isi, dan penulis wajib diisi!';
            } else {
                // Handle file upload
                $gambar = $artikel['gambar']; // Keep existing image by default
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'artikel_' . time() . '_' . uniqid() . '.' . $ext;
                        // FIX: Use correct absolute path
                        $upload_dir = __DIR__ . '/../../public/uploads/artikel/';
                        
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
                    } else {
                        $error = 'Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
                    }
                }
                
                // Update database
                if (empty($error)) {
                    $query = "UPDATE artikel SET judul = $1, isi = $2, penulis = $3, gambar = $4, personil_id = $5, kategori_id = $6, updated_at = NOW() 
                              WHERE id = $7";
                    
                    $params = array($judul, $isi, $penulis, $gambar);
                    if ($personil_id === 'NULL') {
                        $params[] = null;
                    } else {
                        $params[] = $personil_id;
                    }
                    $params[] = $kategori_id;
                    $params[] = $id;
                    
                    $result = pg_query_params($this->conn, $query, $params);
                    
                    if ($result) {
                        header('Location: ../views/manage_artikel.php?success=edit');
                        exit();
                    } else {
                        $error = 'Gagal mengupdate artikel: ' . pg_last_error($this->conn);
                    }
                }
            }
        }
        
        return ['error' => $error, 'success' => $success, 'artikel' => $artikel];
    }
    
    // Delete artikel
    public function delete($id) {
        if ($id) {
            // Get artikel data first to delete image
            $query = "SELECT gambar FROM artikel WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $artikel = pg_fetch_assoc($result);
            
            if ($artikel) {
                // Delete from database
                $delete_query = "DELETE FROM artikel WHERE id = $1";
                $delete_result = pg_query_params($this->conn, $delete_query, array($id));
                
                if ($delete_result) {
                    // Delete image file
                    // FIX: Use correct public path
                    if ($artikel['gambar'] && file_exists('../public/uploads/artikel/' . $artikel['gambar'])) {
                        unlink('../public/uploads/artikel/' . $artikel['gambar']);
                    }
                    header('Location: ../views/manage_artikel.php?success=delete');
                } else {
                    header('Location: ../views/manage_artikel.php?error=delete');
                }
            } else {
                header('Location: ../views/manage_artikel.php?error=notfound');
            }
        } else {
            header('Location: ../views/manage_artikel.php?error=invalid');
        }
        exit();
    }
    
    // Get all artikel with pagination
    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        
        // Search functionality
        $where = $search ? "WHERE judul ILIKE '%$search%' OR penulis ILIKE '%$search%' OR isi ILIKE '%$search%'" : '';
        
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
}
?>
