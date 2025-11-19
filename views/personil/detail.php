<?php
require_once '../../includes/config.php';

// Get personil ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get personil details from database
$query = "SELECT * FROM personil WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));

if (pg_num_rows($result) == 0) {
    header('Location: index.php');
    exit();
}

$personil = pg_fetch_assoc($result);
$page_title = $personil['nama'];

include '../../includes/header.php';
include '../../includes/navbar.php';

// Use uploaded photo if available, otherwise use placeholder
if (!empty($personil['foto'])) {
    $foto_url = BASE_URL . "/public/uploads/personil/" . htmlspecialchars($personil['foto']);
} else {
    $foto_url = "https://ui-avatars.com/api/?name=" . urlencode($personil['nama']) . "&size=400&background=4A90E2&color=fff";
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down"><?php echo htmlspecialchars($personil['nama']); ?></h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100"><?php echo htmlspecialchars($personil['jabatan']); ?></p>
    </div>
</div>

<!-- Personil Detail -->
<section class="content-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card" data-aos="fade-up">
                    <div class="card-body p-5">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4 mb-md-0">
                                <img src="<?php echo $foto_url; ?>" alt="<?php echo htmlspecialchars($personil['nama']); ?>" class="img-fluid rounded-circle border border-primary border-5 mb-3" style="max-width: 250px;"
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($personil['nama']); ?>&size=400&background=4A90E2&color=fff'">
                                <h4><?php echo htmlspecialchars($personil['nama']); ?></h4>
                                <p class="text-primary fw-semibold"><?php echo htmlspecialchars($personil['jabatan']); ?></p>
                                <div class="mb-3">
                                    <a href="mailto:<?php echo htmlspecialchars($personil['email']); ?>" class="btn btn-outline-primary">
                                        <i class="bi bi-envelope me-2"></i>Kirim Email
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h3 class="mb-4">Tentang</h3>
                                <p class="lead"><?php echo nl2br(htmlspecialchars($personil['deskripsi'])); ?></p>
                                
                                <hr class="my-4">
                                
                                <h4 class="mb-3">Informasi Kontak</h4>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-envelope text-primary me-2"></i>
                                        <strong>Email:</strong> <?php echo htmlspecialchars($personil['email']); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4" data-aos="fade-up">
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Personil
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Personil -->
<section class="content-section bg-light-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Tim Lainnya</h2>
        </div>
        <div class="row g-4">
            <?php
            // Get other personil (exclude current)
            $query_other = "SELECT * FROM personil WHERE id != $1 ORDER BY RANDOM() LIMIT 3";
            $result_other = pg_query_params($conn, $query_other, array($id));
            
            $delay = 0;
            while ($row = pg_fetch_assoc($result_other)) {
                $delay += 100;
                $foto_url_other = "https://ui-avatars.com/api/?name=" . urlencode($row['nama']) . "&size=300&background=4A90E2&color=fff";
                ?>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <div class="card personil-card h-100">
                        <div class="card-body">
                            <img src="<?php echo $foto_url_other; ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>" class="personil-img">
                            <h5 class="mb-2"><?php echo htmlspecialchars($row['nama']); ?></h5>
                            <p class="text-primary fw-semibold mb-3"><?php echo htmlspecialchars($row['jabatan']); ?></p>
                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm">
                                Lihat Profil
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

<?php
pg_close($conn);
include '../../includes/footer.php';
?>