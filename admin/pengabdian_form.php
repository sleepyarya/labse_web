<?php
require_once 'auth_check.php';
require_once 'controllers/pengabdianController.php';
require_once '../includes/config.php';

$controller = new PengabdianController();
$is_edit = isset($_GET['id']) && !empty($_GET['id']);
$pengabdian = null;
$error = '';

// Get personil list for dropdown
$personil_list = $controller->getAllPersonil();

if ($is_edit) {
    $id = intval($_GET['id']);
    $result = $controller->edit($id);
    $error = $result['error'];
    $pengabdian = $result['pengabdian'];

    if (!$pengabdian) {
        header('Location: manage_pengabdian.php?error=notfound');
        exit();
    }
} else {
    $result = $controller->add();
    $error = $result['error'];
}

include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">

    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0"><?php echo $is_edit ? 'Edit' : 'Tambah'; ?> Pengabdian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_pengabdian.php">Kelola Pengabdian</a></li>
                    <li class="breadcrumb-item active"><?php echo $is_edit ? 'Edit' : 'Tambah Baru'; ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="card" data-aos="fade-up">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-people-fill me-2"></i>Form <?php echo $is_edit ? 'Edit' : 'Tambah'; ?> Pengabdian
                        </h5>
                    </div>
                    <div class="card-body">

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data" id="formPengabdian">

                            <!-- Judul -->
                            <div class="mb-3">
                                <label class="form-label">Judul Pengabdian <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" required maxlength="255"
                                    value="<?php echo $is_edit ? htmlspecialchars($pengabdian['judul']) : (isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''); ?>"
                                    placeholder="Masukkan judul pengabdian">
                            </div>

                            <div class="row">
                                <!-- Tanggal -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control" required
                                        value="<?php echo $is_edit ? $pengabdian['tanggal'] : (isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d')); ?>">
                                </div>

                                <!-- Lokasi -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" name="lokasi" class="form-control" required
                                        value="<?php echo $is_edit ? htmlspecialchars($pengabdian['lokasi']) : (isset($_POST['lokasi']) ? htmlspecialchars($_POST['lokasi']) : ''); ?>"
                                        placeholder="Lokasi kegiatan">
                                </div>
                            </div>

                            <!-- Penyelenggara -->
                            <div class="mb-3">
                                <label class="form-label">Penyelenggara <span class="text-danger">*</span></label>
                                <input type="text" name="penyelenggara" class="form-control" required
                                    value="<?php echo $is_edit ? htmlspecialchars($pengabdian['penyelenggara']) : (isset($_POST['penyelenggara']) ? htmlspecialchars($_POST['penyelenggara']) : ''); ?>"
                                    placeholder="Nama penyelenggara">
                            </div>

                            <!-- Personil (Opsional) -->
                            <div class="mb-3">
                                <label class="form-label">Personil (Opsional)</label>
                                <select name="personil_id" class="form-select">
                                    <option value="">Pilih Personil (Opsional)</option>
                                    <?php
                                    $selected_personil = $is_edit ? $pengabdian['personil_id'] : (isset($_POST['personil_id']) ? $_POST['personil_id'] : '');
                                    foreach ($personil_list as $p):
                                    ?>
                                        <option value="<?php echo $p['id']; ?>" <?php echo $selected_personil == $p['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($p['nama']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Pilih personil yang bertanggung jawab (jika ada)</small>
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-3">
                                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="5" required
                                    placeholder="Deskripsi detail kegiatan pengabdian"><?php echo $is_edit ? htmlspecialchars($pengabdian['deskripsi']) : (isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''); ?></textarea>
                            </div>

                            <!-- Gambar -->
                            <div class="mb-3">
                                <label class="form-label">Gambar Dokumentasi</label>
                                <?php if ($is_edit && $pengabdian['gambar']): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo BASE_URL . '/uploads/pengabdian/' . htmlspecialchars($pengabdian['gambar']); ?>"
                                            class="img-thumbnail" style="max-height: 150px;" alt="Current image">
                                        <div class="form-text">Gambar saat ini</div>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="gambar" class="form-control" accept="image/*" id="gambarInput">
                                <small class="text-muted">JPG, PNG. Max: 5MB. <?php echo $is_edit ? 'Biarkan kosong jika tidak ingin mengubah.' : ''; ?></small>
                                <div id="previewContainer" class="mt-2" style="display: none;">
                                    <img id="previewImage" src="" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>

                            <hr>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i><?php echo $is_edit ? 'Update' : 'Simpan'; ?>
                                </button>
                                <?php if (!$is_edit): ?>
                                    <button type="reset" class="btn btn-secondary">
                                        <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                    </button>
                                <?php endif; ?>
                                <a href="manage_pengabdian.php" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-3">
                <div class="card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Petunjuk</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Judul</strong> kegiatan pengabdian
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Tanggal</strong> pelaksanaan kegiatan
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Lokasi</strong> tempat kegiatan
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Penyelenggara</strong> atau pihak yang terlibat
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Gambar</strong> dokumentasi kegiatan (opsional tapi disarankan)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

<script>
    // Preview image
    document.getElementById('gambarInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('previewContainer').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    // Form validation on submit
    document.getElementById('formPengabdian').addEventListener('submit', function(e) {
        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    });
</script>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>