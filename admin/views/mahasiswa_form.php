<?php
// Admin Mahasiswa Form View
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../controllers/mahasiswaController.php';

$controller = new MahasiswaController();
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$is_edit = !empty($id);

if ($is_edit) {
    $result = $controller->edit($id);
    $page_title = 'Edit Data Mahasiswa';
} else {
    $result = $controller->add();
    $page_title = 'Tambah Data Mahasiswa';
}

$error = $result['error'] ?? '';
$mahasiswa = $result['mahasiswa'] ?? null;

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
                    <li class="breadcrumb-item"><a href="manage_mahasiswa.php">Kelola Mahasiswa</a></li>
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
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Form <?php echo $page_title; ?></h5>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" required 
                                       value="<?php echo $mahasiswa ? htmlspecialchars($mahasiswa['nama']) : (isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''); ?>"
                                       placeholder="Masukkan nama lengkap mahasiswa">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">NIM (Nomor Induk Mahasiswa) <span class="text-danger">*</span></label>
                                <input type="text" name="nim" class="form-control" required 
                                       value="<?php echo $mahasiswa ? htmlspecialchars($mahasiswa['nim']) : (isset($_POST['nim']) ? htmlspecialchars($_POST['nim']) : ''); ?>"
                                       placeholder="Contoh: 2141720001">
                                <small class="text-muted">NIM harus unik dan tidak boleh sama dengan mahasiswa lain</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select name="jurusan" class="form-select" required>
                                    <option value="">Pilih Jurusan</option>
                                    <?php 
                                    $jurusan_list = [
                                        'Teknologi Informasi',
                                        'Teknik Informatika', 
                                        'Sistem Informasi Bisnis',
                                        'Teknik Komputer dan Jaringan',
                                        'Teknik Elektro',
                                        'Teknik Mesin',
                                        'Teknik Sipil',
                                        'Akuntansi',
                                        'Administrasi Niaga'
                                    ];
                                    
                                    $selected_jurusan = $mahasiswa ? $mahasiswa['jurusan'] : (isset($_POST['jurusan']) ? $_POST['jurusan'] : '');
                                    
                                    foreach ($jurusan_list as $jurusan_option) {
                                        $selected = ($selected_jurusan === $jurusan_option) ? 'selected' : '';
                                        echo "<option value=\"" . htmlspecialchars($jurusan_option) . "\" $selected>" . htmlspecialchars($jurusan_option) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required 
                                       value="<?php echo $mahasiswa ? htmlspecialchars($mahasiswa['email']) : (isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''); ?>"
                                       placeholder="email@student.polinema.ac.id">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alasan Bergabung</label>
                                <textarea name="alasan" class="form-control" rows="4" 
                                          placeholder="Jelaskan alasan ingin bergabung dengan Lab Software Engineering (opsional)"><?php echo $mahasiswa ? htmlspecialchars($mahasiswa['alasan']) : (isset($_POST['alasan']) ? htmlspecialchars($_POST['alasan']) : ''); ?></textarea>
                                <small class="text-muted">Informasi ini membantu kami memahami motivasi mahasiswa</small>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save me-2"></i><?php echo $is_edit ? 'Update' : 'Simpan'; ?> Data
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                </button>
                                <a href="manage_mahasiswa.php" class="btn btn-outline-danger">
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
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Semua field bertanda * wajib diisi</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>NIM harus unik untuk setiap mahasiswa</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Email harus valid dan aktif</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Pilih jurusan sesuai dengan data mahasiswa</li>
                            <li class="mb-0"><i class="bi bi-check-circle text-success me-2"></i>Alasan bergabung membantu evaluasi</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-question-circle me-2"></i>Bantuan</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-2">Data mahasiswa ini akan digunakan untuk:</p>
                        <ul class="small mb-0">
                            <li>Pendaftaran anggota lab</li>
                            <li>Komunikasi dan koordinasi</li>
                            <li>Laporan kegiatan lab</li>
                            <li>Sertifikat dan penghargaan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- End Admin Content -->

<?php
pg_close($conn);
include '../includes/admin_footer.php';
?>
