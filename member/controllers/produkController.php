<?php
// Member Controller: Produk Controller with OWNERSHIP VALIDATION
// Description: Handles CRUD for member's own produk only

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../core/session.php';

class MemberProdukController {
    
    private $conn;
    private $member_id;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        // CRITICAL: Get logged in member ID
        $this->member_id = $_SESSION['member_id'] ?? null;
    }
    
    // Get list of kategori
    public function getKategoriList() {
        $query = "SELECT id, nama_kategori, warna FROM kategori_produk WHERE is_active = TRUE ORDER BY nama_kategori ASC";
        $result = pg_query($this->conn, $query);
        $kategori_list = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $kategori_list[] = $row;
            }
        }
        return $kategori_list;
    }
    
    // Add new produk - auto assign to logged in member
    public function add() {
        $error = '';
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_produk = pg_escape_string($this->conn, trim($_POST['nama_produk']));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi']));
            $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
            $teknologi = pg_escape_string($this->conn, trim($_POST['teknologi']));
            $link_demo = pg_escape_string($this->conn, trim($_POST['link_demo']));
            $link_repository = pg_escape_string($this->conn, trim($_POST['link_repository']));
            $kategori_id = isset($_POST['kategori_id']) && !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            
            // Backward compatibility
            $kategori = '';
            if ($kategori_id) {
                $kat_query = "SELECT nama_kategori FROM kategori_produk WHERE id = $1";
                $kat_res = pg_query_params($this->conn, $kat_query, array($kategori_id));
                $kat_row = pg_fetch_assoc($kat_res);
                if ($kat_row) {
                    $kategori = $kat_row['nama_kategori'];
                }
            } else {
                $kategori = pg_escape_string($this->conn, trim($_POST['kategori'] ?? ''));
            }
            
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
                        $upload_dir = __DIR__ . '/../../public/uploads/produk/';
                        
                        // Create dir if not exists
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
                    $query = "INSERT INTO produk (nama_produk, deskripsi, tahun, kategori, teknologi, gambar, link_demo, link_repository, personil_id, kategori_id) 
                              VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10) RETURNING id";
                    $result = pg_query_params($this->conn, $query, array(
                        $nama_produk, $deskripsi, $tahun, $kategori, $teknologi, $gambar, $link_demo, $link_repository, $this->member_id, $kategori_id
                    ));
                    
                    if ($result) {
                        $row = pg_fetch_assoc($result);
                        $produk_id = $row['id'];
                        
                        // Log activity: Create Produk
                        require_once __DIR__ . '/../../includes/activity_logger.php';
                        log_activity($this->conn, $this->member_id, $_SESSION['member_nama'], 'CREATE_PRODUK', 
                            "Membuat produk baru: {$nama_produk}", 'produk', $produk_id);
                        
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
            $teknologi = pg_escape_string($this->conn, trim($_POST['teknologi']));
            $link_demo = pg_escape_string($this->conn, trim($_POST['link_demo']));
            $link_repository = pg_escape_string($this->conn, trim($_POST['link_repository']));
            $kategori_id = isset($_POST['kategori_id']) && !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            
            // Backward compatibility
            $kategori = '';
            if ($kategori_id) {
                $kat_query = "SELECT nama_kategori FROM kategori_produk WHERE id = $1";
                $kat_res = pg_query_params($this->conn, $kat_query, array($kategori_id));
                $kat_row = pg_fetch_assoc($kat_res);
                if ($kat_row) {
                    $kategori = $kat_row['nama_kategori'];
                }
            } else {
                $kategori = pg_escape_string($this->conn, trim($_POST['kategori'] ?? ''));
            }
            
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
                        $upload_dir = __DIR__ . '/../../public/uploads/produk/';
                        
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
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
                              gambar = $6, link_demo = $7, link_repository = $8, kategori_id = $9, updated_at = NOW() 
                          WHERE id = $10 AND personil_id = $11";
                $result = pg_query_params($this->conn, $query, array(
                    $nama_produk, $deskripsi, $tahun, $kategori, $teknologi, $gambar, $link_demo, $link_repository, $kategori_id, $id, $this->member_id
                ));
                
                if ($result && pg_affected_rows($result) > 0) {
                    // Log activity: Edit Produk
                    require_once __DIR__ . '/../../includes/activity_logger.php';
                    log_activity($this->conn, $this->member_id, $_SESSION['member_nama'], 'EDIT_PRODUK', 
                        "Mengedit produk: {$nama_produk}", 'produk', $id);
                    
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
            $query = "SELECT nama_produk, gambar FROM produk WHERE id = $1 AND personil_id = $2";
            $result = pg_query_params($this->conn, $query, array($id, $this->member_id));
            $produk = pg_fetch_assoc($result);
            
            if ($produk) {
                // Log activity: Delete Produk (before deletion)
                require_once __DIR__ . '/../../includes/activity_logger.php';
                log_activity($this->conn, $this->member_id, $_SESSION['member_nama'], 'DELETE_PRODUK', 
                    "Menghapus produk: {$produk['nama_produk']}", 'produk', $id);
                
                // Delete from database
                $delete_query = "DELETE FROM produk WHERE id = $1 AND personil_id = $2";
                $delete_result = pg_query_params($this->conn, $delete_query, array($id, $this->member_id));
                
                if ($delete_result && pg_affected_rows($delete_result) > 0) {
                    // Delete gambar file
                    if ($produk['gambar'] && file_exists(__DIR__ . '/../../public/uploads/produk/' . $produk['gambar'])) {
                        unlink(__DIR__ . '/../../public/uploads/produk/' . $produk['gambar']);
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
        
        // Get produk data with kategori
        $query = "SELECT p.*, kp.nama_kategori as kat_nama, kp.warna as kat_warna 
                  FROM produk p 
                  LEFT JOIN kategori_produk kp ON p.kategori_id = kp.id 
                  $where 
                  ORDER BY p.tahun DESC, p.created_at DESC 
                  LIMIT $limit OFFSET $offset";
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
        $query = "SELECT p.*, kp.nama_kategori as kat_nama 
                  FROM produk p 
                  LEFT JOIN kategori_produk kp ON p.kategori_id = kp.id 
                  WHERE p.id = $1 AND p.personil_id = $2";
        $result = pg_query_params($this->conn, $query, array($id, $this->member_id));
        return pg_fetch_assoc($result);
    }
}
?>
