<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

$member_id = $_SESSION['member_id'];
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verify artikel exists and belongs to member (or allow viewing any article for members)
$query = "SELECT a.*, p.nama as penulis_nama, p.jabatan 
          FROM artikel a 
          LEFT JOIN personil p ON a.personil_id = p.id 
          WHERE a.id = $1";
$result = pg_query_params($conn, $query, array($article_id));

if (!$result || pg_num_rows($result) == 0) {
    header('Location: my_articles.php');
    exit();
}

$article = pg_fetch_assoc($result);

$page_title = $article['judul'];
include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Lihat Artikel</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_articles.php">Artikel Saya</a></li>
                    <li class="breadcrumb-item active">Lihat Artikel</li>
                </ol>
            </nav>
        </div>
        <div class="user-dropdown">
            <a href="my_articles.php" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
    
    <!-- Article Content -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Article Header -->
            <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
                <?php if (!empty($article['gambar'])): ?>
                <div class="card-img-top position-relative" style="height: 300px; overflow: hidden;">
                    <img src="<?php echo BASE_URL; ?>/uploads/artikel/<?php echo htmlspecialchars($article['gambar']); ?>" 
                         alt="<?php echo htmlspecialchars($article['judul']); ?>"
                         style="width: 100%; height: 100%; object-fit: cover;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="d-none align-items-center justify-content-center bg-light text-muted" 
                         style="width: 100%; height: 100%; position: absolute; top: 0; left: 0;">
                        <i class="bi bi-image" style="font-size: 3rem;"></i>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="card-body p-4">
                    <h1 class="card-title mb-3"><?php echo htmlspecialchars($article['judul']); ?></h1>
                    
                    <div class="d-flex flex-wrap gap-3 mb-4 text-muted">
                        <div>
                            <i class="bi bi-person-circle me-1"></i>
                            <strong><?php echo htmlspecialchars($article['penulis']); ?></strong>
                            <?php if (!empty($article['jabatan'])): ?>
                                <small class="ms-1">(<?php echo htmlspecialchars($article['jabatan']); ?>)</small>
                            <?php endif; ?>
                        </div>
                        <div>
                            <i class="bi bi-calendar3 me-1"></i>
                            <?php echo date('d F Y', strtotime($article['created_at'])); ?>
                        </div>
                        <div>
                            <i class="bi bi-clock me-1"></i>
                            <?php echo date('H:i', strtotime($article['created_at'])); ?>
                        </div>
                    </div>
                    
                    <?php if ($article['personil_id'] == $member_id): ?>
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <div>
                            <strong>Artikel Anda</strong> - Anda dapat mengedit atau menghapus artikel ini.
                        </div>
                        <div class="ms-auto">
                            <a href="edit_article.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-primary me-2">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <a href="delete_article.php?id=<?php echo $article['id']; ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Yakin ingin menghapus artikel ini?')">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Article Body -->
            <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body p-4">
                    <div class="article-content">
                        <?php echo nl2br(htmlspecialchars($article['isi'])); ?>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="200">
                <a href="my_articles.php" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Artikel
                </a>
                
                <?php if ($article['personil_id'] == $member_id): ?>
                <a href="edit_article.php?id=<?php echo $article['id']; ?>" class="btn btn-primary me-2">
                    <i class="bi bi-pencil me-1"></i>Edit Artikel
                </a>
                <?php endif; ?>
                
                <a href="add_article.php" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i>Buat Artikel Baru
                </a>
            </div>
            
        </div>
    </div>
    
</div>
<!-- End Member Content -->

<style>
    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }
    
    .article-content p {
        margin-bottom: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem !important;
        }
        
        .article-content {
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .d-flex.gap-3 {
            flex-direction: column;
            gap: 0.5rem !important;
        }
        
        .alert .ms-auto {
            margin-left: 0 !important;
            margin-top: 1rem;
        }
        
        .alert .ms-auto .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .member-topbar .user-dropdown .btn {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
        
        .card-img-top {
            height: 200px !important;
        }
        
        h1.card-title {
            font-size: 1.5rem;
        }
    }
</style>

<?php
pg_close($conn);
include 'includes/member_footer.php';
?>
