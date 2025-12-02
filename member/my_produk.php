<?php
require_once 'auth_check.php';
require_once 'controllers/produkController.php';
require_once '../includes/config.php';

$controller = new MemberProdukController();

// Pagination & Search
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$data = $controller->getMyProduk($page, 10, $search);
$items = $data['items'];
$total_pages = $data['total_pages'];
$total_records = $data['total_records'];

$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<div class="member-content">
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Produk Saya</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Produk Saya</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="container-fluid">
        
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" data-aos="fade-down">
            <i class="bi bi-check-circle me-2"></i>
            <?php 
            if ($success == 'add') echo 'Produk berhasil ditambahkan!';
            elseif ($success == 'edit') echo 'Produk berhasil diupdate!';
            elseif ($success == 'delete') echo 'Produk berhasil dihapus!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" data-aos="fade-down">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php 
            if ($error == 'unauthorized') echo 'Anda tidak memiliki akses!';
            else echo htmlspecialchars($error);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Action Bar -->
        <div class="card mb-4" data-aos="fade-up">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <a href="add_produk.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Produk
                        </a>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                            <?php if ($search): ?>
                            <a href="my_produk.php" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Data Table -->
        <div class="card" data-aos="fade-up" data-aos-delay="100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Daftar Produk (<?php echo $total_records; ?>)</h5>
            </div>
            <div class="card-body">
                
                <?php if (count($items) > 0): ?>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Gambar</th>
                                <th width="25%">Nama Produk</th>
                                <th width="8%">Tahun</th>
                                <th width="12%">Kategori</th>
                                <th width="18%">Teknologi</th>
                                <th width="10%">Link</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = ($page - 1) * 10 + 1;
                            foreach ($items as $item): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <?php if ($item['gambar']): ?>
                                    <img src="<?php echo BASE_URL; ?>/uploads/produk/<?php echo htmlspecialchars($item['gambar']); ?>" 
                                         class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 80px; height: 60px; border-radius: 5px;">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['nama_produk']); ?></strong>
                                </td>
                                <td><span class="badge bg-primary"><?php echo $item['tahun']; ?></span></td>
                                <td>
                                    <?php if ($item['kategori']): ?>
                                    <span class="badge <?php echo $item['kategori'] == 'Hardware' ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo htmlspecialchars($item['kategori']); ?>
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td><small><?php echo htmlspecialchars(substr($item['teknologi'], 0, 50)) . (strlen($item['teknologi']) > 50 ? '...' : ''); ?></small></td>
                                <td>
                                    <?php if ($item['link_demo']): ?>
                                    <a href="<?php echo htmlspecialchars($item['link_demo']); ?>" class="btn btn-sm btn-outline-success" target="_blank" title="Demo">
                                        <i class="bi bi-play-circle"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="view_produk.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit_produk.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars(addslashes($item['nama_produk'])); ?>')" 
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
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
                <?php endif; ?>
                
                <?php else: ?>
                
                <div class="text-center py-5">
                    <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">Belum Ada Produk</h5>
                    <p class="text-muted">Silakan tambahkan produk pertama Anda</p>
                    <a href="add_produk.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Produk
                    </a>
                </div>
                
                <?php endif; ?>
                
            </div>
        </div>
        
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus produk:</p>
                <h6 id="deleteNama" class="text-danger"></h6>
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

<script>
function confirmDelete(id, nama) {
    document.getElementById('deleteNama').textContent = nama;
    document.getElementById('confirmDeleteBtn').href = 'delete_produk.php?id=' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

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
include 'includes/member_footer.php';
?>
