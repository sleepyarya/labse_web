<?php
// Admin Artikel Form View
require_once '../auth_check.php';
require_once '../../core/database.php';
require_once '../controllers/artikelController.php';

$controller = new ArtikelController();
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$is_edit = !empty($id);

if ($is_edit) {
    $result = $controller->edit($id);
    $page_title = 'Edit Artikel';
} else {
    $result = $controller->add();
    $page_title = 'Tambah Artikel';
}

$error = $result['error'] ?? '';
$artikel = $result['artikel'] ?? null;

include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0"><?php echo $page_title; ?></h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_artikel.php">Kelola Artikel</a></li>
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
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-file-plus me-2"></i>Form <?php echo $page_title; ?></h5>
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
                                       value="<?php echo $artikel ? htmlspecialchars($artikel['judul']) : (isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''); ?>"
                                       placeholder="Masukkan judul artikel yang menarik">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Penulis <span class="text-danger">*</span></label>
                                
                                <!-- Personil Dropdown -->
                                <div class="mb-2">
                                    <select name="personil_id" id="personilSelect" class="form-select">
                                        <option value="">-- Pilih Personil (Opsional) --</option>
                                        <?php 
                                        $personil_list = $controller->getPersonilList();
                                        $current_personil_id = $artikel['personil_id'] ?? '';
                                        foreach ($personil_list as $p): 
                                        ?>
                                            <option value="<?php echo $p['id']; ?>" <?php echo $current_personil_id == $p['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($p['nama']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Pilih personil jika artikel ini milik anggota lab tertentu.</small>
                                </div>

                                <!-- Manual Input -->
                                <div id="manualPenulisDiv" style="<?php echo !empty($current_personil_id) ? 'display:none;' : ''; ?>">
                                    <input type="text" name="penulis" id="penulisInput" class="form-control" 
                                           value="<?php echo $artikel ? htmlspecialchars($artikel['penulis']) : (isset($_POST['penulis']) ? htmlspecialchars($_POST['penulis']) : htmlspecialchars($_SESSION['admin_nama'])); ?>"
                                           placeholder="Nama penulis artikel">
                                    <small class="text-muted">Atau tulis nama penulis secara manual jika bukan personil.</small>
                                </div>
                            </div>

                            <script>
                            document.getElementById('personilSelect').addEventListener('change', function() {
                                const manualDiv = document.getElementById('manualPenulisDiv');
                                const penulisInput = document.getElementById('penulisInput');
                                
                                if (this.value) {
                                    manualDiv.style.display = 'none';
                                    // Optional: Clear manual input or set it to selected personil name
                                    // penulisInput.value = this.options[this.selectedIndex].text.trim();
                                } else {
                                    manualDiv.style.display = 'block';
                                }
                            });
                            </script>
                            
                            <div class="mb-3">
                                <label class="form-label">Isi Artikel <span class="text-danger">*</span></label>
                                <textarea name="isi" id="isiArtikel" class="form-control" rows="15" required 
                                          placeholder="Tulis isi artikel di sini..."><?php echo $artikel ? htmlspecialchars($artikel['isi']) : (isset($_POST['isi']) ? htmlspecialchars($_POST['isi']) : ''); ?></textarea>
                                <small class="text-muted">Gunakan editor untuk format teks (bold, italic, list, dll)</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Gambar Artikel</label>
                                <?php if ($artikel && $artikel['gambar']): ?>
                                <div class="mb-2">
                                    <img src="../../public/uploads/artikel/<?php echo htmlspecialchars($artikel['gambar']); ?>" 
                                         class="img-thumbnail" style="max-width: 200px;">
                                    <small class="d-block text-muted">Gambar saat ini</small>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="gambar" class="form-control" accept="image/*" id="gambarInput">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB. <?php echo $is_edit ? 'Kosongkan jika tidak ingin mengubah gambar.' : 'Gambar utama untuk artikel.'; ?></small>
                                <div id="previewContainer" class="mt-3" style="display: none;">
                                    <img id="previewImage" src="" class="img-thumbnail" style="max-width: 300px;">
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-2"></i><?php echo $is_edit ? 'Update' : 'Simpan'; ?> Artikel
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
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Tips Menulis</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Gunakan judul yang menarik</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Tulis konten yang informatif</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Gunakan gambar berkualitas</li>
                            <li class="mb-0"><i class="bi bi-check-circle text-success me-2"></i>Periksa ejaan sebelum publish</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- End Admin Content -->

<script>
// Image preview functionality
document.getElementById('gambarInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
});
</script>

<?php
pg_close($conn);
include '../includes/admin_footer.php';
?>
