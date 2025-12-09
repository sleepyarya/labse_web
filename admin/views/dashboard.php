<?php
// Admin Dashboard View
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../core/database.php';

$page_title = 'Dashboard';
include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';

pg_query($conn, "REFRESH MATERIALIZED VIEW mv_dashboard_stats");

// 2. Ambil semua statistik dalam SATU kali query
$query = "SELECT * FROM mv_dashboard_stats";
$result = pg_query($conn, $query);
$stats = pg_fetch_assoc($result);

// Fallback jika data kosong
if (!$stats) {
    $stats = [
        'total_personil' => 0,
        'total_artikel' => 0,
        'total_mahasiswa' => 0,
        'total_profil' => 0,
        'mahasiswa_pending' => 0
    ];
}
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Dashboard</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="user-dropdown">
            <span class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_nama']); ?></strong></span>
        </div>
    </div>
    
    <!-- Dashboard Content -->
    <div class="row g-4">
        <!-- Statistics Cards -->
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card card text-center">
                <div class="card-body">
                    <i class="bi bi-person-fill text-primary" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">
                        <?php
                        $result = pg_query($conn, "SELECT COUNT(*) as total FROM personil");
                        $row = pg_fetch_assoc($result);
                        echo $row['total'];
                        ?>
                    </h3>
                    <p class="text-muted">Personil</p>
                    <a href="manage_personil.php" class="btn btn-sm btn-outline-primary">Kelola</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card card text-center">
                <div class="card-body">
                    <i class="bi bi-file-text-fill text-success" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">
                        <?php
                        $result = pg_query($conn, "SELECT COUNT(*) as total FROM artikel");
                        $row = pg_fetch_assoc($result);
                        echo $row['total'];
                        ?>
                    </h3>
                    <p class="text-muted">Artikel</p>
                    <a href="manage_artikel.php" class="btn btn-sm btn-outline-success">Kelola</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card card text-center">
                <div class="card-body">
                    <i class="bi bi-people-fill text-warning" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">
                        <?php
                        $result = pg_query($conn, "SELECT COUNT(*) as total FROM mahasiswa");
                        $row = pg_fetch_assoc($result);
                        echo $row['total'];
                        ?>
                    </h3>
                    <p class="text-muted">Mahasiswa</p>
                    <a href="manage_mahasiswa.php" class="btn btn-sm btn-outline-warning">Kelola</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card card text-center">
                <div class="card-body">
                    <i class="bi bi-journal-bookmark-fill text-info" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">
                        <?php
                        $result = pg_query($conn, "SELECT COUNT(*) as total FROM lab_profile");
                        $row = pg_fetch_assoc($result);
                        echo $row['total'];
                        ?>
                    </h3>
                    <p class="text-muted">Profil</p>
                    <a href="#" class="btn btn-sm btn-outline-info">Kelola</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mt-4">
            <div class="col-12">
                <div class="card" data-aos="fade-up">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="manage_personil.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-people me-2"></i>Kelola Personil
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="manage_artikel.php" class="btn btn-outline-success w-100">
                                <i class="bi bi-file-text me-2"></i>Kelola Artikel
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="manage_mahasiswa.php" class="btn btn-outline-warning w-100">
                                <i class="bi bi-person-badge me-2"></i>Kelola Mahasiswa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card" data-aos="fade-up">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Artikel Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Penulis</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = pg_query($conn, "SELECT * FROM artikel ORDER BY created_at DESC LIMIT 5");
                                while ($row = pg_fetch_assoc($result)) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['judul']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['penulis']) . '</td>';
                                    echo '<td>' . date('d M Y', strtotime($row['created_at'])) . '</td>';
                                    echo '<td><a href="../../pages/blog/detail.php?id=' . $row['id'] . '" class="btn btn-sm btn-outline-primary" target="_blank">Lihat</a></td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- End Admin Content -->

<style>
    /* Dashboard Responsive Styles */
    @media (max-width: 992px) {
        .stat-card {
            margin-bottom: 1rem;
        }
        
        .stat-card h3 {
            font-size: 2rem;
        }
        
        .stat-card i {
            font-size: 2.5rem !important;
        }
    }
    
    @media (max-width: 768px) {
        .admin-topbar h4 {
            font-size: 1.25rem;
        }
        
        .stat-card h3 {
            font-size: 1.75rem;
        }
        
        .stat-card i {
            font-size: 2rem !important;
        }
        
        .stat-card .card-body {
            padding: 1.25rem;
        }
        
        .stat-card p {
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
        }
        
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        
        .table {
            font-size: 0.85rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem;
        }
        
        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .card-header h5 {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .col-md-6 {
            margin-bottom: 1rem;
        }
        
        .stat-card h3 {
            font-size: 1.5rem;
        }
        
        .stat-card i {
            font-size: 1.75rem !important;
        }
        
        .stat-card .card-body {
            padding: 1rem;
        }
        
        .table {
            font-size: 0.75rem;
            min-width: 500px;
        }
        
        .table th,
        .table td {
            padding: 0.375rem;
            white-space: nowrap;
        }
    }
</style>

<?php
pg_close($conn);
include '../includes/admin_footer.php';
?>
