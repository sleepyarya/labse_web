<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

$member_id = $_SESSION['member_id'];
$member_nama = $_SESSION['member_nama'];
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $tanggal = trim($_POST['tanggal']);
    $lokasi = trim($_POST['lokasi']);
    $penyelenggara = $member_nama; // Automatic from session
    
    if (empty($judul) || empty($deskripsi) || empty($tanggal) || empty($lokasi)) {
        $error = 'Semua field wajib diisi!';
    } else {
        // Handle gambar upload
        $gambar = null;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['gambar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                if ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
                    $error = 'Ukuran file terlalu besar! Maksimal 5MB.';
                } else {
                    $new_filename = uniqid() . '.' . $ext;
                    $upload_path = '../public/uploads/pengabdian/' . $new_filename;
                    
                    if (!file_exists('../public/uploads/pengabdian/')) {
                        mkdir('../public/uploads/pengabdian/', 0777, true);
                    }
                    
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                        $gambar = $new_filename;
                    } else {
                        $error = 'Gagal mengupload gambar.';
                    }
                }
            } else {
                $error = 'Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.';
            }
        }
        
        // Insert dengan personil_id
        if (empty($error)) {
            $query = "INSERT INTO pengabdian (judul, deskripsi, tanggal, lokasi, penyelenggara, gambar, personil_id) 
                      VALUES ($1, $2, $3, $4, $5, $6, $7)";
            $result = pg_query_params($conn, $query, array($judul, $deskripsi, $tanggal, $lokasi, $penyelenggara, $gambar, $member_id));
            
            if ($result) {
                header('Location: my_pengabdian.php?success=add');
                exit();
            } else {
                $error = 'Gagal menambahkan kegiatan pengabdian.';
            }
        }
    }
}

$page_title = 'Tambah Kegiatan Pengabdian';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Tambah Kegiatan Pengabdian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_pengabdian.php">Pengabdian Saya</a></li>
                    <li class="breadcrumb-item active">Tambah Baru</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Form Card -->
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-plus-circle me-2"></i>Form Kegiatan Pengabdian Baru
            </h5>
        </div>
        <div class="card-body">
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data" id="formPengabdian">
                
                <!-- Penyelenggara (Read-only) -->
                <div class="mb-3">
                    <label class="form-label">Penyelenggara</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($member_nama); ?>" readonly>
                    <small class="text-muted">Otomatis terisi dengan nama Anda</small>
                </div>
                
                <!-- Judul -->
                <div class="mb-3">
                    <label class="form-label">Judul Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="judul" placeholder="Masukkan judul kegiatan" required>
                </div>
                
                <!-- Tanggal -->
                <div class="mb-3">
                    <label class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="tanggal" required>
                </div>
                
                <!-- Lokasi -->
                <div class="mb-3">
                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="lokasi" placeholder="Contoh: Aula Kelurahan Tembalang, Semarang" required>
                </div>
                
                <!-- Gambar -->
                <div class="mb-3">
                    <label class="form-label">Gambar Dokumentasi</label>
                    <input type="file" class="form-control" name="gambar" id="gambarInput" accept="image/*">
                    <small class="text-muted">Format: JPG, JPEG, PNG, GIF. Maksimal 5MB</small>
                    
                    <!-- Preview -->
                    <div id="previewContainer" style="display: none;" class="mt-3">
                        <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                    </div>
                </div>
                
                <!-- Deskripsi -->
                <div class="mb-4">
                    <label class="form-label">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="deskripsi" id="deskripsiKegiatan" rows="10" 
                              placeholder="Deskripsikan detail kegiatan pengabdian..." required></textarea>
                    <small class="text-muted">Minimal 100 karakter</small>
                </div>
                
                <!-- Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Simpan
                    </button>
                    <a href="my_pengabdian.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                </div>
                
            </form>
            
        </div>
    </div>
    
</div>
<!-- End Member Content -->

<style>
    .form-control:focus, .form-select:focus {
        border-color: #4A90E2;
        box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
    }
</style>

<script>
// Preview image before upload
document.getElementById('gambarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 5MB');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('previewContainer').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

// Form validation
document.getElementById('formPengabdian').addEventListener('submit', function(e) {
    const deskripsi = document.querySelector('textarea[name="deskripsi"]').value.trim();
    
    if (deskripsi.length < 100) {
        e.preventDefault();
        alert('Deskripsi minimal 100 karakter!');
        return false;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
});
</script>

<?php
pg_close($conn);
include 'includes/member_footer.php';
?>
