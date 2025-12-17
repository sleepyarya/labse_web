<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: my_pengabdian.php');
    exit();
}

$id = intval($_GET['id']);
$member_id = $_SESSION['member_id'];

// Get pengabdian data (only owned by this member)
$query = "SELECT * FROM pengabdian WHERE id = $1 AND personil_id = $2";
$result = pg_query_params($conn, $query, array($id, $member_id));
$pengabdian = pg_fetch_assoc($result);

if (!$pengabdian) {
    header('Location: my_pengabdian.php?error=notfound');
    exit();
}

$page_title = 'Detail Kegiatan Pengabdian';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Detail Kegiatan Pengabdian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_pengabdian.php">Pengabdian Saya</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-body">
                    
                    <!-- Title -->
                    <h3 class="mb-3"><?php echo htmlspecialchars($pengabdian['judul']); ?></h3>
                    
                    <!-- Meta Info -->
                    <div class="mb-4">
                        <span class="badge bg-primary me-2">
                            <i class="bi bi-calendar3 me-1"></i><?php echo date('d M Y', strtotime($pengabdian['tanggal'])); ?>
                        </span>
                        <span class="badge bg-success me-2">
                            <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($pengabdian['lokasi']); ?>
                        </span>
                        <span class="badge bg-info">
                            <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($pengabdian['penyelenggara']); ?>
                        </span>
                    </div>
                    
                    <!-- Image -->
                    <?php if ($pengabdian['gambar']): ?>
                    <div class="mb-4">
                        <img src="<?php echo BASE_URL; ?>/public/uploads/pengabdian/<?php echo htmlspecialchars($pengabdian['gambar']); ?>" 
                             class="img-fluid rounded" alt="Dokumentasi"
                             onerror="this.style.display='none'">
                    </div>
                    <?php endif; ?>
                    
                    <!-- Description -->
                    <div class="mb-4">
                        <h5 class="mb-3">Deskripsi Kegiatan</h5>
                        <p style="white-space: pre-line;"><?php echo nl2br(htmlspecialchars($pengabdian['deskripsi'])); ?></p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <a href="edit_pengabdian.php?id=<?php echo $pengabdian['id']; ?>" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                        <a href="delete_pengabdian.php?id=<?php echo $pengabdian['id']; ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Yakin ingin menghapus kegiatan ini?')">
                            <i class="bi bi-trash me-2"></i>Hapus
                        </a>
                        <a href="my_pengabdian.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th width="40%">Tanggal:</th>
                            <td><?php echo date('d M Y', strtotime($pengabdian['tanggal'])); ?></td>
                        </tr>
                        <tr>
                            <th>Lokasi:</th>
                            <td><?php echo htmlspecialchars($pengabdian['lokasi']); ?></td>
                        </tr>
                        <tr>
                            <th>Penyelenggara:</th>
                            <td><?php echo htmlspecialchars($pengabdian['penyelenggara']); ?></td>
                        </tr>
                        <tr>
                            <th>Dibuat:</th>
                            <td><?php echo date('d M Y H:i', strtotime($pengabdian['created_at'])); ?></td>
                        </tr>
                        <?php if ($pengabdian['updated_at']): ?>
                        <tr>
                            <th>Diupdate:</th>
                            <td><?php echo date('d M Y H:i', strtotime($pengabdian['updated_at'])); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- End Member Content -->

<?php
pg_close($conn);
include 'includes/member_footer.php';
?>
