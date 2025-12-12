<?php
// Controller: Member Profile Controller
// Description: Handles profile management operations for members

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../core/session.php';

class MemberProfileController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Get member profile data
    public function getProfile() {
        $member_id = $_SESSION['member_id'];
        
        $query = "SELECT * FROM personil WHERE id = $1";
        $result = pg_query_params($this->conn, $query, array($member_id));
        
        return pg_fetch_assoc($result);
    }
    
    // Update member profile
    public function updateProfile() {
        $member_id = $_SESSION['member_id'];
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama = trim($_POST['nama']);
            $jabatan = trim($_POST['jabatan']);
            $email = trim($_POST['email']);
            $deskripsi = trim($_POST['deskripsi']);
            $password_lama = trim($_POST['password_lama']);
            $password_baru = trim($_POST['password_baru']);
            $password_konfirmasi = trim($_POST['password_konfirmasi']);
            
            // Basic validation
            if (empty($nama) || empty($jabatan) || empty($email)) {
                $error = 'Nama, jabatan, dan email harus diisi!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Format email tidak valid!';
            } else {
                // Get current member data
                $current_member = $this->getProfile();
                
                // Get corresponding user record from users table (role=personil)
                $user_query = "SELECT id, password FROM users WHERE role = 'personil' AND reference_id = $1";
                $user_result = pg_query_params($this->conn, $user_query, array($member_id));
                $user = pg_fetch_assoc($user_result);
                $user_id = $user ? $user['id'] : null;
                
                // Handle password change (managed via users table)
                $password_hash = $user ? $user['password'] : null; // Keep current users.password by default
                $password_changed = false;
                
                if (!empty($password_lama) || !empty($password_baru) || !empty($password_konfirmasi)) {
                    if (empty($password_lama) || empty($password_baru) || empty($password_konfirmasi)) {
                        $error = 'Untuk mengubah password, semua field password harus diisi!';
                    } elseif ($password_baru !== $password_konfirmasi) {
                        $error = 'Password baru dan konfirmasi password tidak sama!';
                    } elseif (strlen($password_baru) < 6) {
                        $error = 'Password baru minimal 6 karakter!';
                    } elseif (!$user || empty($user['password']) || !password_verify($password_lama, $user['password'])) {
                        $error = 'Password lama tidak benar!';
                    } else {
                        $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                        $password_changed = true;
                    }
                }
                
                // Handle file upload
                $foto = $current_member['foto']; // Keep existing photo by default
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['foto']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'profile_' . $member_id . '_' . time() . '.' . $ext;
                        $upload_dir = __DIR__ . '/../../public/uploads/personil/';
                        
                        // Create directory if not exists
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $new_filename)) {
                            // Delete old photo
                            if ($current_member['foto'] && file_exists($upload_dir . $current_member['foto'])) {
                                unlink($upload_dir . $current_member['foto']);
                            }
                            $foto = $new_filename;
                        }
                    } else {
                        $error = 'Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
                    }
                }
                
                // Update database if no errors
                if (empty($error)) {
                    // Update personil profile data (no password column)
                    $query = "UPDATE personil SET 
                              nama = $1, 
                              jabatan = $2, 
                              email = $3, 
                              deskripsi = $4, 
                              foto = $5, 
                              updated_at = NOW() 
                              WHERE id = $6";
                    
                    $result = pg_query_params($this->conn, $query, array(
                        $nama, $jabatan, $email, $deskripsi, $foto, $member_id
                    ));
                    
                    if ($result) {
                        // Update related users record: email and password (if changed)
                        if ($user_id) {
                            // Update email in users table
                            $update_user_email = "UPDATE users SET email = $1 WHERE id = $2";
                            pg_query_params($this->conn, $update_user_email, array($email, $user_id));
                            
                            // Update password in users table if changed
                            if ($password_changed && $password_hash) {
                                $update_user_pwd = "UPDATE users SET password = $1 WHERE id = $2";
                                pg_query_params($this->conn, $update_user_pwd, array($password_hash, $user_id));
                            }
                        }
                        
                        // Update session data
                        $_SESSION['member_nama'] = $nama;
                        $_SESSION['member_jabatan'] = $jabatan;
                        $_SESSION['member_email'] = $email;
                        $_SESSION['member_foto'] = $foto;
                        
                        $success = 'Profil berhasil diperbarui!';
                    } else {
                        $error = 'Gagal memperbarui profil. Silakan coba lagi.';
                    }
                }
            }
        }
        
        return ['error' => $error, 'success' => $success];
    }
    
    // Get member statistics for dashboard
    public function getStatistics() {
        $member_id = $_SESSION['member_id'];
        
        // Get total articles
        $query_articles = "SELECT COUNT(*) as total FROM artikel WHERE personil_id = $1";
        $result_articles = pg_query_params($this->conn, $query_articles, array($member_id));
        $total_articles = pg_fetch_assoc($result_articles)['total'];
        
        return [
            'total_articles' => $total_articles
        ];
    }
}
?>
