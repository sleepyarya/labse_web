<?php
// Controller: Member Penelitian Controller
// Description: Handles CRUD operations for member penelitian management with ownership validation

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';

class MemberPenelitianController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
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
            $kategori = trim($_POST['kategori']);
            $abstrak = trim($_POST['abstrak']);
            $link_publikasi = trim($_POST['link_publikasi']);
            
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
                        $upload_dir = '../uploads/penelitian/';
                        
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
                        $upload_dir = '../uploads/penelitian/';
                        
                        if (move_uploaded_file($_FILES['file_pdf']['tmp_name'], $upload_dir . $new_filename)) {
                            $file_pdf = $new_filename;
                        }
                    } else {
                        $error = 'File harus berformat PDF!';
                    }
                }
                
                if (empty($error)) {
                    // Insert penelitian dengan personil_id
                    $query = "INSERT INTO hasil_penelitian (judul, deskripsi, tahun, kategori, abstrak, gambar, file_pdf, link_publikasi, personil_id) 
                              VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";
                    $result = pg_query_params($this->conn, $query, array(
                        $judul, $deskripsi, $tahun, $kategori, $abstrak, $gambar, $file_pdf, $link_publikasi, $member_id
                    ));
                    
                    if ($result) {
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
            $kategori = trim($_POST['kategori']);
            $abstrak = trim($_POST['abstrak']);
            $link_publikasi = trim($_POST['link_publikasi']);
            
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
                        $new_filename = 'penelitian_' . uniqid() . '.pdf';
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
                
                // Update penelitian dengan ownership validation
                $query = "UPDATE hasil_penelitian 
                          SET judul = $1, deskripsi = $2, tahun = $3, kategori = $4, abstrak = $5, 
                              gambar = $6, file_pdf = $7, link_publikasi = $8, updated_at = NOW() 
                          WHERE id = $9 AND personil_id = $10";
                $result = pg_query_params($this->conn, $query, array(
                    $judul, $deskripsi, $tahun, $kategori, $abstrak, $gambar, $file_pdf, $link_publikasi, $id, $member_id
                ));
                
                if ($result) {
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
            $query = "SELECT gambar, file_pdf FROM hasil_penelitian WHERE id = $1 AND personil_id = $2";
            $result = pg_query_params($this->conn, $query, array($id, $member_id));
            $penelitian = pg_fetch_assoc($result);
            
            if ($penelitian) {
                // Delete from database
                $delete_query = "DELETE FROM hasil_penelitian WHERE id = $1 AND personil_id = $2";
                $delete_result = pg_query_params($this->conn, $delete_query, array($id, $member_id));
                
                if ($delete_result) {
                    // Delete gambar file
                    if ($penelitian['gambar'] && file_exists('../uploads/penelitian/' . $penelitian['gambar'])) {
                        unlink('../uploads/penelitian/' . $penelitian['gambar']);
                    }
                    // Delete PDF file
                    if ($penelitian['file_pdf'] && file_exists('../uploads/penelitian/' . $penelitian['file_pdf'])) {
                        unlink('../uploads/penelitian/' . $penelitian['file_pdf']);
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
        $where = "WHERE personil_id = $member_id";
        if ($search) {
            $where .= " AND (judul ILIKE '%$search%' OR deskripsi ILIKE '%$search%')";
        }
        if ($tahun) {
            $where .= " AND tahun = $tahun";
        }
        if ($kategori) {
            $where .= " AND kategori = '$kategori'";
        }
        
        // Get total records
        $count_query = "SELECT COUNT(*) as total FROM hasil_penelitian $where";
        $count_result = pg_query($this->conn, $count_query);
        $total_records = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_records / $limit);
        
        // Get penelitian data
        $query = "SELECT * FROM hasil_penelitian $where ORDER BY tahun DESC, created_at DESC LIMIT $limit OFFSET $offset";
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
        
        $query = "SELECT * FROM hasil_penelitian WHERE id = $1 AND personil_id = $2";
        $result = pg_query_params($this->conn, $query, array($id, $member_id));
        
        return pg_fetch_assoc($result);
    }
}
?>
