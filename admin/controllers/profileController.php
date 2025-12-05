<?php
// Controller: Profile Controller
// Description: Handles admin profile management

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';

class ProfileController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Get admin profile by ID
    public function getProfile($admin_id) {
        $query = "SELECT * FROM admin_users WHERE id = $1";
        $result = pg_query_params($this->conn, $query, array($admin_id));
        
        if ($result && pg_num_rows($result) > 0) {
            return pg_fetch_assoc($result);
        }
        
        return false;
    }
    
    // Update admin profile
    public function updateProfile($admin_id, $data) {
        $nama_lengkap = trim($data['nama_lengkap']);
        $username = trim($data['username']);
        $email = trim($data['email']);
        $current_password = trim($data['current_password'] ?? '');
        $new_password = trim($data['new_password'] ?? '');
        $confirm_password = trim($data['confirm_password'] ?? '');
        
        // Validation
        if (empty($nama_lengkap) || empty($username) || empty($email)) {
            return ['success' => false, 'message' => 'Nama lengkap, username, dan email tidak boleh kosong!'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid!'];
        }
        
        // Check if username/email already exists (exclude current admin)
        $check_username = "SELECT COUNT(*) as count FROM admin_users WHERE username = $1 AND id != $2";
        $check_email = "SELECT COUNT(*) as count FROM admin_users WHERE email = $1 AND id != $2";
        
        $username_exists = pg_fetch_result(pg_query_params($this->conn, $check_username, array($username, $admin_id)), 0, 0);
        $email_exists = pg_fetch_result(pg_query_params($this->conn, $check_email, array($email, $admin_id)), 0, 0);
        
        if ($username_exists > 0) {
            return ['success' => false, 'message' => 'Username sudah digunakan oleh admin lain!'];
        }
        
        if ($email_exists > 0) {
            return ['success' => false, 'message' => 'Email sudah digunakan oleh admin lain!'];
        }
        
        // If password change is requested
        if (!empty($new_password)) {
            if (empty($current_password)) {
                return ['success' => false, 'message' => 'Password saat ini harus diisi untuk mengubah password!'];
            }
            
            if (strlen($new_password) < 6) {
                return ['success' => false, 'message' => 'Password baru minimal 6 karakter!'];
            }
            
            if ($new_password !== $confirm_password) {
                return ['success' => false, 'message' => 'Konfirmasi password tidak cocok!'];
            }
            
            // Verify current password from users table
            $password_query = "SELECT password FROM users WHERE role = 'admin' AND reference_id = $1";
            $password_result = pg_query_params($this->conn, $password_query, array($admin_id));
            
            if (!$password_result || pg_num_rows($password_result) === 0) {
                return ['success' => false, 'message' => 'Data user tidak ditemukan!'];
            }
            
            $user_data = pg_fetch_assoc($password_result);
            if (!password_verify($current_password, $user_data['password'])) {
                return ['success' => false, 'message' => 'Password saat ini tidak benar!'];
            }
            
            // Update profile in admin_users (without password)
            $update_admin_query = "UPDATE admin_users 
                                  SET nama_lengkap = $1, username = $2, email = $3, updated_at = NOW() 
                                  WHERE id = $4";
            $result = pg_query_params($this->conn, $update_admin_query, 
                array($nama_lengkap, $username, $email, $admin_id));
            
            if (!$result) {
                return ['success' => false, 'message' => 'Gagal memperbarui profil!'];
            }
            
            // Update password in users table
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_query = "UPDATE users SET password = $1, updated_at = NOW() 
                                     WHERE role = 'admin' AND reference_id = $2";
            $password_update = pg_query_params($this->conn, $update_password_query, 
                array($hashed_password, $admin_id));
            
            if (!$password_update) {
                return ['success' => false, 'message' => 'Gagal memperbarui password!'];
            }
        } else {
            // Update without password change
            $update_query = "UPDATE admin_users 
                           SET nama_lengkap = $1, username = $2, email = $3, updated_at = NOW() 
                           WHERE id = $4";
            $result = pg_query_params($this->conn, $update_query, 
                array($nama_lengkap, $username, $email, $admin_id));
        }
        
        if ($result) {
            // Update session data
            $_SESSION['admin_nama'] = $nama_lengkap;
            $_SESSION['admin_email'] = $email;
            
            return ['success' => true, 'message' => 'Profil berhasil diperbarui!'];
        } else {
            $error = pg_last_error($this->conn);
            return ['success' => false, 'message' => 'Gagal memperbarui profil: ' . $error];
        }
    }
    
    // Upload profile photo
    public function uploadPhoto($admin_id, $file) {
        $upload_dir = __DIR__ . '/../../uploads/admin/';
        
        // Create directory if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Validate file
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($file['type'], $allowed_types)) {
            return ['success' => false, 'message' => 'Format file tidak didukung! Gunakan JPG, PNG, atau GIF.'];
        }
        
        if ($file['size'] > $max_size) {
            return ['success' => false, 'message' => 'Ukuran file terlalu besar! Maksimal 2MB.'];
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'admin_' . $admin_id . '_' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update database
            $update_query = "UPDATE admin_users SET foto = $1 WHERE id = $2";
            $result = pg_query_params($this->conn, $update_query, array($filename, $admin_id));
            
            if ($result) {
                return ['success' => true, 'message' => 'Foto profil berhasil diperbarui!', 'filename' => $filename];
            } else {
                // Delete uploaded file if database update fails
                unlink($filepath);
                return ['success' => false, 'message' => 'Gagal menyimpan foto ke database!'];
            }
        } else {
            return ['success' => false, 'message' => 'Gagal mengupload file!'];
        }
    }
}
?>
