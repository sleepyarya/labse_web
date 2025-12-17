<?php
// Simplified - edit dan view members, lalu update sidebar
// Karena pattern sama, saya akan notify user setelah semua selesai
// Member sidebar update
require_once 'auth_check.php';
require_once '../includes/config.php';
require_once 'controllers/produkController.php';

$controller = new MemberProdukController();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: my_produk.php?error=invalid');
    exit();
}

$id = intval($_GET['id']);
$produk = $controller->getById($id);

if (!$produk) {
    header('Location: my_produk.php?error=notfound');
    exit();
}

include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<div class="member-content">
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Detail Produk</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_produk.php">Produk Saya</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body p-4">
                        <h2 class="mb-3"><?php echo htmlspecialchars($produk['nama_produk']); ?></h2>
                        
                        <div class="mb-4">
                            <span class="badge bg-primary me-2">
                                <i class="bi bi-calendar3 me-1"></i><?php echo $produk['tahun']; ?>
                            </span>
                            <?php if ($produk['kategori']): ?>
                            <span class="badge <?php echo $produk['kategori'] == 'Hardware' ? 'bg-danger' : 'bg-success'; ?>">
                                <i class="bi bi-tag me-1"></i><?php echo $produk['kategori']; ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                        
                        <?php if ($produk['gambar']): ?>
                        <div class="mb-4">
                            <img src="<?php echo BASE_URL; ?>/uploads/produk/<?php echo htmlspecialchars($produk['gambar']); ?>" 
                                 class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                        </div>
                        <?php endif; ?>
                        
                        <h5 class="mb-3">Deskripsi</h5>
                        <p style="white-space: pre-line;"><?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>
                        
                        <?php if ($produk['teknologi']): ?>
                        <div class="alert alert-light border mt-4">
                            <h6><i class="bi bi-gear me-2"></i>Teknologi</h6>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($produk['teknologi'])); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <a href="my_produk.php" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <a href="edit_produk.php?id=<?php echo $produk['id']; ?>" class="btn btn-warning">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Link Terkait</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($produk['link_demo']): ?>
                        <a href="<?php echo htmlspecialchars($produk['link_demo']); ?>" 
                           class="btn btn-success w-100 mb-2" target="_blank">
                            <i class="bi bi-play-circle me-2"></i>Demo
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($produk['link_repository']): ?>
                        <a href="<?php echo htmlspecialchars($produk['link_repository']); ?>" 
                           class="btn btn-dark w-100" target="_blank">
                            <i class="bi bi-github me-2"></i>Repository
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!$produk['link_demo'] && !$produk['link_repository']): ?>
                        <p class="text-muted mb-0">Tidak ada link tersedia</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
pg_close($conn);
include 'includes/member_footer.php';
?>
