<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Home';

// Check recruitment status
$recruitment_query = "SELECT is_open, message FROM recruitment_settings WHERE id = 1";
$recruitment_result = pg_query($conn, $recruitment_query);
$recruitment_settings = pg_fetch_assoc($recruitment_result);
$is_recruitment_open = ($recruitment_settings && ($recruitment_settings['is_open'] == 't' || $recruitment_settings['is_open'] == '1'));
$recruitment_message = $recruitment_settings ? $recruitment_settings['message'] : 'Maaf, Lab SE sedang tidak membuka recruitment saat ini.';

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content" data-aos="fade-right">
                <h1 class="hero-title"><?php echo htmlspecialchars(get_content('hero', 'title', 'Laboratorium Software Engineering')); ?></h1>
                <p class="hero-subtitle"><?php echo htmlspecialchars(get_content('hero', 'subtitle', 'Berinovasi, Berkolaborasi, dan Berkembang bersama Teknologi Masa Depan')); ?></p>
                <div class="mt-4">
                    <a href="<?php echo BASE_URL; ?>/views/tentang.php" class="btn btn-primary btn-lg me-3">
                        <i class="bi bi-info-circle me-2"></i>Tentang Kami
                    </a>
                    <?php if ($is_recruitment_open): ?>
                    <a href="<?php echo BASE_URL; ?>/views/recruitment/" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-people me-2"></i>Gabung Sekarang
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="text-center">
                    <?php 
                    $hero_image = get_content('hero', 'image_path', '/public/img/logo-se.png');
                    ?>
                    <img src="<?php echo BASE_URL . $hero_image; ?>" alt="Lab Software Engineering" class="img-fluid" style="max-width: 450px;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="content-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title"><?php echo htmlspecialchars(get_content('about', 'title', 'Tentang Lab SE')); ?></h2>
            <p class="lead text-muted"><?php echo htmlspecialchars(get_content('about', 'subtitle', 'Pusat Keunggulan Pengembangan Perangkat Lunak')); ?></p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-trophy text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="card-title"><?php echo htmlspecialchars(get_content('about', 'card1_title', 'Unggul dalam Penelitian')); ?></h4>
                        <p class="card-text"><?php echo htmlspecialchars(get_content('about', 'card1_desc', 'Melakukan penelitian inovatif dalam bidang rekayasa perangkat lunak dan teknologi informasi terkini.')); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="card-title"><?php echo htmlspecialchars(get_content('about', 'card2_title', 'Tim Berkualitas')); ?></h4>
                        <p class="card-text"><?php echo htmlspecialchars(get_content('about', 'card2_desc', 'Didukung oleh dosen dan peneliti berpengalaman dengan sertifikasi internasional.')); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-lightbulb text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="card-title"><?php echo htmlspecialchars(get_content('about', 'card3_title', 'Inovasi Berkelanjutan')); ?></h4>
                        <p class="card-text"><?php echo htmlspecialchars(get_content('about', 'card3_desc', 'Menghasilkan solusi software inovatif yang memberikan dampak nyata bagi masyarakat.')); ?></p>
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
                echo '<a href="' . BASE_URL . '/views/blog/detail.php?id=' . $row['id'] . '" class="btn btn-outline-primary">Baca Selengkapnya</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?php echo BASE_URL; ?>/views/blog/" class="btn btn-primary btn-lg">
                Lihat Semua Artikel <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Pengabdian Masyarakat Section -->
<section class="content-section bg-light-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Pengabdian Masyarakat & Pelatihan</h2>
            <p class="lead text-muted">Kegiatan pengabdian pada masyarakat yang kami laksanakan</p>
        </div>
        <div class="row g-4">
            <?php
            $query_pengabdian = "SELECT * FROM pengabdian ORDER BY tanggal DESC LIMIT 3";
            $result_pengabdian = pg_query($conn, $query_pengabdian);
            $delay = 0;
            while ($pengabdian = pg_fetch_assoc($result_pengabdian)) {
                $delay += 100;
                echo '<div class="col-md-4" data-aos="fade-up" data-aos-delay="' . $delay . '">';
                echo '<div class="card h-100">';
                if ($pengabdian['gambar']) {
                    echo '<img src="' . BASE_URL . '/public/uploads/pengabdian/' . htmlspecialchars($pengabdian['gambar']) . '" class="card-img-top" style="height: 200px; object-fit: cover;" alt="' . htmlspecialchars($pengabdian['judul']) . '">';
                }
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($pengabdian['judul']) . '</h5>';
                echo '<p class="text-muted small mb-3">';
                echo '<i class="bi bi-calendar me-2"></i>' . date('d M Y', strtotime($pengabdian['tanggal']));
                echo ' | <i class="bi bi-geo-alt ms-2 me-2"></i>' . htmlspecialchars($pengabdian['lokasi']);
                echo '</p>';
                $deskripsi_short = strip_tags($pengabdian['deskripsi']);
                echo '<p class="card-text">' . substr($deskripsi_short, 0, 120) . '...</p>';
                echo '<a href="' . BASE_URL . '/views/pengabdian/detail.php?id=' . $pengabdian['id'] . '" class="btn btn-outline-primary">Lihat Detail</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?php echo BASE_URL; ?>/views/pengabdian/" class="btn btn-primary btn-lg">
                Lihat Semua Kegiatan <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="content-section bg-light-section">
    <div class="container text-center" data-aos="zoom-in">
        <h2 class="mb-4">Tertarik Bergabung dengan Kami?</h2>
        <?php if ($is_recruitment_open): ?>
            <p class="lead mb-4">Kami terbuka untuk mahasiswa yang ingin mengembangkan kemampuan di bidang software engineering</p>
            <a href="<?php echo BASE_URL; ?>/views/recruitment/" class="btn btn-primary btn-lg">
                <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
            </a>
        <?php else: ?>
            <div class="alert alert-warning d-inline-block" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong><?php echo htmlspecialchars($recruitment_message); ?></strong>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
pg_close($conn);
include 'includes/footer.php';
?>
