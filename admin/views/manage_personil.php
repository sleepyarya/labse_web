<?php
// Admin Manage Personil View
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../controllers/personilController.php';

$controller = new PersonilController();
$page_title = 'Kelola Personil';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->delete($_GET['id']);
}

// Get pagination and search parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get personil data
$result = $controller->getAll($page, 10, $search);
$personil = $result['personil'];
$total_pages = $result['total_pages'];
$current_page = $result['current_page'];
$total_records = $result['total_records'];

// Handle success/error messages
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Kelola Personil</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Personil</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        
        <!-- Success/Error Messages -->
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            <?php 
            switch($success) {
                case 'add': echo 'Personil berhasil ditambahkan!'; break;
                case 'edit': echo 'Personil berhasil diperbarui!'; break;
                case 'delete': echo 'Personil berhasil dihapus!'; break;
                default: echo 'Operasi berhasil!';
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php 
            switch($error) {
                case 'delete': echo 'Gagal menghapus personil!'; break;
                case 'notfound': echo 'Personil tidak ditemukan!'; break;
                default: echo 'Terjadi kesalahan!';
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Action Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="bi bi-people me-2"></i>Daftar Personil
                            <span class="badge bg-primary ms-2"><?php echo $total_records; ?> Total</span>
                        </h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="personil_form.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Personil
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Search Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari berdasarkan nama, jabatan, atau email..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Personil Table -->
        <div class="card">
            <div class="card-body">
                <?php if (count($personil) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Email</th>
                                <th>Tanggal Dibuat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($personil as $row): ?>
                            <tr>
                                <td>
                                    <?php if ($row['foto']): ?>
                                        <img src="../../public/uploads/personil/<?php echo htmlspecialchars($row['foto']); ?>" 
                                             class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['nama']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($row['jabatan']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="personil_form.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama']); ?>')" 
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
                        <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">
                        <?php echo $search ? 'Tidak ada personil yang ditemukan dengan kata kunci "' . htmlspecialchars($search) . '"' : 'Belum ada data personil'; ?>
                    </p>
                    <a href="personil_form.php" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Personil Pertama
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
    
</div>
<!-- End Admin Content -->

<script>
function confirmDelete(id, nama) {
    if (confirm('Apakah Anda yakin ingin menghapus personil "' + nama + '"?\n\nData yang dihapus tidak dapat dikembalikan.')) {
        window.location.href = '?action=delete&id=' + id;
    }
}
</script>

<?php
pg_close($conn);
include '../includes/admin_footer.php';
?>
