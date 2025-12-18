<?php
// Controller: User Management Controller
// Description: Handles user management operations for all roles - Modified to decouple from Personnel table

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../core/session.php';
// UserSyncService dinonaktifkan untuk mencegah sinkronisasi otomatis ke tabel personil/mahasiswa
// require_once __DIR__ . '/../../core/UserSyncService.php';

class UserController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Get all users with details
    public function getAll($page = 1, $limit = 10, $search = '', $role_filter = '') {
        $offset = ($page - 1) * $limit;
        
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
        
        // Total records query
        $count_query = "SELECT COUNT(*) as total FROM users u {$where_sql}";
        $count_result = !empty($params) ? pg_query_params($this->conn, $count_query, $params) : pg_query($this->conn, $count_query);
        $total_records = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_records / $limit);
        
        // Get users with details (Melihat profil jika ada, tapi tidak mewajibkan)
        $query = "SELECT 
                    u.id, u.username, u.email, u.role, u.reference_id, u.is_active, 
                    u.last_login, u.created_at,
                    CASE 
                        WHEN u.role = 'admin' THEN au.nama_lengkap
                        WHEN u.role = 'personil' THEN p.nama
                        WHEN u.role = 'mahasiswa' THEN m.nama
                    END as full_name
                  FROM users u
                  LEFT JOIN admin_users au ON u.role = 'admin' AND u.reference_id = au.id
                  LEFT JOIN personil p ON u.role = 'personil' AND u.reference_id = p.id
                  LEFT JOIN mahasiswa m ON u.role = 'mahasiswa' AND u.reference_id = m.id
                  {$where_sql}
                  ORDER BY u.created_at DESC
                  LIMIT {$limit} OFFSET {$offset}";
        
        $result = !empty($params) ? pg_query_params($this->conn, $query, $params) : pg_query($this->conn, $query);
        
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
    
    // Create New User (HANYA MASUK KE TABEL USERS)
    public function store($data) {
        $username = $data['username'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $data['role'];

        // Cek apakah username/email sudah ada
        $check = pg_query_params($this->conn, "SELECT id FROM users WHERE username = $1 OR email = $2", [$username, $email]);
        if (pg_num_rows($check) > 0) {
            return ['success' => false, 'message' => 'Username atau Email sudah terdaftar!'];
        }

        $query = "INSERT INTO users (username, email, password, role, is_active, created_at) 
                  VALUES ($1, $2, $3, $4, TRUE, NOW())";
        
        $result = pg_query_params($this->conn, $query, [$username, $email, $password, $role]);

        if ($result) {
            return ['success' => true, 'message' => 'User berhasil ditambahkan. Data tidak akan masuk ke tabel personil secara otomatis.'];
        } else {
            return ['success' => false, 'message' => 'Gagal menambahkan user.'];
        }
    }

    public function getById($id) {
        $query = "SELECT u.* FROM users u WHERE u.id = $1";
        $result = pg_query_params($this->conn, $query, array($id));
        return pg_fetch_assoc($result);
    }
    
    public function toggleActive($id) {
        $query = "SELECT is_active, role, reference_id FROM users WHERE id = $1";
        $result = pg_query_params($this->conn, $query, array($id));
        $user = pg_fetch_assoc($result);
        
        if (!$user) return ['success' => false, 'message' => 'User tidak ditemukan!'];
        
        $new_status = ($user['is_active'] === 't') ? 'FALSE' : 'TRUE';
        $update_query = "UPDATE users SET is_active = {$new_status} WHERE id = $1";
        $update_result = pg_query_params($this->conn, $update_query, array($id));
        
        return $update_result ? ['success' => true, 'message' => 'Status user diperbarui!'] : ['success' => false, 'message' => 'Gagal ubah status!'];
    }
    
    public function resetPassword($id, $new_password) {
        if (strlen($new_password) < 6) return ['success' => false, 'message' => 'Password minimal 6 karakter!'];
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_users = "UPDATE users SET password = $1 WHERE id = $2";
        $result = pg_query_params($this->conn, $update_users, array($hashed_password, $id));
        return $result ? ['success' => true, 'message' => 'Password berhasil direset!'] : ['success' => false, 'message' => 'Gagal reset password!'];
    }
    
    // Delete User (Diedit agar tidak menghapus data di tabel Personil/Mahasiswa)
    public function delete($id) {
        $query = "SELECT role, reference_id FROM users WHERE id = $1";
        $result = pg_query_params($this->conn, $query, array($id));
        $user = pg_fetch_assoc($result);
        
        if (!$user) return ['success' => false, 'message' => 'User tidak ditemukan!'];
        
        // Mencegah hapus admin terakhir
        if ($user['role'] === 'admin') {
            $count = pg_fetch_result(pg_query($this->conn, "SELECT COUNT(*) FROM users WHERE role = 'admin'"), 0, 0);
            if ($count <= 1) return ['success' => false, 'message' => 'Tidak bisa menghapus admin terakhir!'];
        }
        
        // HANYA hapus dari tabel users. Referensi di tabel personil/mahasiswa dibiarkan tetap ada.
        $delete_query = "DELETE FROM users WHERE id = $1";
        $delete_result = pg_query_params($this->conn, $delete_query, array($id));
        
        if ($delete_result) {
            return ['success' => true, 'message' => 'Akun login user berhasil dihapus. Data personil terkait tetap aman.'];
        } else {
            return ['success' => false, 'message' => 'Gagal menghapus akun user.'];
        }
    }
    
    public function getStatistics() {
        $stats = [];
        $stats['total'] = pg_fetch_result(pg_query($this->conn, "SELECT COUNT(*) FROM users"), 0, 0);
        $stats['active'] = pg_fetch_result(pg_query($this->conn, "SELECT COUNT(*) FROM users WHERE is_active = TRUE"), 0, 0);
        
        $role_result = pg_query($this->conn, "SELECT role, COUNT(*) as count FROM users GROUP BY role");
        $stats['by_role'] = [];
        while ($row = pg_fetch_assoc($role_result)) {
            $stats['by_role'][$row['role']] = $row['count'];
        }
        return $stats;
    }
}
?>
