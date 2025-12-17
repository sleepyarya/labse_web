<?php
// Admin Kelola Kategori Penelitian
require_once 'auth_check.php';
require_once __DIR__ . '/../includes/config.php';

$page_title = 'Kelola Kategori Penelitian';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';

$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // Add new kategori
        if ($action === 'add') {
            $nama_kategori = trim($_POST['nama_kategori']);
            $deskripsi = trim($_POST['deskripsi']);
            $warna = trim($_POST['warna']) ?: '#0d6efd';
            
            // Generate slug
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $nama_kategori));
            
            if (empty($nama_kategori)) {
                $error_msg = 'Nama kategori harus diisi!';
            } else {
                // Check for duplicate
                $check_query = "SELECT COUNT(*) as total FROM kategori_penelitian WHERE nama_kategori = $1";
                $check_result = pg_query_params($conn, $check_query, array($nama_kategori));
                $check_data = pg_fetch_assoc($check_result);
                
                if ($check_data['total'] > 0) {
                    $error_msg = 'Nama kategori sudah ada!';
                } else {
                    $query = "INSERT INTO kategori_penelitian (nama_kategori, slug, deskripsi, warna, is_active) VALUES ($1, $2, $3, $4, TRUE)";
                    $result = pg_query_params($conn, $query, array($nama_kategori, $slug, $deskripsi, $warna));
                    
                    if ($result) {
                        $success_msg = 'Kategori berhasil ditambahkan!';
                    } else {
                        $error_msg = 'Gagal menambahkan kategori!';
                    }
                }
            }
        }
        
        // Edit kategori
        elseif ($action === 'edit') {
            $id = (int)$_POST['id'];
            $nama_kategori = trim($_POST['nama_kategori']);
            $deskripsi = trim($_POST['deskripsi']);
            $warna = trim($_POST['warna']) ?: '#0d6efd';
            $is_active = isset($_POST['is_active']) ? TRUE : FALSE;
            
            // Generate slug
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $nama_kategori));
            
            if (empty($nama_kategori)) {
                $error_msg = 'Nama kategori harus diisi!';
            } else {
                // Check for duplicate (exclude current id)
                $check_query = "SELECT COUNT(*) as total FROM kategori_penelitian WHERE nama_kategori = $1 AND id != $2";
                $check_result = pg_query_params($conn, $check_query, array($nama_kategori, $id));
                $check_data = pg_fetch_assoc($check_result);
                
                if ($check_data['total'] > 0) {
                    $error_msg = 'Nama kategori sudah ada!';
                } else {
                    $query = "UPDATE kategori_penelitian SET nama_kategori = $1, slug = $2, deskripsi = $3, warna = $4, is_active = $5, updated_at = NOW() WHERE id = $6";
                    $result = pg_query_params($conn, $query, array($nama_kategori, $slug, $deskripsi, $warna, $is_active ? 't' : 'f', $id));
                    
                    if ($result) {
                        $success_msg = 'Kategori berhasil diperbarui!';
                    } else {
                        $error_msg = 'Gagal memperbarui kategori!';
                    }
                }
            }
        }
        
        // Delete kategori
        elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            // Check if there are penelitian using this category
            $check_query = "SELECT COUNT(*) as total FROM hasil_penelitian WHERE kategori_id = $1";
            $check_result = pg_query_params($conn, $check_query, array($id));
            $check_data = pg_fetch_assoc($check_result);
            
            if ($check_data['total'] > 0) {
                $error_msg = 'Tidak dapat menghapus kategori karena masih ada ' . $check_data['total'] . ' penelitian menggunakan kategori ini!';
            } else {
                $query = "DELETE FROM kategori_penelitian WHERE id = $1";
                $result = pg_query_params($conn, $query, array($id));
                
                if ($result) {
                    $success_msg = 'Kategori berhasil dihapus!';
                } else {
                    $error_msg = 'Gagal menghapus kategori!';
                }
            }
        }
        
        // Toggle status
        elseif ($action === 'toggle') {
            $id = (int)$_POST['id'];
            
            $query = "UPDATE kategori_penelitian SET is_active = NOT is_active, updated_at = NOW() WHERE id = $1";
            $result = pg_query_params($conn, $query, array($id));
            
            if ($result) {
                $success_msg = 'Status kategori berhasil diubah!';
            } else {
                $error_msg = 'Gagal mengubah status kategori!';
            }
        }
    }
}

// Get all kategori
$query = "SELECT k.*, 
          (SELECT COUNT(*) FROM hasil_penelitian WHERE kategori_id = k.id) as total_penelitian
          FROM kategori_penelitian k 
          ORDER BY k.nama_kategori ASC";
$result = pg_query($conn, $query);
$kategori_list = [];
while ($row = pg_fetch_assoc($result)) {
    $kategori_list[] = $row;
}

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total,
    COUNT(*) FILTER (WHERE is_active = true) as active,
    COUNT(*) FILTER (WHERE is_active = false) as inactive
    FROM kategori_penelitian";
$stats_result = pg_query($conn, $stats_query);
$stats = pg_fetch_assoc($stats_result);
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Kelola Kategori Penelitian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Kategori Penelitian</li>
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
                            <div class="small">Total Kategori</div>
                            <div class="h3 mb-0"><?php echo $stats['total']; ?></div>
                        </div>
                        <i class="bi bi-tags fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small">Kategori Aktif</div>
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
                            <div class="small">Kategori Tidak Aktif</div>
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
                        <i class="bi bi-tags me-2"></i>Daftar Kategori Penelitian
                    </h5>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Kategori Table -->
        <div class="card">
            <div class="card-body">
                <?php if (count($kategori_list) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Warna</th>
                                <th>Deskripsi</th>
                                <th>Penelitian</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($kategori_list as $row): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo htmlspecialchars($row['warna']); ?>;">
                                        <?php echo htmlspecialchars($row['nama_kategori']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 24px; height: 24px; border-radius: 4px; background-color: <?php echo htmlspecialchars($row['warna']); ?>;"></div>
                                        <code><?php echo htmlspecialchars($row['warna']); ?></code>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    $desc = htmlspecialchars($row['deskripsi'] ?: '-');
                                    echo strlen($desc) > 50 ? substr($desc, 0, 50) . '...' : $desc;
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $row['total_penelitian']; ?> penelitian</span>
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
                                                onclick="editKategori(<?php echo htmlspecialchars(json_encode($row)); ?>)"
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
                                                onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_kategori']); ?>', <?php echo $row['total_penelitian']; ?>)"
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
                    <i class="bi bi-tags" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">Belum ada kategori penelitian</p>
                    <button type="button" class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Kategori Pertama
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
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="add_nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_nama_kategori" name="nama_kategori" required placeholder="Contoh: Fundamental">
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_warna" class="form-label">Warna Badge</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="add_warna" name="warna" value="#0d6efd">
                            <input type="text" class="form-control" id="add_warna_text" value="#0d6efd" readonly>
                        </div>
                        <small class="text-muted">Pilih warna untuk badge kategori</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="add_deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi singkat tentang kategori..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
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
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label for="edit_nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_warna" class="form-label">Warna Badge</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="edit_warna" name="warna" value="#0d6efd">
                            <input type="text" class="form-control" id="edit_warna_text" value="#0d6efd" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                        <label class="form-check-label" for="edit_is_active">
                            Aktif (Tampil di pilihan kategori)
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
                    
                    <p>Apakah Anda yakin ingin menghapus kategori:</p>
                    <h5 id="delete_nama_kategori" class="text-danger"></h5>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Kategori hanya dapat dihapus jika tidak ada penelitian yang menggunakannya.
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
// Sync color picker with text input
document.getElementById('add_warna').addEventListener('input', function() {
    document.getElementById('add_warna_text').value = this.value;
});

document.getElementById('edit_warna').addEventListener('input', function() {
    document.getElementById('edit_warna_text').value = this.value;
});

function editKategori(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_nama_kategori').value = data.nama_kategori;
    document.getElementById('edit_warna').value = data.warna || '#0d6efd';
    document.getElementById('edit_warna_text').value = data.warna || '#0d6efd';
    document.getElementById('edit_deskripsi').value = data.deskripsi || '';
    document.getElementById('edit_is_active').checked = (data.is_active === 't' || data.is_active === true);
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function confirmDelete(id, nama, totalPenelitian) {
    if (totalPenelitian > 0) {
        alert('Tidak dapat menghapus kategori ini karena masih ada ' + totalPenelitian + ' penelitian menggunakannya!');
        return;
    }
    
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_nama_kategori').textContent = nama;
    
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
