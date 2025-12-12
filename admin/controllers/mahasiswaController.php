<?php
// Controller: Mahasiswa Controller
// Description: Handles CRUD operations for mahasiswa management

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../core/session.php';

class MahasiswaController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Add new mahasiswa
    public function add() {
        $error = '';
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama = pg_escape_string($this->conn, trim($_POST['nama']));
            $nim = pg_escape_string($this->conn, trim($_POST['nim']));
            $jurusan_id = isset($_POST['jurusan_id']) ? (int)$_POST['jurusan_id'] : 0;
            $email = pg_escape_string($this->conn, trim($_POST['email']));
            $alasan = pg_escape_string($this->conn, trim($_POST['alasan']));
            
            // Get jurusan name from ID
            $jurusan = '';
            if ($jurusan_id > 0) {
                $jurusan_query = "SELECT nama_jurusan FROM jurusan WHERE id = $1 AND is_active = TRUE";
                $jurusan_result = pg_query_params($this->conn, $jurusan_query, array($jurusan_id));
                if ($jurusan_result && pg_num_rows($jurusan_result) > 0) {
                    $jurusan_data = pg_fetch_assoc($jurusan_result);
                    $jurusan = $jurusan_data['nama_jurusan'];
                }
            }
            
            // Validation
            if (empty($nama) || empty($nim) || empty($jurusan_id) || empty($email)) {
                $error = 'Nama, NIM, jurusan, dan email wajib diisi!';
            } elseif (empty($jurusan)) {
                $error = 'Jurusan tidak valid!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Format email tidak valid!';
            } else {
                // Check NIM duplicate
                $check_query = "SELECT COUNT(*) as total FROM mahasiswa WHERE nim = $1";
                $check_result = pg_query_params($this->conn, $check_query, array($nim));
                $check_data = pg_fetch_assoc($check_result);
                
                if ($check_data['total'] > 0) {
                    $error = 'NIM sudah terdaftar! Gunakan NIM yang berbeda.';
                } else {
                    // Insert to database with pending status and jurusan_id
                    $query = "INSERT INTO mahasiswa (nama, nim, jurusan, jurusan_id, email, alasan, status_approval, created_at) 
                              VALUES ($1, $2, $3, $4, $5, $6, 'pending', NOW())";
                    $result = pg_query_params($this->conn, $query, array($nama, $nim, $jurusan, $jurusan_id, $email, $alasan));
                    
                    if ($result) {
                        header('Location: ../views/manage_mahasiswa.php?success=add');
                        exit();
                    } else {
                        $error = 'Gagal menambahkan data: ' . pg_last_error($this->conn);
                    }
                }
            }
        }
        
        return ['error' => $error, 'success' => $success];
    }
    
    // Edit mahasiswa
    public function edit($id) {
        $error = '';
        $success = false;
        $mahasiswa = null;
        
        // Get mahasiswa data
        if ($id) {
            $query = "SELECT * FROM mahasiswa WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $mahasiswa = pg_fetch_assoc($result);
            
            if (!$mahasiswa) {
                $error = 'Data mahasiswa tidak ditemukan!';
                return ['error' => $error, 'mahasiswa' => null];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama = pg_escape_string($this->conn, trim($_POST['nama']));
            $nim = pg_escape_string($this->conn, trim($_POST['nim']));
            $jurusan_id = isset($_POST['jurusan_id']) ? (int)$_POST['jurusan_id'] : 0;
            $email = pg_escape_string($this->conn, trim($_POST['email']));
            $alasan = pg_escape_string($this->conn, trim($_POST['alasan']));
            
            // Get jurusan name from ID
            $jurusan = '';
            if ($jurusan_id > 0) {
                $jurusan_query = "SELECT nama_jurusan FROM jurusan WHERE id = $1 AND is_active = TRUE";
                $jurusan_result = pg_query_params($this->conn, $jurusan_query, array($jurusan_id));
                if ($jurusan_result && pg_num_rows($jurusan_result) > 0) {
                    $jurusan_data = pg_fetch_assoc($jurusan_result);
                    $jurusan = $jurusan_data['nama_jurusan'];
                }
            }
            
            // Validation
            if (empty($nama) || empty($nim) || empty($jurusan_id) || empty($email)) {
                $error = 'Nama, NIM, jurusan, dan email wajib diisi!';
            } elseif (empty($jurusan)) {
                $error = 'Jurusan tidak valid!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Format email tidak valid!';
            } else {
                // Check NIM duplicate (exclude current record)
                $check_query = "SELECT COUNT(*) as total FROM mahasiswa WHERE nim = $1 AND id != $2";
                $check_result = pg_query_params($this->conn, $check_query, array($nim, $id));
                $check_data = pg_fetch_assoc($check_result);
                
                if ($check_data['total'] > 0) {
                    $error = 'NIM sudah terdaftar! Gunakan NIM yang berbeda.';
                } else {
                    // Update database with jurusan_id
                    $query = "UPDATE mahasiswa SET nama = $1, nim = $2, jurusan = $3, jurusan_id = $4, email = $5, alasan = $6, updated_at = NOW() 
                              WHERE id = $7";
                    $result = pg_query_params($this->conn, $query, array($nama, $nim, $jurusan, $jurusan_id, $email, $alasan, $id));
                    
                    if ($result) {
                        header('Location: ../views/manage_mahasiswa.php?success=edit');
                        exit();
                    } else {
                        $error = 'Gagal mengupdate data: ' . pg_last_error($this->conn);
                    }
                }
            }
        }
        
        return ['error' => $error, 'success' => $success, 'mahasiswa' => $mahasiswa];
    }
    
    // Delete mahasiswa
    public function delete($id) {
        if ($id) {
            $delete_query = "DELETE FROM mahasiswa WHERE id = $1";
            $delete_result = pg_query_params($this->conn, $delete_query, array($id));
            
            if ($delete_result) {
                header('Location: ../views/manage_mahasiswa.php?success=delete');
            } else {
                header('Location: ../views/manage_mahasiswa.php?error=delete');
            }
        } else {
            header('Location: ../views/manage_mahasiswa.php?error=invalid');
        }
        exit();
    }
    
    // Get all mahasiswa with pagination and status filter
    public function getAll($page = 1, $limit = 10, $search = '', $status = '') {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $where_conditions = [];
        
        if ($search) {
            $where_conditions[] = "(m.nama ILIKE '%$search%' OR m.nim ILIKE '%$search%' OR m.jurusan ILIKE '%$search%' OR m.email ILIKE '%$search%')";
        }
        
        if ($status) {
            $where_conditions[] = "m.status_approval = '$status'";
        }
        
        $where = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        // Get total records with proper alias
        $count_query = "SELECT COUNT(*) as total FROM mahasiswa m $where";
        $count_result = pg_query($this->conn, $count_query);
        
        if (!$count_result) {
            error_log("Count query failed: " . pg_last_error($this->conn));
            return [
                'mahasiswa' => [],
                'total_records' => 0,
                'total_pages' => 0,
                'current_page' => $page
            ];
        }
        
        $total_records = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_records / $limit);
        
        // Get mahasiswa data with admin info for approved_by
        $query = "SELECT m.*, au.nama_lengkap as approved_by_name 
                  FROM mahasiswa m 
                  LEFT JOIN admin_users au ON m.approved_by = au.id 
                  $where 
                  ORDER BY m.created_at DESC 
                  LIMIT $limit OFFSET $offset";
        $result = pg_query($this->conn, $query);
        
        if (!$result) {
            error_log("Main query failed: " . pg_last_error($this->conn));
            return [
                'mahasiswa' => [],
                'total_records' => 0,
                'total_pages' => 0,
                'current_page' => $page
            ];
        }
        
        $mahasiswa = [];
        while ($row = pg_fetch_assoc($result)) {
            $mahasiswa[] = $row;
        }
        
        return [
            'mahasiswa' => $mahasiswa,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
    
    // Approve mahasiswa
    public function approve($id, $admin_id) {
        // First, get mahasiswa data
        $query_get = "SELECT * FROM mahasiswa WHERE id = $1";
        $result_get = pg_query_params($this->conn, $query_get, array($id));
        $mahasiswa = pg_fetch_assoc($result_get);
        
        if (!$mahasiswa) {
            return ['success' => false, 'message' => 'Data mahasiswa tidak ditemukan!'];
        }
        
        // Start transaction
        pg_query($this->conn, "BEGIN");
        
        try {
            // 1. Update status
            $query_update = "UPDATE mahasiswa 
                      SET status_approval = 'approved', 
                          approved_by = $1, 
                          approved_at = NOW() 
                      WHERE id = $2";
            $result_update = pg_query_params($this->conn, $query_update, array($admin_id, $id));
            
            if (!$result_update) {
                throw new Exception("Gagal update status mahasiswa");
            }
            
            pg_query($this->conn, "COMMIT");
            return ['success' => true, 'message' => 'Mahasiswa berhasil disetujui!'];
            
        } catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }
    
    // Reject mahasiswa
    public function reject($id, $admin_id, $reason = '') {
        $query = "UPDATE mahasiswa 
                  SET status_approval = 'rejected', 
                      approved_by = $1, 
                      approved_at = NOW(), 
                      rejection_reason = $3 
                  WHERE id = $2";
        $result = pg_query_params($this->conn, $query, array($admin_id, $id, $reason));
        
        if ($result) {
            return ['success' => true, 'message' => 'Mahasiswa berhasil ditolak!'];
        } else {
            return ['success' => false, 'message' => 'Gagal menolak mahasiswa!'];
        }
    }
    
    // Get statistics
    public function getStatistics() {
        $stats = [];
        
        // Total mahasiswa
        $total_query = "SELECT COUNT(*) as total FROM mahasiswa";
        $stats['total'] = pg_fetch_result(pg_query($this->conn, $total_query), 0, 0);
        
        // By status
        $status_query = "SELECT status_approval, COUNT(*) as count FROM mahasiswa GROUP BY status_approval";
        $status_result = pg_query($this->conn, $status_query);
        
        $stats['by_status'] = [];
        while ($row = pg_fetch_assoc($status_result)) {
            $stats['by_status'][$row['status_approval']] = $row['count'];
        }
        
        return $stats;
    }
}
?>
