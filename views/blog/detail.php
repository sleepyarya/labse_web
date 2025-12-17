<?php
require_once __DIR__ . '/../../includes/config.php';

// Get article ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get article details from database with category
$query = "SELECT a.*, k.nama_kategori, k.warna as kategori_warna 
          FROM artikel a 
          LEFT JOIN kategori_artikel k ON a.kategori_id = k.id 
          WHERE a.id = $1";
$result = pg_query_params($conn, $query, array($id));

if (pg_num_rows($result) == 0) {
    header('Location: index.php');
    exit();
}

$artikel = pg_fetch_assoc($result);
$page_title = $artikel['judul'];

include '../../includes/header.php';
include '../../includes/navbar.php';

// Use uploaded image if exists, otherwise use placeholder
if (!empty($artikel['gambar']) && file_exists('../../public/uploads/artikel/' . $artikel['gambar'])) {
    $img_url = BASE_URL . '/public/uploads/artikel/' . $artikel['gambar'];
} else {
    $img_url = "https://picsum.photos/seed/" . $artikel['id'] . "/1200/600";
}
?>

<!-- Article Header -->
<div class="page-header" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?php echo $img_url; ?>') center/cover;">
    <div class="container text-center">
        <?php if (!empty($artikel['nama_kategori'])): ?>
        <span class="badge mb-3 px-3 py-2" data-aos="fade-down" 
              style="background-color: <?php echo htmlspecialchars($artikel['kategori_warna'] ?? '#0d6efd'); ?>; font-size: 0.9rem;">
            <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($artikel['nama_kategori']); ?>
        </span>
        <?php endif; ?>
        <h1 class="display-4 fw-bold" data-aos="fade-down" data-aos-delay="100"><?php echo htmlspecialchars($artikel['judul']); ?></h1>
        <div class="mt-4" data-aos="fade-up" data-aos-delay="200">
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
                        
                        <!-- Share Buttons
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
                        </div> -->
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
            // Get related articles - prioritize same category, then latest
            $kategori_id = $artikel['kategori_id'] ?? null;
            
            if ($kategori_id) {
                // Get articles from same category first
                $query_related = "SELECT a.*, k.nama_kategori, k.warna as kategori_warna 
                                  FROM artikel a 
                                  LEFT JOIN kategori_artikel k ON a.kategori_id = k.id 
                                  WHERE a.id != $1 AND a.kategori_id = $2 
                                  ORDER BY a.created_at DESC LIMIT 3";
                $result_related = pg_query_params($conn, $query_related, array($id, $kategori_id));
            } else {
                // Get any articles
                $query_related = "SELECT a.*, k.nama_kategori, k.warna as kategori_warna 
                                  FROM artikel a 
                                  LEFT JOIN kategori_artikel k ON a.kategori_id = k.id 
                                  WHERE a.id != $1 
                                  ORDER BY a.created_at DESC LIMIT 3";
                $result_related = pg_query_params($conn, $query_related, array($id));
            }
            
            // If not enough from same category, fill with latest articles
            $related_count = pg_num_rows($result_related);
            $shown_ids = [$id];
            
            $delay = 0;
            while ($row = pg_fetch_assoc($result_related)) {
                $delay += 100;
                $shown_ids[] = $row['id'];
                // Use uploaded image if exists, otherwise use placeholder
                if (!empty($row['gambar']) && file_exists('../../public/uploads/artikel/' . $row['gambar'])) {
                    $related_img = BASE_URL . '/public/uploads/artikel/' . $row['gambar'];
                } else {
                    $related_img = "https://picsum.photos/seed/" . $row['id'] . "/600/400";
                }
                ?>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <div class="card h-100 shadow-sm border-0 hover-card">
                        <div class="position-relative overflow-hidden">
                            <img src="<?php echo $related_img; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['judul']); ?>" style="height: 200px; object-fit: cover;">
                            <?php if (!empty($row['nama_kategori'])): ?>
                            <span class="badge position-absolute top-0 end-0 m-2" 
                                  style="background-color: <?php echo htmlspecialchars($row['kategori_warna'] ?? '#0d6efd'); ?>;">
                                <?php echo htmlspecialchars($row['nama_kategori']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
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
            
            // If still need more articles, get from other categories
            if ($related_count < 3 && $kategori_id) {
                $remaining = 3 - $related_count;
                $exclude_ids = implode(',', $shown_ids);
                $query_more = "SELECT a.*, k.nama_kategori, k.warna as kategori_warna 
                               FROM artikel a 
                               LEFT JOIN kategori_artikel k ON a.kategori_id = k.id 
                               WHERE a.id NOT IN ($exclude_ids) 
                               ORDER BY a.created_at DESC LIMIT $remaining";
                $result_more = pg_query($conn, $query_more);
                
                while ($row = pg_fetch_assoc($result_more)) {
                    $delay += 100;
                    if (!empty($row['gambar']) && file_exists('../../public/uploads/artikel/' . $row['gambar'])) {
                        $related_img = BASE_URL . '/public/uploads/artikel/' . $row['gambar'];
                    } else {
                        $related_img = "https://picsum.photos/seed/" . $row['id'] . "/600/400";
                    }
                    ?>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <div class="position-relative overflow-hidden">
                                <img src="<?php echo $related_img; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['judul']); ?>" style="height: 200px; object-fit: cover;">
                                <?php if (!empty($row['nama_kategori'])): ?>
                                <span class="badge position-absolute top-0 end-0 m-2" 
                                      style="background-color: <?php echo htmlspecialchars($row['kategori_warna'] ?? '#0d6efd'); ?>;">
                                    <?php echo htmlspecialchars($row['nama_kategori']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
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
            }
            ?>
        </div>
    </div>
</section>

<?php
pg_close($conn);
include '../../includes/footer.php';
?>
