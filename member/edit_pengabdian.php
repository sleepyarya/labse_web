<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: my_pengabdian.php');
    exit();
}

$id = intval($_GET['id']);
$member_id = $_SESSION['member_id'];
$error = '';

// Get pengabdian data and check ownership
$query = "SELECT * FROM pengabdian WHERE id = $1 AND personil_id = $2";
$result = pg_query_params($conn, $query, array($id, $member_id));
$pengabdian = pg_fetch_assoc($result);

if (!$pengabdian) {
    header('Location: my_pengabdian.php?error=notfound');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $tanggal = trim($_POST['tanggal']);
    $lokasi = trim($_POST['lokasi']);
    
    if (empty($judul) || empty($deskripsi) || empty($tanggal) || empty($lokasi)) {
        $error = 'Semua field wajib diisi!';
    } else {
        // Handle gambar upload
        $gambar = $pengabdian['gambar']; // Keep existing
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
                        // Delete old image
                        if ($pengabdian['gambar'] && file_exists('../public/uploads/pengabdian/' . $pengabdian['gambar'])) {
                            unlink('../public/uploads/pengabdian/' . $pengabdian['gambar']);
                        }
                        $gambar = $new_filename;
                    } else {
                        $error = 'Gagal mengupload gambar.';
                    }
                }
            } else {
                $error = 'Format file tidak didukung!';
            }
        }
        
        // Update pengabdian (only if owned by this member)
        if (empty($error)) {
            $query = "UPDATE pengabdian SET judul = $1, deskripsi = $2, tanggal = $3, lokasi = $4, gambar = $5, updated_at = NOW() 
                      WHERE id = $6 AND personil_id = $7";
            $result = pg_query_params($conn, $query, array($judul, $deskripsi, $tanggal, $lokasi, $gambar, $id, $member_id));
            
            if ($result) {
                header('Location: my_pengabdian.php?success=edit');
                exit();
            } else {
                $error = 'Gagal mengupdate kegiatan pengabdian.';
            }
        }
    }
}

$page_title = 'Edit Kegiatan Pengabdian';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Edit Kegiatan Pengabdian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_pengabdian.php">Pengabdian Saya</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Form Card -->
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-pencil-square me-2"></i>Form Edit Kegiatan Pengabdian
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
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($pengabdian['penyelenggara']); ?>" readonly>
                </div>
                
                <!-- Judul -->
                <div class="mb-3">
                    <label class="form-label">Judul Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="judul" value="<?php echo htmlspecialchars($pengabdian['judul']); ?>" required>
                </div>
                
                <!-- Tanggal -->
                <div class="mb-3">
                    <label class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="tanggal" value="<?php echo htmlspecialchars($pengabdian['tanggal']); ?>" required>
                </div>
                
                <!-- Lokasi -->
                <div class="mb-3">
                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="lokasi" value="<?php echo htmlspecialchars($pengabdian['lokasi']); ?>" required>
                </div>
                
                <!-- Gambar -->
                <div class="mb-3">
                    <label class="form-label">Gambar Dokumentasi</label>
                    <?php if ($pengabdian['gambar']): ?>
                    <div class="mb-2">
                        <img src="<?php echo BASE_URL; ?>/public/uploads/pengabdian/<?php echo htmlspecialchars($pengabdian['gambar']); ?>" 
                             class="img-thumbnail" style="max-width: 300px;">
                        <p class="text-muted small mb-0">Gambar saat ini</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" name="gambar" id="gambarInput" accept="image/*">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                    
                    <!-- Preview -->
                    <div id="previewContainer" style="display: none;" class="mt-3">
                        <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                        <p class="text-muted small">Preview gambar baru</p>
                    </div>
                </div>
                
                <!-- Deskripsi -->
                <div class="mb-4">
                    <label class="form-label">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="deskripsi" id="deskripsiKegiatan" rows="10" required><?php echo htmlspecialchars($pengabdian['deskripsi']); ?></textarea>
                </div>
                
                <!-- Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Update
                    </button>
                    <a href="my_pengabdian.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                </div>
                
            </form>
            
        </div>
    </div>
    
</div>

<script>
// Preview image
document.getElementById('gambarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
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
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
});
</script>

<?php
pg_close($conn);
include 'includes/member_footer.php';
?>
