<?php
require_once 'auth_check.php';
require_once 'controllers/penelitianController.php';
require_once '../includes/config.php';

// Validate ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: my_penelitian.php?error=invalid');
    exit();
}

$id = intval($_GET['id']);
$controller = new MemberPenelitianController();
$penelitian = $controller->getById($id);

// If not found or unauthorized
if (!$penelitian) {
    header('Location: my_penelitian.php?error=unauthorized');
    exit();
}

$page_title = 'Detail Penelitian';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<!-- Main Content -->
<div class="member-content">
    
    <!-- Top Bar -->
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Detail Penelitian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_penelitian.php">Penelitian Saya</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="row">
        
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
                <div class="card-body p-4">
                    
                    <!-- Title & Meta -->
                    <h3 class="mb-3"><?php echo htmlspecialchars($penelitian['judul']); ?></h3>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary me-2">
                            <i class="bi bi-calendar3 me-1"></i><?php echo $penelitian['tahun']; ?>
                        </span>
                        <?php if ($penelitian['kategori']): ?>
                        <span class="badge bg-info">
                            <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($penelitian['kategori']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <!-- Cover Image -->
                    <?php if ($penelitian['gambar']): ?>
                    <div class="mb-4">
                        <img src="<?php echo BASE_URL; ?>/public/uploads/penelitian/<?php echo htmlspecialchars($penelitian['gambar']); ?>"  
                             class="img-fluid rounded shadow" alt="Cover">
                    </div>
                    <?php endif; ?>
                    
                    <!-- Abstrak -->
                    <?php if ($penelitian['abstrak']): ?>
                    <div class="mb-4">
                        <div class="alert alert-light border">
                            <h5 class="mb-2"><i class="bi bi-file-text me-2"></i>Abstrak</h5>
                            <p style="white-space: pre-line; line-height: 1.8; margin-bottom: 0;">
                                <?php echo nl2br(htmlspecialchars($penelitian['abstrak'])); ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <h5 class="mb-3">Deskripsi</h5>
                        <p style="white-space: pre-line; line-height: 1.8;">
                            <?php echo nl2br(htmlspecialchars($penelitian['deskripsi'])); ?>
                        </p>
                    </div>
                    
                    <!-- Downloads & Links -->
                    <div class="mb-4">
                        <h5 class="mb-3">Dokumen & Publikasi</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if ($penelitian['file_pdf']): ?>
                            <a href="<?php echo BASE_URL; ?>/public/uploads/penelitian/<?php echo htmlspecialchars($penelitian['file_pdf']); ?>"  
                               class="btn btn-danger" target="_blank">
                                <i class="bi bi-file-pdf me-2"></i>Download PDF
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($penelitian['link_publikasi']): ?>
                            <a href="<?php echo htmlspecialchars($penelitian['link_publikasi']); ?>" 
                               class="btn btn-primary" target="_blank" rel="noopener">
                                <i class="bi bi-link-45deg me-2"></i>Lihat Publikasi
                            </a>
                            <?php endif; ?>
                            
                            <?php if (!$penelitian['file_pdf'] && !$penelitian['link_publikasi']): ?>
                            <p class="text-muted mb-0">Tidak ada dokumen atau link publikasi</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <hr>
                    <div class="d-flex gap-2">
                        <a href="edit_penelitian.php?id=<?php echo $penelitian['id']; ?>" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                        <a href="my_penelitian.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            
            <!-- Info Card -->
            <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th width="40%">Tahun:</th>
                            <td><?php echo $penelitian['tahun']; ?></td>
                        </tr>
                        <?php if ($penelitian['kategori']): ?>
                        <tr>
                            <th>Kategori:</th>
                            <td><?php echo htmlspecialchars($penelitian['kategori']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Dibuat:</th>
                            <td><?php echo date('d M Y', strtotime($penelitian['created_at'])); ?></td>
                        </tr>
                        <?php if ($penelitian['updated_at']): ?>
                        <tr>
                            <th>Diupdate:</th>
                            <td><?php echo date('d M Y', strtotime($penelitian['updated_at'])); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="edit_penelitian.php?id=<?php echo $penelitian['id']; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Penelitian
                        </a>
                        <a href="delete_penelitian.php?id=<?php echo $penelitian['id']; ?>" 
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Yakin ingin menghapus penelitian ini?\n\nFile gambar dan PDF akan dihapus permanent.')">
                            <i class="bi bi-trash me-2"></i>Hapus Penelitian
                        </a>
                    </div>
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
