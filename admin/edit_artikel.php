<?php
// Proteksi halaman - harus login dulu
require_once 'auth_check.php';
require_once '../includes/config.php';

$page_title = 'Edit Artikel';
$error = '';
$success = false;

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: manage_artikel.php?error=ID tidak valid');
    exit();
}

// Get existing data
$query = "SELECT * FROM artikel WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));

if (pg_num_rows($result) == 0) {
    header('Location: manage_artikel.php?error=Data tidak ditemukan');
    exit();
}

$data = pg_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = pg_escape_string($conn, trim($_POST['judul']));
    $isi = pg_escape_string($conn, trim($_POST['isi']));
    $penulis = pg_escape_string($conn, trim($_POST['penulis']));
    
    // Validation
    if (empty($judul) || empty($isi) || empty($penulis)) {
        $error = 'Judul, isi, dan penulis wajib diisi!';
    } else {
        // Handle file upload
        $gambar = $data['gambar']; // Keep existing image
        
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['gambar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = 'artikel_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_dir = '../uploads/artikel/';
                
                // Create directory if not exists
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                    // Delete old image if exists
                    if ($data['gambar'] && file_exists($upload_dir . $data['gambar'])) {
                        unlink($upload_dir . $data['gambar']);
                    }
                    $gambar = $new_filename;
                }
            } else {
                $error = 'Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
            }
        }
        
        // Update database
        if (empty($error)) {
            $query = "UPDATE artikel SET judul = $1, isi = $2, penulis = $3, gambar = $4 
                      WHERE id = $5";
            $result = pg_query_params($conn, $query, array($judul, $isi, $penulis, $gambar, $id));
            
            if ($result) {
                header('Location: manage_artikel.php?success=edit');
                exit();
            } else {
                $error = 'Gagal mengupdate artikel: ' . pg_last_error($conn);
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
            <h4 class="mb-0">Edit Artikel</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_artikel.php">Kelola Artikel</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="card" data-aos="fade-up">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Edit Artikel</h5>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" id="formArtikel">
                            
                            <div class="mb-3">
                                <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" required 
                                       value="<?php echo htmlspecialchars($data['judul']); ?>"
                                       placeholder="Masukkan judul artikel yang menarik">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Penulis <span class="text-danger">*</span></label>
                                <input type="text" name="penulis" class="form-control" required 
                                       value="<?php echo htmlspecialchars($data['penulis']); ?>"
                                       placeholder="Nama penulis artikel">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Isi Artikel <span class="text-danger">*</span></label>
                                <textarea name="isi" id="isiArtikel" class="form-control" rows="15" required 
                                          placeholder="Tulis isi artikel di sini..."><?php echo htmlspecialchars($data['isi']); ?></textarea>
                                <small class="text-muted">Gunakan editor untuk format teks (bold, italic, list, dll)</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Gambar Artikel</label>
                                
                                <?php if ($data['gambar']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo BASE_URL . '/uploads/artikel/' . htmlspecialchars($data['gambar']); ?>" 
                                         class="img-thumbnail" style="max-width: 300px;" id="currentImage">
                                    <p class="text-muted mb-0 mt-1"><small>Gambar saat ini</small></p>
                                </div>
                                <?php endif; ?>
                                
                                <input type="file" name="gambar" class="form-control" accept="image/*" id="gambarInput">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB. Kosongkan jika tidak ingin mengubah gambar.</small>
                                <div id="previewContainer" class="mt-3" style="display: none;">
                                    <img id="previewImage" src="" class="img-thumbnail" style="max-width: 300px;">
                                    <p class="text-muted mb-0 mt-1"><small>Preview gambar baru</small></p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save me-2"></i>Update Artikel
                                </button>
                                <a href="manage_artikel.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                            
                        </form>
                        
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3">
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
                                <strong>Dibuat:</strong><br>
                                <small class="text-muted"><?php echo date('d M Y H:i', strtotime($data['created_at'])); ?></small>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Kosongkan gambar jika tidak ingin mengubah
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Gambar lama akan terhapus jika upload baru
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mt-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistik</h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <p class="mb-2"><strong>Jumlah Karakter:</strong> <span id="charCount">0</span></p>
                            <p class="mb-0"><strong>Jumlah Kata:</strong> <span id="wordCount">0</span></p>
                        </small>
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
    
    #isiArtikel {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
    }
</style>

<script>
// Preview image before upload
document.getElementById('gambarInput').addEventListener('change', function(e) {
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
document.getElementById('formArtikel').addEventListener('submit', function(e) {
    const judul = document.querySelector('input[name="judul"]').value.trim();
    const isi = document.querySelector('textarea[name="isi"]').value.trim();
    const penulis = document.querySelector('input[name="penulis"]').value.trim();
    
    if (!judul || !isi || !penulis) {
        e.preventDefault();
        alert('Judul, Isi, dan Penulis wajib diisi!');
        return false;
    }
    
    if (isi.length < 50) {
        e.preventDefault();
        alert('Isi artikel minimal 50 karakter!');
        return false;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengupdate...';
});

// Character and word counter
function updateStats() {
    const text = document.getElementById('isiArtikel').value;
    const charCount = text.length;
    const wordCount = text.trim().split(/\s+/).filter(word => word.length > 0).length;
    
    document.getElementById('charCount').textContent = charCount;
    document.getElementById('wordCount').textContent = wordCount;
    
    // Border color based on length
    const textarea = document.getElementById('isiArtikel');
    if (charCount < 50) {
        textarea.style.borderColor = '#dc3545';
    } else {
        textarea.style.borderColor = '#ffc107';
    }
}

document.getElementById('isiArtikel').addEventListener('input', updateStats);

// Initial count
updateStats();
</script>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>
