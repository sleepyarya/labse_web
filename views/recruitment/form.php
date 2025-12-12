<?php
require_once '../../includes/config.php';
$page_title = 'Form Pendaftaran';

// Check recruitment status first
$recruitment_query = "SELECT is_open FROM recruitment_settings WHERE id = 1";
$recruitment_result = pg_query($conn, $recruitment_query);
$recruitment_settings = pg_fetch_assoc($recruitment_result);
$is_recruitment_open = ($recruitment_settings && ($recruitment_settings['is_open'] == 't' || $recruitment_settings['is_open'] == '1'));

// Redirect if recruitment is closed
if (!$is_recruitment_open) {
    header('Location: index.php?error=closed');
    exit();
}

// Get active jurusan from database
$jurusan_query = "SELECT id, nama_jurusan FROM jurusan WHERE is_active = TRUE ORDER BY nama_jurusan ASC";
$jurusan_result = pg_query($conn, $jurusan_query);
$jurusan_list = [];
if ($jurusan_result) {
    while ($row = pg_fetch_assoc($jurusan_result)) {
        $jurusan_list[] = $row;
    }
}

$success = false;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $nim = trim($_POST['nim']);
    $jurusan_id = isset($_POST['jurusan_id']) ? (int)$_POST['jurusan_id'] : 0;
    $email = trim($_POST['email']);
    $alasan = trim($_POST['alasan']);
    
    // Validation
    if (empty($nama) || empty($nim) || empty($jurusan_id) || empty($email) || empty($alasan)) {
        $error = 'Semua field harus diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        // Get jurusan name from ID
        $jurusan_name_query = "SELECT nama_jurusan FROM jurusan WHERE id = $1 AND is_active = TRUE";
        $jurusan_name_result = pg_query_params($conn, $jurusan_name_query, array($jurusan_id));
        $jurusan_data = pg_fetch_assoc($jurusan_name_result);
        
        if (!$jurusan_data) {
            $error = 'Jurusan tidak valid!';
        } else {
            $jurusan = $jurusan_data['nama_jurusan'];
            
            // Check NIM duplicate
            $check_query = "SELECT COUNT(*) as total FROM mahasiswa WHERE nim = $1";
            $check_result = pg_query_params($conn, $check_query, array($nim));
            $check_data = pg_fetch_assoc($check_result);
            
            if ($check_data['total'] > 0) {
                $error = 'NIM sudah terdaftar! Gunakan NIM yang berbeda.';
            } else {
                // Insert into database with pending status and jurusan_id
                $query = "INSERT INTO mahasiswa (nama, nim, jurusan, jurusan_id, email, alasan, status_approval, created_at) 
                          VALUES ($1, $2, $3, $4, $5, $6, 'pending', NOW())";
                $result = pg_query_params($conn, $query, array($nama, $nim, $jurusan, $jurusan_id, $email, $alasan));
                
                if ($result) {
                    $success = true;
                } else {
                    $error = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
                }
            }
        }
    }
}

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Form Pendaftaran</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Isi formulir di bawah ini untuk bergabung dengan Lab SE</p>
    </div>
</div>

<!-- Form Section -->
<section class="content-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-down">
                    <h4 class="alert-heading"><i class="bi bi-check-circle me-2"></i>Pendaftaran Berhasil Dikirim!</h4>
                    <p><strong>Terima kasih telah mendaftar di Lab Software Engineering.</strong></p>
                    <p>Pendaftaran Anda telah diterima dan sedang dalam <span class="badge bg-warning text-dark">Status: Menunggu Persetujuan</span></p>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-info-circle me-1"></i>Langkah Selanjutnya:</h6>
                            <ul class="mb-0">
                                <li>Tim admin akan meninjau pendaftaran Anda</li>
                                <li>Kami akan menghubungi via email dalam 1-3 hari kerja</li>
                                <li>Pastikan email Anda aktif dan cek folder spam</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-envelope me-1"></i>Kontak:</h6>
                            <p class="mb-1">Jika ada pertanyaan, hubungi:</p>
                            <p class="mb-0"><strong>Email:</strong> labse@polinema.ac.id</p>
                        </div>
                    </div>
                    <hr>
                    <p class="mb-0">
                        <a href="index.php" class="btn btn-outline-primary btn-sm me-2">
                            <i class="bi bi-people me-1"></i>Lihat Daftar Mahasiswa
                        </a>
                        <a href="<?php echo BASE_URL; ?>/" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-house me-1"></i>Kembali ke Beranda
                        </a>
                    </p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" data-aos="fade-down">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card" data-aos="fade-up">
                    <div class="card-body p-5">
                        <h3 class="mb-4">Formulir Pendaftaran Mahasiswa</h3>
                        
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" required placeholder="Masukkan nama lengkap Anda">
                                <div class="invalid-feedback">
                                    Nama lengkap harus diisi.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="nim" class="form-label">NIM <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nim" name="nim" required placeholder="Masukkan NIM Anda">
                                <div class="invalid-feedback">
                                    NIM harus diisi.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="jurusan_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" id="jurusan_id" name="jurusan_id" required>
                                    <option value="" selected disabled>Pilih jurusan</option>
                                    <?php if (count($jurusan_list) > 0): ?>
                                        <?php foreach ($jurusan_list as $jrs): ?>
                                        <option value="<?php echo $jrs['id']; ?>"><?php echo htmlspecialchars($jrs['nama_jurusan']); ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>Tidak ada jurusan tersedia</option>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Silakan pilih jurusan Anda.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="nama@email.com">
                                <div class="invalid-feedback">
                                    Email harus diisi dengan format yang valid.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="alasan" class="form-label">Alasan Bergabung <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alasan" name="alasan" rows="5" required placeholder="Ceritakan alasan Anda ingin bergabung dengan Lab Software Engineering..."></textarea>
                                <div class="invalid-feedback">
                                    Alasan bergabung harus diisi.
                                </div>
                                <div class="form-text">Minimal 50 karakter</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send me-2"></i>Kirim Pendaftaran
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Penting</h5>
                        <ul class="mb-0">
                            <li>Pastikan semua data yang Anda masukkan adalah benar dan valid</li>
                            <li>Email yang Anda daftarkan akan digunakan untuk komunikasi selanjutnya</li>
                            <li>Proses verifikasi akan dilakukan dalam 3-5 hari kerja</li>
                            <li>Anda akan dihubungi melalui email untuk informasi lebih lanjut</li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?php
pg_close($conn);
include '../../includes/footer.php';
?>