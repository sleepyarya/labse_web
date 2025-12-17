<?php
// Admin Manage Mahasiswa View
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../controllers/mahasiswaController.php';

$controller = new MahasiswaController();
$page_title = 'Kelola Mahasiswa';

// Handle actions
$success_message = '';
$error_message = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];
    $admin_id = $_SESSION['admin_id'];
    
    if ($action === 'approve') {
        $result = $controller->approve($id, $admin_id);
        if ($result['success']) {
            $_SESSION['mahasiswa_success'] = $result['message'];
        } else {
            $_SESSION['mahasiswa_error'] = $result['message'];
        }
        
        // Redirect without action parameters to prevent loop
        $redirect_params = array_filter([
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'page' => $_GET['page'] ?? ''
        ]);
        $redirect_url = $_SERVER['PHP_SELF'];
        if (!empty($redirect_params)) {
            $redirect_url .= '?' . http_build_query($redirect_params);
        }
        header('Location: ' . $redirect_url);
        exit();
        
    } elseif ($action === 'reject') {
        $reason = $_GET['reason'] ?? '';
        $result = $controller->reject($id, $admin_id, $reason);
        if ($result['success']) {
            $_SESSION['mahasiswa_success'] = $result['message'];
        } else {
            $_SESSION['mahasiswa_error'] = $result['message'];
        }
        
        // Redirect without action parameters to prevent loop
        $redirect_params = array_filter([
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'page' => $_GET['page'] ?? ''
        ]);
        $redirect_url = $_SERVER['PHP_SELF'];
        if (!empty($redirect_params)) {
            $redirect_url .= '?' . http_build_query($redirect_params);
        }
        header('Location: ' . $redirect_url);
        exit();
    } elseif ($action === 'delete') {
        $controller->delete($id);
    }
}

// Handle rejection with reason
if (isset($_POST['reject_mahasiswa'])) {
    $id = $_POST['mahasiswa_id'];
    $reason = trim($_POST['rejection_reason']);
    $admin_id = $_SESSION['admin_id'];
    
    $result = $controller->reject($id, $admin_id, $reason);
    if ($result['success']) {
        $_SESSION['mahasiswa_success'] = $result['message'];
    } else {
        $_SESSION['mahasiswa_error'] = $result['message'];
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Get messages from session
if (isset($_SESSION['mahasiswa_success'])) {
    $success_message = $_SESSION['mahasiswa_success'];
    unset($_SESSION['mahasiswa_success']);
}
if (isset($_SESSION['mahasiswa_error'])) {
    $error_message = $_SESSION['mahasiswa_error'];
    unset($_SESSION['mahasiswa_error']);
}

// Get pagination and search parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Get mahasiswa data
$result = $controller->getAll($page, 10, $search, $status);
$mahasiswa = $result['mahasiswa'];
$total_pages = $result['total_pages'];
$current_page = $result['current_page'];
$total_records = $result['total_records'];

// Get statistics
$stats = $controller->getStatistics();

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
            <h4 class="mb-0">Kelola Mahasiswa</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Mahasiswa</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="small">Total Mahasiswa</div>
                                <div class="h3 mb-0"><?php echo $stats['total']; ?></div>
                            </div>
                            <i class="bi bi-people fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="small">Menunggu Persetujuan</div>
                                <div class="h3 mb-0"><?php echo $stats['by_status']['pending'] ?? 0; ?></div>
                            </div>
                            <i class="bi bi-clock fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="small">Disetujui</div>
                                <div class="h3 mb-0"><?php echo $stats['by_status']['approved'] ?? 0; ?></div>
                            </div>
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card bg-danger text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="small">Ditolak</div>
                                <div class="h3 mb-0"><?php echo $stats['by_status']['rejected'] ?? 0; ?></div>
                            </div>
                            <i class="bi bi-x-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Success/Error Messages -->
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            <?php 
            switch($success) {
                case 'add': echo 'Data mahasiswa berhasil ditambahkan!'; break;
                case 'edit': echo 'Data mahasiswa berhasil diperbarui!'; break;
                case 'delete': echo 'Data mahasiswa berhasil dihapus!'; break;
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
                case 'delete': echo 'Gagal menghapus data mahasiswa!'; break;
                case 'notfound': echo 'Data mahasiswa tidak ditemukan!'; break;
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
                            <i class="bi bi-people me-2"></i>Daftar Mahasiswa
                            <span class="badge bg-warning ms-2"><?php echo $total_records; ?> Total</span>
                        </h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="mahasiswa_form.php" class="btn btn-warning">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Mahasiswa
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filter & Search -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-funnel me-2"></i>Filter & Pencarian
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Cari Mahasiswa</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Nama, NIM, jurusan, atau email..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status Persetujuan</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Menunggu Persetujuan</option>
                            <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Disetujui</option>
                            <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                        <a href="manage_mahasiswa.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Mahasiswa Table -->
        <div class="card">
            <div class="card-body">
                <?php if (count($mahasiswa) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>NIM</th>
                                <th>Jurusan</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Tanggal Daftar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mahasiswa as $row): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['nama']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($row['nim']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($row['jurusan']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <?php 
                                    $status = $row['status_approval'] ?? 'pending';
                                    $status_badges = [
                                        'pending' => 'bg-warning',
                                        'approved' => 'bg-success', 
                                        'rejected' => 'bg-danger'
                                    ];
                                    $status_text = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak'
                                    ];
                                    $badge_class = $status_badges[$status] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo $status_text[$status] ?? 'Unknown'; ?>
                                    </span>
                                    <?php if ($status === 'approved' && $row['approved_by_name']): ?>
                                        <br><small class="text-muted">oleh <?php echo htmlspecialchars($row['approved_by_name']); ?></small>
                                    <?php endif; ?>
                                    <?php if ($status === 'rejected' && $row['rejection_reason']): ?>
                                        <br><small class="text-danger" title="<?php echo htmlspecialchars($row['rejection_reason']); ?>">
                                            Alasan: <?php echo substr(htmlspecialchars($row['rejection_reason']), 0, 30) . '...'; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                    <br>
                                    <small class="text-muted"><?php echo date('H:i', strtotime($row['created_at'])); ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <!-- Detail Button -->
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="showDetail(<?php echo htmlspecialchars(json_encode($row)); ?>)" 
                                                title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        
                                        <?php if ($status === 'pending'): ?>
                                        <!-- Approve Button -->
                                        <a href="?action=approve&id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-outline-success" 
                                           onclick="return confirm('Yakin ingin menyetujui mahasiswa ini?')"
                                           title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                        
                                        <!-- Reject Button -->
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="showRejectModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama']); ?>')"
                                                title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <!-- Delete Button -->
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
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
                        <?php 
                        $query_params = http_build_query(array_filter([
                            'search' => $search,
                            'status' => $status
                        ]));
                        $query_string = $query_params ? '&' . $query_params : '';
                        ?>
                        
                        <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?><?php echo $query_string; ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $query_string; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?><?php echo $query_string; ?>">
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
                        <?php echo $search ? 'Tidak ada mahasiswa yang ditemukan dengan kata kunci "' . htmlspecialchars($search) . '"' : 'Belum ada data mahasiswa'; ?>
                    </p>
                    <a href="mahasiswa_form.php" class="btn btn-warning mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Mahasiswa Pertama
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
    
</div>
<!-- End Admin Content -->

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle me-2"></i>Tolak Mahasiswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="mahasiswa_id" id="reject_mahasiswa_id">
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Anda akan menolak pendaftaran mahasiswa <strong id="reject_mahasiswa_name"></strong>.
                    </div>
                    
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                  rows="4" required placeholder="Jelaskan alasan penolakan..."></textarea>
                        <div class="form-text">Alasan ini akan disimpan dan dapat dilihat oleh admin lain.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="reject_mahasiswa" class="btn btn-danger">
                        <i class="bi bi-x-lg me-1"></i>Tolak Mahasiswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, nama) {
    if (confirm('Apakah Anda yakin ingin menghapus data mahasiswa "' + nama + '"?\n\nData yang dihapus tidak dapat dikembalikan.')) {
        window.location.href = '?action=delete&id=' + id;
    }
}

function showRejectModal(id, nama) {
    document.getElementById('reject_mahasiswa_id').value = id;
    document.getElementById('reject_mahasiswa_name').textContent = nama;
    document.getElementById('rejection_reason').value = '';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function showDetail(data) {
    const content = `
        <div class="row">
            <div class="col-sm-4"><strong>Nama:</strong></div>
            <div class="col-sm-8">${data.nama}</div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-4"><strong>NIM:</strong></div>
            <div class="col-sm-8">${data.nim}</div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-4"><strong>Jurusan:</strong></div>
            <div class="col-sm-8">${data.jurusan}</div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-4"><strong>Email:</strong></div>
            <div class="col-sm-8">${data.email}</div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-4"><strong>Alasan Bergabung:</strong></div>
            <div class="col-sm-8">${data.alasan || '-'}</div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-4"><strong>Tanggal Daftar:</strong></div>
            <div class="col-sm-8">${new Date(data.created_at).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })}</div>
        </div>
    `;
    
    document.getElementById('detailContent').innerHTML = content;
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}
</script>

<?php
pg_close($conn);
include '../includes/admin_footer.php';
?>
