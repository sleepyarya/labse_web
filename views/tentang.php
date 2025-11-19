<?php
require_once __DIR__ . '/../core/database.php';
$page_title = 'Tentang Kami';
include '../includes/header.php';
include '../includes/navbar.php';

// Get about content from database
$query = "SELECT * FROM lab_profile WHERE kategori = 'tentang' LIMIT 1";
$result = pg_query($conn, $query);
$about = pg_fetch_assoc($result);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down"><?php echo htmlspecialchars($about['judul']); ?></h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Mengenal lebih dekat Lab Software Engineering</p>
    </div>
</div>

<!-- About Content -->
<section class="content-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card" data-aos="fade-up">
                    <div class="card-body p-5">
                        <p class="lead"><?php echo nl2br(htmlspecialchars($about['konten'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="content-section bg-light-section">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100">
                    <div class="card-body">
                        <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                        <h2 class="mt-3 fw-bold">50+</h2>
                        <p class="text-muted">Mahasiswa Aktif</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100">
                    <div class="card-body">
                        <i class="bi bi-journal-code text-primary" style="font-size: 3rem;"></i>
                        <h2 class="mt-3 fw-bold">30+</h2>
                        <p class="text-muted">Proyek Selesai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100">
                    <div class="card-body">
                        <i class="bi bi-trophy-fill text-primary" style="font-size: 3rem;"></i>
                        <h2 class="mt-3 fw-bold">15+</h2>
                        <p class="text-muted">Penghargaan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="card h-100">
                    <div class="card-body">
                        <i class="bi bi-book-fill text-primary" style="font-size: 3rem;"></i>
                        <h2 class="mt-3 fw-bold">40+</h2>
                        <p class="text-muted">Publikasi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="content-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Mengapa Memilih Lab SE?</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6" data-aos="fade-right">
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle-fill text-primary fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5>Fasilitas Modern</h5>
                        <p class="text-muted">Dilengkapi dengan peralatan dan software terkini untuk mendukung penelitian dan pengembangan.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle-fill text-primary fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5>Mentor Berpengalaman</h5>
                        <p class="text-muted">Dibimbing oleh dosen dan praktisi yang ahli di bidangnya dengan pengalaman industri.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-right" data-aos-delay="100">
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle-fill text-primary fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5>Proyek Nyata</h5>
                        <p class="text-muted">Kesempatan mengerjakan proyek riil dari industri dan berkontribusi pada penelitian aktual.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left" data-aos-delay="100">
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle-fill text-primary fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5>Networking Luas</h5>
                        <p class="text-muted">Membangun koneksi dengan profesional industri dan akademisi dari berbagai institusi.</p>
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