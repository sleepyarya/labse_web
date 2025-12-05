<?php
// Controller: Admin Controller
// Description: Handles CRUD operations for admin users management

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/UserSyncService.php';

class AdminController {
    
    private $conn;
    private $userSyncService;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->userSyncService = new UserSyncService($conn);
    }
    
    // Add new admin
    public function add() {
        $error = '';
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = pg_escape_string($this->conn, trim($_POST['username']));
            $nama_lengkap = pg_escape_string($this->conn, trim($_POST['nama_lengkap']));
            $email = pg_escape_string($this->conn, trim($_POST['email']));
            $password = trim($_POST['password']);
            $confirm_password = trim($_POST['confirm_password']);
            
            // Validation
            if (empty($username) || empty($nama_lengkap) || empty($email) || empty($password)) {
                $error = 'Semua field wajib diisi!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Format email tidak valid!';
            } elseif (strlen($password) < 6) {
                $error = 'Password minimal 6 karakter!';
            } elseif ($password !== $confirm_password) {
                $error = 'Password dan konfirmasi password tidak cocok!';
            } elseif ($this->userSyncService->isUsernameExists($username)) {
                $error = 'Username sudah terdaftar!';
            } elseif ($this->userSyncService->isEmailExists($email)) {
                $error = 'Email sudah terdaftar!';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert to admin_users table
                $query = "INSERT INTO admin_users (username, nama_lengkap, email) 
                          VALUES ($1, $2, $3) RETURNING id";
                $result = pg_query_params($this->conn, $query, array(
                    $username,
                    $nama_lengkap,
                    $email
                ));
                
                if ($result) {
                    $admin_id = pg_fetch_result($result, 0, 0);
                    
                    // Sinkronkan ke tabel users
                    $sync_success = $this->userSyncService->syncUser(
                        $username,
                        $email,
                        $hashed_password,
                        'admin',
                        $admin_id,
                        false
                    );
                    
                    if (!$sync_success) {
                        error_log("Failed to sync admin to users table: ID {$admin_id}");
                    }
                    
                    header('Location: ../views/manage_admin.php?success=add');
                    exit();
                } else {
                    $error = 'Gagal menambahkan admin: ' . pg_last_error($this->conn);
                }
            }
        }
        
        return ['error' => $error, 'success' => $success];
    }
    
    // Edit admin
    public function edit($id) {
        $error = '';
        $success = false;
        $admin = null;
        
        // Get admin data
        if ($id) {
            $query = "SELECT * FROM admin_users WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $admin = pg_fetch_assoc($result);
            
            if (!$admin) {
                $error = 'Data admin tidak ditemukan!';
                return ['error' => $error, 'admin' => null];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = pg_escape_string($this->conn, trim($_POST['username']));
            $nama_lengkap = pg_escape_string($this->conn, trim($_POST['nama_lengkap']));
            $email = pg_escape_string($this->conn, trim($_POST['email']));
            $password = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');
            
            // Get current user ID from users table
            $user_query = "SELECT id FROM users WHERE reference_id = $1 AND role = 'admin'";
            $user_result = pg_query_params($this->conn, $user_query, array($id));
            $user = pg_fetch_assoc($user_result);
            $user_id = $user ? $user['id'] : null;
            $current_user_password = null;
            if ($user_id) {
                $user_data_query = "SELECT password FROM users WHERE id = $1";
                $user_data_result = pg_query_params($this->conn, $user_data_query, array($user_id));
                if ($user_data_result && pg_num_rows($user_data_result) > 0) {
                    $user_data = pg_fetch_assoc($user_data_result);
                    $current_user_password = $user_data['password'];
                }
            }
            
            // Validation
            if (empty($username) || empty($nama_lengkap) || empty($email)) {
                $error = 'Username, nama lengkap, dan email wajib diisi!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Format email tidak valid!';
            } elseif ($this->userSyncService->isUsernameExists($username, $user_id)) {
                $error = 'Username sudah digunakan oleh user lain!';
            } elseif ($this->userSyncService->isEmailExists($email, $user_id)) {
                $error = 'Email sudah digunakan oleh user lain!';
            } elseif (!empty($password) && strlen($password) < 6) {
                $error = 'Password minimal 6 karakter!';
            } elseif (!empty($password) && $password !== $confirm_password) {
                $error = 'Password dan konfirmasi password tidak cocok!';
            } else {
                // Update password only if provided
                $hashed_password = $current_user_password; // Keep old password from users table
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                }
                
                // Update admin_users table
                $query = "UPDATE admin_users SET username = $1, nama_lengkap = $2, email = $3 
                          WHERE id = $4";
                $result = pg_query_params($this->conn, $query, array(
                    $username,
                    $nama_lengkap,
                    $email,
                    $id
                ));
                
                if ($result) {
                    // Update di tabel users
                    $sync_success = $this->userSyncService->syncUser(
                        $username,
                        $email,
                        $hashed_password,
                        'admin',
                        $id,
                        true
                    );
                    
                    if (!$sync_success) {
                        error_log("Failed to sync admin update to users table: ID {$id}");
                    }
                    
                    header('Location: ../views/manage_admin.php?success=edit');
                    exit();
                } else {
                    $error = 'Gagal mengupdate admin: ' . pg_last_error($this->conn);
                }
            }
        }
        
        return ['error' => $error, 'success' => $success, 'admin' => $admin];
    }
    
    // Delete admin
    public function delete($id) {
        if ($id) {
            // Prevent self-deletion
            if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $id) {
                header('Location: ../views/manage_admin.php?error=self_delete');
                exit();
            }
            
            // Check if this is the last admin
            $count_query = "SELECT COUNT(*) as count FROM admin_users";
            $count_result = pg_query($this->conn, $count_query);
            $count = pg_fetch_assoc($count_result)['count'];
            
            if ($count <= 1) {
                header('Location: ../views/manage_admin.php?error=last_admin');
                exit();
            }
            
            // Delete from database
            $delete_query = "DELETE FROM admin_users WHERE id = $1";
            $delete_result = pg_query_params($this->conn, $delete_query, array($id));
            
            if ($delete_result) {
                // Delete from users table
                $this->userSyncService->hardDeleteUser($id, 'admin');
                header('Location: ../views/manage_admin.php?success=delete');
            } else {
                header('Location: ../views/manage_admin.php?error=delete');
            }
        } else {
            header('Location: ../views/manage_admin.php?error=invalid');
        }
        exit();
    }
    
    // Get all admins with pagination
    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        
        // Search functionality
        $where = $search ? "WHERE username ILIKE '%$search%' OR nama_lengkap ILIKE '%$search%' OR email ILIKE '%$search%'" : '';
        
        // Get total records
        $count_query = "SELECT COUNT(*) as total FROM admin_users $where";
        $count_result = pg_query($this->conn, $count_query);
        $total_records = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_records / $limit);
        
        // Get admin data
        $query = "SELECT * FROM admin_users $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $result = pg_query($this->conn, $query);
        
        $admins = [];
        while ($row = pg_fetch_assoc($result)) {
            $admins[] = $row;
        }
        
        return [
            'admins' => $admins,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
}
?>
