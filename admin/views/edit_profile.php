<?php
// Admin Edit Profile View
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../controllers/profileController.php';

$controller = new ProfileController();
$page_title = 'Edit Profil';

// Handle form submissions
$success_message = '';
$error_message = '';

// Handle profile update
if (isset($_POST['update_profile'])) {
    $result = $controller->updateProfile($_SESSION['admin_id'], $_POST);
    if ($result['success']) {
        $_SESSION['profile_success'] = $result['message'];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['profile_error'] = $result['message'];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle photo upload
if (isset($_POST['upload_photo']) && isset($_FILES['photo'])) {
    $result = $controller->uploadPhoto($_SESSION['admin_id'], $_FILES['photo']);
    if ($result['success']) {
        $_SESSION['profile_success'] = $result['message'];
    } else {
        $_SESSION['profile_error'] = $result['message'];
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Get messages from session
if (isset($_SESSION['profile_success'])) {
    $success_message = $_SESSION['profile_success'];
    unset($_SESSION['profile_success']);
}
if (isset($_SESSION['profile_error'])) {
    $error_message = $_SESSION['profile_error'];
    unset($_SESSION['profile_error']);
}

// Get current admin profile
$admin_profile = $controller->getProfile($_SESSION['admin_id']);
if (!$admin_profile) {
    header('Location: dashboard.php');
    exit();
}

include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Edit Profil</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Edit Profil</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        
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
        
        <div class="row">
            <!-- Profile Photo Section -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-person-circle me-2"></i>Foto Profil
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <?php if (!empty($admin_profile['foto']) && file_exists(__DIR__ . '/../../uploads/admin/' . $admin_profile['foto'])): ?>
                                <img src="../../uploads/admin/<?php echo htmlspecialchars($admin_profile['foto']); ?>" 
                                     alt="Foto Profil" class="rounded-circle mb-3" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 150px; height: 150px;">
                                    <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <h5 class="mb-1"><?php echo htmlspecialchars($admin_profile['nama_lengkap']); ?></h5>
                        <p class="text-muted mb-3"><?php echo htmlspecialchars($admin_profile['username']); ?></p>
                        
                        <form method="POST" enctype="multipart/form-data" class="mb-3">
                            <div class="mb-3">
                                <input type="file" class="form-control" name="photo" accept="image/*" required>
                                <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB.</div>
                            </div>
                            <button type="submit" name="upload_photo" class="btn btn-primary">
                                <i class="bi bi-upload me-1"></i>Upload Foto
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Account Info -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Informasi Akun
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>ID Admin:</strong></div>
                            <div class="col-sm-7"><?php echo htmlspecialchars($admin_profile['id']); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Dibuat:</strong></div>
                            <div class="col-sm-7">
                                <?php echo date('d M Y', strtotime($admin_profile['created_at'])); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Terakhir Update:</strong></div>
                            <div class="col-sm-7">
                                <?php 
                                if ($admin_profile['updated_at']) {
                                    echo date('d M Y H:i', strtotime($admin_profile['updated_at']));
                                } else {
                                    echo '-';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Profile Form Section -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>Edit Informasi Profil
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="nama_lengkap" class="form-label">
                                            Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                               value="<?php echo htmlspecialchars($admin_profile['nama_lengkap']); ?>" 
                                               required placeholder="Masukkan nama lengkap">
                                        <div class="invalid-feedback">
                                            Nama lengkap harus diisi.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="username" class="form-label">
                                            Username <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo htmlspecialchars($admin_profile['username']); ?>" 
                                               required placeholder="Masukkan username">
                                        <div class="invalid-feedback">
                                            Username harus diisi.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($admin_profile['email']); ?>" 
                                       required placeholder="Masukkan email">
                                <div class="invalid-feedback">
                                    Email harus diisi dengan format yang valid.
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h6 class="mb-3">
                                <i class="bi bi-lock me-2"></i>Ubah Password (Opsional)
                            </h6>
                            <p class="text-muted small mb-3">Kosongkan jika tidak ingin mengubah password</p>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Password Saat Ini</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password" name="current_password" 
                                                   placeholder="Password saat ini">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                <i class="bi bi-eye" id="current_password_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                                   placeholder="Password baru (min. 6 karakter)">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                                <i class="bi bi-eye" id="new_password_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                                   placeholder="Ulangi password baru">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                                <i class="bi bi-eye" id="confirm_password_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                // Custom password validation
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                const currentPassword = document.getElementById('current_password').value;
                
                // If new password is provided, validate
                if (newPassword) {
                    if (!currentPassword) {
                        document.getElementById('current_password').setCustomValidity('Password saat ini harus diisi');
                    } else {
                        document.getElementById('current_password').setCustomValidity('');
                    }
                    
                    if (newPassword.length < 6) {
                        document.getElementById('new_password').setCustomValidity('Password minimal 6 karakter');
                    } else {
                        document.getElementById('new_password').setCustomValidity('');
                    }
                    
                    if (newPassword !== confirmPassword) {
                        document.getElementById('confirm_password').setCustomValidity('Konfirmasi password tidak cocok');
                    } else {
                        document.getElementById('confirm_password').setCustomValidity('');
                    }
                } else {
                    // Clear validation if no new password
                    document.getElementById('current_password').setCustomValidity('');
                    document.getElementById('new_password').setCustomValidity('');
                    document.getElementById('confirm_password').setCustomValidity('');
                }
                
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Real-time password confirmation check
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword && confirmPassword) {
        if (newPassword === confirmPassword) {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.setCustomValidity('Konfirmasi password tidak cocok');
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        }
    }
});
</script>

<?php include '../includes/admin_footer.php'; ?>
