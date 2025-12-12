<?php
require_once '../auth_check.php';
require_once '../../includes/config.php';

$page_title = 'Kelola Profil Lab';
include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM lab_profile WHERE id = $1";
    $result = pg_query_params($conn, $query, array($id));
    if ($result) {
        $success_msg = "Item berhasil dihapus.";
    } else {
        $error_msg = "Gagal menghapus item.";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul']);
    $konten = trim($_POST['konten']);
    $kategori = trim($_POST['kategori']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id > 0) {
        // Update
        $query = "UPDATE lab_profile SET judul = $1, konten = $2, kategori = $3, updated_at = NOW() WHERE id = $4";
        $result = pg_query_params($conn, $query, array($judul, $konten, $kategori, $id));
        if ($result) $success_msg = "Item berhasil diperbarui.";
        else $error_msg = "Gagal memperbarui item.";
    } else {
        // Insert
        $query = "INSERT INTO lab_profile (judul, konten, kategori) VALUES ($1, $2, $3)";
        $result = pg_query_params($conn, $query, array($judul, $konten, $kategori));
        if ($result) $success_msg = "Item berhasil ditambahkan.";
        else $error_msg = "Gagal menambahkan item.";
    }
}

// Fetch Data
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : 'all';
$query = "SELECT * FROM lab_profile";
if ($kategori_filter != 'all') {
    if ($kategori_filter == 'visi_misi') {
        $query .= " WHERE kategori IN ('visi', 'misi')";
    } else {
        $query .= " WHERE kategori = '$kategori_filter'";
    }
}
$query .= " ORDER BY kategori, id";
$result = pg_query($conn, $query);
?>

<div class="admin-content">
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Kelola Profil Lab</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profil Lab</li>
                </ol>
            </nav>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm" onclick="resetForm()">
                <i class="bi bi-plus-lg me-2"></i>Tambah Item
            </button>
        </div>
    </div>

    <?php if (isset($success_msg)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $success_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link <?php echo $kategori_filter == 'all' ? 'active' : ''; ?>" href="?kategori=all">Semua</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $kategori_filter == 'tentang' ? 'active' : ''; ?>" href="?kategori=tentang">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $kategori_filter == 'visi_misi' ? 'active' : ''; ?>" href="?kategori=visi_misi">Visi & Misi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $kategori_filter == 'roadmap' ? 'active' : ''; ?>" href="?kategori=roadmap">Roadmap</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $kategori_filter == 'focus' ? 'active' : ''; ?>" href="?kategori=focus">Focus & Scope</a>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Judul</th>
                            <th>Kategori</th>
                            <th>Konten Preview</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = pg_fetch_assoc($result)): ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?php echo htmlspecialchars($row['judul']); ?></td>
                            <td>
                                <?php 
                                $badge_color = 'secondary';
                                if ($row['kategori'] == 'visi') $badge_color = 'primary';
                                if ($row['kategori'] == 'misi') $badge_color = 'info';
                                if ($row['kategori'] == 'roadmap') $badge_color = 'success';
                                if ($row['kategori'] == 'focus') $badge_color = 'warning';
                                ?>
                                <span class="badge bg-<?php echo $badge_color; ?>"><?php echo htmlspecialchars(strtoupper($row['kategori'])); ?></span>
                            </td>
                            <td><?php echo substr(htmlspecialchars($row['konten']), 0, 80) . '...'; ?></td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-primary me-1" 
                                        onclick="editItem(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus item ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Item Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="itemId">
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori" id="itemKategori" required>
                            <option value="tentang">Tentang</option>
                            <option value="visi">Visi</option>
                            <option value="misi">Misi</option>
                            <option value="roadmap">Roadmap</option>
                            <option value="focus">Focus & Scope</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" name="judul" id="itemJudul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konten</label>
                        <textarea class="form-control" name="konten" id="itemKonten" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('modalTitle').innerText = 'Tambah Item Profil';
    document.getElementById('itemId').value = '';
    document.getElementById('itemKategori').value = 'tentang';
    document.getElementById('itemJudul').value = '';
    document.getElementById('itemKonten').value = '';
}

function editItem(data) {
    document.getElementById('modalTitle').innerText = 'Edit Item Profil';
    document.getElementById('itemId').value = data.id;
    document.getElementById('itemKategori').value = data.kategori;
    document.getElementById('itemJudul').value = data.judul;
    document.getElementById('itemKonten').value = data.konten;
    
    var modal = new bootstrap.Modal(document.getElementById('modalForm'));
    modal.show();
}
</script>

<?php include '../includes/admin_footer.php'; ?>
