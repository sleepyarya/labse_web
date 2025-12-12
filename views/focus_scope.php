<?php
require_once '../includes/config.php';
$page_title = 'Focus & Scope';
include '../includes/header.php';
include '../includes/navbar.php';

// Get focus areas from database
$query = "SELECT * FROM lab_profile WHERE kategori = 'focus' ORDER BY id";
$result = pg_query($conn, $query);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Focus & Scope</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Area fokus penelitian dan pengembangan kami</p>
    </div>
</div>

<!-- Main Content Wrapper -->
<div class="main-wrapper" style="min-height: 70vh; padding-bottom: 8rem;">

<!-- Focus Areas -->
<section class="content-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Area Fokus Penelitian</h2>
            <p class="lead text-muted">Kami fokus pada bidang-bidang berikut untuk memberikan kontribusi maksimal</p>
        </div>
        
        <div class="row g-4">
            <?php
            $icons = ['cloud-arrow-up', 'phone', 'bug', 'gear'];
            $colors = ['primary', 'success', 'warning', 'info'];
            $index = 0;
            $delay = 0;
            
            while ($row = pg_fetch_assoc($result)) {
                $delay += 100;
                $icon = $icons[$index % 4];
                $color = $colors[$index % 4];
                ?>
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-<?php echo $color; ?> bg-opacity-10 rounded p-3">
                                        <i class="bi bi-<?php echo $icon; ?>-fill text-<?php echo $color; ?>" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-3"><?php echo htmlspecialchars($row['judul']); ?></h4>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($row['konten']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $index++;
            }
            ?>
        </div>
    </div>
</section>

<!-- Research Scope -->
<section class="content-section bg-light-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Ruang Lingkup Penelitian</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h4 class="mb-4"><i class="bi bi-laptop text-primary me-2"></i>Software Development</h4>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Full-stack Web Development</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Progressive Web Applications (PWA)</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>RESTful API & GraphQL Development</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Microservices Architecture</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Serverless Computing</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h4 class="mb-4"><i class="bi bi-phone text-primary me-2"></i>Mobile Development</h4>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Native Android & iOS Development</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Cross-platform dengan Flutter & React Native</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile UI/UX Design</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile Performance Optimization</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile Security Best Practices</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h4 class="mb-4"><i class="bi bi-shield-check text-primary me-2"></i>Software Quality</h4>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Automated Testing (Unit, Integration, E2E)</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Test-Driven Development (TDD)</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Code Quality Analysis</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Performance Testing & Optimization</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Security Testing & Audit</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="100">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h4 class="mb-4"><i class="bi bi-diagram-3 text-primary me-2"></i>DevOps & Infrastructure</h4>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>CI/CD Pipeline Implementation</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Container Orchestration (Docker, Kubernetes)</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Cloud Infrastructure (AWS, Azure, GCP)</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Infrastructure as Code (Terraform, Ansible)</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Monitoring & Logging Solutions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Technologies -->
<section class="content-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Teknologi yang Kami Gunakan</h2>
        </div>
        <div class="row g-4">
            <div class="col-6 col-md-3 text-center" data-aos="zoom-in" data-aos-delay="0">
                <div class="card p-4">
                    <i class="bi bi-code-slash text-primary" style="font-size: 3rem;"></i>
                    <h6 class="mt-3">JavaScript / TypeScript</h6>
                </div>
            </div>
            <div class="col-6 col-md-3 text-center" data-aos="zoom-in" data-aos-delay="100">
                <div class="card p-4">
                    <i class="bi bi-code-square text-primary" style="font-size: 3rem;"></i>
                    <h6 class="mt-3">Python / Java</h6>
                </div>
            </div>
            <div class="col-6 col-md-3 text-center" data-aos="zoom-in" data-aos-delay="200">
                <div class="card p-4">
                    <i class="bi bi-database text-primary" style="font-size: 3rem;"></i>
                    <h6 class="mt-3">PostgreSQL / MongoDB</h6>
                </div>
            </div>
            <div class="col-6 col-md-3 text-center" data-aos="zoom-in" data-aos-delay="300">
                <div class="card p-4">
                    <i class="bi bi-cloud text-primary" style="font-size: 3rem;"></i>
                    <h6 class="mt-3">AWS / Azure / GCP</h6>
                </div>
            </div>
        </div>
    </div>
</section>

</div>
<!-- End Main Content Wrapper -->

<?php
pg_close($conn);
include '../includes/footer.php';
?>