<?php
// Controller: User Management Controller
// Description: Handles user management operations for all roles

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/UserSyncService.php';

class UserController {
    
    private $conn;
    private $userSyncService;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->userSyncService = new UserSyncService($conn);
    }
    
    // Get all users with details
    public function getAll($page = 1, $limit = 10, $search = '', $role_filter = '') {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $where_conditions = [];
        $params = [];
        $param_count = 1;
        
        if (!empty($search)) {
            $params[] = "%{$search}%";
            $where_conditions[] = "(u.username ILIKE \${$param_count} OR u.email ILIKE \${$param_count})";
            $param_count++;
        }
        
        if (!empty($role_filter)) {
            $params[] = $role_filter;
            $where_conditions[] = "u.role = \${$param_count}";
            $param_count++;
        }
        
        $where_sql = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        // Get total records
        $count_query = "SELECT COUNT(*) as total FROM users u {$where_sql}";
        if (!empty($params)) {
            $count_result = pg_query_params($this->conn, $count_query, $params);
        } else {
            $count_result = pg_query($this->conn, $count_query);
        }
        $total_records = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_records / $limit);
        
        // Get users with details
        $query = "SELECT 
                    u.id,
                    u.username,
                    u.email,
                    u.role,
                    u.reference_id,
                    u.is_active,
                    u.last_login,
                    u.created_at,
                    CASE 
                        WHEN u.role = 'admin' THEN au.nama_lengkap
                        WHEN u.role = 'personil' THEN p.nama
                        WHEN u.role = 'mahasiswa' THEN m.nama
                    END as full_name,
                    CASE 
                        WHEN u.role = 'personil' THEN p.jabatan
                        WHEN u.role = 'mahasiswa' THEN m.nim
                    END as additional_info
                  FROM users u
                  LEFT JOIN admin_users au ON u.role = 'admin' AND u.reference_id = au.id
                  LEFT JOIN personil p ON u.role = 'personil' AND u.reference_id = p.id
                  LEFT JOIN mahasiswa m ON u.role = 'mahasiswa' AND u.reference_id = m.id
                  {$where_sql}
                  ORDER BY u.created_at DESC
                  LIMIT {$limit} OFFSET {$offset}";
        
        if (!empty($params)) {
            $result = pg_query_params($this->conn, $query, $params);
        } else {
            $result = pg_query($this->conn, $query);
        }
        
        $users = [];
        while ($row = pg_fetch_assoc($result)) {
            $users[] = $row;
        }
        
        return [
            'users' => $users,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
    
    // Get user by ID
    public function getById($id) {
        $query = "SELECT 
                    u.*,
                    CASE 
                        WHEN u.role = 'admin' THEN au.nama_lengkap
                        WHEN u.role = 'personil' THEN p.nama
                        WHEN u.role = 'mahasiswa' THEN m.nama
                    END as full_name
                  FROM users u
                  LEFT JOIN admin_users au ON u.role = 'admin' AND u.reference_id = au.id
                  LEFT JOIN personil p ON u.role = 'personil' AND u.reference_id = p.id
                  LEFT JOIN mahasiswa m ON u.role = 'mahasiswa' AND u.reference_id = m.id
                  WHERE u.id = $1";
        
        $result = pg_query_params($this->conn, $query, array($id));
        return pg_fetch_assoc($result);
    }
    
    // Toggle user active status
    public function toggleActive($id) {
        // Get current status
        $query = "SELECT is_active, role, reference_id FROM users WHERE id = $1";
        $result = pg_query_params($this->conn, $query, array($id));
        $user = pg_fetch_assoc($result);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan!'];
        }
        
        // Prevent deactivating self
        if ($user['role'] === 'admin' && 
            isset($_SESSION['admin_id']) && 
            $_SESSION['admin_id'] == $user['reference_id']) {
            return ['success' => false, 'message' => 'Tidak bisa menonaktifkan akun sendiri!'];
        }
        
        // Toggle status
        $new_status = ($user['is_active'] === 't') ? 'FALSE' : 'TRUE';
        $update_query = "UPDATE users SET is_active = {$new_status} WHERE id = $1";
        $update_result = pg_query_params($this->conn, $update_query, array($id));
        
        if ($update_result) {
            $status_text = ($new_status === 'TRUE') ? 'diaktifkan' : 'dinonaktifkan';
            return ['success' => true, 'message' => "User berhasil {$status_text}!"];
        } else {
            return ['success' => false, 'message' => 'Gagal mengubah status user!'];
        }
    }
    
    // Reset password
    public function resetPassword($id, $new_password) {
        if (strlen($new_password) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter!'];
        }
        
        // Get user info
        $query = "SELECT role, reference_id FROM users WHERE id = $1";
        $result = pg_query_params($this->conn, $query, array($id));
        $user = pg_fetch_assoc($result);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan!'];
        }
        
        // Hash password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password in users table
        $update_users = "UPDATE users SET password = $1 WHERE id = $2";
        $result1 = pg_query_params($this->conn, $update_users, array($hashed_password, $id));
        
        if ($result1) {
            return ['success' => true, 'message' => 'Password berhasil direset!'];
        } else {
            return ['success' => false, 'message' => 'Gagal reset password!'];
        }
    }
    
    // Delete user
    public function delete($id) {
        // Get user info
        $query = "SELECT role, reference_id FROM users WHERE id = $1";
        $result = pg_query_params($this->conn, $query, array($id));
        $user = pg_fetch_assoc($result);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan!'];
        }
        
        // Prevent self-deletion
        if ($user['role'] === 'admin' && 
            isset($_SESSION['admin_id']) && 
            $_SESSION['admin_id'] == $user['reference_id']) {
            return ['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri!'];
        }
        
        // Check if last admin
        if ($user['role'] === 'admin') {
            $count_query = "SELECT COUNT(*) as count FROM users WHERE role = 'admin' AND is_active = TRUE";
            $count_result = pg_query($this->conn, $count_query);
            $count = pg_fetch_assoc($count_result)['count'];
            
            if ($count <= 1) {
                return ['success' => false, 'message' => 'Tidak bisa menghapus admin terakhir!'];
            }
        }
        
        // Start transaction for safe deletion
        pg_query($this->conn, "BEGIN");
        
        try {
            // Delete from original table first based on role and reference_id
            if ($user['role'] === 'admin' && $user['reference_id']) {
                $delete_original = "DELETE FROM admin_users WHERE id = $1";
                $result_original = pg_query_params($this->conn, $delete_original, array($user['reference_id']));
                $affected = pg_affected_rows($result_original);
                if ($result_original === false) {
                    throw new Exception('Gagal menghapus dari tabel admin_users');
                }
            } elseif ($user['role'] === 'personil' && $user['reference_id']) {
                $delete_original = "DELETE FROM personil WHERE id = $1";
                $result_original = pg_query_params($this->conn, $delete_original, array($user['reference_id']));
                $affected = pg_affected_rows($result_original);
                if ($result_original === false) {
                    throw new Exception('Gagal menghapus dari tabel personil');
                }
            } elseif ($user['role'] === 'mahasiswa' && $user['reference_id']) {
                $delete_original = "DELETE FROM mahasiswa WHERE id = $1";
                $result_original = pg_query_params($this->conn, $delete_original, array($user['reference_id']));
                $affected = pg_affected_rows($result_original);
                if ($result_original === false) {
                    throw new Exception('Gagal menghapus dari tabel mahasiswa');
                }
            }
            
            // Delete from users table
            $delete_query = "DELETE FROM users WHERE id = $1";
            $delete_result = pg_query_params($this->conn, $delete_query, array($id));
            
            if ($delete_result === false) {
                throw new Exception('Gagal menghapus dari tabel users');
            }
            
            pg_query($this->conn, "COMMIT");
            return ['success' => true, 'message' => 'User berhasil dihapus!'];
            
        } catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            return ['success' => false, 'message' => 'Gagal menghapus user: ' . $e->getMessage()];
        }
    }
    
    // Get statistics
    public function getStatistics() {
        $stats = [];
        
        // Total users
        $total_query = "SELECT COUNT(*) as total FROM users";
        $stats['total'] = pg_fetch_result(pg_query($this->conn, $total_query), 0, 0);
        
        // Active users
        $active_query = "SELECT COUNT(*) as active FROM users WHERE is_active = TRUE";
        $stats['active'] = pg_fetch_result(pg_query($this->conn, $active_query), 0, 0);
        
        // By role
        $role_query = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
        $role_result = pg_query($this->conn, $role_query);
        
        $stats['by_role'] = [];
        while ($row = pg_fetch_assoc($role_result)) {
            $stats['by_role'][$row['role']] = $row['count'];
        }
        
        return $stats;
    }
}
?>
