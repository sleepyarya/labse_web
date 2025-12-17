<?php
require_once __DIR__ . '/../../includes/config.php';
$page_title = 'Personil';
include '../../includes/header.php';
include '../../includes/navbar.php';

// Get all personil from database
$query = "SELECT * FROM personil ORDER BY id";
$result = pg_query($conn, $query);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Tim Kami</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Bertemu dengan para ahli di balik Lab Software Engineering</p>
    </div>
</div>

<!-- Personil Grid -->
<section class="content-section">
    <div class="container">
        <div class="row g-4">
            <?php
            $delay = 0;
            while ($row = pg_fetch_assoc($result)) {
                $delay += 100;
                // Use uploaded photo if available, otherwise use placeholder
                if (!empty($row['foto'])) {
                    $foto_url = BASE_URL . "/public/uploads/personil/" . htmlspecialchars($row['foto']);
                } else {
                    $foto_url = "https://ui-avatars.com/api/?name=" . urlencode($row['nama']) . "&size=300&background=4A90E2&color=fff";
                }
                ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <div class="card personil-card h-100">
                        <div class="card-body">
                            <img src="<?php echo $foto_url; ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>" class="personil-img" 
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($row['nama']); ?>&size=300&background=4A90E2&color=fff'">
                            <h4 class="mb-2"><?php echo htmlspecialchars($row['nama']); ?></h4>
                            <p class="text-primary fw-semibold mb-3"><?php echo htmlspecialchars($row['jabatan']); ?></p>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($row['email']); ?>
                            </p>
                            <p class="text-muted"><?php echo substr(htmlspecialchars($row['deskripsi']), 0, 100); ?>...</p>
                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary">
                                <i class="bi bi-person-lines-fill me-2"></i>Lihat Profil
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<!-- Join Team CTA -->
<section class="content-section bg-light-section">
    <div class="container text-center" data-aos="zoom-in">
        <h2 class="mb-4">Ingin Bergabung dengan Tim Kami?</h2>
        <p class="lead mb-4">Kami selalu mencari individu berbakat yang passionate di bidang software engineering</p>
        <a href="<?php echo BASE_URL; ?>/views/recruitment/" class="btn btn-primary btn-lg">
            <i class="bi bi-briefcase me-2"></i>Lihat Peluang Bergabung
        </a>
    </div>
</section>

<?php
pg_close($conn);
include '../../includes/footer.php';
?>
