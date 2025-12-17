<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

// Handle Actions
$success = '';
$error = '';

// Add Category
if (isset($_POST['add_kategori'])) {
    $nama = pg_escape_string($conn, trim($_POST['nama_kategori']));
    $deskripsi = pg_escape_string($conn, trim($_POST['deskripsi']));
    $warna = pg_escape_string($conn, trim($_POST['warna']));
    
    // Create slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama)));
    
    // Validation
    if (empty($nama)) {
        $error = 'Nama kategori wajib diisi!';
    } else {
        // Check duplicate
        $check = pg_query_params($conn, "SELECT id FROM kategori_produk WHERE nama_kategori = $1", array($nama));
        if (pg_num_rows($check) > 0) {
            $error = 'Nama kategori sudah ada!';
        } else {
            $query = "INSERT INTO kategori_produk (nama_kategori, slug, deskripsi, warna) VALUES ($1, $2, $3, $4)";
            if (pg_query_params($conn, $query, array($nama, $slug, $deskripsi, $warna))) {
                $success = 'Kategori berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan kategori: ' . pg_last_error($conn);
            }
        }
    }
}

// Edit Category
if (isset($_POST['edit_kategori'])) {
    $id = (int)$_POST['id'];
    $nama = pg_escape_string($conn, trim($_POST['nama_kategori']));
    $deskripsi = pg_escape_string($conn, trim($_POST['deskripsi']));
    $warna = pg_escape_string($conn, trim($_POST['warna']));
    $is_active = isset($_POST['is_active']) ? 'TRUE' : 'FALSE';
    
    // Create slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama)));
    
    if (empty($nama)) {
        $error = 'Nama kategori wajib diisi!';
    } else {
        // Check duplicate (exclude self)
        $check = pg_query_params($conn, "SELECT id FROM kategori_produk WHERE nama_kategori = $1 AND id != $2", array($nama, $id));
        if (pg_num_rows($check) > 0) {
            $error = 'Nama kategori sudah ada!';
        } else {
            $query = "UPDATE kategori_produk SET nama_kategori = $1, slug = $2, deskripsi = $3, warna = $4, is_active = $5, updated_at = NOW() WHERE id = $6";
            if (pg_query_params($conn, $query, array($nama, $slug, $deskripsi, $warna, $is_active, $id))) {
                $success = 'Kategori berhasil diupdate!';
            } else {
                $error = 'Gagal mengupdate kategori: ' . pg_last_error($conn);
            }
        }
    }
}

// Delete Category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Check usage in produk
    $check = pg_query_params($conn, "SELECT COUNT(*) as total FROM produk WHERE kategori_id = $1", array($id));
    $usage = pg_fetch_assoc($check)['total'];
    
    if ($usage > 0) {
        $error = "Tidak dapat menghapus kategori karena sedang digunakan oleh $usage produk!";
    } else {
        if (pg_query_params($conn, "DELETE FROM kategori_produk WHERE id = $1", array($id))) {
            $success = 'Kategori berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus kategori: ' . pg_last_error($conn);
        }
    }
}

// Toggle Status (AJAX helper usually, but here simple GET)
if (isset($_GET['toggle_status'])) {
    $id = (int)$_GET['toggle_status'];
    $query = "UPDATE kategori_produk SET is_active = NOT is_active WHERE id = $1";
    pg_query_params($conn, $query, array($id));
    header('Location: manage_kategori_produk.php');
    exit;
}

// Get all categories data
$result = pg_query($conn, "SELECT kp.*, (SELECT COUNT(*) FROM produk p WHERE p.kategori_id = kp.id) as total_produk FROM kategori_produk kp ORDER BY kp.id ASC");

include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Manajemen Kategori Produk</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kategori Produk</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg me-2"></i>Tambah Kategori
        </button>
    </div>
    
    <div class="container-fluid">
        
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Total Kategori</h6>
                                <h2 class="mb-0 mt-2"><?php echo pg_num_rows($result); ?></h2>
                            </div>
                            <i class="bi bi-tags fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Kategori Aktif</h6>
                                <?php
                                $active_count = 0;
                                while($row = pg_fetch_assoc($result)) {
                                    if($row['is_active'] == 't') $active_count++;
                                }
                                pg_result_seek($result, 0); // Reset pointer
                                ?>
                                <h2 class="mb-0 mt-2"><?php echo $active_count; ?></h2>
                            </div>
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Total Produk Terkategori</h6>
                                <?php
                                $total_usage = 0;
                                while($row = pg_fetch_assoc($result)) {
                                    $total_usage += $row['total_produk'];
                                }
                                pg_result_seek($result, 0); // Reset pointer
                                ?>
                                <h2 class="mb-0 mt-2"><?php echo $total_usage; ?></h2>
                            </div>
                            <i class="bi bi-box-seam fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Categories Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Kategori</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Kategori</th>
                                <th>Slug</th>
                                <th>Deskripsi</th>
                                <th>Warna</th>
                                <th>Total Produk</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = pg_fetch_assoc($result)): 
                            ?>
                            <tr>
                                <td class="ps-4"><?php echo $no++; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['nama_kategori']); ?></strong></td>
                                <td><code class="text-muted"><?php echo htmlspecialchars($row['slug']); ?></code></td>
                                <td><?php echo htmlspecialchars($row['deskripsi'] ?? '-'); ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 20px; height: 20px; border-radius: 4px; background-color: <?php echo htmlspecialchars($row['warna'] ?? '#0d6efd'); ?>;"></div>
                                        <span class="small text-muted"><?php echo htmlspecialchars($row['warna'] ?? '#0d6efd'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?php echo $row['total_produk']; ?> Produk
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['is_active'] == 't'): ?>
                                    <span class="badge bg-success-subtle text-success">Aktif</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger">Non-aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="?toggle_status=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Toggle Status">
                                            <i class="bi bi-power"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick='editKategori(<?php echo json_encode($row); ?>)'
                                                title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_kategori']); ?>', <?php echo $row['total_produk']; ?>)"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            
                            <?php if (pg_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada kategori</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kategori" class="form-control" required placeholder="Contoh: Web Application">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat kategori..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Warna Label</label>
                    <input type="color" name="warna" class="form-control form-control-color" value="#0d6efd" title="Pilih warna untuk badge">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="add_kategori" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kategori" id="edit_nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Warna Label</label>
                    <input type="color" name="warna" id="edit_warna" class="form-control form-control-color">
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_active" value="1">
                    <label class="form-check-label" for="edit_active">Status Aktif</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="edit_kategori" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function editKategori(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_nama').value = data.nama_kategori;
    document.getElementById('edit_deskripsi').value = data.deskripsi || '';
    document.getElementById('edit_warna').value = data.warna || '#0d6efd';
    document.getElementById('edit_active').checked = (data.is_active == 't');
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function confirmDelete(id, nama, totalProduk) {
    if (totalProduk > 0) {
        alert('Tidak dapat menghapus kategori ini karena masih ada ' + totalProduk + ' produk menggunakannya!');
        return;
    }
    
    if (confirm('Apakah Anda yakin ingin menghapus kategori "' + nama + '"?')) {
        window.location.href = '?delete=' + id;
    }
}
</script>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>
