<?php
require_once '../includes/config.php';
$page_title = 'Visi & Misi';
include '../includes/header.php';
include '../includes/navbar.php';

// Get visi and misi from database
$query_visi = "SELECT * FROM lab_profile WHERE kategori = 'visi' LIMIT 1";
$result_visi = pg_query($conn, $query_visi);
$visi = pg_fetch_assoc($result_visi);

$query_misi = "SELECT * FROM lab_profile WHERE kategori = 'misi' ORDER BY id";
$result_misi = pg_query($conn, $query_misi);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Visi & Misi</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Arah dan tujuan Lab Software Engineering</p>
    </div>
</div>

<!-- Visi Section -->
<section class="content-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="section-title">Visi</h2>
                </div>
                <div class="card bg-primary text-white" data-aos="zoom-in">
                    <div class="card-body p-5 text-center">
                        <i class="bi bi-eye-fill" style="font-size: 4rem; opacity: 0.8;"></i>
                        <h4 class="mt-4"><?php echo htmlspecialchars($visi['konten']); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Misi Section -->
<section class="content-section bg-light-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Misi</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php
                $count = 1;
                $delay = 0;
                while ($row = pg_fetch_assoc($result_misi)) {
                    $delay += 100;
                    echo '<div class="card mb-4" data-aos="fade-up" data-aos-delay="' . $delay . '">';
                    echo '<div class="card-body p-4">';
                    echo '<div class="d-flex align-items-start">';
                    echo '<div class="flex-shrink-0">';
                    echo '<div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;">';
                    echo $count;
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="flex-grow-1 ms-4">';
                    echo '<h5>' . htmlspecialchars($row['judul']) . '</h5>';
                    echo '<p class="text-muted mb-0">' . htmlspecialchars($row['konten']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    $count++;
                }
                ?>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="content-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Nilai-Nilai Kami</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-lightbulb-fill text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Inovasi</h5>
                        <p class="text-muted">Selalu mencari cara baru dan kreatif dalam menyelesaikan masalah</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-gem text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Kualitas</h5>
                        <p class="text-muted">Berkomitmen menghasilkan karya dengan standar kualitas tertinggi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Kolaborasi</h5>
                        <p class="text-muted">Bekerja sama dalam tim untuk mencapai hasil terbaik</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-graph-up-arrow text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Pembelajaran</h5>
                        <p class="text-muted">Terus belajar dan berkembang mengikuti perkembangan teknologi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
pg_close($conn);
include '../includes/footer.php';
?>