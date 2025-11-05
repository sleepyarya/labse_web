<?php
// Proteksi halaman - harus login dulu
require_once 'auth_check.php';
require_once '../includes/config.php';

$page_title = 'Kelola Artikel';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';

// Handle success/error messages
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? pg_escape_string($conn, $_GET['search']) : '';
$where = $search ? "WHERE judul ILIKE '%$search%' OR penulis ILIKE '%$search%' OR isi ILIKE '%$search%'" : '';

// Get total records
$count_query = "SELECT COUNT(*) as total FROM artikel $where";
$count_result = pg_query($conn, $count_query);
$total_records = pg_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Get artikel data
$query = "SELECT * FROM artikel $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = pg_query($conn, $query);
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Kelola Artikel</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Artikel</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-down">
            <i class="bi bi-check-circle me-2"></i>
            <?php 
            if ($success == 'add') echo 'Artikel berhasil ditambahkan!';
            elseif ($success == 'edit') echo 'Artikel berhasil diupdate!';
            elseif ($success == 'delete') echo 'Artikel berhasil dihapus!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" data-aos="fade-down">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Action Bar -->
        <div class="card mb-4" data-aos="fade-up">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <a href="add_artikel.php" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Artikel
                        </a>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari judul, penulis, atau konten..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-outline-success">
                                <i class="bi bi-search"></i>
                            </button>
                            <?php if ($search): ?>
                            <a href="manage_artikel.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Data Table -->
        <div class="card" data-aos="fade-up" data-aos-delay="100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Daftar Artikel</h5>
            </div>
            <div class="card-body">
                
                <?php if (pg_num_rows($result) > 0): ?>
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Gambar</th>
                                <th width="25%">Judul</th>
                                <th width="30%">Isi</th>
                                <th width="12%">Penulis</th>
                                <th width="10%">Tanggal</th>
                                <th width="8%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = $offset + 1;
                            while ($row = pg_fetch_assoc($result)): 
                            ?>
                            <tr class="fade-in">
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <?php if ($row['gambar']): ?>
                                    <img src="<?php echo BASE_URL . '/uploads/artikel/' . htmlspecialchars($row['gambar']); ?>" 
                                         alt="Gambar" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 80px; height: 60px; border-radius: 5px;">
                                        <i class="bi bi-image fs-5"></i>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['judul']); ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php 
                                        $isi = strip_tags($row['isi']);
                                        echo strlen($isi) > 100 ? substr($isi, 0, 100) . '...' : $isi; 
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars($row['penulis']); ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="edit_artikel.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['judul'])); ?>')" 
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Previous -->
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        
                        <!-- Pages -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <!-- Next -->
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
                
                <?php else: ?>
                
                <div class="text-center py-5">
                    <i class="bi bi-file-text text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">Tidak ada artikel</h5>
                    <p class="text-muted">Silakan tambahkan artikel baru</p>
                    <a href="add_artikel.php" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Artikel
                    </a>
                </div>
                
                <?php endif; ?>
                
            </div>
        </div>
        
    </div>
    
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus artikel:</p>
                <h6 id="deleteArtikelJudul" class="text-danger"></h6>
                <p class="text-muted mb-0"><small>Data yang dihapus tidak dapat dikembalikan!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="bi bi-trash me-2"></i>Ya, Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .table tbody tr {
        transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .btn-group .btn {
        transition: all 0.3s ease;
    }
    
    .btn-group .btn:hover {
        transform: translateY(-2px);
    }
</style>

<script>
function confirmDelete(id, judul) {
    document.getElementById('deleteArtikelJudul').textContent = judul;
    document.getElementById('confirmDeleteBtn').href = 'delete_artikel.php?id=' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Auto hide alerts after 5 seconds
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>
