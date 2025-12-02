<?php
require_once 'auth_check.php';
require_once 'controllers/penelitianController.php';
require_once '../includes/config.php';

$controller = new PenelitianController();
$is_edit = isset($_GET['id']) && !empty($_GET['id']);
$penelitian = null;
$error = '';

// Get personil list for dropdown
$personil_list = $controller->getAllPersonil();

if ($is_edit) {
    $id = intval($_GET['id']);
    $result = $controller->edit($id);
    $error = $result['error'];
    $penelitian = $result['penelitian'];
    
    if (!$penelitian) {
        header('Location: manage_penelitian.php?error=notfound');
        exit();
    }
} else {
    $result = $controller->add();
    $error = $result['error'];
}

$current_year = date('Y');

include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0"><?php echo $is_edit ? 'Edit' : 'Tambah'; ?> Penelitian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_penelitian.php">Kelola Penelitian</a></li>
                    <li class="breadcrumb-item active"><?php echo $is_edit ? 'Edit' : 'Tambah Baru'; ?></li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="card" data-aos="fade-up">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-journal-text me-2"></i>Form <?php echo $is_edit ? 'Edit' : 'Tambah'; ?> Penelitian
                        </h5>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" id="formPenelitian">
                            
                            <!-- Judul -->
                            <div class="mb-3">
                                <label class="form-label">Judul Penelitian <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" required maxlength="255"
                                       value="<?php echo $is_edit ? htmlspecialchars($penelitian['judul']) : (isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''); ?>"
                                       placeholder="Masukkan judul penelitian">
                            </div>
                            
                            <div class="row">
                                <!-- Tahun -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                    <select name="tahun" class="form-select" required>
                                        <?php 
                                        $selected_year = $is_edit ? $penelitian['tahun'] : (isset($_POST['tahun']) ? $_POST['tahun'] : $current_year);
                                        for ($year = $current_year; $year >= $current_year - 10; $year--): 
                                        ?>
                                        <option value="<?php echo $year; ?>" <?php echo $selected_year == $year ? 'selected' : ''; ?>>
                                            <?php echo $year; ?>
                                        </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <!-- Kategori -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="kategori" class="form-select">
                                        <?php 
                                        $selected_kategori = $is_edit ? $penelitian['kategori'] : (isset($_POST['kategori']) ? $_POST['kategori'] : '');
                                        $kategori_options = ['', 'Fundamental', 'Terapan', 'Pengembangan'];
                                        foreach ($kategori_options as $opt):
                                        ?>
                                        <option value="<?php echo $opt; ?>" <?php echo $selected_kategori == $opt ? 'selected' : ''; ?>>
                                            <?php echo $opt ? $opt : 'Pilih Kategori'; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Personil (Admin can select any personil) -->
                            <div class="mb-3">
                                <label class="form-label">Personil (Opsional)</label>
                                <select name="personil_id" class="form-select">
                                    <option value="">Pilih Personil (Opsional)</option>
                                    <?php 
                                    $selected_personil = $is_edit ? $penelitian['personil_id'] : (isset($_POST['personil_id']) ? $_POST['personil_id'] : '');
                                    foreach ($personil_list as $p): 
                                    ?>
                                    <option value="<?php echo $p['id']; ?>" <?php echo $selected_personil == $p['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($p['nama']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Pilih personil yang terkait dengan penelitian ini</small>
                            </div>
                            
                            <!-- Deskripsi -->
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Singkat <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required
                                          placeholder="Deskripsi singkat penelitian"><?php echo $is_edit ? htmlspecialchars($penelitian['deskripsi']) : (isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''); ?></textarea>
                                <small class="text-muted">Ringkasan penelitian secara singkat</small>
                            </div>
                            
                            <!-- Abstrak -->
                            <div class="mb-3">
                                <label class="form-label">Abstrak (Opsional)</label>
                                <textarea name="abstrak" id="abstrak" class="form-control" rows="6"
                                          placeholder="Abstrak lengkap penelitian (opsional)"><?php echo $is_edit ? htmlspecialchars($penelitian['abstrak']) : (isset($_POST['abstrak']) ? htmlspecialchars($_POST['abstrak']) : ''); ?></textarea>
                                <small class="text-muted">Abstrak lengkap penelitian</small>
                            </div>
                            
                            <!-- Link Publikasi -->
                            <div class="mb-3">
                                <label class="form-label">Link Publikasi (Opsional)</label>
                                <input type="url" name="link_publikasi" class="form-control"
                                       value="<?php echo $is_edit ? htmlspecialchars($penelitian['link_publikasi']) : (isset($_POST['link_publikasi']) ? htmlspecialchars($_POST['link_publikasi']) : ''); ?>"
                                       placeholder="https://journal.example.com/paper/123">
                                <small class="text-muted">Link ke publikasi jurnal/conference jika ada</small>
                            </div>
                            
                            <div class="row">
                                <!-- Gambar Cover -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gambar Cover</label>
                                    <?php if ($is_edit && $penelitian['gambar']): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo BASE_URL . '/uploads/penelitian/' . htmlspecialchars($penelitian['gambar']); ?>" 
                                             class="img-thumbnail" style="max-height: 150px;" alt="Current cover">
                                        <div class="form-text">Gambar saat ini</div>
                                    </div>
                                    <?php endif; ?>
                                    <input type="file" name="gambar" class="form-control" accept="image/*" id="gambarInput">
                                    <small class="text-muted">JPG, PNG. Max: 5MB. <?php echo $is_edit ? 'Biarkan kosong jika tidak ingin mengubah.' : ''; ?></small>
                                    <div id="previewContainer" class="mt-2" style="display: none;">
                                        <img id="previewImage" src="" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                </div>
                                
                                <!-- File PDF -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">File PDF</label>
                                    <?php if ($is_edit && $penelitian['file_pdf']): ?>
                                    <div class="mb-2">
                                        <a href="<?php echo BASE_URL . '/uploads/penelitian/' . htmlspecialchars($penelitian['file_pdf']); ?>" 
                                           class="btn btn-sm btn-outline-danger" target="_blank">
                                            <i class="bi bi-file-pdf me-1"></i>Lihat PDF Saat Ini
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    <input type="file" name="file_pdf" class="form-control" accept=".pdf">
                                    <small class="text-muted">PDF only. Max: 10MB. <?php echo $is_edit ? 'Biarkan kosong jika tidak ingin mengubah.' : ''; ?></small>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i><?php echo $is_edit ? 'Update' : 'Simpan'; ?>
                                </button>
                                <?php if (!$is_edit): ?>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                </button>
                                <?php endif; ?>
                                <a href="manage_penelitian.php" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                            </div>
                            
                        </form>
                        
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Info -->
            <div class="col-lg-3">
                <div class="card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Petunjuk</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Judul</strong> harus jelas dan deskriptif
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Tahun</strong> penelitian dilakukan
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Kategori</strong> jenis penelitian
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Personil</strong> yang terkait (opsional)
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>PDF</strong> untuk dokumentasi lengkap
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
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

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

// Form validation on submit
document.getElementById('formPenelitian').addEventListener('submit', function(e) {
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
});
</script>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>
