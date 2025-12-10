<?php
// Controller: Mahasiswa Controller
// Description: Handles CRUD operations for mahasiswa management

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';

class MahasiswaController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Add new mahasiswa (Manual input by Admin)
    public function add() {
        $error = '';
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama = pg_escape_string($this->conn, trim($_POST['nama']));
            $nim = pg_escape_string($this->conn, trim($_POST['nim']));
            $jurusan = pg_escape_string($this->conn, trim($_POST['jurusan']));
            $email = pg_escape_string($this->conn, trim($_POST['email']));
            $alasan = pg_escape_string($this->conn, trim($_POST['alasan']));
            
            // Validation
            if (empty($nama) || empty($nim) || empty($jurusan) || empty($email)) {
                $error = 'Nama, NIM, jurusan, dan email wajib diisi!';
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
                    // Insert to database with pending status
                    // Note: Manual add doesn't create user account yet, approval needed
                    $query = "INSERT INTO mahasiswa (nama, nim, jurusan, email, alasan, status_approval, created_at) 
                              VALUES ($1, $2, $3, $4, $5, 'pending', NOW())";
                    $result = pg_query_params($this->conn, $query, array($nama, $nim, $jurusan, $email, $alasan));
                    
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
            $jurusan = pg_escape_string($this->conn, trim($_POST['jurusan']));
            $email = pg_escape_string($this->conn, trim($_POST['email']));
            $alasan = pg_escape_string($this->conn, trim($_POST['alasan']));
            
            // Validation
            if (empty($nama) || empty($nim) || empty($jurusan) || empty($email)) {
                $error = 'Nama, NIM, jurusan, dan email wajib diisi!';
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
                    // Update database
                    $query = "UPDATE mahasiswa SET nama = $1, nim = $2, jurusan = $3, email = $4, alasan = $5, updated_at = NOW() 
                              WHERE id = $6";
                    $result = pg_query_params($this->conn, $query, array($nama, $nim, $jurusan, $email, $alasan, $id));
                    
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
            // Delete query
            $delete_query = "DELETE FROM mahasiswa WHERE id = $1";
            $delete_result = pg_query_params($this->conn, $delete_query, array($id));
            
            if ($delete_result) {
                // Clean up users table too (manual cleanup if cascade not set)
                $cleanup_query = "DELETE FROM users WHERE reference_id = $1 AND role = 'mahasiswa'";
                pg_query_params($this->conn, $cleanup_query, array($id));
                
                // Refresh dashboard stats
                @pg_query($this->conn, "REFRESH MATERIALIZED VIEW mv_dashboard_stats");

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
        $params = [];
        $param_index = 1;
        
        if ($search) {
            $where_conditions[] = "(m.nama ILIKE $" . $param_index . " OR m.nim ILIKE $" . $param_index . " OR m.jurusan ILIKE $" . $param_index . " OR m.email ILIKE $" . $param_index . ")";
            $params[] = "%$search%";
            $param_index++;
        }
        
        if ($status) {
            $where_conditions[] = "m.status_approval = $" . $param_index;
            $params[] = $status;
            $param_index++;
        }
        
        $where = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        // Get total records
        $count_query = "SELECT COUNT(*) as total FROM mahasiswa m $where";
        $count_result = pg_query_params($this->conn, $count_query, $params);
        
        if (!$count_result) {
            return [
                'mahasiswa' => [], 'total_records' => 0, 'total_pages' => 0, 'current_page' => $page
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
                  LIMIT $" . $param_index . " OFFSET $" . ($param_index + 1);
        
        $params[] = $limit;
        $params[] = $offset;
        
        $result = pg_query_params($this->conn, $query, $params);
        
        $mahasiswa = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $mahasiswa[] = $row;
            }
        }
        
        return [
            'mahasiswa' => $mahasiswa,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
    
    // Approve mahasiswa (OPTIMIZED with Stored Procedure)
    public function approve($id, $admin_id) {
        // Default Dosen Pembimbing ID = 1 (Admin bisa set manual nanti atau pilih di UI jika ada fiturnya)
        $dosen_id = 1; 
        
        // --- PANGGIL STORED PROCEDURE ---
        // Procedure ini otomatis:
        // 1. Update status mahasiswa -> approved
        // 2. Set dosen pembimbing
        // 3. Update users -> is_active = true (Memungkinkan Login)
        
        $query = "CALL sp_approve_mahasiswa($1, $2, $3)";
        $result = pg_query_params($this->conn, $query, array($id, $admin_id, $dosen_id));
        
        if ($result) {
            // Refresh dashboard stats agar angka pending berkurang di cache
            @pg_query($this->conn, "REFRESH MATERIALIZED VIEW mv_dashboard_stats");
            
            return ['success' => true, 'message' => 'Mahasiswa berhasil disetujui & Akun Login diaktifkan!'];
        } else {
            // Fallback manual jika SP gagal (misal belum dibuat)
            $manual_query = "UPDATE mahasiswa SET status_approval = 'approved', approved_by = $1, approved_at = NOW() WHERE id = $2";
            $manual_res = pg_query_params($this->conn, $manual_query, array($admin_id, $id));
            
            if ($manual_res) {
                 // Activate user manually
                 $user_up = "UPDATE users SET is_active = TRUE WHERE reference_id = $1 AND role = 'mahasiswa'";
                 pg_query_params($this->conn, $user_up, array($id));
                 return ['success' => true, 'message' => 'Mahasiswa disetujui (Manual Fallback)'];
            }
            
            return ['success' => false, 'message' => 'Gagal menyetujui: ' . pg_last_error($this->conn)];
        }
    }
    
    // Reject mahasiswa
    public function reject($id, $admin_id, $reason = '') {
        $query = "UPDATE mahasiswa 
                  SET status_approval = 'rejected', 
                      approved_by = $1, 
                      approved_at = NOW(), 
                      rejection_reason = $2 
                  WHERE id = $3";
        $result = pg_query_params($this->conn, $query, array($admin_id, $reason, $id));
        
        if ($result) {
            // Deactivate user if exists (Prevent login if rejected)
            $user_down = "UPDATE users SET is_active = FALSE WHERE reference_id = $1 AND role = 'mahasiswa'";
            pg_query_params($this->conn, $user_down, array($id));
            
            // Refresh dashboard stats
            @pg_query($this->conn, "REFRESH MATERIALIZED VIEW mv_dashboard_stats");

            return ['success' => true, 'message' => 'Mahasiswa berhasil ditolak!'];
        } else {
            return ['success' => false, 'message' => 'Gagal menolak mahasiswa!'];
        }
    }

    public function getStatistics() {
        // Menggunakan fitur FILTER PostgreSQL agar cukup 1 kali query (Lebih Cepat)
        $query = "SELECT 
            COUNT(*) as total,
            COUNT(*) FILTER (WHERE status_approval = 'pending') as pending,
            COUNT(*) FILTER (WHERE status_approval = 'approved') as approved,
            COUNT(*) FILTER (WHERE status_approval = 'rejected') as rejected
        FROM mahasiswa";
        
        $result = pg_query($this->conn, $query);
        $stats = pg_fetch_assoc($result);
        
        // Fallback jika data kosong
        if (!$stats) {
            return [
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0
            ];
        }
        
        return $stats;
    }
}
?>