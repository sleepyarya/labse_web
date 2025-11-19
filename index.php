<?php
require_once __DIR__ . '/core/database.php';
$page_title = 'Home';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content" data-aos="fade-right">
                <h1 class="hero-title">Laboratorium Software Engineering</h1>
                <p class="hero-subtitle">Berinovasi, Berkolaborasi, dan Berkembang bersama Teknologi Masa Depan</p>
                <div class="mt-4">
                    <a href="<?php echo BASE_URL; ?>/views/tentang.php" class="btn btn-primary btn-lg me-3">
                        <i class="bi bi-info-circle me-2"></i>Tentang Kami
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/recruitment/" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-people me-2"></i>Gabung Sekarang
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="text-center">
                    <img src="<?php echo BASE_URL; ?>/public/img/logo-se.png" alt="Lab Software Engineering" class="img-fluid" style="max-width: 450px;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="content-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Tentang Lab SE</h2>
            <p class="lead text-muted">Pusat Keunggulan Pengembangan Perangkat Lunak</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-trophy text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="card-title">Unggul dalam Penelitian</h4>
                        <p class="card-text">Melakukan penelitian inovatif dalam bidang rekayasa perangkat lunak dan teknologi informasi terkini.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="card-title">Tim Berkualitas</h4>
                        <p class="card-text">Didukung oleh dosen dan peneliti berpengalaman dengan sertifikasi internasional.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-lightbulb text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="card-title">Inovasi Berkelanjutan</h4>
                        <p class="card-text">Menghasilkan solusi software inovatif yang memberikan dampak nyata bagi masyarakat.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Focus Areas Section -->
<section class="content-section bg-light-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Area Fokus Penelitian</h2>
            <p class="lead text-muted">Bidang-bidang yang menjadi spesialisasi kami</p>
        </div>
        <div class="row g-4">
            <?php
            $query = "SELECT * FROM lab_profile WHERE kategori = 'focus' ORDER BY id LIMIT 4";
            $result = pg_query($conn, $query);
            $delay = 0;
            while ($row = pg_fetch_assoc($result)) {
                $delay += 100;
                echo '<div class="col-md-6" data-aos="fade-up" data-aos-delay="' . $delay . '">';
                echo '<div class="card h-100">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title text-primary"><i class="bi bi-check-circle me-2"></i>' . htmlspecialchars($row['judul']) . '</h5>';
                echo '<p class="card-text">' . htmlspecialchars($row['konten']) . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Latest Articles Section -->
<section class="content-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Artikel Terbaru</h2>
            <p class="lead text-muted">Baca publikasi dan artikel terkini dari tim kami</p>
        </div>
        <div class="row g-4">
            <?php
            $query = "SELECT * FROM artikel ORDER BY created_at DESC LIMIT 3";
            $result = pg_query($conn, $query);
            $delay = 0;
            while ($row = pg_fetch_assoc($result)) {
                $delay += 100;
                echo '<div class="col-md-4" data-aos="fade-up" data-aos-delay="' . $delay . '">';
                echo '<div class="card h-100">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($row['judul']) . '</h5>';
                echo '<p class="text-muted small mb-3">';
                echo '<i class="bi bi-person me-2"></i>' . htmlspecialchars($row['penulis']);
                echo ' | <i class="bi bi-calendar ms-2 me-2"></i>' . date('d M Y', strtotime($row['created_at']));
                echo '</p>';
                echo '<p class="card-text">' . substr(htmlspecialchars($row['isi']), 0, 150) . '...</p>';
                echo '<a href="' . BASE_URL . '/pages/blog/detail.php?id=' . $row['id'] . '" class="btn btn-outline-primary">Baca Selengkapnya</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?php echo BASE_URL; ?>/pages/blog/" class="btn btn-primary btn-lg">
                Lihat Semua Artikel <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="content-section bg-light-section">
    <div class="container text-center" data-aos="zoom-in">
        <h2 class="mb-4">Tertarik Bergabung dengan Kami?</h2>
        <p class="lead mb-4">Kami terbuka untuk mahasiswa yang ingin mengembangkan kemampuan di bidang software engineering</p>
        <a href="<?php echo BASE_URL; ?>/views/recruitment/" class="btn btn-primary btn-lg">
            <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
        </a>
    </div>
</section>

<?php
pg_close($conn);
include 'includes/footer.php';
?>