<?php
require_once 'auth_check.php';
require_once 'controllers/produkController.php';
require_once '../includes/config.php';

$controller = new MemberProdukController();
$result = $controller->add();
$error = $result['error'];

$current_year = date('Y');

include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<div class="member-content">
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Tambah Produk</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_produk.php">Produk Saya</a></li>
                    <li class="breadcrumb-item active">Tambah Baru</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="card" data-aos="fade-up">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Form Tambah Produk</h5>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="nama_produk" class="form-control" required maxlength="255"
                                       value="<?php echo isset($_POST['nama_produk']) ? htmlspecialchars($_POST['nama_produk']) : ''; ?>"
                                       placeholder="Contoh: Sistem Informasi Akademik">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                    <select name="tahun" class="form-select" required>
                                        <?php 
                                        $selected_year = isset($_POST['tahun']) ? $_POST['tahun'] : $current_year;
                                        for ($year = $current_year; $year >= $current_year - 10; $year--): 
                                        ?>
                                        <option value="<?php echo $year; ?>" <?php echo $selected_year == $year ? 'selected' : ''; ?>>
                                            <?php echo $year; ?>
                                        </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="kategori" class="form-select">
                                        <?php 
                                        $selected_kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';
                                        $kategori_options = ['', 'Hardware', 'Software'];
                                        foreach ($kategori_options as $opt):
                                        ?>
                                        <option value="<?php echo $opt; ?>" <?php echo $selected_kategori == $opt ? 'selected' : ''; ?>>
                                            <?php echo $opt ? $opt : 'Pilih Kategori'; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" class="form-control" rows="5" required
                                          placeholder="Deskripsikan produk Anda..."><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Teknologi yang Digunakan</label>
                                <textarea name="teknologi" class="form-control" rows="3"
                                          placeholder="Contoh: PHP, Laravel, MySQL, Bootstrap"><?php echo isset($_POST['teknologi']) ? htmlspecialchars($_POST['teknologi']) : ''; ?></textarea>
                                <small class="text-muted">Stack teknologi yang digunakan dalam produk ini</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Link Demo</label>
                                    <input type="url" name="link_demo" class="form-control"
                                           value="<?php echo isset($_POST['link_demo']) ? htmlspecialchars($_POST['link_demo']) : ''; ?>"
                                           placeholder="https://demo.example.com">
                                    <small class="text-muted">Link demo produk (opsional)</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Link Repository</label>
                                    <input type="url" name="link_repository" class="form-control"
                                           value="<?php echo isset($_POST['link_repository']) ? htmlspecialchars($_POST['link_repository']) : ''; ?>"
                                           placeholder="https://github.com/username/repo">
                                    <small class="text-muted">Link GitHub/GitLab (opsional)</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Gambar Produk</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*" id="gambarInput">
                                <small class="text-muted">JPG, PNG, GIF. Max: 5MB</small>
                                <div id="previewContainer" class="mt-2" style="display: none;">
                                    <img id="previewImage" src="" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Simpan
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                </button>
                                <a href="my_produk.php" class="btn btn-outline-danger">
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
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Nama</strong> harus jelas
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Deskripsi</strong> lengkap
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Kategori: Hardware/Software
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Link demo opsional
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
</script>

<?php
pg_close($conn);
include 'includes/member_footer.php';
?>
