<?php
require_once 'auth_check.php';
require_once '../includes/config.php';
require_once 'controllers/penelitianController.php';

$controller = new PenelitianController();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = 10;

// Ensure $conn is available for pg functions if needed directly, though controller uses it.
// $conn is included via config.php

$data = $controller->getAll($page, $limit, $search);
$items = $data['items']; // This should be compatible with the view loop

include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<div class="admin-content">
    <div class="admin-topbar d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">Kelola Penelitian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Penelitian</li>
                </ol>
            </nav>
        </div>
        <a href="penelitian_form.php" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Tambah Penelitian</a>
    </div>

    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Cover</th>
                                <th>Judul & Personil</th>
                                <th>Kategori</th>
                                <th>Tahun</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = ($page - 1) * $limit + 1;
                            foreach ($items as $row): 
                            ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td>
                                    <?php if ($row['gambar']): ?>
                                    <img src="<?= BASE_URL ?>/public/uploads/penelitian/<?= htmlspecialchars($row['gambar']) ?>" 
                                         class="rounded shadow-sm border" style="width: 50px; height: 70px; object-fit: cover;"
                                         onerror="this.src='https://picsum.photos/seed/<?= $row['id'] ?>/600/400'">
                                    <?php else: ?>
                                    <img src="https://picsum.photos/seed/<?= $row['id'] ?>/600/400" 
                                         class="rounded shadow-sm border" style="width: 50px; height: 70px; object-fit: cover;"
                                         alt="Auto Generated">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['judul']) ?></div>
                                    <small class="text-muted"><i class="bi bi-person me-1"></i><?= htmlspecialchars($row['personil_nama'] ?? 'Umum') ?></small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill" style="background-color: <?= $row['kat_warna'] ?? '#6c757d' ?>;">
                                        <?= htmlspecialchars($row['kat_nama'] ?? 'Uncategorized') ?>
                                    </span>
                                </td>
                                <td><?= $row['tahun'] ?></td>
                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="penelitian_form.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <a href="delete_penelitian.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Hapus data ini?')" title="Hapus"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
pg_close($conn);
include 'includes/admin_footer.php'; 
?>