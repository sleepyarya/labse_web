<?php
require_once '../../includes/config.php';
$page_title = 'Recruitment';

// Check recruitment status
$recruitment_query = "SELECT is_open, message FROM recruitment_settings WHERE id = 1";
$recruitment_result = pg_query($conn, $recruitment_query);
$recruitment_settings = pg_fetch_assoc($recruitment_result);
$is_recruitment_open = ($recruitment_settings && ($recruitment_settings['is_open'] == 't' || $recruitment_settings['is_open'] == '1'));
$recruitment_message = $recruitment_settings ? $recruitment_settings['message'] : 'Maaf, Lab SE sedang tidak membuka recruitment saat ini.';

// Check if redirected from form with error
$show_closed_error = isset($_GET['error']) && $_GET['error'] == 'closed';

include '../../includes/header.php';
include '../../includes/navbar.php';

// Get all approved students from database (only show approved students to public)
$query = "SELECT * FROM mahasiswa WHERE status_approval = 'approved' ORDER BY created_at DESC";
$result = pg_query($conn, $query);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Recruitment Lab SE</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Bergabunglah bersama kami untuk mengembangkan kemampuan software engineering</p>
    </div>
</div>

<?php if ($show_closed_error): ?>
<section class="content-section pt-0">
    <div class="container">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Recruitment sedang ditutup.</strong> Anda tidak dapat mengakses form pendaftaran saat ini.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action -->
<?php if ($is_recruitment_open): ?>
<section class="content-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card bg-primary text-white" data-aos="zoom-in">
                    <div class="card-body p-5 text-center">
                        <h2 class="mb-4">Buka Peluang Baru di Lab Software Engineering!</h2>
                        <p class="lead mb-4">Kami mencari mahasiswa yang passionate dan bersemangat untuk belajar dan berkembang bersama dalam bidang software engineering.</p>
                        <a href="form.php" class="btn btn-light btn-lg">
                            <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php else: ?>
<section class="content-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="alert alert-warning p-5 text-center" role="alert" data-aos="zoom-in">
                    <i class="bi bi-exclamation-circle" style="font-size: 3rem;"></i>
                    <h3 class="mt-3 mb-3"><?php echo htmlspecialchars($recruitment_message); ?></h3>
                    <p class="mb-0">Pantau terus halaman ini untuk informasi pembukaan recruitment selanjutnya.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Benefits Section -->
<section class="content-section bg-light-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Apa yang Anda Dapatkan?</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-book text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Pelatihan Berkualitas</h5>
                        <p class="text-muted">Akses ke workshop, training, dan mentoring dari expert</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-laptop text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Proyek Real</h5>
                        <p class="text-muted">Terlibat dalam proyek nyata dengan industri dan penelitian</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-award text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Sertifikasi</h5>
                        <p class="text-muted">Kesempatan mendapat sertifikasi internasional</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Networking</h5>
                        <p class="text-muted">Membangun jaringan dengan profesional dan mahasiswa lain</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Registered Students -->
<section class="content-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Mahasiswa yang Sudah Diterima</h2>
            <p class="lead text-muted">Bergabung dengan <?php echo pg_num_rows($result); ?> mahasiswa yang telah diterima di Lab SE</p>
        </div>
        
        <?php if (pg_num_rows($result) > 0): ?>
        <div class="row g-4">
            <?php
            $delay = 0;
            while ($row = pg_fetch_assoc($result)) {
                $delay += 50;
                $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($row['nama']) . "&size=100&background=random";
                ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $avatar_url; ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>" class="rounded-circle me-3" style="width: 60px; height: 60px;">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($row['nama']); ?></h6>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-mortarboard me-1"></i><?php echo htmlspecialchars($row['jurusan']); ?>
                                    </p>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-credit-card-2-front me-1"></i><?php echo htmlspecialchars($row['nim']); ?>
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <p class="text-muted small mb-0"><i class="bi bi-quote"></i> <?php echo htmlspecialchars(substr($row['alasan'], 0, 100)); ?>...</p>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php else: ?>
        <div class="text-center" data-aos="fade-up">
            <p class="lead text-muted">Belum ada mahasiswa yang mendaftar. Jadilah yang pertama!</p>
        </div>
        <?php endif; ?>
        
        <?php if ($is_recruitment_open): ?>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="form.php" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle me-2"></i>Daftar Bergabung
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
pg_close($conn);
include '../../includes/footer.php';
?>