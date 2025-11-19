<?php
// Admin Manage Users View
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../controllers/userController.php';

$page_title = 'Manajemen User';

// Initialize controller
$userController = new UserController();

// Handle actions
$success_message = '';
$error_message = '';

// Clear any previous messages from session
if (isset($_SESSION['user_success_message'])) {
    $success_message = $_SESSION['user_success_message'];
    unset($_SESSION['user_success_message']);
}
if (isset($_SESSION['user_error_message'])) {
    $error_message = $_SESSION['user_error_message'];
    unset($_SESSION['user_error_message']);
}

// Handle update user
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $is_active = isset($_POST['is_active']) ? true : false;
    $new_password = trim($_POST['new_password'] ?? '');
    
    // Validation
    if (empty($username) || empty($email)) {
        $_SESSION['user_error_message'] = 'Username dan email tidak boleh kosong!';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['user_error_message'] = 'Format email tidak valid!';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Get user info untuk reference_id
        $user_info = $userController->getById($user_id);
        
        if (!$user_info) {
            $_SESSION['user_error_message'] = 'User tidak ditemukan!';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $old_role = $user_info['role'];
            $reference_id = $user_info['reference_id'];
        
        // Check duplicate username/email (exclude current user)
        $check_username = "SELECT COUNT(*) as count FROM users WHERE username = $1 AND id != $2";
        $check_email = "SELECT COUNT(*) as count FROM users WHERE email = $1 AND id != $2";
        
        $username_exists = pg_fetch_result(pg_query_params($conn, $check_username, array($username, $user_id)), 0, 0);
        $email_exists = pg_fetch_result(pg_query_params($conn, $check_email, array($email, $user_id)), 0, 0);
        
        if ($username_exists > 0) {
            $_SESSION['user_error_message'] = 'Username sudah digunakan oleh user lain!';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } elseif ($email_exists > 0) {
            $_SESSION['user_error_message'] = 'Email sudah digunakan oleh user lain!';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Update users table
            $update_query = "UPDATE users SET username = $1, email = $2, role = $3, is_active = $4 WHERE id = $5";
            $result = pg_query_params($conn, $update_query, array(
                $username,
                $email,
                $role,
                $is_active ? 'TRUE' : 'FALSE',
                $user_id
            ));
            
            // Update password if provided
            if (!empty($new_password) && strlen($new_password) >= 6) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $pwd_query = "UPDATE users SET password = $1 WHERE id = $2";
                pg_query_params($conn, $pwd_query, array($hashed_password, $user_id));
            }
            
            // Update original table based on role
            if ($role === 'admin') {
                $update_original = "UPDATE admin_users SET username = $1, email = $2 WHERE id = $3";
                pg_query_params($conn, $update_original, array($username, $email, $reference_id));
            } elseif ($role === 'personil') {
                $update_original = "UPDATE personil SET email = $1 WHERE id = $2";
                pg_query_params($conn, $update_original, array($email, $reference_id));
            }
            
            if ($result) {
                $_SESSION['user_success_message'] = 'User berhasil diupdate!';
            } else {
                $_SESSION['user_error_message'] = 'Gagal mengupdate user!';
            }
            
            // Redirect to prevent form resubmission
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
        }
    }
}

// Handle add user
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = trim($_POST['password']);
    $is_active = isset($_POST['is_active']) ? true : false;
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = 'Username, email, dan password tidak boleh kosong!';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password minimal 6 karakter!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Format email tidak valid!';
    } else {
        // Check duplicate username/email - hanya cek di tabel users (yang aktif)
        $check_username = "SELECT COUNT(*) as count FROM users WHERE username = $1";
        $check_email = "SELECT COUNT(*) as count FROM users WHERE email = $1";
        
        $username_exists = pg_fetch_result(pg_query_params($conn, $check_username, array($username)), 0, 0);
        $email_exists = pg_fetch_result(pg_query_params($conn, $check_email, array($email)), 0, 0);
        
        if ($username_exists > 0) {
            $error_message = 'Username sudah digunakan!';
        } elseif ($email_exists > 0) {
            $error_message = 'Email sudah digunakan!';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Start transaction
            pg_query($conn, "BEGIN");
            
            try {
                $reference_id = null;
                
                // Create entry in original table first to get reference_id
                if ($role === 'admin') {
                    $insert_admin = "INSERT INTO admin_users (username, email, nama_lengkap, created_at) 
                                   VALUES ($1, $2, $3, NOW()) RETURNING id";
                    $admin_result = pg_query_params($conn, $insert_admin, array(
                        $username,
                        $email,
                        $username // Default nama_lengkap = username
                    ));
                    
                    if ($admin_result) {
                        $reference_id = pg_fetch_result($admin_result, 0, 0);
                    } else {
                        throw new Exception('Gagal membuat admin user');
                    }
                } elseif ($role === 'personil') {
                    $insert_personil = "INSERT INTO personil (nama, email, is_member, created_at) 
                                      VALUES ($1, $2, TRUE, NOW()) RETURNING id";
                    $personil_result = pg_query_params($conn, $insert_personil, array(
                        $username, // Default nama = username
                        $email
                    ));
                    
                    if ($personil_result) {
                        $reference_id = pg_fetch_result($personil_result, 0, 0);
                    } else {
                        throw new Exception('Gagal membuat personil user');
                    }
                } elseif ($role === 'mahasiswa') {
                    $insert_mahasiswa = "INSERT INTO mahasiswa (nama, email, nim, created_at) 
                                        VALUES ($1, $2, $3, NOW()) RETURNING id";
                    $mahasiswa_result = pg_query_params($conn, $insert_mahasiswa, array(
                        $username, // Default nama = username
                        $email,
                        'AUTO' . time() // Generate dummy NIM
                    ));
                    
                    if ($mahasiswa_result) {
                        $reference_id = pg_fetch_result($mahasiswa_result, 0, 0);
                    } else {
                        throw new Exception('Gagal membuat mahasiswa user');
                    }
                }
                
                // Now insert into users table with reference_id
                $insert_query = "INSERT INTO users (username, email, role, password, reference_id, is_active, created_at) 
                               VALUES ($1, $2, $3, $4, $5, $6, NOW())";
                $result = pg_query_params($conn, $insert_query, array(
                    $username,
                    $email,
                    $role,
                    $hashed_password,
                    $reference_id,
                    $is_active ? 'TRUE' : 'FALSE'
                ));
                
                if ($result) {
                    pg_query($conn, "COMMIT");
                    $success_message = 'User baru berhasil ditambahkan!';
                } else {
                    throw new Exception('Gagal menambahkan ke tabel users');
                }
                
            } catch (Exception $e) {
                pg_query($conn, "ROLLBACK");
                $error_message = 'Gagal menambahkan user baru: ' . $e->getMessage();
            }
        }
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $result = $userController->delete($user_id);
    if ($result['success']) {
        $success_message = $result['message'];
    } else {
        $error_message = $result['message'];
    }
}

// Get filter and search parameters
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 10;

// Get users data
$data = $userController->getAll($page, $limit, $search, $role_filter);
$users = $data['users'];
$total_pages = $data['total_pages'];
$current_page = $data['current_page'];

// Get statistics
$stats = $userController->getStatistics();

include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Manajemen User</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Manajemen User</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small">Total Users</div>
                            <div class="h3 mb-0"><?php echo $stats['total']; ?></div>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small">Active Users</div>
                            <div class="h3 mb-0"><?php echo $stats['active']; ?></div>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small">Admin</div>
                            <div class="h3 mb-0"><?php echo $stats['by_role']['admin'] ?? 0; ?></div>
                        </div>
                        <i class="bi bi-shield-check fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small">Personil</div>
                            <div class="h3 mb-0"><?php echo $stats['by_role']['personil'] ?? 0; ?></div>
                        </div>
                        <i class="bi bi-person-badge fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success/Error Messages -->
    <?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- Filter and Search -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-funnel me-2"></i>Filter & Pencarian
        </div>
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari User</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Username atau Email..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="role" class="form-label">Filter Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">Semua Role</option>
                        <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="personil" <?php echo $role_filter === 'personil' ? 'selected' : ''; ?>>Personil</option>
                        <option value="mahasiswa" <?php echo $role_filter === 'mahasiswa' ? 'selected' : ''; ?>>Mahasiswa</option>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                    <a href="manage_users.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-table me-2"></i>Daftar User</span>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary"><?php echo $data['total_records']; ?> Total</span>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-lg me-1"></i>Tambah User
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada data user
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($user['full_name'] ?? '-'); ?>
                                <?php if ($user['additional_info']): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($user['additional_info']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $role_badges = [
                                    'admin' => 'bg-danger',
                                    'personil' => 'bg-primary',
                                    'mahasiswa' => 'bg-success'
                                ];
                                $badge_class = $role_badges[$user['role']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['is_active'] === 't'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if ($user['last_login']) {
                                    echo date('d/m/Y H:i', strtotime($user['last_login']));
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- Edit -->
                                    <button type="button" class="btn btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editUserModal"
                                            data-user-id="<?php echo $user['id']; ?>"
                                            data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                            data-fullname="<?php echo htmlspecialchars($user['full_name'] ?? '-'); ?>"
                                            data-role="<?php echo $user['role']; ?>"
                                            data-active="<?php echo $user['is_active']; ?>"
                                            title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    
                                    <!-- Delete -->
                                    <?php if (!($user['role'] === 'admin' && isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $user['reference_id'])): ?>
                                    <a href="?action=delete&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-outline-danger" 
                                       onclick="return confirm('PERINGATAN! Menghapus user akan menghapus akses login mereka. Yakin ingin menghapus?')"
                                       title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center">
                    <?php 
                    $query_params = http_build_query(array_filter([
                        'search' => $search,
                        'role' => $role_filter
                    ]));
                    $query_string = $query_params ? '&' . $query_params : '';
                    ?>
                    
                    <?php if ($current_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $current_page - 1; ?><?php echo $query_string; ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $query_string; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $current_page + 1; ?><?php echo $query_string; ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Edit User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Info:</strong> Nama lengkap <strong id="edit_fullname_display"></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                        <div class="form-text">Username untuk login</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                        <div class="form-text">Email harus valid</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="personil">Personil</option>
                            <option value="mahasiswa">Mahasiswa</option>
                        </select>
                        <div class="form-text">Role menentukan hak akses user</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">
                                <strong>User Aktif</strong> (bisa login ke sistem)
                            </label>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="mb-3">Reset Password <small class="text-muted">(Opsional)</small></h6>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="text" class="form-control" id="new_password" name="new_password" 
                               minlength="6" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <div class="form-text">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah.</div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="generatePassword()">
                        <i class="bi bi-lightning me-1"></i>Generate Password Random (12 karakter)
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update_user" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-lg me-2"></i>Tambah User Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Info:</strong> User baru akan ditambahkan ke sistem dengan role yang dipilih.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" required>
                        <div class="form-text">Username untuk login (unik)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" required>
                        <div class="form-text">Email harus valid dan unik</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" name="role" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="personil">Personil</option>
                            <option value="mahasiswa">Mahasiswa</option>
                        </select>
                        <div class="form-text">Role menentukan hak akses user</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_password" name="password" required minlength="6">
                        <div class="form-text">Minimal 6 karakter</div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-3" onclick="generateAddPassword()">
                        <i class="bi bi-lightning me-1"></i>Generate Password Random (12 karakter)
                    </button>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                            <label class="form-check-label">
                                <strong>User Aktif</strong> (bisa login ke sistem)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="add_user" class="btn btn-success">
                        <i class="bi bi-plus-lg me-1"></i>Tambah User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Edit User Modal Handler
const editUserModal = document.getElementById('editUserModal');
editUserModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const userId = button.getAttribute('data-user-id');
    const username = button.getAttribute('data-username');
    const email = button.getAttribute('data-email');
    const fullname = button.getAttribute('data-fullname');
    const role = button.getAttribute('data-role');
    const isActive = button.getAttribute('data-active') === 't';
    
    // Set values
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_fullname_display').textContent = fullname;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_is_active').checked = isActive;
    document.getElementById('new_password').value = '';
    
    // Set selected role
    const roleSelect = document.getElementById('edit_role');
    for (let option of roleSelect.options) {
        if (option.value === role) {
            option.selected = true;
        }
    }
});

// Generate Random Password (for edit modal)
function generatePassword() {
    const length = 12;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    document.getElementById('new_password').value = password;
}

// Generate Random Password (for add modal)
function generateAddPassword() {
    const length = 12;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    document.getElementById('add_password').value = password;
}
</script>

    </div><!-- /.container-fluid -->
</div><!-- /.admin-content -->

<?php
pg_close($conn);
include '../includes/admin_footer.php';
?>
