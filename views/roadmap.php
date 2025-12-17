<?php
require_once '../includes/config.php';
$page_title = 'Roadmap';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Roadmap Pengembangan</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Rencana strategis Lab Software Engineering</p>
    </div>
</div>

<!-- Roadmap Timeline -->
<section class="content-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <?php
                // Fetch Roadmap items
                $query = "SELECT * FROM lab_profile WHERE kategori = 'roadmap' ORDER BY id";
                $result = pg_query($conn, $query);
                $delay = 0;
                $count = 0;
                
                while ($row = pg_fetch_assoc($result)) {
                    $badge_class = 'bg-primary';
                    if ($count > 3) $badge_class = 'bg-success';
                    if ($count > 6) $badge_class = 'bg-info';
                    
                    echo '<div class="timeline-item" data-aos="fade-up" data-aos-delay="' . $delay . '">';
                    echo '<div class="card">';
                    echo '<div class="card-body">';
                    echo '<span class="badge ' . $badge_class . ' mb-2">' . htmlspecialchars($row['judul']) . '</span>';
                    echo '<h4>' . htmlspecialchars($row['konten']) . '</h4>'; // Note: In DB seed, content was description. Wait, in seed I put "Title: Description".
                    // Let's adjust: In seed I put '2024 - Q1' as Title, and 'Modernisasi Infrastruktur: Upgrade...' as Content.
                    // So here: Title is the Year/Quarter (Badge), Content is the Description.
                    // Actually, looking at seed: ('roadmap', '2024 - Q1', 'Modernisasi Infrastruktur: Upgrade...')
                    // The hardcoded view had: Badge=Year, H4=Title, P=Desc.
                    // My seed combined Title and Desc into Content.
                    // Let's split it for better display if possible, or just display content as paragraph.
                    // For now, let's just display content as paragraph and maybe bold the first part if it has a colon.
                    
                    $content_parts = explode(':', $row['konten'], 2);
                    if (count($content_parts) == 2) {
                        echo '<h4>' . htmlspecialchars(trim($content_parts[0])) . '</h4>';
                        echo '<p class="text-muted mb-0">' . htmlspecialchars(trim($content_parts[1])) . '</p>';
                    } else {
                        echo '<p class="text-muted mb-0">' . htmlspecialchars($row['konten']) . '</p>';
                    }
                    
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    
                    $delay += 100;
                    $count++;
                }
                ?>

            </div>
        </div>
    </div>
</section>

<!-- Strategic Goals -->
<section class="content-section bg-light-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Target Strategis</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="100">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-mortarboard-fill text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-3 fw-bold text-primary">100+</h3>
                        <p class="text-muted">Alumni Bersertifikasi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="200">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-text-fill text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-3 fw-bold text-primary">50+</h3>
                        <p class="text-muted">Publikasi Internasional</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="300">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-building text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-3 fw-bold text-primary">20+</h3>
                        <p class="text-muted">Mitra Industri</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="400">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-rocket-takeoff-fill text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-3 fw-bold text-primary">10+</h3>
                        <p class="text-muted">Startup Diinkubasi</p>
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