<?php
require_once 'auth_check.php';
require_once 'controllers/penelitianController.php';
require_once '../includes/config.php';

$controller = new MemberPenelitianController();
$result = $controller->add();

$page_title = 'Tambah Penelitian';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';

$current_year = date('Y');
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Tambah Penelitian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_penelitian.php">Penelitian Saya</a></li>
                    <li class="breadcrumb-item active">Tambah Penelitian</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Form Card -->
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-plus-circle me-2"></i>Form Tambah Penelitian
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
                           placeholder="Masukkan judul penelitian"
                           value="<?php echo isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''; ?>">
                </div>
                
                <div class="row">
                    <!-- Tahun -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                        <select name="tahun" class="form-select" required>
                            <?php for ($year = $current_year; $year >= $current_year - 10; $year--): ?>
                            <option value="<?php echo $year; ?>" <?php echo (isset($_POST['tahun']) && $_POST['tahun'] == $year) ? 'selected' : ''; ?>>
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
                            <option value="Fundamental" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] == 'Fundamental') ? 'selected' : ''; ?>>Fundamental</option>
                            <option value="Terapan" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] == 'Terapan') ? 'selected' : ''; ?>>Terapan</option>
                            <option value="Pengembangan" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] == 'Pengembangan') ? 'selected' : ''; ?>>Pengembangan</option>
                        </select>
                    </div>
                </div>
                
                <!-- Deskripsi -->
                <div class="mb-3">
                    <label class="form-label">Deskripsi Singkat <span class="text-danger">*</span></label>
                    <textarea name="deskripsi" class="form-control" rows="4" required
                              placeholder="Deskripsi singkat penelitian"><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                    <div class="form-text">Jelaskan ringkasan  penelitian secara singkat</div>
                </div>
                
                <!-- Abstrak -->
                <div class="mb-3">
                    <label class="form-label">Abstrak (Opsional)</label>
                    <textarea name="abstrak" class="form-control" rows="6"
                              placeholder="Abstrak lengkap penelitian"><?php echo isset($_POST['abstrak']) ? htmlspecialchars($_POST['abstrak']) : ''; ?></textarea>
                    <div class="form-text">Abstrak lengkap penelitian jika ada</div>
                </div>
                
                <!-- Link Publikasi -->
                <div class="mb-3">
                    <label class="form-label">Link Publikasi (Opsional)</label>
                    <input type="url" name="link_publikasi" class="form-control"
                           placeholder="https://journal.example.com/paper/123"
                           value="<?php echo isset($_POST['link_publikasi']) ? htmlspecialchars($_POST['link_publikasi']) : ''; ?>">
                    <div class="form-text">Link ke publikasi jurnal/conference jika ada</div>
                </div>
                
                <div class="row">
                    <!-- Gambar Cover -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gambar Cover (Opsional)</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <div class="form-text">Format: JPG, PNG. Max: 5MB</div>
                    </div>
                    
                    <!-- File PDF -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">File PDF (Opsional)</label>
                        <input type="file" name="file_pdf" class="form-control" accept=".pdf">
                        <div class="form-text">Format: PDF. Max: 10MB</div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan Penelitian
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
