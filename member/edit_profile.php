<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

$member_id = $_SESSION['member_id'];
$error = '';
$success = '';

// Get member data
$query = "SELECT * FROM personil WHERE id = $1";
$result = pg_query_params($conn, $query, array($member_id));
$member = pg_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $jabatan = trim($_POST['jabatan']);
    $email = trim($_POST['email']);
    $deskripsi = trim($_POST['deskripsi']);
    $password_lama = trim($_POST['password_lama']);
    $password_baru = trim($_POST['password_baru']);
    $password_konfirmasi = trim($_POST['password_konfirmasi']);
    
    if (empty($nama) || empty($email) || empty($jabatan)) {
        $error = 'Nama, email, dan jabatan harus diisi!';
    } else {
        // Check email uniqueness (exclude current member)
        $check_email = "SELECT id FROM personil WHERE email = $1 AND id != $2";
        $result_check = pg_query_params($conn, $check_email, array($email, $member_id));
        
        if (pg_num_rows($result_check) > 0) {
            $error = 'Email sudah digunakan oleh personil lain!';
        } else {
            $foto = $member['foto']; // Keep old photo
            
            // Handle new photo upload
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png'];
                $filename = $_FILES['foto']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $new_filename = 'member_' . $member_id . '_' . time() . '.' . $ext;
                    $upload_path = '../public/uploads/personil/' . $new_filename;
                    
                    // Create directory if not exists
                    if (!file_exists('../public/uploads/personil/')) {
                        mkdir('../public/uploads/personil/', 0777, true);
                    }
                    
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                        // Delete old photo
                        if (!empty($member['foto'])) {
                            $old_path = '../public/uploads/personil/' . $member['foto'];
                            if (file_exists($old_path)) {
                                unlink($old_path);
                            }
                        }
                        $foto = $new_filename;
                    }
                }
            }
            
            // Ambil data user terkait dari tabel users (role=personil)
            $user_query = "SELECT id, password FROM users WHERE role = 'personil' AND reference_id = $1";
            $user_result = pg_query_params($conn, $user_query, array($member_id));
            $user = pg_fetch_assoc($user_result);
            $user_id = $user ? $user['id'] : null;
            
            // Handle password change (hanya di tabel users)
            $password_hash = $user ? $user['password'] : null;
            $password_changed = false;
            if (!empty($password_lama) && !empty($password_baru)) {
                if ($password_baru !== $password_konfirmasi) {
                    $error = 'Password baru dan konfirmasi password tidak sama!';
                } elseif (strlen($password_baru) < 6) {
                    $error = 'Password baru minimal 6 karakter!';
                } elseif (!$user || empty($user['password']) || !password_verify($password_lama, $user['password'])) {
                    $error = 'Password lama tidak sesuai!';
                } else {
                    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                    $password_changed = true;
                }
            }
            
            if (empty($error)) {
                // Update personil data (tanpa kolom password)
                $update_query = "UPDATE personil 
                                SET nama = $1, jabatan = $2, email = $3, deskripsi = $4, foto = $5
                                WHERE id = $6";
                $update_result = pg_query_params($conn, $update_query, 
                                                array($nama, $jabatan, $email, $deskripsi, $foto, $member_id));
                
                if ($update_result) {
                    // Update data user terkait di tabel users
                    if ($user_id) {
                        // Update email di users
                        $update_user_email = "UPDATE users SET email = $1 WHERE id = $2";
                        pg_query_params($conn, $update_user_email, array($email, $user_id));
                        
                        // Update password jika diubah
                        if ($password_changed && $password_hash) {
                            $update_user_pwd = "UPDATE users SET password = $1 WHERE id = $2";
                            pg_query_params($conn, $update_user_pwd, array($password_hash, $user_id));
                        }
                    }
                    
                    // Update session data
                    $_SESSION['member_nama'] = $nama;
                    $_SESSION['member_email'] = $email;
                    $_SESSION['member_jabatan'] = $jabatan;
                    $_SESSION['member_foto'] = $foto;
                    
                    $success = 'Profil berhasil diperbarui!';
                    
                    // Refresh member data
                    $result = pg_query_params($conn, $query, array($member_id));
                    $member = pg_fetch_assoc($result);
                } else {
                    $error = 'Gagal memperbarui profil. Silakan coba lagi.';
                }
            }
        }
    }
}

$page_title = 'Edit Profil';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Edit Profil</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Edit Profil</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Profile Card -->
    <div class="row g-4">
        
        <!-- Profile Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="profile-image-container rounded-circle mb-3 mx-auto" style="width: 120px; height: 120px; border: 3px solid #4A90E2;">
                        <?php if (!empty($member['foto'])): ?>
                            <img src="<?php echo BASE_URL; ?>/public/uploads/personil/<?php echo htmlspecialchars($member['foto']); ?>" 
                                 alt="Profile" class="rounded-circle"
                                 onerror="this.src='<?php echo BASE_URL; ?>/assets/img/default-avatar.png'">
                        <?php else: ?>
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxjaXJjbGUgY3g9IjUwIiBjeT0iNTAiIHI9IjUwIiBmaWxsPSIjZjhmOWZhIi8+CjxjaXJjbGUgY3g9IjUwIiBjeT0iMzUiIHI9IjE1IiBmaWxsPSIjNmM3NTdkIi8+CjxwYXRoIGQ9Ik0yMCA4MCBDIDI2IDY1LCAzMiA1NSwgNTAgNTUgQyA2OCA1NSwgODAgNjUsIDgwIDgwIiBmaWxsPSIjNmM3NTdkIi8+Cjwvc3ZnPg==" 
                                 alt="Default Avatar" class="rounded-circle" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                    
                    <h4 class="mb-1"><?php echo htmlspecialchars($member['nama']); ?></h4>
                    <p class="text-muted mb-2"><?php echo htmlspecialchars($member['jabatan']); ?></p>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($member['email']); ?>
                    </p>
                    
                    <?php if (!empty($member['deskripsi'])): ?>
                    <div class="border-top pt-3 mt-3">
                        <p class="text-muted small mb-0"><?php echo nl2br(htmlspecialchars($member['deskripsi'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="border-top pt-3 mt-3">
                        <small class="text-muted">
                            <i class="bi bi-calendar3 me-1"></i>
                            Bergabung sejak <?php echo date('d M Y', strtotime($member['created_at'])); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Edit Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Edit Informasi Profil
                    </h5>
                </div>
                <div class="card-body">
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data" id="formProfile">
                        
                        <h6 class="mb-3 text-primary">
                            <i class="bi bi-person me-2"></i>Informasi Pribadi
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" 
                                       value="<?php echo htmlspecialchars($member['nama']); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="jabatan" 
                                       value="<?php echo htmlspecialchars($member['jabatan']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($member['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi / Bio</label>
                            <textarea class="form-control" name="deskripsi" rows="4" 
                                      placeholder="Ceritakan sedikit tentang Anda..."><?php echo htmlspecialchars($member['deskripsi']); ?></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" class="form-control" name="foto" id="fotoInput" accept="image/*">
                            <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB</small>
                            
                            <!-- Preview -->
                            <div id="previewContainer" style="display: none;" class="mt-3">
                                <img id="previewImage" src="" alt="Preview" class="rounded" style="max-width: 200px;">
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6 class="mb-3 text-primary">
                            <i class="bi bi-shield-lock me-2"></i>Ubah Password (Opsional)
                        </h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" class="form-control" name="password_lama" id="passwordLama">
                            <small class="text-muted">Isi jika ingin mengubah password</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" class="form-control" name="password_baru" id="passwordBaru">
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" name="password_konfirmasi" id="passwordKonfirmasi">
                            </div>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                        
                    </form>
                    
                </div>
            </div>
        </div>
        
    </div>
    
</div>
<!-- End Member Content -->

<style>
    /* STABLE STYLES - NO ANIMATIONS */
    .form-control:focus {
        border-color: #4A90E2;
        box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
    }
    
    .btn {
        position: relative;
        overflow: hidden;
    }
    
    .card {
        border: 1px solid rgba(0,0,0,0.08);
    }
    
    /* Stable layout for problematic elements */
    .member-content {
        will-change: auto;
    }
    
    /* Fix foto profil flickering */
    .rounded-circle {
        backface-visibility: hidden;
        transform: translateZ(0);
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
        will-change: auto;
    }
    
    /* Stable default avatar - now using img */
    
    /* Stable profile image container */
    .profile-image-container {
        position: relative;
        display: inline-block;
        overflow: hidden;
    }
    
    .profile-image-container img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        backface-visibility: hidden;
        transform: translateZ(0);
    }
    
    /* Prevent layout shifts in profile section */
    .card-body.text-center {
        min-height: 400px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .btn {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
        
        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }
        
        .d-flex.gap-2 .btn {
            width: 100%;
        }
    }
    
    @media (max-width: 480px) {
        .card-body {
            padding: 1rem;
        }
        
        .profile-image-container {
            width: 100px !important;
            height: 100px !important;
        }
        
        /* Default avatar is now img, no special mobile rules needed */
    }
    
    /* Prevent layout shifts only where needed */
    .alert {
        margin-bottom: 1rem;
        min-height: auto;
    }
    
    /* Simple photo preview - no animations */
    #previewContainer {
        opacity: 1;
    }
</style>

<script>
// COMPLETE DISABLE OF ALL ANIMATIONS FOR STABILITY
(function() {
    'use strict';
    
    // Override AOS completely
    window.AOS = {
        init: function() { return false; },
        refresh: function() { return false; },
        refreshHard: function() { return false; }
    };
    
    // Disable jQuery animations if present
    if (window.jQuery) {
        window.jQuery.fx.off = true;
    }
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPage);
    } else {
        initPage();
    }
    
    function initPage() {
        // Remove only AOS attributes, keep other functionality
        const aosElements = document.querySelectorAll('[data-aos]');
        aosElements.forEach(element => {
            element.removeAttribute('data-aos');
            element.removeAttribute('data-aos-delay');
            element.removeAttribute('data-aos-duration');
            element.classList.remove('aos-init', 'aos-animate');
            element.style.opacity = '1';
            element.style.transform = 'none';
        });
        
        // Stabilize all images in profile container
        const profileImages = document.querySelectorAll('.profile-image-container img');
        profileImages.forEach(img => {
            img.style.opacity = '1';
            img.style.transform = 'none';
            img.style.transition = 'none';
            img.style.animation = 'none';
        });
        
        // Simple photo preview - no animations
        const fotoInput = document.getElementById('fotoInput');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');
        
        if (fotoInput && previewContainer && previewImage) {
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar! Maksimal 2MB');
                        this.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewContainer.style.display = 'block';
                        previewContainer.style.opacity = '1';
                    }
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.style.display = 'none';
                }
            });
        }

        // Form validation with proper loading state
        const formProfile = document.getElementById('formProfile');
        if (formProfile) {
            formProfile.addEventListener('submit', function(e) {
                const passwordLama = document.getElementById('passwordLama');
                const passwordBaru = document.getElementById('passwordBaru');
                const passwordKonfirmasi = document.getElementById('passwordKonfirmasi');
                
                if (passwordLama && passwordBaru && passwordKonfirmasi) {
                    const passwordLamaVal = passwordLama.value;
                    const passwordBaruVal = passwordBaru.value;
                    const passwordKonfirmasiVal = passwordKonfirmasi.value;
                    
                    // Validate password change
                    if (passwordLamaVal || passwordBaruVal || passwordKonfirmasiVal) {
                        if (!passwordLamaVal) {
                            e.preventDefault();
                            alert('Password lama harus diisi jika ingin mengubah password!');
                            return false;
                        }
                        if (!passwordBaruVal) {
                            e.preventDefault();
                            alert('Password baru harus diisi!');
                            return false;
                        }
                        if (passwordBaruVal !== passwordKonfirmasiVal) {
                            e.preventDefault();
                            alert('Password baru dan konfirmasi password tidak sama!');
                            return false;
                        }
                        if (passwordBaruVal.length < 6) {
                            e.preventDefault();
                            alert('Password baru minimal 6 karakter!');
                            return false;
                        }
                    }
                }
                
                // Simple loading state - no animations
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    // Prevent double submission
                    if (submitBtn.disabled) {
                        e.preventDefault();
                        return false;
                    }
                    
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Menyimpan...';
                    
                    // Form will submit normally and page will reload
                }
            });
        }
        
        // Skip micro-interactions for stability
        
        // Monitor for new AOS elements but keep other functionality
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            const aosElements = node.querySelectorAll ? node.querySelectorAll('[data-aos]') : [];
                            aosElements.forEach(function(element) {
                                element.removeAttribute('data-aos');
                                element.style.opacity = '1';
                                element.style.transform = 'none';
                            });
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Micro-interactions for better UX
    function addMicroInteractions() {
        // Form input animations
        const formInputs = document.querySelectorAll('.form-control');
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.2s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
        
        // Button ripple effect
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255,255,255,0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                `;
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        });
        
        // Add ripple animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
})();
</script>

<?php
pg_close($conn);

// Light override for AOS only
echo '<script>
// Override AOS only, keep other animations
if (typeof window.AOS === "undefined") {
    window.AOS = {
        init: function() { 
            console.log("AOS disabled for edit profile page"); 
            return false; 
        },
        refresh: function() { return false; },
        refreshHard: function() { return false; }
    };
}
</script>';

include 'includes/member_footer.php';

// Light cleanup after footer
echo '<script>
// Only clean AOS, keep other animations
setTimeout(function() {
    // Remove only AOS attributes
    const aosElements = document.querySelectorAll("[data-aos]");
    aosElements.forEach(function(el) {
        el.removeAttribute("data-aos");
        el.style.opacity = "1";
        el.style.transform = "none";
    });
    
    // Keep AOS disabled
    if (window.AOS) {
        window.AOS.init = function() { 
            console.log("AOS remains disabled"); 
            return false; 
        };
    }
}, 100);
</script>';
?>
