<?php
// Public view - Produk detail
$page_title = 'Detail Produk';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = intval($_GET['id']);

// Get produk data with personil info
$query = "SELECT p.*, pr.nama as personil_nama, pr.email as personil_email 
          FROM produk p
          LEFT JOIN personil pr ON p.personil_id = pr.id
          WHERE p.id = $1";
$result = pg_query_params($conn, $query, array($id));
$produk = pg_fetch_assoc($result);

if (!$produk) {
    header('Location: index.php');
    exit();
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Detail Produk</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Informasi lengkap mengenai produk</p>
    </div>
</div>

<!-- Detail Section -->
<section class="content-section">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="index.php">Produk</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($produk['nama_produk']); ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8" data-aos="fade-up">
                <article class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        
                        <!-- Title -->
                        <h1 class="mb-3"><?php echo htmlspecialchars($produk['nama_produk']); ?></h1>
                        
                        <!-- Meta Info -->
                        <div class="mb-4">
                            <span class="badge bg-primary me-2">
                                <i class="bi bi-calendar3 me-1"></i><?php echo $produk['tahun']; ?>
                            </span>
                            <?php if ($produk['kategori']): ?>
                            <span class="badge <?php echo $produk['kategori'] == 'Hardware' ? 'bg-danger' : 'bg-success'; ?> me-2">
                                <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($produk['kategori']); ?>
                            </span>
                            <?php endif; ?>
                            <?php if ($produk['personil_nama']): ?>
                            <span class="badge bg-info">
                                <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($produk['personil_nama']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                        
                        <!-- Image -->
                        <?php if ($produk['gambar']): ?>
                        <div class="mb-4">
                            <img src="<?php echo BASE_URL; ?>/uploads/produk/<?php echo htmlspecialchars($produk['gambar']); ?>" 
                                 class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>"
                                 onerror="this.src='https://picsum.photos/seed/<?php echo $produk['id']; ?>/800/600'">
                        </div>
                        <?php endif; ?>
                        
                        <!-- Teknologi -->
                        <?php if ($produk['teknologi']): ?>
                        <div class="mb-4">
                            <div class="alert alert-light border">
                                <h5 class="mb-2"><i class="bi bi-gear me-2"></i>Teknologi yang Digunakan</h5>
                                <p style="margin-bottom: 0;">
                                    <?php echo nl2br(htmlspecialchars($produk['teknologi'])); ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Description -->
                        <div class="mb-4">
                            <h5 class="mb-3">Deskripsi Produk</h5>
                            <p style="white-space: pre-line; line-height: 1.8;">
                                <?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?>
                            </p>
                        </div>
                        
                        <!-- Links -->
                        <div class="mb-4">
                            <h5 class="mb-3">Link Terkait</h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php if ($produk['link_demo']): ?>
                                <a href="<?php echo htmlspecialchars($produk['link_demo']); ?>" 
                                   class="btn btn-success" target="_blank" rel="noopener">
                                    <i class="bi bi-play-circle me-2"></i>Demo Produk
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($produk['link_repository']): ?>
                                <a href="<?php echo htmlspecialchars($produk['link_repository']); ?>" 
                                   class="btn btn-dark" target="_blank" rel="noopener">
                                    <i class="bi bi-github me-2"></i>Repository
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!$produk['link_demo'] && !$produk['link_repository']): ?>
                                <p class="text-muted mb-0">Tidak ada link demo atau repository</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Back Button -->
                        <hr>
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Produk
                            </a>
                        </div>
                        
                    </div>
                </article>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                
                <!-- Info Card -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Produk</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr>
                                <th width="40%"><i class="bi bi-calendar3 me-2"></i>Tahun:</th>
                                <td><?php echo $produk['tahun']; ?></td>
                            </tr>
                            <?php if ($produk['kategori']): ?>
                            <tr>
                                <th><i class="bi bi-tag me-2"></i>Kategori:</th>
                                <td><span class="badge <?php echo $produk['kategori'] == 'Hardware' ? 'bg-danger' : 'bg-success'; ?>"><?php echo htmlspecialchars($produk['kategori']); ?></span></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($produk['personil_nama']): ?>
                            <tr>
                                <th><i class="bi bi-person me-2"></i>Pembuat:</th>
                                <td><?php echo htmlspecialchars($produk['personil_nama']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th><i class="bi bi-clock me-2"></i>Dipublikasi:</th>
                                <td><?php echo date('d M Y', strtotime($produk['created_at'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Share Card -->
                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-share me-2"></i>Bagikan</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(BASE_URL . '/views/produk/detail.php?id=' . $id); ?>" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-facebook me-2"></i>Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(BASE_URL . '/views/produk/detail.php?id=' . $id); ?>&text=<?php echo urlencode($produk['nama_produk']); ?>" 
                               target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-twitter me-2"></i>Twitter
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($produk['nama_produk'] . ' - ' . BASE_URL . '/views/produk/detail.php?id=' . $id); ?>" 
                               target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-whatsapp me-2"></i>WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<!-- Recent Products Section -->
<section class="content-section bg-light-section">
    <div class="container">
        <h4 class="mb-4">Produk Lainnya</h4>
        <div class="row g-4">
            <?php
            // Get other recent produk
            $recent_query = "SELECT * FROM produk WHERE id != $1 ORDER BY tahun DESC, created_at DESC LIMIT 3";
            $recent_result = pg_query_params($conn, $recent_query, array($id));
            
            while ($recent = pg_fetch_assoc($recent_result)):
            ?>
            <div class="col-md-4" data-aos="fade-up">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <?php if ($recent['gambar']): ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/produk/<?php echo htmlspecialchars($recent['gambar']); ?>" 
                         class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?php echo htmlspecialchars($recent['nama_produk']); ?>"
                         onerror="this.src='https://picsum.photos/seed/<?php echo $recent['id']; ?>/600/400'">
                    <?php else: ?>
                    <!-- Auto Generated Image -->
                     <img src="https://picsum.photos/seed/<?php echo $recent['id']; ?>/600/400" 
                         class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?php echo htmlspecialchars($recent['nama_produk']); ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h6 class="card-title"><?php echo htmlspecialchars($recent['nama_produk']); ?></h6>
                        <p class="card-text small text-muted">
                            <i class="bi bi-calendar3 me-1"></i><?php echo $recent['tahun']; ?>
                            <?php if ($recent['kategori']): ?>
                            <span class="badge <?php echo $recent['kategori'] == 'Hardware' ? 'bg-danger' : 'bg-success'; ?> ms-2"><?php echo $recent['kategori']; ?></span>
                            <?php endif; ?>
                        </p>
                        <a href="detail.php?id=<?php echo $recent['id']; ?>" class="btn btn-sm btn-outline-primary">
                            Baca Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<style>
.content-text {
    font-size: 1.1rem;
    color: #333;
}

.hover-card {
    transition: transform 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
}
</style>

<?php
pg_close($conn);
require_once __DIR__ . '/../../includes/footer.php';
?>
