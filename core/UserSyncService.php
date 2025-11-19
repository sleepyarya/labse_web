<?php
/**
 * User Sync Service
 * 
 * Service untuk sinkronisasi data antara tabel users (pusat autentikasi)
 * dengan tabel admin_users, personil, dan mahasiswa
 */

class UserSyncService {
    
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Tambah atau update user di tabel users
     * 
     * @param string $username Username
     * @param string $email Email
     * @param string $password Password (plain text, akan di-hash)
     * @param string $role Role: admin, personil, atau mahasiswa
     * @param int $reference_id ID dari tabel asli
     * @param bool $is_update Apakah ini update atau create baru
     * @return bool Success status
     */
    public function syncUser($username, $email, $password, $role, $reference_id, $is_update = false) {
        try {
            // Hash password jika belum
            if (!$this->isPasswordHashed($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            } else {
                $hashed_password = $password;
            }
            
            // Cek apakah user sudah ada di tabel users
            $check_query = "SELECT id FROM users WHERE reference_id = $1 AND role = $2";
            $check_result = pg_query_params($this->conn, $check_query, array($reference_id, $role));
            
            if (pg_num_rows($check_result) > 0) {
                // Update existing user
                $user = pg_fetch_assoc($check_result);
                $update_query = "UPDATE users SET 
                                username = $1, 
                                email = $2, 
                                password = $3, 
                                updated_at = NOW() 
                                WHERE id = $4";
                $result = pg_query_params($this->conn, $update_query, array(
                    $username,
                    $email,
                    $hashed_password,
                    $user['id']
                ));
                
                return $result !== false;
            } else {
                // Insert new user
                $insert_query = "INSERT INTO users (username, email, password, role, reference_id, is_active) 
                                VALUES ($1, $2, $3, $4, $5, TRUE)";
                $result = pg_query_params($this->conn, $insert_query, array(
                    $username,
                    $email,
                    $hashed_password,
                    $role,
                    $reference_id
                ));
                
                return $result !== false;
            }
        } catch (Exception $e) {
            error_log("UserSyncService Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hapus user dari tabel users (soft delete - set is_active = FALSE)
     * 
     * @param int $reference_id ID dari tabel asli
     * @param string $role Role user
     * @return bool Success status
     */
    public function deleteUser($reference_id, $role) {
        try {
            $query = "UPDATE users SET is_active = FALSE WHERE reference_id = $1 AND role = $2";
            $result = pg_query_params($this->conn, $query, array($reference_id, $role));
            return $result !== false;
        } catch (Exception $e) {
            error_log("UserSyncService Delete Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hard delete user dari tabel users
     * 
     * @param int $reference_id ID dari tabel asli
     * @param string $role Role user
     * @return bool Success status
     */
    public function hardDeleteUser($reference_id, $role) {
        try {
            $query = "DELETE FROM users WHERE reference_id = $1 AND role = $2";
            $result = pg_query_params($this->conn, $query, array($reference_id, $role));
            return $result !== false;
        } catch (Exception $e) {
            error_log("UserSyncService Hard Delete Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate username unik dari email
     * 
     * @param string $email Email address
     * @param string $role Role untuk prefix
     * @return string Username yang unik
     */
    public function generateUsername($email, $role = '') {
        // Ambil bagian sebelum @
        $base_username = strstr($email, '@', true);
        
        // Cek apakah username sudah ada
        $check_query = "SELECT COUNT(*) as count FROM users WHERE username = $1";
        $result = pg_query_params($this->conn, $check_query, array($base_username));
        $count = pg_fetch_assoc($result)['count'];
        
        if ($count > 0) {
            // Jika sudah ada, tambahkan suffix
            $suffix = 1;
            do {
                $new_username = $base_username . $suffix;
                $check_result = pg_query_params($this->conn, $check_query, array($new_username));
                $count = pg_fetch_assoc($check_result)['count'];
                $suffix++;
            } while ($count > 0);
            
            return $new_username;
        }
        
        return $base_username;
    }
    
    /**
     * Cek apakah password sudah di-hash
     * 
     * @param string $password Password string
     * @return bool True jika sudah di-hash
     */
    private function isPasswordHashed($password) {
        // Password yang di-hash dengan password_hash() dimulai dengan $2y$
        return strlen($password) === 60 && substr($password, 0, 4) === '$2y$';
    }
    
    /**
     * Update password user
     * 
     * @param int $reference_id ID dari tabel asli
     * @param string $role Role user
     * @param string $new_password Password baru (plain text)
     * @return bool Success status
     */
    public function updatePassword($reference_id, $role, $new_password) {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $query = "UPDATE users SET password = $1, updated_at = NOW() 
                     WHERE reference_id = $2 AND role = $3";
            $result = pg_query_params($this->conn, $query, array(
                $hashed_password,
                $reference_id,
                $role
            ));
            
            return $result !== false;
        } catch (Exception $e) {
            error_log("UserSyncService Update Password Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cek apakah email sudah terdaftar
     * 
     * @param string $email Email to check
     * @param int $exclude_id ID yang dikecualikan (untuk update)
     * @return bool True jika email sudah ada
     */
    public function isEmailExists($email, $exclude_id = null) {
        if ($exclude_id) {
            $query = "SELECT COUNT(*) as count FROM users WHERE email = $1 AND id != $2";
            $result = pg_query_params($this->conn, $query, array($email, $exclude_id));
        } else {
            $query = "SELECT COUNT(*) as count FROM users WHERE email = $1";
            $result = pg_query_params($this->conn, $query, array($email));
        }
        
        $count = pg_fetch_assoc($result)['count'];
        return $count > 0;
    }
    
    /**
     * Cek apakah username sudah terdaftar
     * 
     * @param string $username Username to check
     * @param int $exclude_id ID yang dikecualikan (untuk update)
     * @return bool True jika username sudah ada
     */
    public function isUsernameExists($username, $exclude_id = null) {
        if ($exclude_id) {
            $query = "SELECT COUNT(*) as count FROM users WHERE username = $1 AND id != $2";
            $result = pg_query_params($this->conn, $query, array($username, $exclude_id));
        } else {
            $query = "SELECT COUNT(*) as count FROM users WHERE username = $1";
            $result = pg_query_params($this->conn, $query, array($username));
        }
        
        $count = pg_fetch_assoc($result)['count'];
        return $count > 0;
    }
}
?>
