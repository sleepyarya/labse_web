<?php
// Controller: Penelitian Controller (Admin)
// Description: Handles CRUD operations for penelitian management - FULL ACCESS for admin

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';

class PenelitianController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    public function getKategoriList() {
        $query = "SELECT id, nama_kategori FROM kategori_penelitian WHERE is_active = TRUE ORDER BY nama_kategori ASC";
        $result = pg_query($this->conn, $query);
        
        $list = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $list[] = $row;
            }
        }
        return $list;
    }

    // Add new penelitian
    public function add() {
        $error = '';
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = pg_escape_string($this->conn, trim($_POST['judul']));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi']));
            $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
            // FIX: Ambil kategori_id (integer) dari form, bukan kategori (string)
            $kategori_id = isset($_POST['kategori']) ? (int)$_POST['kategori'] : null;
            $abstrak = pg_escape_string($this->conn, trim($_POST['abstrak']));
            $link_publikasi = pg_escape_string($this->conn, trim($_POST['link_publikasi']));
            $personil_id = isset($_POST['personil_id']) && !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : null;
            
            // Validation
            if (empty($judul) || empty($deskripsi) || empty($tahun)) {
                $error = 'Judul, deskripsi, dan tahun wajib diisi!';
            } else {
                // Handle gambar upload
                $gambar = '';
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'penelitian_' . time() . '_' . uniqid() . '.' . $ext;
                        $upload_dir = '../uploads/penelitian/';
                        
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                            $gambar = $new_filename;
                        }
                    } else {
                        $error = 'Format gambar tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
                    }
                }
                
                // Handle PDF upload
                $file_pdf = '';
                if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
                    $filename = $_FILES['file_pdf']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if ($ext == 'pdf') {
                        $new_filename = 'penelitian_' . time() . '_' . uniqid() . '.pdf';
                        $upload_dir = '../uploads/penelitian/';
                        
                        if (move_uploaded_file($_FILES['file_pdf']['tmp_name'], $upload_dir . $new_filename)) {
                            $file_pdf = $new_filename;
                        }
                    } else {
                        $error = 'File harus berformat PDF!';
                    }
                }
                
                // Insert to database using Stored Procedure
                if (empty($error)) {
                    // sp_create_penelitian with 10 parameters
                    $query = "SELECT sp_create_penelitian($1, $2, $3, $4, $5, $6, $7, $8, $9, $10) as new_id";

                    $params = array(
                        $judul, 
                        $deskripsi, 
                        (int)$tahun, 
                        "", // String kosong untuk nama kategori
                        $abstrak, 
                        $gambar, 
                        $file_pdf, 
                        $link_publikasi, 
                        $personil_id,
                        $kategori_id // Integer, ambil dari form
                    );

                    $result = pg_query_params($this->conn, $query, $params);
                    
                    if ($result) {
                        header('Location: ' . BASE_URL . '/admin/manage_penelitian.php?success=add');
                        exit();
                    } else {
                        $error = 'Gagal menambahkan penelitian: ' . pg_last_error($this->conn);
                    }
                }
            }
        }
        
        return ['error' => $error, 'success' => $success];
    }
    
    // Edit penelitian - FULL ACCESS (no ownership check)
    public function edit($id) {
        $error = '';
        $success = false;
        $penelitian = null;
        
        // Get penelitian data
        if ($id) {
            $query = "SELECT * FROM hasil_penelitian WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $penelitian = pg_fetch_assoc($result);
            
            if (!$penelitian) {
                $error = 'Data penelitian tidak ditemukan!';
                return ['error' => $error, 'penelitian' => null];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = pg_escape_string($this->conn, trim($_POST['judul']));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi']));
            $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
            $kategori = pg_escape_string($this->conn, trim($_POST['kategori']));
            $abstrak = pg_escape_string($this->conn, trim($_POST['abstrak']));
            $link_publikasi = pg_escape_string($this->conn, trim($_POST['link_publikasi']));
            $personil_id = isset($_POST['personil_id']) && !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : null;
            
            // Validation
            if (empty($judul) || empty($deskripsi) || empty($tahun)) {
                $error = 'Judul, deskripsi, dan tahun wajib diisi!';
            } else {
                // Handle gambar upload
                $gambar = $penelitian['gambar'];
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'penelitian_' . time() . '_' . uniqid() . '.' . $ext;
                        $upload_dir = '../uploads/penelitian/';
                        
                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                            // Delete old image
                            if ($penelitian['gambar'] && file_exists($upload_dir . $penelitian['gambar'])) {
                                unlink($upload_dir . $penelitian['gambar']);
                            }
                            $gambar = $new_filename;
                        }
                    }
                }
                
                // Handle PDF upload
                $file_pdf = $penelitian['file_pdf'];
                if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
                    $filename = $_FILES['file_pdf']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if ($ext == 'pdf') {
                        $new_filename = 'penelitian_' . time() . '_' . uniqid() . '.pdf';
                        $upload_dir = '../uploads/penelitian/';
                        
                        if (move_uploaded_file($_FILES['file_pdf']['tmp_name'], $upload_dir . $new_filename)) {
                            // Delete old PDF
                            if ($penelitian['file_pdf'] && file_exists($upload_dir . $penelitian['file_pdf'])) {
                                unlink($upload_dir . $penelitian['file_pdf']);
                            }
                            $file_pdf = $new_filename;
                        }
                    }
                }
                
                // Update database - NO ownership check, admin can edit all
                $query = "UPDATE hasil_penelitian 
                          SET judul = $1, deskripsi = $2, tahun = $3, kategori = $4, abstrak = $5, 
                              gambar = $6, file_pdf = $7, link_publikasi = $8, personil_id = $9, updated_at = NOW() 
                          WHERE id = $10";
                $result = pg_query_params($this->conn, $query, array(
                    $judul, $deskripsi, $tahun, $kategori, $abstrak, $gambar, $file_pdf, $link_publikasi, $personil_id, $id
                ));
                
                if ($result) {
                    header('Location: ' . BASE_URL . '/admin/manage_penelitian.php?success=edit');
                    exit();
                } else {
                    $error = 'Gagal mengupdate penelitian: ' . pg_last_error($this->conn);
                }
            }
        }
        
        return ['error' => $error, 'success' => $success, 'penelitian' => $penelitian];
    }
    
    // Delete penelitian - FULL ACCESS (no ownership check)
    public function delete($id) {
        if ($id) {
            // Get penelitian data first to delete files
            $query = "SELECT gambar, file_pdf FROM hasil_penelitian WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $penelitian = pg_fetch_assoc($result);
            
            if ($penelitian) {
                // Delete from database
                $delete_query = "DELETE FROM hasil_penelitian WHERE id = $1";
                $delete_result = pg_query_params($this->conn, $delete_query, array($id));
                
                if ($delete_result) {
                    // Delete gambar file
                    if ($penelitian['gambar'] && file_exists('../uploads/penelitian/' . $penelitian['gambar'])) {
                        unlink('../uploads/penelitian/' . $penelitian['gambar']);
                    }
                    // Delete PDF file
                    if ($penelitian['file_pdf'] && file_exists('../uploads/penelitian/' . $penelitian['file_pdf'])) {
                        unlink('../uploads/penelitian/' . $penelitian['file_pdf']);
                    }
                    header('Location: ' . BASE_URL . '/admin/manage_penelitian.php?success=delete');
                } else {
                    header('Location: ' . BASE_URL . '/admin/manage_penelitian.php?error=delete');
                }
            } else {
                header('Location: ' . BASE_URL . '/admin/manage_penelitian.php?error=notfound');
            }
        } else {
            header('Location: ' . BASE_URL . '/admin/manage_penelitian.php?error=invalid');
        }
        exit();
    }
    
    // Get all penelitian with pagination
    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        
        // Search functionality
        $where = $search ? "WHERE judul ILIKE '%$search%' OR deskripsi ILIKE '%$search%' OR abstrak ILIKE '%$search%' OR kategori ILIKE '%$search%'" : '';
        
        // Get total records
        $count_query = "SELECT COUNT(*) as total FROM hasil_penelitian $where";
        $count_result = pg_query($this->conn, $count_query);
        $total_records = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_records / $limit);
        
        // Get penelitian data with personil name
        $query = "SELECT hp.*, p.nama as personil_nama 
                  FROM hasil_penelitian hp
                  LEFT JOIN personil p ON hp.personil_id = p.id
                  $where 
                  ORDER BY hp.tahun DESC, hp.created_at DESC 
                  LIMIT $limit OFFSET $offset";
        $result = pg_query($this->conn, $query);
        
        $items = [];
        while ($row = pg_fetch_assoc($result)) {
            $items[] = $row;
        }
        
        return [
            'items' => $items,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
    
    // Get single penelitian by ID
    public function getById($id) {
        $query = "SELECT * FROM hasil_penelitian WHERE id = $1";
        $result = pg_query_params($this->conn, $query, array($id));
        return pg_fetch_assoc($result);
    }
    
    // Get all personil for dropdown
    public function getAllPersonil() {
        $query = "SELECT id, nama FROM personil ORDER BY nama ASC";
        $result = pg_query($this->conn, $query);
        
        $personil = [];
        while ($row = pg_fetch_assoc($result)) {
            $personil[] = $row;
        }
        return $personil;
    }
}
?>
