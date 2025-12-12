<?php
// Admin Kelola Jurusan
require_once 'auth_check.php';
require_once __DIR__ . '/../includes/config.php';

$page_title = 'Kelola Jurusan';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';

$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // Add new jurusan
        if ($action === 'add') {
            $nama_jurusan = trim($_POST['nama_jurusan']);
            $kode_jurusan = trim($_POST['kode_jurusan']);
            $deskripsi = trim($_POST['deskripsi']);
            
            if (empty($nama_jurusan)) {
                $error_msg = 'Nama jurusan harus diisi!';
            } else {
                // Check for duplicate
                $check_query = "SELECT COUNT(*) as total FROM jurusan WHERE nama_jurusan = $1";
                $check_result = pg_query_params($conn, $check_query, array($nama_jurusan));
                $check_data = pg_fetch_assoc($check_result);
                
                if ($check_data['total'] > 0) {
                    $error_msg = 'Nama jurusan sudah ada!';
                } else {
                    $query = "INSERT INTO jurusan (nama_jurusan, kode_jurusan, deskripsi, is_active) VALUES ($1, $2, $3, TRUE)";
                    $result = pg_query_params($conn, $query, array($nama_jurusan, $kode_jurusan, $deskripsi));
                    
                    if ($result) {
                        $success_msg = 'Jurusan berhasil ditambahkan!';
                    } else {
                        $error_msg = 'Gagal menambahkan jurusan!';
                    }
                }
            }
        }
        
        // Edit jurusan
        elseif ($action === 'edit') {
            $id = (int)$_POST['id'];
            $nama_jurusan = trim($_POST['nama_jurusan']);
            $kode_jurusan = trim($_POST['kode_jurusan']);
            $deskripsi = trim($_POST['deskripsi']);
            $is_active = isset($_POST['is_active']) ? TRUE : FALSE;
            
            if (empty($nama_jurusan)) {
                $error_msg = 'Nama jurusan harus diisi!';
            } else {
                // Check for duplicate (exclude current id)
                $check_query = "SELECT COUNT(*) as total FROM jurusan WHERE nama_jurusan = $1 AND id != $2";
                $check_result = pg_query_params($conn, $check_query, array($nama_jurusan, $id));
                $check_data = pg_fetch_assoc($check_result);
                
                if ($check_data['total'] > 0) {
                    $error_msg = 'Nama jurusan sudah ada!';
                } else {
                    $query = "UPDATE jurusan SET nama_jurusan = $1, kode_jurusan = $2, deskripsi = $3, is_active = $4, updated_at = NOW() WHERE id = $5";
                    $result = pg_query_params($conn, $query, array($nama_jurusan, $kode_jurusan, $deskripsi, $is_active ? 't' : 'f', $id));
                    
                    if ($result) {
                        $success_msg = 'Jurusan berhasil diperbarui!';
                    } else {
                        $error_msg = 'Gagal memperbarui jurusan!';
                    }
                }
            }
        }
        
        // Delete jurusan
        elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            // Check if there are mahasiswa using this jurusan
            $check_query = "SELECT COUNT(*) as total FROM mahasiswa WHERE jurusan_id = $1";
            $check_result = pg_query_params($conn, $check_query, array($id));
            $check_data = pg_fetch_assoc($check_result);
            
            if ($check_data['total'] > 0) {
                $error_msg = 'Tidak dapat menghapus jurusan karena masih ada ' . $check_data['total'] . ' mahasiswa terdaftar dengan jurusan ini!';
            } else {
                $query = "DELETE FROM jurusan WHERE id = $1";
                $result = pg_query_params($conn, $query, array($id));
                
                if ($result) {
                    $success_msg = 'Jurusan berhasil dihapus!';
                } else {
                    $error_msg = 'Gagal menghapus jurusan!';
                }
            }
        }
        
        // Toggle status
        elseif ($action === 'toggle') {
            $id = (int)$_POST['id'];
            
            $query = "UPDATE jurusan SET is_active = NOT is_active, updated_at = NOW() WHERE id = $1";
            $result = pg_query_params($conn, $query, array($id));
            
            if ($result) {
                $success_msg = 'Status jurusan berhasil diubah!';
            } else {
                $error_msg = 'Gagal mengubah status jurusan!';
            }
        }
    }
}

// Get all jurusan
$query = "SELECT j.*, 
          (SELECT COUNT(*) FROM mahasiswa WHERE jurusan_id = j.id) as total_mahasiswa
          FROM jurusan j 
          ORDER BY j.nama_jurusan ASC";
$result = pg_query($conn, $query);
$jurusan_list = [];
while ($row = pg_fetch_assoc($result)) {
    $jurusan_list[] = $row;
}

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total,
    COUNT(*) FILTER (WHERE is_active = true) as active,
    COUNT(*) FILTER (WHERE is_active = false) as inactive
    FROM jurusan";
$stats_result = pg_query($conn, $stats_query);
$stats = pg_fetch_assoc($stats_result);
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Kelola Jurusan</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Jurusan</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        
        <!-- Success/Error Messages -->
        <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small">Total Jurusan</div>
                            <div class="h3 mb-0"><?php echo $stats['total']; ?></div>
                        </div>
                        <i class="bi bi-building fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small">Jurusan Aktif</div>
                            <div class="h3 mb-0"><?php echo $stats['active']; ?></div>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-secondary text-white">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small">Jurusan Tidak Aktif</div>
                            <div class="h3 mb-0"><?php echo $stats['inactive']; ?></div>
                        </div>
                        <i class="bi bi-pause-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>Daftar Jurusan
                    </h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Jurusan
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Jurusan Table -->
        <div class="card">
            <div class="card-body">
                <?php if (count($jurusan_list) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Jurusan</th>
                                <th>Kode</th>
                                <th>Deskripsi</th>
                                <th>Mahasiswa</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($jurusan_list as $row): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['nama_jurusan']); ?></strong></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['kode_jurusan'] ?: '-'); ?></span></td>
                                <td>
                                    <?php 
                                    $desc = htmlspecialchars($row['deskripsi'] ?: '-');
                                    echo strlen($desc) > 50 ? substr($desc, 0, 50) . '...' : $desc;
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $row['total_mahasiswa']; ?> mahasiswa</span>
                                </td>
                                <td>
                                    <?php if ($row['is_active'] === 't' || $row['is_active'] === true): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                onclick="editJurusan(<?php echo htmlspecialchars(json_encode($row)); ?>)"
                                                title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <!-- Toggle Status Button -->
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-<?php echo ($row['is_active'] === 't' || $row['is_active'] === true) ? 'secondary' : 'success'; ?>"
                                                    title="<?php echo ($row['is_active'] === 't' || $row['is_active'] === true) ? 'Nonaktifkan' : 'Aktifkan'; ?>">
                                                <i class="bi bi-<?php echo ($row['is_active'] === 't' || $row['is_active'] === true) ? 'pause' : 'play'; ?>"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Delete Button -->
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_jurusan']); ?>', <?php echo $row['total_mahasiswa']; ?>)"
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
                <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-building" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">Belum ada data jurusan</p>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Jurusan Pertama
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
    
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Jurusan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="add_nama_jurusan" class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_nama_jurusan" name="nama_jurusan" required placeholder="Contoh: Teknik Informatika">
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_kode_jurusan" class="form-label">Kode Jurusan</label>
                        <input type="text" class="form-control" id="add_kode_jurusan" name="kode_jurusan" placeholder="Contoh: TI" maxlength="20">
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="add_deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi singkat tentang jurusan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label for="edit_nama_jurusan" class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_jurusan" name="nama_jurusan" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_kode_jurusan" class="form-label">Kode Jurusan</label>
                        <input type="text" class="form-control" id="edit_kode_jurusan" name="kode_jurusan" maxlength="20">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                        <label class="form-check-label" for="edit_is_active">
                            Aktif (Tampil di form pendaftaran)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
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
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    
                    <p>Apakah Anda yakin ingin menghapus jurusan:</p>
                    <h5 id="delete_nama_jurusan" class="text-danger"></h5>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Jurusan hanya dapat dihapus jika tidak ada mahasiswa yang terdaftar.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-1"></i>Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editJurusan(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_nama_jurusan').value = data.nama_jurusan;
    document.getElementById('edit_kode_jurusan').value = data.kode_jurusan || '';
    document.getElementById('edit_deskripsi').value = data.deskripsi || '';
    document.getElementById('edit_is_active').checked = (data.is_active === 't' || data.is_active === true);
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function confirmDelete(id, nama, totalMahasiswa) {
    if (totalMahasiswa > 0) {
        alert('Tidak dapat menghapus jurusan ini karena masih ada ' + totalMahasiswa + ' mahasiswa terdaftar!');
        return;
    }
    
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_nama_jurusan').textContent = nama;
    
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
