<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

$page_title = 'Penelitian Saya';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';

$member_id = $_SESSION['member_id'];

// Pagination & Filter
$items_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build WHERE clause
$where = "WHERE personil_id = $1";
$params = array($member_id);
$param_count = 2;

if ($search) {
    $where .= " AND (judul ILIKE $$param_count OR deskripsi ILIKE $$param_count)";
    $params[] = "%$search%";
}

// Get total penelitian
$count_query = "SELECT COUNT(*) as total FROM hasil_penelitian $where";
$count_result = pg_query_params($conn, $count_query, $params);
$total_items = pg_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_items / $items_per_page);

// Get penelitian
$query = "SELECT id, judul, tahun, kategori, gambar, file_pdf, created_at 
          FROM hasil_penelitian 
          $where 
          ORDER BY tahun DESC, created_at DESC 
          LIMIT $items_per_page OFFSET $offset";
$result = pg_query_params($conn, $query, $params);
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Penelitian Saya</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Penelitian Saya</li>
                </ol>
            </nav>
        </div>
        <div class="user-dropdown">
            <span class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['member_nama']); ?></strong></span>
            <?php if (!empty($_SESSION['member_foto'])): ?>
                <img src="<?php echo BASE_URL; ?>public/uploads/personil/<?php echo htmlspecialchars($_SESSION['member_foto']); ?>" 
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
                <i class="bi bi-journal-text me-2"></i>Daftar Hasil Penelitian
            </h5>
            <a href="add_penelitian.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Penelitian
            </a>
        </div>
        <div class="card-body p-0">
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?php 
                    if ($_GET['success'] == 'add') echo 'Penelitian berhasil ditambahkan!';
                    elseif ($_GET['success'] == 'edit') echo 'Penelitian berhasil diperbarui!';
                    elseif ($_GET['success'] == 'delete') echo 'Penelitian berhasil dihapus!';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php 
                    if ($_GET['error'] == 'unauthorized') echo 'Anda tidak memiliki akses untuk mengubah penelitian ini!';
                    else echo 'Terjadi kesalahan. Silakan coba lagi.';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Search -->
            <?php if ($total_items > 0): ?>
            <div class="p-3 border-bottom">
                <form method="GET" class="row g-2">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Cari judul atau deskripsi..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            
            <?php if (pg_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Cover</th>
                                <th>Judul Penelitian</th>
                                <th width="10%">Tahun</th>
                                <th width="12%">Kategori</th>
                                <th width="18%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = $offset + 1;
                            while ($item = pg_fetch_assoc($result)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <?php if (!empty($item['gambar'])): ?>
                                        <img src="<?php echo BASE_URL; ?>/public/uploads/penelitian/<?php echo htmlspecialchars($item['gambar']); ?>" 
                                             alt="Cover" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;"
                                             onerror="this.src='<?php echo BASE_URL; ?>/public/img/no-image.png'">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white text-center" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                            <i class="bi bi-journal-text"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['judul']); ?></strong>
                                    <?php if ($item['file_pdf']): ?>
                                    <br>
                                    <small class="text-muted">
                                        <i class="bi bi-file-pdf text-danger me-1"></i>PDF tersedia
                                    </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?php echo $item['tahun']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item['kategori']): ?>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars($item['kategori']); ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="view_penelitian.php?id=<?php echo $item['id']; ?>" 
                                           class="btn btn-outline-info" title="Lihat Detail" data-bs-toggle="tooltip">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit_penelitian.php?id=<?php echo $item['id']; ?>" 
                                           class="btn btn-outline-primary" title="Edit" data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_penelitian.php?id=<?php echo $item['id']; ?>" 
                                           class="btn btn-outline-danger" title="Hapus" data-bs-toggle="tooltip"
                                           onclick="return confirm('Yakin ingin menghapus penelitian ini?\n\nFile gambar dan PDF akan dihapus permanent.')">
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
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">
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
                    <h5 class="mt-3">
                        <?php echo $search ? 'Tidak Ada Hasil Pencarian' : 'Belum Ada Penelitian'; ?>
                    </h5>
                    <p class="mb-4">
                        <?php echo $search ? 'Coba kata kunci lain' : 'Anda belum menambahkan penelitian apapun'; ?>
                    </p>
                    <?php if (!$search): ?>
                    <a href="add_penelitian.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Penelitian Pertama
                    </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
    
</div>
<!-- End Member Content -->

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
