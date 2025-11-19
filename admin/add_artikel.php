<?php
// Redirect to new view structure
header('Location: views/artikel_form.php' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit();
?>

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
        $gambar = '';
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
                    $gambar = $new_filename;
                }
            } else {
                $error = 'Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
            }
        }
        
        // Insert to database
        if (empty($error)) {
            $query = "INSERT INTO artikel (judul, isi, penulis, gambar) 
                      VALUES ($1, $2, $3, $4)";
            $result = pg_query_params($conn, $query, array($judul, $isi, $penulis, $gambar));
            
            if ($result) {
                header('Location: manage_artikel.php?success=add');
                exit();
            } else {
                $error = 'Gagal menambahkan artikel: ' . pg_last_error($conn);
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
            <h4 class="mb-0">Tambah Artikel Baru</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_artikel.php">Kelola Artikel</a></li>
                    <li class="breadcrumb-item active">Tambah Baru</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="card" data-aos="fade-up">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-file-plus me-2"></i>Form Tambah Artikel</h5>
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
                                       value="<?php echo isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''; ?>"
                                       placeholder="Masukkan judul artikel yang menarik">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Penulis <span class="text-danger">*</span></label>
                                <input type="text" name="penulis" class="form-control" required 
                                       value="<?php echo isset($_POST['penulis']) ? htmlspecialchars($_POST['penulis']) : htmlspecialchars($_SESSION['admin_nama']); ?>"
                                       placeholder="Nama penulis artikel">
                                <small class="text-muted">Default: <?php echo htmlspecialchars($_SESSION['admin_nama']); ?></small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Isi Artikel <span class="text-danger">*</span></label>
                                <textarea name="isi" id="isiArtikel" class="form-control" rows="15" required 
                                          placeholder="Tulis isi artikel di sini..."><?php echo isset($_POST['isi']) ? htmlspecialchars($_POST['isi']) : ''; ?></textarea>
                                <small class="text-muted">Gunakan editor untuk format teks (bold, italic, list, dll)</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Gambar Artikel</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*" id="gambarInput">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB. Gambar utama untuk artikel.</small>
                                <div id="previewContainer" class="mt-3" style="display: none;">
                                    <img id="previewImage" src="" class="img-thumbnail" style="max-width: 300px;">
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-2"></i>Simpan Artikel
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                </button>
                                <a href="manage_artikel.php" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                            </div>
                            
                        </form>
                        
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Petunjuk</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Judul</strong> harus menarik dan deskriptif
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Isi</strong> minimal 100 karakter
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Gambar</strong> mendukung JPG, PNG, GIF
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Gunakan paragraf dan heading untuk struktur
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mt-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips</h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <p class="mb-2">• Buat judul yang SEO-friendly</p>
                            <p class="mb-2">• Gunakan gambar berkualitas tinggi</p>
                            <p class="mb-2">• Struktur artikel dengan heading</p>
                            <p class="mb-0">• Pastikan konten original</p>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
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
        
        .form-control,
        .form-select {
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
            min-height: 150px;
        }
        
        #isiArtikel {
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
        
        .form-control,
        .form-select {
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
            min-height: 120px;
        }
        
        #isiArtikel {
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
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
});

// Character counter for isi
document.getElementById('isiArtikel').addEventListener('input', function() {
    const length = this.value.length;
    const minLength = 50;
    
    if (length < minLength) {
        this.style.borderColor = '#dc3545';
    } else {
        this.style.borderColor = '#198754';
    }
});
</script>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>