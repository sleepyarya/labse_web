<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

$page_title = 'Artikel Saya';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';

$member_id = $_SESSION['member_id'];

// Pagination
$items_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get total articles
$count_query = "SELECT COUNT(*) as total FROM artikel WHERE personil_id = $1";
$count_result = pg_query_params($conn, $count_query, array($member_id));
$total_articles = pg_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_articles / $items_per_page);

// Get articles
$query = "SELECT id, judul, penulis, gambar, created_at 
          FROM artikel 
          WHERE personil_id = $1 
          ORDER BY created_at DESC 
          LIMIT $2 OFFSET $3";
$result = pg_query_params($conn, $query, array($member_id, $items_per_page, $offset));
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Artikel Saya</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Artikel Saya</li>
                </ol>
            </nav>
        </div>
        <div class="user-dropdown">
            <span class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['member_nama']); ?></strong></span>
            <?php if (!empty($_SESSION['member_foto'])): ?>
                <img src="<?php echo BASE_URL; ?>/uploads/personil/<?php echo htmlspecialchars($_SESSION['member_foto']); ?>" 
                     alt="Profile" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="user-avatar-placeholder" style="display: none;">
                    <?php echo strtoupper(substr($_SESSION['member_nama'], 0, 1)); ?>
                </div>
            <?php else: ?>
                <div class="user-avatar-placeholder">
                    <?php echo strtoupper(substr($_SESSION['member_nama'], 0, 1)); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Content Card -->
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">
                <i class="bi bi-file-text me-2"></i>Daftar Artikel
            </h5>
            <a href="add_article.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Buat Artikel Baru
            </a>
        </div>
        <div class="card-body p-0">
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?php 
                    if ($_GET['success'] == 'add') echo 'Artikel berhasil ditambahkan!';
                    elseif ($_GET['success'] == 'edit') echo 'Artikel berhasil diperbarui!';
                    elseif ($_GET['success'] == 'delete') echo 'Artikel berhasil dihapus!';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (pg_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Gambar</th>
                                <th>Judul Artikel</th>
                                <th width="15%">Tanggal</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = $offset + 1;
                            while ($article = pg_fetch_assoc($result)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <?php if (!empty($article['gambar'])): ?>
                                        <img src="<?php echo BASE_URL; ?>/public/uploads/artikel/<?php echo htmlspecialchars($article['gambar']); ?>" 
                                             alt="Thumbnail" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;"
                                             onerror="this.src='<?php echo BASE_URL; ?>/public/img/no-image.png'">
                                    <?php else: ?>
                                        <div class="bg-light text-center" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($article['judul']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($article['penulis']); ?>
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?php echo date('d M Y', strtotime($article['created_at'])); ?>
                                        <br>
                                        <i class="bi bi-clock me-1"></i>
                                        <?php echo date('H:i', strtotime($article['created_at'])); ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="view_article.php?id=<?php echo $article['id']; ?>" 
                                           class="btn btn-outline-info" title="Lihat Artikel" data-bs-toggle="tooltip">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit_article.php?id=<?php echo $article['id']; ?>" 
                                           class="btn btn-outline-primary" title="Edit Artikel" data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_article.php?id=<?php echo $article['id']; ?>" 
                                           class="btn btn-outline-danger" title="Hapus Artikel" data-bs-toggle="tooltip"
                                           onclick="return confirm('Yakin ingin menghapus artikel ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="card-footer bg-white py-3">
                    <nav>
                        <ul class="pagination pagination-sm mb-0 justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Belum Ada Artikel</h5>
                    <p class="mb-4">Anda belum membuat artikel apapun</p>
                    <a href="add_article.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Buat Artikel Pertama
                    </a>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
    
</div>
<!-- End Member Content -->

<style>
    /* Responsive Table Styles */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .card-header .btn {
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .table {
            font-size: 0.85rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem;
        }
        
        .btn-group {
            flex-direction: row;
            width: auto;
        }
        
        .btn-group .btn {
            border-radius: 0.25rem !important;
            margin-right: 0.25rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .table {
            font-size: 0.75rem;
            min-width: 700px;
        }
        
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
</style>

<script>
    // Initialize Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<?php
pg_close($conn);
include 'includes/member_footer.php';
?>
