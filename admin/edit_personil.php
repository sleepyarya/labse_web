<?php
// Proteksi halaman - harus login dulu
require_once 'auth_check.php';
require_once '../includes/config.php';

$page_title = 'Edit Personil';
$error = '';
$success = false;

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: manage_personil.php?error=ID tidak valid');
    exit();
}

// Get existing data
$query = "SELECT * FROM personil WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));

if (pg_num_rows($result) == 0) {
    header('Location: manage_personil.php?error=Data tidak ditemukan');
    exit();
}

$data = pg_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = pg_escape_string($conn, trim($_POST['nama']));
    $jabatan = pg_escape_string($conn, trim($_POST['jabatan']));
    $deskripsi = pg_escape_string($conn, trim($_POST['deskripsi']));
    $email = pg_escape_string($conn, trim($_POST['email']));
    
    // Validation
    if (empty($nama) || empty($jabatan) || empty($email)) {
        $error = 'Nama, jabatan, dan email wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        // Handle file upload
        $foto = $data['foto']; // Keep existing photo
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['foto']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = 'personil_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_dir = '../uploads/personil/';
                
                // Create directory if not exists
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $new_filename)) {
                    // Delete old photo if exists
                    if ($data['foto'] && file_exists($upload_dir . $data['foto'])) {
                        unlink($upload_dir . $data['foto']);
                    }
                    $foto = $new_filename;
                }
            } else {
                $error = 'Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
            }
        }
        
        // Update database
        if (empty($error)) {
            $query = "UPDATE personil SET nama = $1, jabatan = $2, deskripsi = $3, foto = $4, email = $5 
                      WHERE id = $6";
            $result = pg_query_params($conn, $query, array($nama, $jabatan, $deskripsi, $foto, $email, $id));
            
            if ($result) {
                header('Location: manage_personil.php?success=edit');
                exit();
            } else {
                $error = 'Gagal mengupdate personil: ' . pg_last_error($conn);
            }
        }
    }
}

include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Edit Personil</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_personil.php">Kelola Personil</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card" data-aos="fade-up">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Edit Personil</h5>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" id="formPersonil">
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" required 
                                       value="<?php echo htmlspecialchars($data['nama']); ?>"
                                       placeholder="Masukkan nama lengkap">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select name="jabatan" class="form-select" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Dosen" <?php echo $data['jabatan'] == 'Dosen' ? 'selected' : ''; ?>>Dosen</option>
                                    <option value="Ketua Lab" <?php echo $data['jabatan'] == 'Ketua Lab' ? 'selected' : ''; ?>>Ketua Lab</option>
                                    <option value="Sekretaris Lab" <?php echo $data['jabatan'] == 'Sekretaris Lab' ? 'selected' : ''; ?>>Sekretaris Lab</option>
                                    <option value="Asisten Lab" <?php echo $data['jabatan'] == 'Asisten Lab' ? 'selected' : ''; ?>>Asisten Lab</option>
                                    <option value="Staff" <?php echo $data['jabatan'] == 'Staff' ? 'selected' : ''; ?>>Staff</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required 
                                       value="<?php echo htmlspecialchars($data['email']); ?>"
                                       placeholder="contoh@email.com">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="4" 
                                          placeholder="Deskripsi singkat tentang personil..."><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
                                <small class="text-muted">Opsional - Informasi tambahan tentang personil</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Foto</label>
                                
                                <?php if ($data['foto']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo BASE_URL . '/uploads/personil/' . htmlspecialchars($data['foto']); ?>" 
                                         class="img-thumbnail" style="max-width: 200px;" id="currentPhoto">
                                    <p class="text-muted mb-0 mt-1"><small>Foto saat ini</small></p>
                                </div>
                                <?php endif; ?>
                                
                                <input type="file" name="foto" class="form-control" accept="image/*" id="fotoInput">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.</small>
                                <div id="previewContainer" class="mt-3" style="display: none;">
                                    <img id="previewImage" src="" class="img-thumbnail" style="max-width: 200px;">
                                    <p class="text-muted mb-0 mt-1"><small>Preview foto baru</small></p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save me-2"></i>Update
                                </button>
                                <a href="manage_personil.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                            
                        </form>
                        
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <strong>ID:</strong> #<?php echo $data['id']; ?>
                            </li>
                            <li class="mb-2">
                                <strong>Ditambahkan:</strong><br>
                                <small class="text-muted"><?php echo date('d M Y H:i', strtotime($data['created_at'])); ?></small>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Kosongkan foto jika tidak ingin mengubah
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Foto lama akan terhapus jika upload foto baru
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
    }
    
    .btn {
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
</style>

<script>
// Preview image before upload
document.getElementById('fotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('previewContainer').style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('previewContainer').style.display = 'none';
    }
});

// Form validation
document.getElementById('formPersonil').addEventListener('submit', function(e) {
    const nama = document.querySelector('input[name="nama"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const jabatan = document.querySelector('select[name="jabatan"]').value;
    
    if (!nama || !email || !jabatan) {
        e.preventDefault();
        alert('Nama, Jabatan, dan Email wajib diisi!');
        return false;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengupdate...';
});
</script>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>
