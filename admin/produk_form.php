<?php
require_once 'auth_check.php';
require_once 'controllers/produkController.php';
require_once '../includes/config.php';

$controller = new ProdukController();
$is_edit = isset($_GET['id']) && !empty($_GET['id']);
$produk = null;
$error = '';

// Get personil list for dropdown
// Get personil list for dropdown
$personil_list = $controller->getAllPersonil();
// Get kategori list for dropdown
$kategori_list = $controller->getKategoriList();

if ($is_edit) {
    $id = intval($_GET['id']);
    $result = $controller->edit($id);
    $error = $result['error'];
    $produk = $result['produk'];
    
    if (!$produk) {
        header('Location: manage_produk.php?error=notfound');
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

<div class="admin-content">
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0"><?php echo $is_edit ? 'Edit' : 'Tambah'; ?> Produk</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_produk.php">Kelola Produk</a></li>
                    <li class="breadcrumb-item active"><?php echo $is_edit ? 'Edit' : 'Tambah Baru'; ?></li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-<?php echo $is_edit ? 'pencil' : 'plus-circle'; ?> me-2"></i>
                            Form <?php echo $is_edit ? 'Edit' : 'Tambah'; ?> Produk
                        </h5>
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
                                <input type="text" name="nama_produk" class="form-control" required
                                       value="<?php echo $is_edit ? htmlspecialchars($produk['nama_produk']) : (isset($_POST['nama_produk']) ? htmlspecialchars($_POST['nama_produk']) : ''); ?>">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                    <select name="tahun" class="form-select" required>
                                        <?php 
                                        $selected_year = $is_edit ? $produk['tahun'] : (isset($_POST['tahun']) ? $_POST['tahun'] : $current_year);
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
                                    <select name="kategori_id" class="form-select">
                                        <option value="">Pilih Kategori</option>
                                        <?php 
                                        $selected_kategori_id = $is_edit ? ($produk['kategori_id'] ?? '') : (isset($_POST['kategori_id']) ? $_POST['kategori_id'] : '');
                                        foreach ($kategori_list as $kat):
                                        ?>
                                        <option value="<?php echo $kat['id']; ?>" <?php echo $selected_kategori_id == $kat['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted"><a href="manage_kategori_produk.php" target="_blank">Kelola Kategori</a></small>
                                </div>
                            </div>
                            
                            <!-- Personil (Admin can select any) -->
                            <div class="mb-3">
                                <label class="form-label">Personil (Opsional)</label>
                                <select name="personil_id" class="form-select">
                                    <option value="">Tidak Terkait Personil</option>
                                    <?php 
                                    $selected_personil = $is_edit ? $produk['personil_id'] : (isset($_POST['personil_id']) ? $_POST['personil_id'] : '');
                                    foreach ($personil_list as $p): 
                                    ?>
                                    <option value="<?php echo $p['id']; ?>" <?php echo $selected_personil == $p['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($p['nama']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" class="form-control" rows="5" required><?php echo $is_edit ? htmlspecialchars($produk['deskripsi']) : (isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Teknologi yang Digunakan</label>
                                <textarea name="teknologi" class="form-control" rows="3"
                                          placeholder="Contoh: PHP, Laravel, MySQL"><?php echo $is_edit ? htmlspecialchars($produk['teknologi']) : (isset($_POST['teknologi']) ? htmlspecialchars($_POST['teknologi']) : ''); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Link Demo</label>
                                    <input type="url" name="link_demo" class="form-control"
                                           value="<?php echo $is_edit ? htmlspecialchars($produk['link_demo']) : (isset($_POST['link_demo']) ? htmlspecialchars($_POST['link_demo']) : ''); ?>"
                                           placeholder="https://demo.example.com">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Link Repository</label>
                                    <input type="url" name="link_repository" class="form-control"
                                           value="<?php echo $is_edit ? htmlspecialchars($produk['link_repository']) : (isset($_POST['link_repository']) ? htmlspecialchars($_POST['link_repository']) : ''); ?>"
                                           placeholder="https://github.com/user/repo">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Gambar Produk</label>
                                <?php if ($is_edit && $produk['gambar']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo BASE_URL; ?>/public/uploads/produk/<?php echo htmlspecialchars($produk['gambar']); ?>" 
                                         class="img-thumbnail" style="max-height: 150px;">
                                    <div class="form-text">Gambar saat ini</div>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="gambar" class="form-control" accept="image/*" id="gambarInput">
                                <small class="text-muted">JPG, PNG. Max: 5MB. <?php echo $is_edit ? 'Biarkan kosong jika tidak ingin mengubah.' : ''; ?></small>
                                <div id="previewContainer" class="mt-2" style="display: none;">
                                    <img id="previewImage" src="" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i><?php echo $is_edit ? 'Update' : 'Simpan'; ?>
                                </button>
                                <?php if (!$is_edit): ?>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                </button>
                                <?php endif; ?>
                                <a href="manage_produk.php" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                            </div>
                            
                        </form>
                        
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Petunjuk</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Nama & deskripsi wajib diisi
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Kategori: Hardware/Software
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Link demo/repository opsional
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Personil dapat dipilih atau kosong
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
include 'includes/admin_footer.php';
?>
