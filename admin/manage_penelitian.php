<?php
require_once 'auth_check.php';
require_once '../includes/config.php';
require_once 'controllers/penelitianController.php';

$controller = new PenelitianController();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = 10;

$data = $controller->getAll($page, $limit, $search);
$items = $data['items'];

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
                                // FIX PATH GAMBAR
                                $img_name = $row['gambar'];
                                $web_path = "public/uploads/penelitian/" . $img_name;
                                $abs_path = __DIR__ . "/../public/uploads/penelitian/" . $img_name;

                                if (!empty($img_name) && file_exists($abs_path)) {
                                    $final_img = "../" . $web_path;
                                } else {
                                    $final_img = "https://ui-avatars.com/api/?name=" . urlencode($row['judul']) . "&background=f0f0f0&color=999&size=128";
                                }
                            ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td>
                                    <img src="<?= $final_img ?>" class="rounded shadow-sm border" style="width: 50px; height: 70px; object-fit: cover;">
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

<?php include 'includes/admin_footer.php'; ?>