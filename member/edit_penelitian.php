<?php
require_once 'auth_check.php';
require_once 'controllers/penelitianController.php';
require_once '../includes/config.php';

// Validate ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: my_penelitian.php?error=invalid');
    exit();
}

$id = intval($_GET['id']);
$controller = new MemberPenelitianController();
$result = $controller->edit($id);

// If unauthorized or not found
if (!empty($result['error']) && $result['penelitian'] === null) {
    header('Location: my_penelitian.php?error=unauthorized');
    exit();
}

$penelitian = $result['penelitian'];
$page_title = 'Edit Penelitian';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';

$current_year = date('Y');
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Edit Penelitian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_penelitian.php">Penelitian Saya</a></li>
                    <li class="breadcrumb-item active">Edit Penelitian</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Form Card -->
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-pencil me-2"></i>Form Edit Penelitian
            </h5>
        </div>
        <div class="card-body p-4">
            
            <?php if (!empty($result['error'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo $result['error']; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                
                <!-- Judul -->
                <div class="mb-3">
                    <label class="form-label">Judul Penelitian <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" required maxlength="255"
                           value="<?php echo htmlspecialchars($penelitian['judul']); ?>">
                </div>
                
                <div class="row">
                    <!-- Tahun -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                        <select name="tahun" class="form-select" required>
                            <?php for ($year = $current_year; $year >= $current_year - 10; $year--): ?>
                            <option value="<?php echo $year; ?>" <?php echo $penelitian['tahun'] == $year ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <!-- Kategori -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select">
                            <option value="">Pilih Kategori</option>
                            <option value="Fundamental" <?php echo $penelitian['kategori'] == 'Fundamental' ? 'selected' : ''; ?>>Fundamental</option>
                            <option value="Terapan" <?php echo $penelitian['kategori'] == 'Terapan' ? 'selected' : ''; ?>>Terapan</option>
                            <option value="Pengembangan" <?php echo $penelitian['kategori'] == 'Pengembangan' ? 'selected' : ''; ?>>Pengembangan</option>
                        </select>
                    </div>
                </div>
                
                <!-- Deskripsi -->
                <div class="mb-3">
                    <label class="form-label">Deskripsi Singkat <span class="text-danger">*</span></label>
                    <textarea name="deskripsi" class="form-control" rows="4" required><?php echo htmlspecialchars($penelitian['deskripsi']); ?></textarea>
                </div>
                
                <!-- Abstrak -->
                <div class="mb-3">
                    <label class="form-label">Abstrak (Opsional)</label>
                    <textarea name="abstrak" class="form-control" rows="6"><?php echo htmlspecialchars($penelitian['abstrak'] ?? ''); ?></textarea>
                </div>
                
                <!-- Link Publikasi -->
                <div class="mb-3">
                    <label class="form-label">Link Publikasi (Opsional)</label>
                    <input type="url" name="link_publikasi" class="form-control"
                           value="<?php echo htmlspecialchars($penelitian['link_publikasi'] ?? ''); ?>">
                </div>
                
                <div class="row">
                    <!-- Gambar Cover -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gambar Cover</label>
                        <?php if ($penelitian['gambar']): ?>
                        <div class="mb-2">
                            <img src="<?php echo BASE_URL; ?>/uploads/penelitian/<?php echo htmlspecialchars($penelitian['gambar']); ?>" 
                                 class="img-thumbnail" style="max-height: 150px;" alt="Current cover">
                            <div class="form-text">Gambar saat ini</div>
                        </div>
                        <?php endif; ?>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <div class="form-text">Biarkan kosong jika tidak ingin mengubah. Max: 5MB</div>
                    </div>
                    
                    <!-- File PDF -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">File PDF</label>
                        <?php if ($penelitian['file_pdf']): ?>
                        <div class="mb-2">
                            <a href="<?php echo BASE_URL; ?>/uploads/penelitian/<?php echo htmlspecialchars($penelitian['file_pdf']); ?>" 
                               class="btn btn-sm btn-outline-danger" target="_blank">
                                <i class="bi bi-file-pdf me-1"></i>Lihat PDF Saat Ini
                            </a>
                        </div>
                        <?php endif; ?>
                        <input type="file" name="file_pdf" class="form-control" accept=".pdf">
                        <div class="form-text">Biarkan kosong jika tidak ingin mengubah. Max: 10MB</div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                    </button>
                    <a href="my_penelitian.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                </div>
                
            </form>
            
        </div>
    </div>
    
</div>
<!-- End Member Content -->

<?php
pg_close($conn);
include 'includes/member_footer.php';
?>
