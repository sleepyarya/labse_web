<?php
// Public view - Pengabdian detail
require_once __DIR__ . '/../../includes/config.php';
$page_title = 'Detail Pengabdian';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = intval($_GET['id']);

// Get pengabdian data
$query = "SELECT * FROM pengabdian WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));
$pengabdian = pg_fetch_assoc($result);

if (!$pengabdian) {
    header('Location: index.php');
    exit();
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Detail Kegiatan</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Informasi lengkap mengenai kegiatan pengabdian masyarakat</p>
    </div>
</div>

<!-- Detail Section -->
<section class="content-section">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="index.php">Pengabdian Masyarakat</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($pengabdian['judul']); ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8" data-aos="fade-up">
                <article class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        
                        <!-- Title -->
                        <h1 class="mb-3"><?php echo htmlspecialchars($pengabdian['judul']); ?></h1>
                        
                        <!-- Meta Info -->
                        <div class="mb-4">
                            <span class="badge bg-primary me-2">
                                <i class="bi bi-calendar3 me-1"></i><?php echo date('d M Y', strtotime($pengabdian['tanggal'])); ?>
                            </span>
                            <span class="badge bg-success me-2">
                                <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($pengabdian['lokasi']); ?>
                            </span>
                            <span class="badge bg-info">
                                <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($pengabdian['penyelenggara']); ?>
                            </span>
                        </div>
                        
                        <hr>
                        
                        <!-- Image -->
                        <?php if ($pengabdian['gambar']): ?>
                        <div class="mb-4">
                            <img src="<?php echo BASE_URL; ?>/public/uploads/pengabdian/<?php echo htmlspecialchars($pengabdian['gambar']); ?>" 
                                 class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($pengabdian['judul']); ?>"
                                 onerror="this.style.display='none'">
                        </div>
                        <?php endif; ?>
                        
                        <!-- Description -->
                        <div class="content-text">
                            <h5 class="mb-3">Tentang Kegiatan Ini</h5>
                            <p style="white-space: pre-line; line-height: 1.8;"><?php echo nl2br(htmlspecialchars($pengabdian['deskripsi'])); ?></p>
                        </div>
                        
                        <!-- Back Button -->
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Kegiatan
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
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Kegiatan</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr>
                                <th width="40%"><i class="bi bi-calendar3 me-2"></i>Tanggal:</th>
                                <td><?php echo date('d F Y', strtotime($pengabdian['tanggal'])); ?></td>
                            </tr>
                            <tr>
                                <th><i class="bi bi-geo-alt me-2"></i>Lokasi:</th>
                                <td><?php echo htmlspecialchars($pengabdian['lokasi']); ?></td>
                            </tr>
                            <tr>
                                <th><i class="bi bi-person me-2"></i>Penyelenggara:</th>
                                <td><?php echo htmlspecialchars($pengabdian['penyelenggara']); ?></td>
                            </tr>
                            <tr>
                                <th><i class="bi bi-clock me-2"></i>Dipublikasi:</th>
                                <td><?php echo date('d M Y', strtotime($pengabdian['created_at'])); ?></td>
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
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(BASE_URL . '/views/pengabdian/detail.php?id=' . $id); ?>" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-facebook me-2"></i>Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(BASE_URL . '/views/pengabdian/detail.php?id=' . $id); ?>&text=<?php echo urlencode($pengabdian['judul']); ?>" 
                               target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-twitter me-2"></i>Twitter
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($pengabdian['judul'] . ' - ' . BASE_URL . '/views/pengabdian/detail.php?id=' . $id); ?>" 
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

<!-- Recent Activities Section -->
<section class="content-section bg-light-section">
    <div class="container">
        <h4 class="mb-4">Kegiatan Lainnya</h4>
        <div class="row g-4">
            <?php
            // Get other recent pengabdian
            $recent_query = "SELECT * FROM pengabdian WHERE id != $1 ORDER BY tanggal DESC LIMIT 3";
            $recent_result = pg_query_params($conn, $recent_query, array($id));
            
            while ($recent = pg_fetch_assoc($recent_result)):
            ?>
            <div class="col-md-4" data-aos="fade-up">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <?php if ($recent['gambar']): ?>
                    <img src="<?php echo BASE_URL; ?>/public/uploads/pengabdian/<?php echo htmlspecialchars($recent['gambar']); ?>" 
                         class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?php echo htmlspecialchars($recent['judul']); ?>"
                         onerror="this.src='<?php echo BASE_URL; ?>/public/img/no-image.png'">
                    <?php endif; ?>
                    <div class="card-body">
                        <h6 class="card-title"><?php echo htmlspecialchars($recent['judul']); ?></h6>
                        <p class="card-text small text-muted">
                            <i class="bi bi-calendar3 me-1"></i><?php echo date('d M Y', strtotime($recent['tanggal'])); ?>
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
