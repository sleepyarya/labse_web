<?php
require_once 'auth_check.php';
require_once '../core/database.php';

$page_title = 'Kelola Recruitment';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';

$success_msg = '';
$error_msg = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $is_open = isset($_POST['is_open']) ? 'TRUE' : 'FALSE';
    $message = trim($_POST['message']);
    $updated_by = $_SESSION['admin_nama'] ?? 'Admin';
    
    // Update recruitment settings (always id = 1)
    $query = "UPDATE recruitment_settings SET is_open = $1, message = $2, updated_at = NOW(), updated_by = $3 WHERE id = 1";
    $result = pg_query_params($conn, $query, array($is_open, $message, $updated_by));
    
    if ($result) {
        $success_msg = "Pengaturan recruitment berhasil diperbarui!";
    } else {
        $error_msg = "Gagal memperbarui pengaturan recruitment.";
    }
}

// Fetch current settings
$query = "SELECT * FROM recruitment_settings WHERE id = 1";
$result = pg_query($conn, $query);
$settings = pg_fetch_assoc($result);

// If no settings exist, create default
if (!$settings) {
    $query = "INSERT INTO recruitment_settings (is_open, message, updated_by) 
              VALUES (TRUE, 'Maaf, Lab SE sedang tidak membuka recruitment saat ini. Silakan cek kembali nanti.', 'System')";
    pg_query($conn, $query);
    
    // Fetch again
    $query = "SELECT * FROM recruitment_settings WHERE id = 1";
    $result = pg_query($conn, $query);
    $settings = pg_fetch_assoc($result);
}
?>

<div class="admin-content">
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Kelola Recruitment</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Recruitment</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if ($success_msg): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?php echo $success_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Pengaturan Recruitment</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <!-- Status Toggle -->
                        <div class="mb-4 p-4 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Status Recruitment</h6>
                                    <p class="text-muted small mb-0">Aktifkan atau nonaktifkan pendaftaran recruitment</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           id="recruitmentSwitch" name="is_open" 
                                           style="width: 3rem; height: 1.5rem; cursor: pointer;"
                                           <?php echo ($settings['is_open'] == 't' || $settings['is_open'] == '1') ? 'checked' : ''; ?>>
                                    <label class="form-check-label ms-2" for="recruitmentSwitch" id="switchLabel">
                                        <strong><?php echo ($settings['is_open'] == 't' || $settings['is_open'] == '1') ? 'Aktif' : 'Nonaktif'; ?></strong>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Message -->
                        <div class="mb-4">
                            <label for="message" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>Pesan Ketika Recruitment Ditutup
                            </label>
                            <textarea class="form-control" id="message" name="message" 
                                      rows="4" required 
                                      placeholder="Masukkan pesan yang akan ditampilkan ketika recruitment ditutup..."><?php echo htmlspecialchars($settings['message']); ?></textarea>
                            <div class="form-text">Pesan ini akan ditampilkan di halaman landing dan recruitment ketika status recruitment dinonaktifkan.</div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Current Status Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Status Saat Ini</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status:</small>
                        <?php if ($settings['is_open'] == 't' || $settings['is_open'] == '1'): ?>
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i>Recruitment Aktif
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger fs-6 px-3 py-2">
                                <i class="bi bi-x-circle me-1"></i>Recruitment Ditutup
                            </span>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <small class="text-muted d-block mb-1"><i class="bi bi-clock me-1"></i>Terakhir Update:</small>
                        <strong><?php echo $settings['updated_at'] ? date('d M Y, H:i', strtotime($settings['updated_at'])) : '-'; ?></strong>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1"><i class="bi bi-person me-1"></i>Diupdate Oleh:</small>
                        <strong><?php echo htmlspecialchars($settings['updated_by']); ?></strong>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-info"><i class="bi bi-lightbulb me-2"></i>Informasi</h6>
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Ketika recruitment <strong>aktif</strong>, pengunjung dapat melihat button pendaftaran dan mengisi form</li>
                        <li class="mb-2">Ketika recruitment <strong>nonaktif</strong>, button pendaftaran akan disembunyikan dan pesan custom akan ditampilkan</li>
                        <li>Perubahan akan langsung berlaku di halaman landing page dan recruitment</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update label when switch is toggled
document.getElementById('recruitmentSwitch').addEventListener('change', function() {
    const label = document.getElementById('switchLabel');
    if (this.checked) {
        label.innerHTML = '<strong>Aktif</strong>';
    } else {
        label.innerHTML = '<strong>Nonaktif</strong>';
    }
});
</script>

<?php include 'includes/admin_footer.php'; ?>
