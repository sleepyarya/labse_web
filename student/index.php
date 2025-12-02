<?php
require_once 'auth_check.php';
require_once '../core/database.php';

$page_title = 'Dashboard Mahasiswa';
include 'includes/header.php';
include 'includes/sidebar.php';

// Get student data
$student_id = $_SESSION['student_id'];
$query = "SELECT m.* 
          FROM mahasiswa m 
          WHERE m.id = $1";
$result = pg_query_params($conn, $query, array($student_id));
$student = pg_fetch_assoc($result);

// Get research stats
$query_penelitian = "SELECT COUNT(*) as total FROM penelitian WHERE mahasiswa_id = $1";
$result_penelitian = pg_query_params($conn, $query_penelitian, array($student_id));
$total_penelitian = pg_fetch_result($result_penelitian, 0, 0);

?>

<div class="member-content">
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Dashboard</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="user-dropdown">
            <div class="text-end d-none d-md-block">
                <div class="fw-bold small"><?php echo htmlspecialchars($student['nama']); ?></div>
                <div class="text-muted small" style="font-size: 0.7rem;"><?php echo htmlspecialchars($student['nim']); ?></div>
            </div>
            <div class="user-avatar-placeholder">
                <i class="bi bi-person"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Welcome Card -->
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Selamat Datang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo htmlspecialchars($student['nama']); ?>
                            </div>
                            <p class="text-muted mt-2 mb-0">
                                NIM: <?php echo htmlspecialchars($student['nim']); ?> | 
                                Jurusan: <?php echo htmlspecialchars($student['jurusan']); ?>
                            </p>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-circle fa-2x text-gray-300" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Penelitian Card -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Penelitian Diupload</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $total_penelitian; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-journal-text fa-2x text-gray-300" style="font-size: 2rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <img src="../assets/img/empty.svg" alt="Empty" style="width: 100px; opacity: 0.5; margin-bottom: 1rem;" onerror="this.style.display='none'">
                        <p class="text-muted">Belum ada aktivitas terbaru.</p>
                        <a href="penelitian.php" class="btn btn-primary btn-sm">
                            <i class="bi bi-upload me-1"></i> Upload Hasil Penelitian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
