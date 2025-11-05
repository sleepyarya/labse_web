<?php
// Proteksi halaman - harus login dulu
require_once 'auth_check.php';
require_once '../includes/config.php';

$page_title = 'Kelola Mahasiswa';
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
$where = $search ? "WHERE nama ILIKE '%$search%' OR nim ILIKE '%$search%' OR jurusan ILIKE '%$search%' OR email ILIKE '%$search%'" : '';

// Get total records
$count_query = "SELECT COUNT(*) as total FROM mahasiswa $where";
$count_result = pg_query($conn, $count_query);
$total_records = pg_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Get mahasiswa data
$query = "SELECT * FROM mahasiswa $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = pg_query($conn, $query);
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Kelola Pendaftar Mahasiswa</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Mahasiswa</li>
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
            if ($success == 'add') echo 'Data mahasiswa berhasil ditambahkan!';
            elseif ($success == 'edit') echo 'Data mahasiswa berhasil diupdate!';
            elseif ($success == 'delete') echo 'Data mahasiswa berhasil dihapus!';
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
                        <a href="add_mahasiswa.php" class="btn btn-warning">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Data Mahasiswa
                        </a>
                        <span class="ms-3 text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Total: <strong><?php echo $total_records; ?></strong> pendaftar
                        </span>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, NIM, jurusan, atau email..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-outline-warning">
                                <i class="bi bi-search"></i>
                            </button>
                            <?php if ($search): ?>
                            <a href="manage_mahasiswa.php" class="btn btn-outline-secondary">
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
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Daftar Mahasiswa Pendaftar</h5>
            </div>
            <div class="card-body">
                
                <?php if (pg_num_rows($result) > 0): ?>
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Nama</th>
                                <th width="12%">NIM</th>
                                <th width="15%">Jurusan</th>
                                <th width="18%">Email</th>
                                <th width="15%">Tanggal Daftar</th>
                                <th width="15%" class="text-center">Aksi</th>
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
                                    <strong><?php echo htmlspecialchars($row['nama']); ?></strong>
                                    <?php if ($row['alasan']): ?>
                                    <br><small class="text-muted">
                                        <i class="bi bi-chat-left-quote"></i> 
                                        <?php 
                                        $alasan = htmlspecialchars($row['alasan']);
                                        echo strlen($alasan) > 50 ? substr($alasan, 0, 50) . '...' : $alasan; 
                                        ?>
                                    </small>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo htmlspecialchars($row['nim']); ?></code></td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($row['jurusan']); ?></span></td>
                                <td>
                                    <small><?php echo htmlspecialchars($row['email']); ?></small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3"></i>
                                        <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                        <br>
                                        <i class="bi bi-clock"></i>
                                        <?php echo date('H:i', strtotime($row['created_at'])); ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-info btn-view-detail" 
                                                data-id="<?php echo $row['id']; ?>"
                                                data-nama="<?php echo htmlspecialchars($row['nama']); ?>"
                                                data-nim="<?php echo htmlspecialchars($row['nim']); ?>"
                                                data-jurusan="<?php echo htmlspecialchars($row['jurusan']); ?>"
                                                data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                                data-alasan="<?php echo htmlspecialchars($row['alasan']); ?>"
                                                data-tanggal="<?php echo date('d M Y H:i', strtotime($row['created_at'])); ?>"
                                                title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a href="edit_mahasiswa.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger btn-confirm-delete" 
                                                data-id="<?php echo $row['id']; ?>"
                                                data-nama="<?php echo htmlspecialchars($row['nama']); ?>"
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
                    <i class="bi bi-person-x text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">Belum ada pendaftar</h5>
                    <p class="text-muted">Data mahasiswa yang mendaftar akan muncul di sini</p>
                    <a href="add_mahasiswa.php" class="btn btn-warning">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Data Mahasiswa
                    </a>
                </div>
                
                <?php endif; ?>
                
            </div>
        </div>
        
    </div>
    
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailModalLabel"><i class="bi bi-person-circle me-2"></i>Detail Pendaftar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small">Nama Lengkap</label>
                            <h6 id="detailNama"></h6>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">NIM</label>
                            <h6><code id="detailNim"></code></h6>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Jurusan</label>
                            <h6 id="detailJurusan"></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small">Email</label>
                            <h6 id="detailEmail"></h6>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Tanggal Daftar</label>
                            <h6 id="detailTanggal"></h6>
                        </div>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="text-muted small d-block mb-2">Alasan Bergabung</label>
                    <div class="p-3 bg-light border rounded" id="detailAlasan" style="white-space: pre-wrap; word-wrap: break-word;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="editFromModal" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-2"></i>Edit Data
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data mahasiswa:</p>
                <h6 id="deleteMahasiswaNama" class="text-danger"></h6>
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
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #fff8e1;
    }
    
    .btn-group .btn {
        transition: all 0.3s ease;
    }
    
    .btn-group .btn:hover {
        transform: translateY(-2px);
    }
    
    code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.9em;
    }
</style>

<script>
// Wait for DOM to be ready
let detailModalInstance = null;
let deleteModalInstance = null;

// Initialize modals after page load
window.addEventListener('load', function() {
    const detailModalEl = document.getElementById('detailModal');
    const deleteModalEl = document.getElementById('deleteModal');
    
    if (detailModalEl) {
        detailModalInstance = new bootstrap.Modal(detailModalEl, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
    }
    
    if (deleteModalEl) {
        deleteModalInstance = new bootstrap.Modal(deleteModalEl, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
    }
});

// View Detail Modal - Using event delegation with preventDefault
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-view-detail');
    if (btn) {
        e.preventDefault();
        e.stopPropagation();
        
        const id = btn.getAttribute('data-id');
        const nama = btn.getAttribute('data-nama');
        const nim = btn.getAttribute('data-nim');
        const jurusan = btn.getAttribute('data-jurusan');
        const email = btn.getAttribute('data-email');
        const alasan = btn.getAttribute('data-alasan');
        const tanggal = btn.getAttribute('data-tanggal');
        
        // Update modal content
        document.getElementById('detailNama').textContent = nama || '';
        document.getElementById('detailNim').textContent = nim || '';
        document.getElementById('detailJurusan').textContent = jurusan || '';
        document.getElementById('detailEmail').textContent = email || '';
        document.getElementById('detailTanggal').textContent = tanggal || '';
        document.getElementById('detailAlasan').textContent = alasan || 'Tidak ada alasan yang diberikan';
        document.getElementById('editFromModal').href = 'edit_mahasiswa.php?id=' + id;
        
        // Show modal using instance or create new one
        try {
            if (detailModalInstance) {
                detailModalInstance.show();
            } else {
                // Fallback: get or create instance
                const modalEl = document.getElementById('detailModal');
                let instance = bootstrap.Modal.getInstance(modalEl);
                if (!instance) {
                    instance = new bootstrap.Modal(modalEl, {
                        backdrop: true,
                        keyboard: true
                    });
                }
                instance.show();
            }
        } catch (error) {
            console.error('Error showing detail modal:', error);
        }
        
        return false;
    }
});

// Confirm Delete Modal - Using event delegation with preventDefault
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-confirm-delete');
    if (btn) {
        e.preventDefault();
        e.stopPropagation();
        
        const id = btn.getAttribute('data-id');
        const nama = btn.getAttribute('data-nama');
        
        // Update modal content
        document.getElementById('deleteMahasiswaNama').textContent = nama || '';
        document.getElementById('confirmDeleteBtn').href = 'delete_mahasiswa.php?id=' + id;
        
        // Show modal using instance or create new one
        try {
            if (deleteModalInstance) {
                deleteModalInstance.show();
            } else {
                // Fallback: get or create instance
                const modalEl = document.getElementById('deleteModal');
                let instance = bootstrap.Modal.getInstance(modalEl);
                if (!instance) {
                    instance = new bootstrap.Modal(modalEl, {
                        backdrop: true,
                        keyboard: true
                    });
                }
                instance.show();
            }
        } catch (error) {
            console.error('Error showing delete modal:', error);
        }
        
        return false;
    }
});

// Prevent modal from closing accidentally
document.addEventListener('shown.bs.modal', function(e) {
    console.log('Modal shown:', e.target.id);
});

document.addEventListener('hidden.bs.modal', function(e) {
    console.log('Modal hidden:', e.target.id);
});

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
