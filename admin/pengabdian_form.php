<?php
// Page: Form Pengabdian (Admin)
require_once '../includes/config.php';
require_once 'controllers/pengabdianController.php';
// PersonilController tidak dibutuhkan lagi karena getAllPersonil sudah ada di PengabdianController

// Cek Login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$controller = new PengabdianController();
// $personilController = new PersonilController(); // Hapus
$personil_list = $controller->getAllPersonil(); // Ambil data personil untuk dropdown dari PengabdianController

$error = '';
$success = '';
$data = null;
$is_edit = false;

// Cek apakah mode Edit
if (isset($_GET['id'])) {
    $is_edit = true;
    $id = (int)$_GET['id'];
    $result = $controller->edit($id);

    if (isset($result['pengabdian'])) {
        $data = $result['pengabdian'];
    } else {
        header('Location: manage_pengabdian.php?error=notfound');
        exit();
    }
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($is_edit) {
        // Mode Edit
        $result = $controller->edit($id);
    } else {
        // Mode Add
        $result = $controller->add();
    }

    if (isset($result['error']) && $result['error']) {
        $error = $result['error'];
        // Keep input data if error
        $data = $_POST;
    }
}

$page_title = ($is_edit ? 'Edit' : 'Tambah') . ' Pengabdian';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4"><?php echo $page_title; ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="manage_pengabdian.php">Manajemen Pengabdian</a></li>
                <li class="breadcrumb-item active"><?php echo $is_edit ? 'Edit' : 'Tambah'; ?></li>
            </ol>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-hand-holding-heart me-1"></i>
                    Formulir Data Pengabdian Masyarakat
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="judul" class="form-label">Judul Kegiatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="judul" name="judul"
                                        value="<?php echo isset($data['judul']) ? htmlspecialchars($data['judul']) : ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required><?php echo isset($data['deskripsi']) ? htmlspecialchars($data['deskripsi']) : ''; ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="lokasi" name="lokasi"
                                            value="<?php echo isset($data['lokasi']) ? htmlspecialchars($data['lokasi']) : ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="penyelenggara" class="form-label">Penyelenggara <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="penyelenggara" name="penyelenggara"
                                            value="<?php echo isset($data['penyelenggara']) ? htmlspecialchars($data['penyelenggara']) : 'Lab Software Engineering'; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal"
                                        value="<?php echo isset($data['tanggal']) ? htmlspecialchars($data['tanggal']) : date('Y-m-d'); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="personil_id" class="form-label">Personil Penanggung Jawab</label>
                                    <select class="form-select" id="personil_id" name="personil_id">
                                        <option value="">-- Pilih Personil --</option>
                                        <?php foreach ($personil_list as $p): ?>
                                            <option value="<?php echo $p['id']; ?>"
                                                <?php echo (isset($data['personil_id']) && $data['personil_id'] == $p['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($p['nama']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Opsional: Pilih dosen/anggota yang memimpin.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="gambar" class="form-label">Gambar Dokumentasi</label>
                                    <?php if ($is_edit && !empty($data['gambar'])): ?>
                                        <div class="mb-2">
                                            <img src="../uploads/pengabdian/<?php echo htmlspecialchars($data['gambar']); ?>" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                                            <div class="form-text text-muted">Gambar saat ini</div>
                                        </div>
                                    <?php endif; ?>
                                    <input class="form-control" type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/jpg">
                                    <div class="form-text">Format: JPG, PNG. Maks 2MB.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Data
                            </button>
                            <a href="manage_pengabdian.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include 'includes/admin_footer.php'; ?>
</div>