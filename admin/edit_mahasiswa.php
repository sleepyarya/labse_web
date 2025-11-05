<?php
// Proteksi halaman - harus login dulu
require_once 'auth_check.php';
require_once '../includes/config.php';

$page_title = 'Edit Data Mahasiswa';
$error = '';
$success = false;

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: manage_mahasiswa.php?error=ID tidak valid');
    exit();
}

// Get existing data
$query = "SELECT * FROM mahasiswa WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));

if (pg_num_rows($result) == 0) {
    header('Location: manage_mahasiswa.php?error=Data tidak ditemukan');
    exit();
}

$data = pg_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = pg_escape_string($conn, trim($_POST['nama']));
    $nim = pg_escape_string($conn, trim($_POST['nim']));
    $jurusan = pg_escape_string($conn, trim($_POST['jurusan']));
    $email = pg_escape_string($conn, trim($_POST['email']));
    $alasan = pg_escape_string($conn, trim($_POST['alasan']));
    
    // Validation
    if (empty($nama) || empty($nim) || empty($jurusan) || empty($email)) {
        $error = 'Nama, NIM, jurusan, dan email wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        // Check NIM duplicate (exclude current record)
        $check_query = "SELECT COUNT(*) as total FROM mahasiswa WHERE nim = $1 AND id != $2";
        $check_result = pg_query_params($conn, $check_query, array($nim, $id));
        $check_data = pg_fetch_assoc($check_result);
        
        if ($check_data['total'] > 0) {
            $error = 'NIM sudah digunakan mahasiswa lain!';
        } else {
            // Update database
            $query = "UPDATE mahasiswa SET nama = $1, nim = $2, jurusan = $3, email = $4, alasan = $5 
                      WHERE id = $6";
            $result = pg_query_params($conn, $query, array($nama, $nim, $jurusan, $email, $alasan, $id));
            
            if ($result) {
                header('Location: manage_mahasiswa.php?success=edit');
                exit();
            } else {
                $error = 'Gagal mengupdate data: ' . pg_last_error($conn);
            }
        }
    }
}

include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Edit Data Mahasiswa</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_mahasiswa.php">Kelola Mahasiswa</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card" data-aos="fade-up">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Edit Mahasiswa</h5>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="formMahasiswa">
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" required 
                                       value="<?php echo htmlspecialchars($data['nama']); ?>"
                                       placeholder="Masukkan nama lengkap mahasiswa">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">NIM <span class="text-danger">*</span></label>
                                        <input type="text" name="nim" class="form-control" required 
                                               value="<?php echo htmlspecialchars($data['nim']); ?>"
                                               placeholder="Contoh: 2141762001"
                                               pattern="[0-9]{10}"
                                               title="NIM harus 10 digit angka">
                                        <small class="text-muted">10 digit angka</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                        <select name="jurusan" class="form-select" required>
                                            <option value="">-- Pilih Jurusan --</option>
                                            <option value="Teknik Informatika" <?php echo $data['jurusan'] == 'Teknik Informatika' ? 'selected' : ''; ?>>Teknik Informatika</option>
                                            <option value="Sistem Informasi Bisnis" <?php echo $data['jurusan'] == 'Sistem Informasi Bisnis' ? 'selected' : ''; ?>>Sistem Informasi Bisnis</option>
                                            <option value="D4 Teknik Informatika" <?php echo $data['jurusan'] == 'D4 Teknik Informatika' ? 'selected' : ''; ?>>D4 Teknik Informatika</option>
                                            <option value="D4 Sistem Informasi Bisnis" <?php echo $data['jurusan'] == 'D4 Sistem Informasi Bisnis' ? 'selected' : ''; ?>>D4 Sistem Informasi Bisnis</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required 
                                       value="<?php echo htmlspecialchars($data['email']); ?>"
                                       placeholder="contoh@student.polinema.ac.id">
                                <small class="text-muted">Gunakan email aktif mahasiswa</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alasan Bergabung</label>
                                <textarea name="alasan" class="form-control" rows="5" 
                                          placeholder="Tuliskan alasan ingin bergabung dengan Lab Software Engineering..."><?php echo htmlspecialchars($data['alasan']); ?></textarea>
                                <small class="text-muted">Opsional - Alasan atau motivasi bergabung dengan lab</small>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save me-2"></i>Update
                                </button>
                                <a href="manage_mahasiswa.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                            
                        </form>
                        
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <strong>ID:</strong> #<?php echo $data['id']; ?>
                            </li>
                            <li class="mb-2">
                                <strong>Terdaftar:</strong><br>
                                <small class="text-muted"><?php echo date('d M Y H:i', strtotime($data['created_at'])); ?></small>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                NIM tidak boleh duplikat
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Pastikan data akurat
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
    }
    
    .btn {
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
</style>

<script>
// Form validation
document.getElementById('formMahasiswa').addEventListener('submit', function(e) {
    const nama = document.querySelector('input[name="nama"]').value.trim();
    const nim = document.querySelector('input[name="nim"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const jurusan = document.querySelector('select[name="jurusan"]').value;
    
    if (!nama || !nim || !email || !jurusan) {
        e.preventDefault();
        alert('Nama, NIM, Jurusan, dan Email wajib diisi!');
        return false;
    }
    
    // Validate NIM format (10 digits)
    if (!/^\d{10}$/.test(nim)) {
        e.preventDefault();
        alert('NIM harus 10 digit angka!');
        return false;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengupdate...';
});

// Auto format NIM (only numbers)
document.querySelector('input[name="nim"]').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>
