<?php
// Public view - Penelitian detail
$page_title = 'Detail Penelitian';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = intval($_GET['id']);

// Get penelitian data with personil info
$query = "SELECT hp.*, p.nama as personil_nama, p.email as personil_email 
          FROM hasil_penelitian hp
          LEFT JOIN personil p ON hp.personil_id = p.id
          WHERE hp.id = $1";
$result = pg_query_params($conn, $query, array($id));
$penelitian = pg_fetch_assoc($result);

if (!$penelitian) {
    header('Location: index.php');
    exit();
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Detail Penelitian</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Informasi lengkap mengenai hasil penelitian</p>
    </div>
</div>

<!-- Detail Section -->
<section class="content-section">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="index.php">Hasil Penelitian</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($penelitian['judul']); ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8" data-aos="fade-up">
                <article class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        
                        <!-- Title -->
                        <h1 class="mb-3"><?php echo htmlspecialchars($penelitian['judul']); ?></h1>
                        
                        <!-- Meta Info -->
                        <div class="mb-4">
                            <span class="badge bg-primary me-2">
                                <i class="bi bi-calendar3 me-1"></i><?php echo $penelitian['tahun']; ?>
                            </span>
                            <?php if ($penelitian['kategori']): ?>
                            <span class="badge bg-info me-2">
                                <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($penelitian['kategori']); ?>
                            </span>
                            <?php endif; ?>
                            <?php if ($penelitian['personil_nama']): ?>
                            <span class="badge bg-success">
                                <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($penelitian['personil_nama']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                        
                        <!-- Image -->
                        <?php if ($penelitian['gambar']): ?>
                        <div class="mb-4">
                            <img src="<?php echo BASE_URL; ?>/uploads/penelitian/<?php echo htmlspecialchars($penelitian['gambar']); ?>" 
                                 class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($penelitian['judul']); ?>"
                                 onerror="this.style.display='none'">
                        </div>
                        <?php endif; ?>
                        
                        <!-- Abstrak -->
                        <?php if ($penelitian['abstrak']): ?>
                        <div class="mb-4">
                            <div class="alert alert-light border">
                                <h5 class="mb-2"><i class="bi bi-file-text me-2"></i>Abstrak</h5>
                                <p style="white-space: pre-line; line-height: 1.8; margin-bottom: 0;">
                                    <?php echo nl2br(htmlspecialchars($penelitian['abstrak'])); ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Description -->
                        <div class="content-text">
                            <h5 class="mb-3">Deskripsi Penelitian</h5>
                            <p style="white-space: pre-line; line-height: 1.8;"><?php echo nl2br(htmlspecialchars($penelitian['deskripsi'])); ?></p>
                        </div>
                        
                        <!-- Download & Links -->
                        <div class="mt-4">
                            <h5 class="mb-3">Akses Dokumen</h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php if ($penelitian['file_pdf']): ?>
                                <a href="<?php echo BASE_URL; ?>/uploads/penelitian/<?php echo htmlspecialchars($penelitian['file_pdf']); ?>" 
                                   class="btn btn-danger" target="_blank">
                                    <i class="bi bi-file-pdf me-2"></i>Download PDF
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($penelitian['link_publikasi']): ?>
                                <a href="<?php echo htmlspecialchars($penelitian['link_publikasi']); ?>" 
                                   class="btn btn-primary" target="_blank" rel="noopener">
                                    <i class="bi bi-link-45deg me-2"></i>Lihat Publikasi
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Back Button -->
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Penelitian
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
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Penelitian</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr>
                                <th width="40%"><i class="bi bi-calendar3 me-2"></i>Tahun:</th>
                                <td><?php echo $penelitian['tahun']; ?></td>
                            </tr>
                            <?php if ($penelitian['kategori']): ?>
                            <tr>
                                <th><i class="bi bi-tag me-2"></i>Kategori:</th>
                                <td><?php echo htmlspecialchars($penelitian['kategori']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($penelitian['personil_nama']): ?>
                            <tr>
                                <th><i class="bi bi-person me-2"></i>Peneliti:</th>
                                <td><?php echo htmlspecialchars($penelitian['personil_nama']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th><i class="bi bi-clock me-2"></i>Dipublikasi:</th>
                                <td><?php echo date('d M Y', strtotime($penelitian['created_at'])); ?></td>
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
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(BASE_URL . '/views/penelitian/detail.php?id=' . $id); ?>" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-facebook me-2"></i>Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(BASE_URL . '/views/penelitian/detail.php?id=' . $id); ?>&text=<?php echo urlencode($penelitian['judul']); ?>" 
                               target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-twitter me-2"></i>Twitter
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($penelitian['judul'] . ' - ' . BASE_URL . '/views/penelitian/detail.php?id=' . $id); ?>" 
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

<!-- Recent Research Section -->
<section class="content-section bg-light-section">
    <div class="container">
        <h4 class="mb-4">Penelitian Lainnya</h4>
        <div class="row g-4">
            <?php
            // Get other recent penelitian
            $recent_query = "SELECT * FROM hasil_penelitian WHERE id != $1 ORDER BY tahun DESC, created_at DESC LIMIT 3";
            $recent_result = pg_query_params($conn, $recent_query, array($id));
            
            while ($recent = pg_fetch_assoc($recent_result)):
            ?>
            <div class="col-md-4" data-aos="fade-up">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <?php if ($recent['gambar']): ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/penelitian/<?php echo htmlspecialchars($recent['gambar']); ?>" 
                         class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?php echo htmlspecialchars($recent['judul']); ?>"
                         onerror="this.src='<?php echo BASE_URL; ?>/assets/img/no-image.png'">
                    <?php else: ?>
                    <div class="bg-gradient d-flex align-items-center justify-content-center" style="height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-journal-text text-white" style="font-size: 2rem;"></i>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h6 class="card-title"><?php echo htmlspecialchars($recent['judul']); ?></h6>
                        <p class="card-text small text-muted">
                            <i class="bi bi-calendar3 me-1"></i><?php echo $recent['tahun']; ?>
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
