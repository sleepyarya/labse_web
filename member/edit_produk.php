<?php
require_once 'auth_check.php';
require_once 'controllers/produkController.php';
require_once '../includes/config.php';

$controller = new MemberProdukController();
$kategori_list = $controller->getKategoriList();


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: my_produk.php?error=invalid');
    exit();
}

$id = intval($_GET['id']);
$result = $controller->edit($id);
$error = $result['error'];
$produk = $result['produk'];

if (!$produk) {
    header('Location: my_produk.php?error=notfound');
    exit();
}

$current_year = date('Y');

include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<div class="member-content">
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Edit Produk</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_produk.php">Produk Saya</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Produk</h5>
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
                                       value="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                    <select name="tahun" class="form-select" required>
                                        <?php for ($year = $current_year; $year >= $current_year - 10; $year--): ?>
                                        <option value="<?php echo $year; ?>" <?php echo $produk['tahun'] == $year ? 'selected' : ''; ?>>
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
                                        $selected_kategori_id = $produk['kategori_id'];
                                        foreach ($kategori_list as $kat):
                                        ?>
                                        <option value="<?php echo $kat['id']; ?>" <?php echo $selected_kategori_id == $kat['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" class="form-control" rows="5" required><?php echo htmlspecialchars($produk['deskripsi']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Teknologi</label>
                                <textarea name="teknologi" class="form-control" rows="3"><?php echo htmlspecialchars($produk['teknologi']); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Link Demo</label>
                                    <input type="url" name="link_demo" class="form-control"
                                           value="<?php echo htmlspecialchars($produk['link_demo']); ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Link Repository</label>
                                    <input type="url" name="link_repository" class="form-control"
                                           value="<?php echo htmlspecialchars($produk['link_repository']); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Gambar Produk</label>
                                <?php if ($produk['gambar']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo BASE_URL; ?>/uploads/produk/<?php echo htmlspecialchars($produk['gambar']); ?>" 
                                         class="img-thumbnail" style="max-height: 150px;">
                                    <div class="form-text">Gambar saat ini. Upload baru untuk mengubah.</div>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="gambar" class="form-control" accept="image/*" id="gambarInput">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah</small>
                                <div id="previewContainer" class="mt-2" style="display: none;">
                                    <img id="previewImage" src="" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save me-2"></i>Update
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
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Info</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-2"><strong>Dibuat:</strong><br><?php echo date('d M Y', strtotime($produk['created_at'])); ?></p>
                        <?php if ($produk['updated_at'] != $produk['created_at']): ?>
                        <p class="small mb-0"><strong>Diupdate:</strong><br><?php echo date('d M Y H:i', strtotime($produk['updated_at'])); ?></p>
                        <?php endif; ?>
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
