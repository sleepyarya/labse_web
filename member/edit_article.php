<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

$member_id = $_SESSION['member_id'];
$error = '';

// Get artikel ID
$artikel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verify artikel exists and belongs to member
$query_check = "SELECT * FROM artikel WHERE id = $1 AND personil_id = $2";
$result_check = pg_query_params($conn, $query_check, array($artikel_id, $member_id));

if (!$result_check || pg_num_rows($result_check) == 0) {
    header('Location: my_articles.php');
    exit();
}

$artikel = pg_fetch_assoc($result_check);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul']);
    $isi = trim($_POST['isi']);
    
    if (empty($judul) || empty($isi)) {
        $error = 'Judul dan isi artikel harus diisi!';
    } else {
        $gambar = $artikel['gambar']; // Keep old image
        
        // Handle new image upload
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
                        // Delete old image
                        if (!empty($artikel['gambar'])) {
                            $old_path = '../public/uploads/artikel/' . $artikel['gambar'];
                            if (file_exists($old_path)) {
                                unlink($old_path);
                            }
                        }
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
        
        // Update artikel only if no errors
        if (empty($error)) {
            $query = "UPDATE artikel SET judul = $1, isi = $2, gambar = $3 WHERE id = $4 AND personil_id = $5";
            $result = pg_query_params($conn, $query, array($judul, $isi, $gambar, $artikel_id, $member_id));
            
            if ($result) {
                // Log activity: Edit Article
                require_once '../includes/activity_logger.php';
                log_activity($conn, $member_id, $_SESSION['member_nama'], 'EDIT_ARTICLE', 
                    "Mengedit artikel: {$judul}", 'artikel', $artikel_id);
                
                // Refresh artikel data after successful update
                $result_check = pg_query_params($conn, $query_check, array($artikel_id, $member_id));
                if ($result_check && pg_num_rows($result_check) > 0) {
                    $artikel = pg_fetch_assoc($result_check);
                }
                header('Location: my_articles.php?success=edit');
                exit();
            } else {
                $error = 'Gagal memperbarui artikel. Silakan coba lagi.';
            }
        }
    }
}

$page_title = 'Edit Artikel';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Edit Artikel</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_articles.php">Artikel Saya</a></li>
                    <li class="breadcrumb-item active">Edit Artikel</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Form Card -->
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-pencil me-2"></i>Form Edit Artikel
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
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($artikel['penulis']); ?>" readonly>
                </div>
                
                <!-- Judul -->
                <div class="mb-3">
                    <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="judul" 
                           value="<?php echo htmlspecialchars($artikel['judul']); ?>" required>
                </div>
                
                <!-- Gambar Current -->
                <?php if (!empty($artikel['gambar'])): ?>
                <div class="mb-3">
                    <label class="form-label">Gambar Saat Ini</label>
                    <div id="currentImage">
                        <img src="<?php echo BASE_URL; ?>/public/uploads/artikel/<?php echo htmlspecialchars($artikel['gambar']); ?>" 
                             alt="Current Image" class="img-thumbnail" style="max-width: 300px;">
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Gambar Baru -->
                <div class="mb-3">
                    <label class="form-label">Upload Gambar Baru (Opsional)</label>
                    <input type="file" class="form-control" name="gambar" id="gambarInput" accept="image/*">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                    
                    <!-- Preview -->
                    <div id="previewContainer" style="display: none;" class="mt-3">
                        <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                    </div>
                </div>
                
                <!-- Isi Artikel -->
                <div class="mb-4">
                    <label class="form-label">Isi Artikel <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="isi" id="isiArtikel" rows="15" required><?php echo htmlspecialchars($artikel['isi']); ?></textarea>
                </div>
                
                <!-- Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                    </button>
                    <a href="my_articles.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                    <a href="delete_article.php?id=<?php echo $artikel_id; ?>" class="btn btn-danger ms-auto"
                       onclick="return confirm('Yakin ingin menghapus artikel ini?')">
                        <i class="bi bi-trash me-2"></i>Hapus Artikel
                    </a>
                </div>
                
            </form>
            
        </div>
    </div>
    
</div>
<!-- End Member Content -->

<style>
    .form-control:focus {
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
        
        .ms-auto {
            margin-left: 0 !important;
        }
        
        #previewContainer img,
        #currentImage img {
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
        
        textarea.form-control {
            min-height: 150px;
            font-size: 0.85rem;
        }
    }
</style>

<script>
// Preview new image
document.getElementById('gambarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 2MB');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('previewContainer').style.display = 'block';
            const currentImage = document.getElementById('currentImage');
            if (currentImage) {
                currentImage.style.display = 'none';
            }
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('previewContainer').style.display = 'none';
        const currentImage = document.getElementById('currentImage');
        if (currentImage) {
            currentImage.style.display = 'block';
        }
    }
});

// Form validation
document.getElementById('formArtikel').addEventListener('submit', function(e) {
    const judul = document.querySelector('input[name="judul"]').value.trim();
    const isi = document.querySelector('textarea[name="isi"]').value.trim();
    const fileInput = document.querySelector('input[name="gambar"]');
    
    console.log('Form submitted with:', {
        judul: judul,
        isi: isi.substring(0, 50) + '...',
        hasFile: fileInput.files.length > 0,
        fileName: fileInput.files[0] ? fileInput.files[0].name : 'none'
    });
    
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
    
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
});
</script>

<?php
pg_close($conn);
include 'includes/member_footer.php';
?>
