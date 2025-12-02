<?php
// Member Controller: Produk Controller with OWNERSHIP VALIDATION
// Description: Handles CRUD for member's own produk only

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';

class MemberProdukController {
    
    private $conn;
    private $member_id;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        // CRITICAL: Get logged in member ID - FIXED to use 'member_id' like other controllers
        $this->member_id = $_SESSION['member_id'] ?? null;
    }
    
    // Add new produk - auto assign to logged in member
    public function add() {
        $error = '';
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_produk = pg_escape_string($this->conn, trim($_POST['nama_produk']));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi']));
            $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
            $kategori = pg_escape_string($this->conn, trim($_POST['kategori']));
            $teknologi = pg_escape_string($this->conn, trim($_POST['teknologi']));
            $link_demo = pg_escape_string($this->conn, trim($_POST['link_demo']));
            $link_repository = pg_escape_string($this->conn, trim($_POST['link_repository']));
            
            // Validation
            if (empty($nama_produk) || empty($deskripsi) || empty($tahun)) {
                $error = 'Nama produk, deskripsi, dan tahun wajib diisi!';
            } else {
                // Handle gambar upload
                $gambar = '';
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'produk_' . time() . '_' . uniqid() . '.' . $ext;
                        $upload_dir = '../../uploads/produk/';
                        
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
                
                // Insert to database - CRITICAL: Always use logged in member ID
                if (empty($error)) {
                    $query = "INSERT INTO produk (nama_produk, deskripsi, tahun, kategori, teknologi, gambar, link_demo, link_repository, personil_id) 
                              VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";
                    $result = pg_query_params($this->conn, $query, array(
                        $nama_produk, $deskripsi, $tahun, $kategori, $teknologi, $gambar, $link_demo, $link_repository, $this->member_id
                    ));
                    
                    if ($result) {
                        header('Location: my_produk.php?success=add');
                        exit();
                    } else {
                        $error = 'Gagal menambahkan produk: ' . pg_last_error($this->conn);
                    }
                }
            }
        }
        
        return ['error' => $error, 'success' => $success];
    }
    
    // Edit produk - ONLY OWN PRODUCTS
    public function edit($id) {
        $error = '';
        $success = false;
        $produk = null;
        
        // Get produk data - CRITICAL: Check ownership
        if ($id) {
            $query = "SELECT * FROM produk WHERE id = $1 AND personil_id = $2";
            $result = pg_query_params($this->conn, $query, array($id, $this->member_id));
            $produk = pg_fetch_assoc($result);
            
            if (!$produk) {
                $error = 'Data produk tidak ditemukan atau Anda tidak memiliki akses!';
                return ['error' => $error, 'produk' => null];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_produk = pg_escape_string($this->conn, trim($_POST['nama_produk']));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi']));
            $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
            $kategori = pg_escape_string($this->conn, trim($_POST['kategori']));
            $teknologi = pg_escape_string($this->conn, trim($_POST['teknologi']));
            $link_demo = pg_escape_string($this->conn, trim($_POST['link_demo']));
            $link_repository = pg_escape_string($this->conn, trim($_POST['link_repository']));
            
            // Validation
            if (empty($nama_produk) || empty($deskripsi) || empty($tahun)) {
                $error = 'Nama produk, deskripsi, dan tahun wajib diisi!';
            } else {
                // Handle gambar upload
                $gambar = $produk['gambar'];
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'produk_' . time() . '_' . uniqid() . '.' . $ext;
                        $upload_dir = '../../uploads/produk/';
                        
                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                            // Delete old image
                            if ($produk['gambar'] && file_exists($upload_dir . $produk['gambar'])) {
                                unlink($upload_dir . $produk['gambar']);
                            }
                            $gambar = $new_filename;
                        }
                    }
                }
                
                // Update database - CRITICAL: Always check ownership
                $query = "UPDATE produk 
                          SET nama_produk = $1, deskripsi = $2, tahun = $3, kategori = $4, teknologi = $5, 
                              gambar = $6, link_demo = $7, link_repository = $8, updated_at = NOW() 
                          WHERE id = $9 AND personil_id = $10";
                $result = pg_query_params($this->conn, $query, array(
                    $nama_produk, $deskripsi, $tahun, $kategori, $teknologi, $gambar, $link_demo, $link_repository, $id, $this->member_id
                ));
                
                if ($result && pg_affected_rows($result) > 0) {
                    header('Location: my_produk.php?success=edit');
                    exit();
                } else {
                    $error = 'Gagal mengupdate produk atau Anda tidak memiliki akses!';
                }
            }
        }
        
        return ['error' => $error, 'success' => $success, 'produk' => $produk];
    }
    
    // Delete produk - ONLY OWN PRODUCTS
    public function delete($id) {
        if ($id) {
            // Get produk data - CRITICAL: Check ownership
            $query = "SELECT gambar FROM produk WHERE id = $1 AND personil_id = $2";
            $result = pg_query_params($this->conn, $query, array($id, $this->member_id));
            $produk = pg_fetch_assoc($result);
            
            if ($produk) {
                // Delete from database
                $delete_query = "DELETE FROM produk WHERE id = $1 AND personil_id = $2";
                $delete_result = pg_query_params($this->conn, $delete_query, array($id, $this->member_id));
                
                if ($delete_result && pg_affected_rows($delete_result) > 0) {
                    // Delete gambar file
                    if ($produk['gambar'] && file_exists('../../uploads/produk/' . $produk['gambar'])) {
                        unlink('../../uploads/produk/' . $produk['gambar']);
                    }
                    header('Location: my_produk.php?success=delete');
                } else {
                    header('Location: my_produk.php?error=unauthorized');
                }
            } else {
                header('Location: my_produk.php?error=notfound');
            }
        } else {
            header('Location: my_produk.php?error=invalid');
        }
        exit();
    }
    
    // Get member's own produk with pagination
    public function getMyProduk($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        
        // Search functionality
        $where = "WHERE personil_id = $1";
        $params = [$this->member_id];
        
        if ($search) {
            $where .= " AND (nama_produk ILIKE $2 OR deskripsi ILIKE $2 OR teknologi ILIKE $2)";
            $params[] = "%$search%";
        }
        
        // Get total records
        $count_query = "SELECT COUNT(*) as total FROM produk $where";
        $count_result = pg_query_params($this->conn, $count_query, $params);
        $total_records = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_records / $limit);
        
        // Get produk data
        $query = "SELECT * FROM produk $where ORDER BY tahun DESC, created_at DESC LIMIT $limit OFFSET $offset";
        $result = pg_query_params($this->conn, $query, $params);
        
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
    
    // Get single produk by ID - ONLY OWN PRODUCTS
    public function getById($id) {
        $query = "SELECT * FROM produk WHERE id = $1 AND personil_id = $2";
        $result = pg_query_params($this->conn, $query, array($id, $this->member_id));
        return pg_fetch_assoc($result);
    }
}
?>
