<?php
// Member Dashboard View
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../core/database.php';
require_once '../controllers/artikelController.php';
require_once '../controllers/profileController.php';

$page_title = 'Dashboard';

// Initialize controllers
$artikelController = new MemberArtikelController();
$profileController = new MemberProfileController();

// Get member statistics
$member_id = $_SESSION['member_id'];
$total_articles = $artikelController->getTotalCount();
$recent_articles = $artikelController->getRecentArticles(5);

include '../includes/member_header.php';
include '../includes/member_sidebar.php';
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Dashboard</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="user-dropdown">
            <span class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['member_nama']); ?></strong></span>
            <?php if (!empty($_SESSION['member_foto'])): ?>
                <img src="<?php echo BASE_URL; ?>/public/uploads/personil/<?php echo htmlspecialchars($_SESSION['member_foto']); ?>" 
                     alt="Profile" onerror="this.src='<?php echo BASE_URL; ?>/public/img/default-avatar.png'">
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Dashboard Content -->
    <div class="row g-4">
        
        <!-- Welcome Card -->
        <div class="col-12" data-aos="fade-up">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4A90E2 0%, #68BBE3 100%);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-3">
                                <i class="bi bi-hand-wave me-2"></i>
                                Selamat Datang, <?php echo htmlspecialchars($_SESSION['member_nama']); ?>!
                            </h3>
                            <p class="mb-0" style="opacity: 0.95;">
                                <i class="bi bi-briefcase me-2"></i><?php echo htmlspecialchars($_SESSION['member_jabatan']); ?>
                            </p>
                            <p class="mb-0 mt-2" style="opacity: 0.9;">
                                <i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($_SESSION['member_email']); ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="bi bi-person-workspace" style="font-size: 5rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-file-text-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="mb-2"><?php echo $total_articles; ?></h2>
                    <p class="text-muted mb-3">Total Artikel</p>
                    <a href="my_articles.php" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Lihat Semua
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-plus-circle-fill text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-2">Buat Artikel Baru</h5>
                    <p class="text-muted mb-3">Bagikan pengetahuan Anda</p>
                    <a href="artikel_form.php" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-lg me-1"></i>Buat Sekarang
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-12 col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-person-circle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-2">Profil Saya</h5>
                    <p class="text-muted mb-3">Kelola informasi pribadi</p>
                    <a href="edit_profile.php" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil me-1"></i>Edit Profil
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Articles -->
        <div class="col-12" data-aos="fade-up" data-aos-delay="400">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Artikel Terbaru Saya
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count($recent_articles) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul Artikel</th>
                                        <th>Tanggal Dibuat</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_articles as $article): ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-file-text text-primary me-2"></i>
                                            <?php echo htmlspecialchars($article['judul']); ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?php echo date('d M Y, H:i', strtotime($article['created_at'])); ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="artikel_form.php?id=<?php echo $article['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-3 mb-0">Belum ada artikel</p>
                            <a href="artikel_form.php" class="btn btn-primary mt-3">
                                <i class="bi bi-plus-circle me-2"></i>Buat Artikel Pertama
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (count($recent_articles) >= 5): ?>
                <div class="card-footer bg-white text-center py-3">
                    <a href="my_articles.php" class="text-decoration-none">
                        Lihat Semua Artikel <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Quick Tips -->
        <div class="col-12" data-aos="fade-up" data-aos-delay="500">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #4A90E2 !important;">
                <div class="card-body">
                    <h5 class="mb-3">
                        <i class="bi bi-lightbulb text-warning me-2"></i>Tips Member
                    </h5>
                    <ul class="mb-0">
                        <li class="mb-2">Artikel yang Anda buat hanya dapat diubah atau dihapus oleh Anda sendiri</li>
                        <li class="mb-2">Pastikan artikel memiliki judul yang menarik dan deskripsi yang jelas</li>
                        <li class="mb-2">Upload gambar berkualitas untuk menarik perhatian pembaca</li>
                        <li class="mb-0">Perbarui profil Anda secara berkala untuk informasi terkini</li>
                    </ul>
                </div>
            </div>
        </div>
        
    </div>
    
</div>
<!-- End Member Content -->

<style>
    /* Fix profile image size in topbar */
    .user-dropdown img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #4A90E2;
    }
    
    /* Dashboard Responsive Styles */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }
    
    @media (max-width: 768px) {
        .member-topbar h4 {
            font-size: 1.1rem;
        }
        
        .card-body {
            padding: 1.25rem !important;
        }
        
        .table {
            font-size: 0.85rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .user-dropdown img {
            width: 35px;
            height: 35px;
        }
        
        .card h3 {
            font-size: 1.25rem;
        }
        
        .card h2 {
            font-size: 1.75rem;
        }
        
        .card h5 {
            font-size: 1rem;
        }
        
        .table {
            font-size: 0.75rem;
        }
        
        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }
</style>

<?php
pg_close($conn);
include '../includes/member_footer.php';
?>
