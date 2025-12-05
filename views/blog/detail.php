<?php
require_once __DIR__ . '/../../core/database.php';

// Get article ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get article details from database
$query = "SELECT * FROM artikel WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));

if (pg_num_rows($result) == 0) {
    header('Location: index.php');
    exit();
}

$artikel = pg_fetch_assoc($result);
$page_title = $artikel['judul'];

include '../../includes/header.php';
include '../../includes/navbar.php';

// Use placeholder image
$img_url = "https://picsum.photos/seed/" . $artikel['id'] . "/1200/600";
?>

<!-- Article Header -->
<div class="page-header" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?php echo $img_url; ?>') center/cover;">
    <div class="container text-center">
        <h1 class="display-4 fw-bold" data-aos="fade-down"><?php echo htmlspecialchars($artikel['judul']); ?></h1>
        <div class="mt-4" data-aos="fade-up" data-aos-delay="100">
            <span class="badge bg-white text-primary px-3 py-2 me-2">
                <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($artikel['penulis']); ?>
            </span>
            <span class="badge bg-white text-primary px-3 py-2">
                <i class="bi bi-calendar me-1"></i><?php echo date('d F Y', strtotime($artikel['created_at'])); ?>
            </span>
        </div>
    </div>
</div>

<!-- Article Content -->
<section class="content-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <article class="card" data-aos="fade-up">
                    <div class="card-body p-5">
                        <div class="article-content">
                            <?php 
                            // Split content into paragraphs
                            $paragraphs = explode("\n", $artikel['isi']);
                            foreach ($paragraphs as $paragraph) {
                                if (!empty(trim($paragraph))) {
                                    echo '<p class="lead mb-4">' . nl2br(htmlspecialchars($paragraph)) . '</p>';
                                }
                            }
                            ?>
                        </div>
                        
                        <hr class="my-5">
                        
                        <!-- Author Info -->
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <?php 
                                $author_avatar = "https://ui-avatars.com/api/?name=" . urlencode($artikel['penulis']) . "&size=80&background=4A90E2&color=fff";
                                ?>
                                <img src="<?php echo $author_avatar; ?>" alt="<?php echo htmlspecialchars($artikel['penulis']); ?>" class="rounded-circle" style="width: 80px; height: 80px;">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">Tentang Penulis</h5>
                                <p class="text-primary mb-2"><?php echo htmlspecialchars($artikel['penulis']); ?></p>
                                <p class="text-muted small mb-0">Dosen dan peneliti di Lab Software Engineering</p>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Share Buttons -->
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Bagikan Artikel:</h5>
                            <div>
                                <button class="btn btn-outline-primary me-2">
                                    <i class="bi bi-facebook"></i>
                                </button>
                                <button class="btn btn-outline-info me-2">
                                    <i class="bi bi-twitter"></i>
                                </button>
                                <button class="btn btn-outline-success me-2">
                                    <i class="bi bi-whatsapp"></i>
                                </button>
                                <button class="btn btn-outline-danger">
                                    <i class="bi bi-envelope"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
                
                <div class="text-center mt-4" data-aos="fade-up">
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Artikel
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Articles -->
<section class="content-section bg-light-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Artikel Terkait</h2>
        </div>
        <div class="row g-4">
            <?php
            // Get other articles (exclude current)
            $query_related = "SELECT * FROM artikel WHERE id != $1 ORDER BY created_at DESC LIMIT 3";
            $result_related = pg_query_params($conn, $query_related, array($id));
            
            $delay = 0;
            while ($row = pg_fetch_assoc($result_related)) {
                $delay += 100;
                // Use uploaded image if exists, otherwise use placeholder
                if (!empty($row['gambar']) && file_exists('../../public/uploads/artikel/' . $row['gambar'])) {
                    $related_img = BASE_URL . '/public/uploads/artikel/' . $row['gambar'];
                } else {
                    $related_img = "https://picsum.photos/seed/" . $row['id'] . "/600/400";
                }
                ?>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <div class="card h-100">
                        <img src="<?php echo $related_img; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['judul']); ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($row['judul']); ?></h6>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($row['penulis']); ?> | 
                                <i class="bi bi-calendar ms-2 me-1"></i><?php echo date('d M Y', strtotime($row['created_at'])); ?>
                            </p>
                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm">
                                Baca Artikel
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