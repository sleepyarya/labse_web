<?php
// Admin Personil Form View
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../controllers/personilController.php';

$controller = new PersonilController();
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$is_edit = !empty($id);

if ($is_edit) {
    $result = $controller->edit($id);
    $page_title = 'Edit Personil';
} else {
    $result = $controller->add();
    $page_title = 'Tambah Personil';
}

$error = $result['error'] ?? '';
$personil = $result['personil'] ?? null;

include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0"><?php echo $page_title; ?></h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_personil.php">Kelola Personil</a></li>
                    <li class="breadcrumb-item active"><?php echo $is_edit ? 'Edit' : 'Tambah Baru'; ?></li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Form <?php echo $page_title; ?></h5>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" required 
                                       value="<?php echo $personil ? htmlspecialchars($personil['nama']) : (isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''); ?>"
                                       placeholder="Masukkan nama lengkap">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" class="form-control" required 
                                       value="<?php echo $personil ? htmlspecialchars($personil['jabatan']) : (isset($_POST['jabatan']) ? htmlspecialchars($_POST['jabatan']) : ''); ?>"
                                       placeholder="Contoh: Dosen, Asisten Lab, Koordinator">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required 
                                       value="<?php echo $personil ? htmlspecialchars($personil['email']) : (isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''); ?>"
                                       placeholder="email@example.com">
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_member" name="is_member" <?php echo ($personil && $personil['is_member']) ? 'checked' : (!isset($_POST['is_member']) ? '' : 'checked'); ?>>
                                    <label class="form-check-label" for="is_member">
                                        Buat akun login untuk Personil (member)
                                    </label>
                                </div>
                                <div class="form-text">Centang untuk membuat akun login. Jika dikosongkan, tidak akan dibuat akun.</div>
                            </div>

                            <div id="accountPreview" style="display: none;" class="mb-3">
                                <label class="form-label">Preview Akun (otomatis)</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Username</span>
                                    <input type="text" id="preview_username" class="form-control" readonly>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Password</span>
                                    <input type="text" id="preview_password" class="form-control" readonly>
                                </div>
                                <div class="form-text mt-1"> Password default = 123456.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="4" 
                                          placeholder="Deskripsi singkat tentang personil (opsional)"><?php echo $personil ? htmlspecialchars($personil['deskripsi']) : (isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Foto Profil</label>
                                <?php if ($personil && $personil['foto']): ?>
                                <div class="mb-2">
                                    <img src="../../public/uploads/personil/<?php echo htmlspecialchars($personil['foto']); ?>" 
                                         class="img-thumbnail" style="max-width: 150px;">
                                    <small class="d-block text-muted">Foto saat ini</small>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="foto" class="form-control" accept="image/*" id="fotoInput">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB. <?php echo $is_edit ? 'Kosongkan jika tidak ingin mengubah foto.' : ''; ?></small>
                                <div id="previewContainer" class="mt-3" style="display: none;">
                                    <img id="previewImage" src="" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i><?php echo $is_edit ? 'Update' : 'Simpan'; ?> Personil
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                </button>
                                <a href="manage_personil.php" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Panduan</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Nama lengkap wajib diisi</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Jabatan sesuai posisi di lab</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Email harus valid dan aktif</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Foto profil opsional</li>
                            <li class="mb-0"><i class="bi bi-check-circle text-success me-2"></i>Deskripsi untuk informasi tambahan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- End Admin Content -->

<script>
// Image preview functionality
document.getElementById('fotoInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
});

// Auto-generate username preview and password when creating personil
function generatePreview() {
    const nameEl = document.querySelector('input[name="nama"]');
    const isMemberEl = document.getElementById('is_member');
    const previewBox = document.getElementById('accountPreview');
    const previewUsername = document.getElementById('preview_username');
    const previewPassword = document.getElementById('preview_password');

    if (!nameEl) return;
    const name = nameEl.value.trim();
    if (isMemberEl && isMemberEl.checked && name) {
        // first word, lowercase, remove non-alnum
        let first = name.split(/\s+/)[0].toLowerCase();
        try { first = first.normalize('NFD').replace(/\p{Diacritic}/gu, ''); } catch(e) {}
        first = first.replace(/[^a-z0-9]/g, '');
        if (!first) first = 'user';
        const username = first.substring(0,30) + '123';
        previewUsername.value = username;
        previewPassword.value = '123456';
        previewBox.style.display = 'block';
    } else {
        previewBox.style.display = 'none';
    }
}

document.querySelector('input[name="nama"]').addEventListener('input', generatePreview);
document.getElementById('is_member').addEventListener('change', generatePreview);
// initialize on load
generatePreview();
</script>

<?php
pg_close($conn);
include '../includes/admin_footer.php';
?>
