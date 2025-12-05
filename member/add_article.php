<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

$member_id = $_SESSION['member_id'];
$member_nama = $_SESSION['member_nama'];
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul']);
    $isi = trim($_POST['isi']);
    $penulis = $member_nama; // Otomatis dari session
    
    if (empty($judul) || empty($isi)) {
        $error = 'Judul dan isi artikel harus diisi!';
    } else {
        // Handle gambar upload
        $gambar = null;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['gambar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                // Check file size (max 2MB)
                if ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
                    $error = 'Ukuran file terlalu besar! Maksimal 2MB.';
                } else {
                    $new_filename = uniqid() . '.' . $ext;
                    $upload_path = '../public/uploads/artikel/' . $new_filename;
                    
                    // Create directory if not exists
                    if (!file_exists('../public/uploads/artikel/')) {
                        mkdir('../public/uploads/artikel/', 0777, true);
                    }
                    
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                        $gambar = $new_filename;
                    } else {
                        $error = 'Gagal mengupload gambar. Silakan coba lagi.';
                    }
                }
            } else {
                $error = 'Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.';
            }
        } elseif (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
            // Handle upload errors (error 4 = no file uploaded, which is OK)
            switch ($_FILES['gambar']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error = 'File terlalu besar!';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error = 'File hanya terupload sebagian!';
                    break;
                default:
                    $error = 'Terjadi kesalahan saat upload file!';
                    break;
            }
        }
        
        // Insert artikel dengan personil_id only if no errors
        if (empty($error)) {
            $query = "INSERT INTO artikel (judul, isi, penulis, gambar, personil_id) 
                      VALUES ($1, $2, $3, $4, $5) RETURNING id";
            $result = pg_query_params($conn, $query, array($judul, $isi, $penulis, $gambar, $member_id));
            
            if ($result) {
                // Get inserted article ID
                $row = pg_fetch_assoc($result);
                $artikel_id = $row['id'];
                
                // Log activity: Create Article
                require_once '../includes/activity_logger.php';
                log_activity($conn, $member_id, $member_nama, 'CREATE_ARTICLE', 
                    "Membuat artikel baru: {$judul}", 'artikel', $artikel_id);
                
                header('Location: my_articles.php?success=add');
                exit();
            } else {
                $error = 'Gagal menambahkan artikel. Silakan coba lagi.';
            }
        }
    }
}

$page_title = 'Buat Artikel';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Buat Artikel Baru</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_articles.php">Artikel Saya</a></li>
                    <li class="breadcrumb-item active">Buat Artikel</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Form Card -->
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-plus-circle me-2"></i>Form Artikel Baru
            </h5>
        </div>
        <div class="card-body">
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data" id="formArtikel">
                
                <!-- Penulis (Read-only) -->
                <div class="mb-3">
                    <label class="form-label">Penulis</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($member_nama); ?>" readonly>
                    <small class="text-muted">Otomatis terisi dengan nama Anda</small>
                </div>
                
                <!-- Judul -->
                <div class="mb-3">
                    <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="judul" placeholder="Masukkan judul artikel yang menarik" required>
                </div>
                
                <!-- Gambar -->
                <div class="mb-3">
                    <label class="form-label">Gambar Artikel</label>
                    <input type="file" class="form-control" name="gambar" id="gambarInput" accept="image/*">
                    <small class="text-muted">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB</small>
                    
                    <!-- Preview -->
                    <div id="previewContainer" style="display: none;" class="mt-3">
                        <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                    </div>
                </div>
                
                <!-- Isi Artikel -->
                <div class="mb-4">
                    <label class="form-label">Isi Artikel <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="isi" id="isiArtikel" rows="15" 
                              placeholder="Tulis konten artikel Anda di sini..." required></textarea>
                    <small class="text-muted">Tulis artikel yang informatif dan berkualitas</small>
                </div>
                
                <!-- Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Publikasikan Artikel
                    </button>
                    <a href="my_articles.php" class="btn btn-secondary">
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
    
    /* Responsive Form Styles */
    @media (max-width: 768px) {
        .card-header h5 {
            font-size: 1rem;
        }
        
        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .form-control {
            font-size: 0.9rem;
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
        
        textarea.form-control {
            min-height: 200px;
        }
        
        #previewContainer img {
            max-width: 100%;
            height: auto;
        }
    }
    
    @media (max-width: 480px) {
        .card-body {
            padding: 1rem;
        }
        
        .form-label {
            font-size: 0.85rem;
        }
        
        .form-control {
            font-size: 0.85rem;
            padding: 0.5rem;
        }
        
        small.text-muted {
            font-size: 0.75rem;
        }
        
        .btn {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
        
        textarea.form-control {
            min-height: 150px;
            font-size: 0.85rem;
        }
    }
</style>

<script>
// Preview image before upload
document.getElementById('gambarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Check file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 2MB');
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
document.getElementById('formArtikel').addEventListener('submit', function(e) {
    const judul = document.querySelector('input[name="judul"]').value.trim();
    const isi = document.querySelector('textarea[name="isi"]').value.trim();
    
    if (!judul || !isi) {
        e.preventDefault();
        alert('Judul dan isi artikel wajib diisi!');
        return false;
    }
    
    if (isi.length < 100) {
        e.preventDefault();
        alert('Isi artikel terlalu pendek! Minimal 100 karakter.');
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
