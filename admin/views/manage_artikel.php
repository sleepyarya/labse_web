<?php
// Admin Manage Artikel View
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../controllers/artikelController.php';

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$controller = new ArtikelController();
$page_title = 'Kelola Artikel';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->delete($_GET['id']);
}

// Get pagination and search parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get artikel data
$result = $controller->getAll($page, 10, $search);

// --- PERBAIKAN UTAMA: Hapus duplikat dan gunakan null coalescing ---
// Ambil 'articles' atau 'items', default array kosong
$articles = $result['articles'] ?? $result['items'] ?? [];

// Ambil data paging dengan default value
$total_pages = $result['total_pages'] ?? 1;
$current_page = $result['current_page'] ?? 1;
$total_records = $result['total_records'] ?? 0;

// Get kategori list for mapping
$kategori_map = [];
$kategori_list = $controller->getKategoriList();
foreach ($kategori_list as $kat) {
    $kategori_map[$kat['id']] = $kat;
}

// Handle success/error messages
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';
?>

<div class="admin-content">
    
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Kelola Artikel</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Artikel</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="container-fluid">
        
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            <?php 
            switch($success) {
                case 'add': echo 'Artikel berhasil ditambahkan!'; break;
                case 'edit': echo 'Artikel berhasil diperbarui!'; break;
                case 'delete': echo 'Artikel berhasil dihapus!'; break;
                default: echo 'Operasi berhasil!';
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php 
            switch($error) {
                case 'delete': echo 'Gagal menghapus artikel!'; break;
                case 'notfound': echo 'Artikel tidak ditemukan!'; break;
                default: echo 'Terjadi kesalahan!';
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="bi bi-file-text me-2"></i>Daftar Artikel
                            <span class="badge bg-success ms-2"><?php echo $total_records; ?> Total</span>
                        </h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="artikel_form.php" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Artikel
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari berdasarkan judul, penulis, atau isi artikel..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-success w-100">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <?php if (!empty($articles) && count($articles) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Gambar</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Penulis</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articles as $row): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($row['gambar'])): ?>
                                        <img src="<?php echo BASE_URL; ?>/uploads/artikel/<?php echo htmlspecialchars($row['gambar'] ?? ''); ?>" 
                                             class="rounded" width="60" height="40" style="object-fit: cover;"
                                             onerror="this.src='https://picsum.photos/seed/<?php echo $row['id']; ?>/600/400';">
                                    <?php else: ?>
                                        <img src="https://picsum.photos/seed/<?php echo $row['id']; ?>/600/400" 
                                             class="rounded" width="60" height="40" style="object-fit: cover;" alt="Auto Generated">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['judul'] ?? ''); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo substr(strip_tags($row['isi'] ?? ''), 0, 80) . '...'; ?>
                                    </small>
                                </td>
                                <td>
                                    <?php 
                                    $kat_id = $row['kategori_id'] ?? null;
                                    if ($kat_id && isset($kategori_map[$kat_id])): 
                                        $kat = $kategori_map[$kat_id];
                                    ?>
                                        <span class="badge" style="background-color: <?php echo htmlspecialchars($kat['warna'] ?? '#6c757d'); ?>;">
                                            <?php echo htmlspecialchars($kat['nama_kategori'] ?? ''); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['penulis'] ?? ''); ?></td>
                                <td>
                                    <?php 
                                    if (!empty($row['created_at'])) {
                                        echo date('d M Y', strtotime($row['created_at']));
                                        echo '<br><small class="text-muted">' . date('H:i', strtotime($row['created_at'])) . '</small>';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info view-article-btn" 
                                                data-article='<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>' 
                                                title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a href="artikel_form.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['judul'] ?? ''); ?>')" 
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">
                        <?php echo $search ? 'Tidak ada artikel yang ditemukan dengan kata kunci "' . htmlspecialchars($search) . '"' : 'Belum ada artikel'; ?>
                    </p>
                    <a href="artikel_form.php" class="btn btn-success mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Buat Artikel Pertama
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
    
</div>

<div class="modal fade" id="articleDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Artikel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="articleDetailContent">
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.view-article-btn');
    
    viewButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            try {
                const articleData = JSON.parse(this.getAttribute('data-article'));
                showArticleDetail(articleData);
            } catch (error) {
                console.error('Error parsing article data:', error);
                alert('Terjadi kesalahan saat memuat data artikel');
            }
        });
    });
});

function confirmDelete(id, judul) {
    if (confirm('Apakah Anda yakin ingin menghapus artikel "' + judul + '"?\n\nData yang dihapus tidak dapat dikembalikan.')) {
        window.location.href = '?action=delete&id=' + id;
    }
}

function showArticleDetail(data) {
    const baseUrl = '<?php echo BASE_URL; ?>';
    
    try {
        // Handle null values in JS
        const judul = data.judul || '';
        const penulis = data.penulis || '';
        const isi = data.isi || '';
        const dateStr = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID', {
            year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
        }) : '-';

        const content = `
            <div class="row mb-3">
                <div class="col-md-4">
                    ${data.gambar ? 
                        `<img src="${baseUrl}/public/uploads/artikel/${data.gambar}" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;" onerror="this.parentElement.innerHTML='<div class=\\'bg-light rounded d-flex align-items-center justify-content-center\\' style=\\'height: 200px;\\'><i class=\\'bi bi-image text-muted\\' style=\\'font-size: 3rem;\\'></i></div>';">` : 
                        `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        </div>`
                    }
                </div>
                <div class="col-md-8">
                    <h4 class="mb-3">${judul}</h4>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Penulis:</strong></div>
                        <div class="col-sm-8">${penulis}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Tanggal Publish:</strong></div>
                        <div class="col-sm-8">${dateStr}</div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <h6><strong>Isi Artikel:</strong></h6>
                    <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                        ${isi}
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('articleDetailContent').innerHTML = content;
        const modalElement = document.getElementById('articleDetailModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
    } catch (error) {
        console.error('Error in showArticleDetail:', error);
        alert('Terjadi kesalahan saat menampilkan detail artikel');
    }
}
</script>

<?php
pg_close($conn);
include '../includes/admin_footer.php';
?>