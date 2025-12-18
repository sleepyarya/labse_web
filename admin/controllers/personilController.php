<?php
// Controller: Personil Controller
// Description: Handles CRUD operations for personil management

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/UserSyncService.php';

class PersonilController {
    
    private $conn;
    private $userSyncService;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->userSyncService = new UserSyncService($conn);
    }
    
    // Add new personil
    public function add() {
        $error = '';
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama = pg_escape_string($this->conn, trim($_POST['nama']));
            $jabatan = pg_escape_string($this->conn, trim($_POST['jabatan']));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi']));
            $email = pg_escape_string($this->conn, trim($_POST['email']));
            
            // Validation
            if (empty($nama) || empty($jabatan) || empty($email)) {
                $error = 'Nama, jabatan, dan email wajib diisi!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Format email tidak valid!';
            } else {
                // Handle file upload
                $foto = '';
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['foto']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'personil_' . time() . '_' . uniqid() . '.' . $ext;
                        $upload_dir = '../public/uploads/personil/';
                        
                        // Create directory if not exists
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $new_filename)) {
                            $foto = $new_filename;
                        }
                    } else {
                        $error = 'Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
                    }
                }
                
                // Check if personil is set as member
                $is_member = isset($_POST['is_member']) && $_POST['is_member'] == '1';
                $password = null;
                $hashed_password = null;

                if ($is_member) {
                    // If admin supplied a password use it (must be >=6 chars); otherwise we'll auto-generate later
                    $password = trim($_POST['password'] ?? '');
                    if (!empty($password)) {
                        if (strlen($password) < 6) {
                            $error = 'Password minimal 6 karakter!';
                        } else {
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        }
                    } else {
                        // no error here; password will be auto-generated after insert
                    }
                }
                
                // Insert to database
                if (empty($error)) {
                    $query = "INSERT INTO personil (nama, jabatan, deskripsi, foto, email, is_member) 
                              VALUES ($1, $2, $3, $4, $5, $6) RETURNING id";
                    $result = pg_query_params($this->conn, $query, array(
                        $nama, 
                        $jabatan, 
                        $deskripsi, 
                        $foto, 
                        $email, 
                        $is_member ? 'true' : 'false'
                    ));
                    
                    if ($result) {
                        $personil_id = pg_fetch_result($result, 0, 0);
                        
                        // Jika is_member, sinkronkan ke tabel users
                        if ($is_member) {
                            // If password provided by admin, use it. Otherwise auto-generate username and default password.
                            if (empty($password)) {
                                // generate username from first word of name + '123'
                                $parts = preg_split('/\s+/', $nama);
                                $first = strtolower($parts[0] ?? 'user');
                                $first = iconv('UTF-8', 'ASCII//TRANSLIT', $first);
                                $first = preg_replace('/[^a-z0-9]/', '', $first);
                                if ($first === '') $first = 'user';
                                $base_username = substr($first, 0, 30) . '123';

                                // ensure uniqueness
                                $username = $base_username;
                                $attempt = 0;
                                while ($this->userSyncService->isUsernameExists($username) && $attempt < 1000) {
                                    $attempt++;
                                    $username = $base_username . $attempt;
                                }
                                // default password = '123456'
                                $password = '123456';
                                $hashed_password = null; // let UserSyncService hash it
                            } else {
                                // admin provided a password; use provided username generation fallback
                                $username = $this->userSyncService->generateUsername($email, 'personil');
                                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            }

                            $sync_success = $this->userSyncService->syncUser(
                                $username,
                                $email,
                                $password,
                                'personil',
                                $personil_id,
                                false
                            );
                            
                        if (!$sync_success) {
                            error_log("Failed to sync personil to users table: ID {$personil_id}");
                        }
                        }
                        
                        header('Location: ../views/manage_personil.php?success=add');
                        exit();
                    } else {
                        $error = 'Gagal menambahkan data: ' . pg_last_error($this->conn);
                    }
                }
            }
        }
        
        return ['error' => $error, 'success' => $success];
    }
    
    // Edit personil
    public function edit($id) {
        $error = '';
        $success = false;
        $personil = null;
        
        // Get personil data
        if ($id) {
            $query = "SELECT * FROM personil WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $personil = pg_fetch_assoc($result);
            
            if (!$personil) {
                $error = 'Data personil tidak ditemukan!';
                return ['error' => $error, 'personil' => null];
            }
            
            // Get current user data from users table for password reference
            $user_query = "SELECT id, password FROM users WHERE reference_id = $1 AND role = 'personil'";
            $user_result = pg_query_params($this->conn, $user_query, array($id));
            $user = pg_fetch_assoc($user_result);
            $current_user_password = $user ? $user['password'] : null;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama = pg_escape_string($this->conn, trim($_POST['nama']));
            $jabatan = pg_escape_string($this->conn, trim($_POST['jabatan']));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi']));
            $email = pg_escape_string($this->conn, trim($_POST['email']));
            
            // Validation
            if (empty($nama) || empty($jabatan) || empty($email)) {
                $error = 'Nama, jabatan, dan email wajib diisi!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Format email tidak valid!';
            } else {
                // Handle file upload
                $foto = $personil['foto']; // Keep existing photo by default
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['foto']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'personil_' . time() . '_' . uniqid() . '.' . $ext;
                        $upload_dir = '../public/uploads/personil/';
                        
                        // Create directory if not exists
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $new_filename)) {
                            // Delete old photo
                            if ($personil['foto'] && file_exists($upload_dir . $personil['foto'])) {
                                unlink($upload_dir . $personil['foto']);
                            }
                            $foto = $new_filename;
                        }
                    } else {
                        $error = 'Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
                    }
                }
                
                // Check if personil is set as member
                $is_member = isset($_POST['is_member']) && $_POST['is_member'] == '1';
                $password = trim($_POST['password'] ?? '');
                $hashed_password = $current_user_password; // Keep old password from users table by default
                
                if ($is_member && !empty($password)) {
                    // Update password only if provided
                    if (strlen($password) < 6) {
                        $error = 'Password minimal 6 karakter!';
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    }
                }
                
                // Update database
                if (empty($error)) {
                    $query = "UPDATE personil SET nama = $1, jabatan = $2, deskripsi = $3, foto = $4, email = $5, 
                              is_member = $6, updated_at = NOW() WHERE id = $7";
                    $result = pg_query_params($this->conn, $query, array(
                        $nama, 
                        $jabatan, 
                        $deskripsi, 
                        $foto, 
                        $email,
                        $is_member ? 'true' : 'false',
                        $id
                    ));
                    
                    if ($result) {
                        // Update di tabel users jika is_member
                        if ($is_member) {
                            $username = $this->userSyncService->generateUsername($email, 'personil');
                            $sync_success = $this->userSyncService->syncUser(
                                $username,
                                $email,
                                $hashed_password,
                                'personil',
                                $id,
                                true
                            );
                            
                            if (!$sync_success) {
                                error_log("Failed to sync personil update to users table: ID {$id}");
                            }
                        } else {
                            // Jika is_member diset false, soft delete dari users
                            $this->userSyncService->deleteUser($id, 'personil');
                        }
                        
                        header('Location: ../views/manage_personil.php?success=edit');
                        exit();
                    } else {
                        $error = 'Gagal mengupdate data: ' . pg_last_error($this->conn);
                    }
                }
            }
        }
        
        return ['error' => $error, 'success' => $success, 'personil' => $personil];
    }
    
    // Delete personil
    public function delete($id) {
        if ($id) {
            // Get personil data first to delete photo
            $query = "SELECT foto FROM personil WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $personil = pg_fetch_assoc($result);
            
            if ($personil) {
                // Delete from database
                $delete_query = "DELETE FROM personil WHERE id = $1";
                $delete_result = pg_query_params($this->conn, $delete_query, array($id));
                
                if ($delete_result) {
                    // Delete from users table
                    $this->userSyncService->hardDeleteUser($id, 'personil');
                    
                    // Delete photo file
                    if ($personil['foto'] && file_exists('../public/uploads/personil/' . $personil['foto'])) {
                        unlink('../public/uploads/personil/' . $personil['foto']);
                    }
                    header('Location: ../views/manage_personil.php?success=delete');
                } else {
                    header('Location: ../views/manage_personil.php?error=delete');
                }
            } else {
                header('Location: ../views/manage_personil.php?error=notfound');
            }
        } else {
            header('Location: ../views/manage_personil.php?error=invalid');
        }
        exit();
    }
    
    // Get all personil with pagination
    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        
        // Search functionality
        $where = $search ? "WHERE nama ILIKE '%$search%' OR jabatan ILIKE '%$search%' OR email ILIKE '%$search%'" : '';
        
        // Get total records
        $count_query = "SELECT COUNT(*) as total FROM personil $where";
        $count_result = pg_query($this->conn, $count_query);
        $total_records = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_records / $limit);
        
        // Get personil data
        $query = "SELECT * FROM personil $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $result = pg_query($this->conn, $query);
        
        $personil = [];
        while ($row = pg_fetch_assoc($result)) {
            $personil[] = $row;
        }
        
        return [
            'personil' => $personil,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
}
?>
