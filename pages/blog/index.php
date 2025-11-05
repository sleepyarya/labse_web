<?php
require_once '../../includes/config.php';
$page_title = 'Blog & Artikel';
include '../../includes/header.php';
include '../../includes/navbar.php';

// Get all articles from database
$query = "SELECT * FROM artikel ORDER BY created_at DESC";
$result = pg_query($conn, $query);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Blog & Artikel</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Temukan artikel, penelitian, dan insight terbaru tentang software engineering</p>
    </div>
</div>

<!-- Blog Grid -->
<section class="content-section">
    <div class="container">
        <div class="row g-4">
            <?php
            $delay = 0;
            while ($row = pg_fetch_assoc($result)) {
                $delay += 100;
                // Use uploaded image if exists, otherwise use placeholder
                if (!empty($row['gambar']) && file_exists('../../uploads/artikel/' . $row['gambar'])) {
                    $img_url = BASE_URL . '/uploads/artikel/' . $row['gambar'];
                } else {
                    $img_url = "https://picsum.photos/seed/" . $row['id'] . "/600/400";
                }
                ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <div class="card h-100">
                        <img src="<?php echo $img_url; ?>" class="card-img-top blog-card-img" alt="<?php echo htmlspecialchars($row['judul']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['judul']); ?></h5>
                            <div class="mb-3">
                                <span class="badge bg-primary"><?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($row['penulis']); ?>
                            </p>
                            <p class="card-text text-muted mb-4"><?php echo substr(htmlspecialchars($row['isi']), 0, 150); ?>...</p>
                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary mt-auto">
                                Baca Selengkapnya <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        
        <?php if (pg_num_rows($result) == 0): ?>
        <div class="text-center py-5" data-aos="fade-up">
            <i class="bi bi-file-earmark-text text-muted" style="font-size: 5rem;"></i>
            <h3 class="mt-3">Belum ada artikel</h3>
            <p class="text-muted">Artikel akan segera ditambahkan. Silakan kembali lagi nanti.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Newsletter Section -->
<section class="content-section bg-light-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center" data-aos="zoom-in">
                    <h2 class="mb-4">Berlangganan Newsletter</h2>
                    <p class="lead mb-4">Dapatkan update artikel terbaru langsung ke email Anda</p>
                    <form class="row g-3 justify-content-center">
                        <div class="col-md-6">
                            <input type="email" class="form-control form-control-lg" placeholder="Masukkan email Anda">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-envelope me-2"></i>Subscribe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="content-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Kategori Artikel</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-code-slash text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Web Development</h5>
                        <p class="text-muted small">Artikel tentang pengembangan web modern</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-phone text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Mobile Development</h5>
                        <p class="text-muted small">Tips dan trik mobile app development</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-shield-check text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Software Quality</h5>
                        <p class="text-muted small">Best practices dalam QA dan testing</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-gear text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">DevOps & CI/CD</h5>
                        <p class="text-muted small">Otomasi dan deployment strategies</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
pg_close($conn);
include '../../includes/footer.php';
?>