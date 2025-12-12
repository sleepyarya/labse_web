<?php
// Controller: Member Penelitian Controller
// Description: Handles CRUD operations for member penelitian management with ownership validation

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../core/session.php';

class MemberPenelitianController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Get list of kategori
    public function getKategoriList() {
        $query = "SELECT id, nama_kategori, warna FROM kategori_penelitian WHERE is_active = TRUE ORDER BY nama_kategori ASC";
        $result = pg_query($this->conn, $query);
        $kategori_list = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $kategori_list[] = $row;
            }
        }
        return $kategori_list;
    }
    
    // Add new penelitian by member
    public function add() {
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $member_id = $_SESSION['member_id'];
            
            $judul = trim($_POST['judul']);
            $deskripsi = trim($_POST['deskripsi']);
            $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
            $abstrak = trim($_POST['abstrak']);
            $link_publikasi = trim($_POST['link_publikasi']);
            $kategori_id = isset($_POST['kategori_id']) && !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            
            // Backward compatibility for kategori string
            $kategori = '';
            if ($kategori_id) {
                // Get name from DB
                $k_query = "SELECT nama_kategori FROM kategori_penelitian WHERE id = $1";
                $k_res = pg_query_params($this->conn, $k_query, array($kategori_id));
                $k_row = pg_fetch_assoc($k_res);
                if ($k_row) $kategori = $k_row['nama_kategori'];
            } else {
                // Fallback manual input or old field
                $kategori = trim($_POST['kategori'] ?? '');
            }
            
            if (empty($judul) || empty($deskripsi) || empty($tahun)) {
                $error = 'Judul, deskripsi, dan tahun harus diisi!';
            } else {
                // Handle gambar upload
                $gambar = null;
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'penelitian_' . uniqid() . '.' . $ext;
                        $upload_dir = __DIR__ . '/../../public/uploads/penelitian/';
                        
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                            $gambar = $new_filename;
                        }
                    }
                }
                
                // Handle PDF upload
                $file_pdf = null;
                if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
                    $filename = $_FILES['file_pdf']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if ($ext == 'pdf') {
                        $new_filename = 'penelitian_' . uniqid() . '.pdf';
                        $upload_dir = __DIR__ . '/../../public/uploads/penelitian/';
                        
                        // Create dir if not exists (redundant but safe)
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['file_pdf']['tmp_name'], $upload_dir . $new_filename)) {
                            $file_pdf = $new_filename;
                        }
                    } else {
                        $error = 'File harus berformat PDF!';
                    }
                }
                
                if (empty($error)) {
                    // Insert penelitian dengan personil_id dan kategori_id
                    $query = "INSERT INTO hasil_penelitian (judul, deskripsi, tahun, kategori, abstrak, gambar, file_pdf, link_publikasi, personil_id, kategori_id) 
                              VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10) RETURNING id";
                    $result = pg_query_params($this->conn, $query, array(
                        $judul, $deskripsi, $tahun, $kategori, $abstrak, $gambar, $file_pdf, $link_publikasi, $member_id, $kategori_id
                    ));
                    
                    if ($result) {
                        $row = pg_fetch_assoc($result);
                        $penelitian_id = $row['id'];
                        
                        // Log activity: Create Penelitian
                        require_once __DIR__ . '/../../includes/activity_logger.php';
                        log_activity($this->conn, $member_id, $_SESSION['member_nama'], 'CREATE_PENELITIAN', 
                            "Membuat penelitian baru: {$judul}", 'penelitian', $penelitian_id);
                        
                        header('Location: my_penelitian.php?success=add');
                        exit();
                    } else {
                        $error = 'Gagal menambahkan penelitian: ' . pg_last_error($this->conn);
                    }
                }
            }
        }
        
        return ['error' => $error, 'success' => $success];
    }
    
    // Edit penelitian by member with ownership validation
    public function edit($id) {
        $error = '';
        $success = '';
        $penelitian = null;
        $member_id = $_SESSION['member_id'];
        
        // Get penelitian data and verify ownership
        if ($id) {
            $query = "SELECT * FROM hasil_penelitian WHERE id = $1 AND personil_id = $2";
            $result = pg_query_params($this->conn, $query, array($id, $member_id));
            $penelitian = pg_fetch_assoc($result);
            
            if (!$penelitian) {
                $error = 'Penelitian tidak ditemukan atau Anda tidak memiliki akses untuk mengedit penelitian ini!';
                return ['error' => $error, 'penelitian' => null];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = trim($_POST['judul']);
            $deskripsi = trim($_POST['deskripsi']);
            $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
            $abstrak = trim($_POST['abstrak']);
            $link_publikasi = trim($_POST['link_publikasi']);
            $kategori_id = isset($_POST['kategori_id']) && !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            
            // Backward compatibility
            $kategori = '';
            if ($kategori_id) {
                $k_query = "SELECT nama_kategori FROM kategori_penelitian WHERE id = $1";
                $k_res = pg_query_params($this->conn, $k_query, array($kategori_id));
                $k_row = pg_fetch_assoc($k_res);
                if ($k_row) $kategori = $k_row['nama_kategori'];
            } else {
                $kategori = trim($_POST['kategori'] ?? '');
            }
            
            if (empty($judul) || empty($deskripsi) || empty($tahun)) {
                $error = 'Judul, deskripsi, dan tahun harus diisi!';
            } else {
                // Handle gambar upload
                $gambar = $penelitian['gambar'];
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'penelitian_' . uniqid() . '.' . $ext;
                        $upload_dir = __DIR__ . '/../../public/uploads/penelitian/';
                        
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
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
                        $new_filename = 'penelitian_' . uniqid() . '.pdf';
                        $upload_dir = __DIR__ . '/../../public/uploads/penelitian/';
                        
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['file_pdf']['tmp_name'], $upload_dir . $new_filename)) {
                            // Delete old PDF
                            if ($penelitian['file_pdf'] && file_exists($upload_dir . $penelitian['file_pdf'])) {
                                unlink($upload_dir . $penelitian['file_pdf']);
                            }
                            $file_pdf = $new_filename;
                        }
                    }
                }
                
                // Update penelitian dengan ownership validation and kategori_id
                $query = "UPDATE hasil_penelitian 
                          SET judul = $1, deskripsi = $2, tahun = $3, kategori = $4, abstrak = $5, 
                              gambar = $6, file_pdf = $7, link_publikasi = $8, kategori_id = $9, updated_at = NOW() 
                          WHERE id = $10 AND personil_id = $11";
                $result = pg_query_params($this->conn, $query, array(
                    $judul, $deskripsi, $tahun, $kategori, $abstrak, $gambar, $file_pdf, $link_publikasi, $kategori_id, $id, $member_id
                ));
                
                if ($result) {
                    // Log activity: Edit Penelitian
                    require_once __DIR__ . '/../../includes/activity_logger.php';
                    log_activity($this->conn, $member_id, $_SESSION['member_nama'], 'EDIT_PENELITIAN', 
                        "Mengedit penelitian: {$judul}", 'penelitian', $id);
                    
                    header('Location: my_penelitian.php?success=edit');
                    exit();
                } else {
                    $error = 'Gagal mengupdate penelitian: ' . pg_last_error($this->conn);
                }
            }
        }
        
        return ['error' => $error, 'success' => $success, 'penelitian' => $penelitian];
    }
    
    // Delete penelitian by member WITH OWNERSHIP VALIDATION
    public function delete($id) {
        $member_id = $_SESSION['member_id'];
        
        if ($id) {
            // Get penelitian data first to verify ownership and delete files
            $query = "SELECT judul, gambar, file_pdf FROM hasil_penelitian WHERE id = $1 AND personil_id = $2";
            $result = pg_query_params($this->conn, $query, array($id, $member_id));
            $penelitian = pg_fetch_assoc($result);
            
            if ($penelitian) {
                // Log activity: Delete Penelitian (before deletion)
                require_once __DIR__ . '/../../includes/activity_logger.php';
                log_activity($this->conn, $member_id, $_SESSION['member_nama'], 'DELETE_PENELITIAN', 
                    "Menghapus penelitian: {$penelitian['judul']}", 'penelitian', $id);
                
                // Delete from database
                $delete_query = "DELETE FROM hasil_penelitian WHERE id = $1 AND personil_id = $2";
                $delete_result = pg_query_params($this->conn, $delete_query, array($id, $member_id));
                
                if ($delete_result) {
                    // Delete gambar file
                    if ($penelitian['gambar'] && file_exists(__DIR__ . '/../../public/uploads/penelitian/' . $penelitian['gambar'])) {
                        unlink(__DIR__ . '/../../public/uploads/penelitian/' . $penelitian['gambar']);
                    }
                    // Delete PDF file
                    if ($penelitian['file_pdf'] && file_exists(__DIR__ . '/../../public/uploads/penelitian/' . $penelitian['file_pdf'])) {
                        unlink(__DIR__ . '/../../public/uploads/penelitian/' . $penelitian['file_pdf']);
                    }
                    header('Location: my_penelitian.php?success=delete');
                } else {
                    header('Location: my_penelitian.php?error=delete');
                }
            } else {
                header('Location: my_penelitian.php?error=unauthorized');
            }
        } else {
            header('Location: my_penelitian.php?error=invalid');
        }
        exit();
    }
    
    // Get member's penelitian with pagination
    public function getMyPenelitian($page = 1, $limit = 10, $search = '', $tahun = '', $kategori = '') {
        $member_id = $_SESSION['member_id'];
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $where = "WHERE hp.personil_id = $member_id";
        if ($search) {
            $where .= " AND (hp.judul ILIKE '%$search%' OR hp.deskripsi ILIKE '%$search%')";
        }
        if ($tahun) {
            $where .= " AND hp.tahun = $tahun";
        }
        if ($kategori) {
            $where .= " AND hp.kategori = '$kategori'";
        }
        
        // Get total records
        $count_query = "SELECT COUNT(*) as total FROM hasil_penelitian hp $where";
        $count_result = pg_query($this->conn, $count_query);
        $total_records = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_records / $limit);
        
        // Get penelitian data AND kategori details
        $query = "SELECT hp.*, kp.nama_kategori as kat_nama, kp.warna as kat_warna
                  FROM hasil_penelitian hp
                  LEFT JOIN kategori_penelitian kp ON hp.kategori_id = kp.id
                  $where 
                  ORDER BY hp.tahun DESC, hp.created_at DESC 
                  LIMIT $limit OFFSET $offset";
        $result = pg_query($this->conn, $query);
        
        $penelitian = [];
        while ($row = pg_fetch_assoc($result)) {
            $penelitian[] = $row;
        }
        
        return [
            'penelitian' => $penelitian,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
    
    // Get total penelitian count for member
    public function getTotalCount() {
        $member_id = $_SESSION['member_id'];
        
        $query = "SELECT COUNT(*) as total FROM hasil_penelitian WHERE personil_id = $1";
        $result = pg_query_params($this->conn, $query, array($member_id));
        
        return pg_fetch_assoc($result)['total'];
    }
    
    // Get single penelitian with ownership validation
    public function getById($id) {
        $member_id = $_SESSION['member_id'];
        
        $query = "SELECT hp.*, kp.nama_kategori as kat_nama 
                  FROM hasil_penelitian hp
                  LEFT JOIN kategori_penelitian kp ON hp.kategori_id = kp.id
                  WHERE hp.id = $1 AND hp.personil_id = $2";
        $result = pg_query_params($this->conn, $query, array($id, $member_id));
        
        return pg_fetch_assoc($result);
    }
}
?>
