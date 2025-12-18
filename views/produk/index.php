<?php
// Public view - List all produk
$page_title = 'Produk';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : '';
$kategori_id = isset($_GET['kategori_id']) ? (int)$_GET['kategori_id'] : '';

// Pagination
$items_per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Build WHERE clause
$where_conditions = [];
$params = [];
$param_count = 1;

if ($search) {
    // Search in relevant fields + category name
    $where_conditions[] = "(p.nama_produk ILIKE $" . $param_count . " OR p.deskripsi ILIKE $" . $param_count . " OR p.teknologi ILIKE $" . $param_count . " OR kp.nama_kategori ILIKE $" . $param_count . ")";
    $params[] = "%$search%";
    $param_count++;
}

if ($tahun) {
    $where_conditions[] = "p.tahun = $" . $param_count;
    $params[] = $tahun;
    $param_count++;
}

if ($kategori_id) {
    $where_conditions[] = "p.kategori_id = $" . $param_count;
    $params[] = $kategori_id;
    $param_count++;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total (need join for search by category)
$count_query = "SELECT COUNT(*) as total 
                FROM produk p 
                LEFT JOIN kategori_produk kp ON p.kategori_id = kp.id 
                $where_clause";
$count_result = empty($params) ? pg_query($conn, $count_query) : pg_query_params($conn, $count_query, $params);
$total_items = pg_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_items / $items_per_page);

// Get data
$query = "SELECT p.*, pr.nama as personil_nama, kp.nama_kategori as kat_nama, kp.warna as kat_warna
          FROM produk p
          LEFT JOIN personil pr ON p.personil_id = pr.id
          LEFT JOIN kategori_produk kp ON p.kategori_id = kp.id
          $where_clause 
          ORDER BY p.tahun DESC, p.created_at DESC 
          LIMIT $items_per_page OFFSET $offset";
$result = empty($params) ? pg_query($conn, $query) : pg_query_params($conn, $query, $params);

// Get available years for filter
$years_query = "SELECT DISTINCT tahun FROM produk ORDER BY tahun DESC";
$years_result = pg_query($conn, $years_query);

// Get categories for filter
$cat_query = "SELECT * FROM kategori_produk WHERE is_active = TRUE ORDER BY nama_kategori ASC";
$cat_result = pg_query($conn, $cat_query);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Produk Lab Software Engineering</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Hasil karya dan inovasi dari Lab Software Engineering</p>
    </div>
</div>

<!-- Content Section -->
<section class="content-section">
    <div class="container">
        
        <!-- Filter Section -->
        <div class="card mb-4" data-aos="fade-up">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-search me-1"></i>Cari Produk</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama produk atau teknologi..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="bi bi-calendar-range me-1"></i>Tahun</label>
                        <select name="tahun" class="form-select">
                            <option value="">Semua Tahun</option>
                            <?php while ($year = pg_fetch_assoc($years_result)): ?>
                            <option value="<?php echo $year['tahun']; ?>" <?php echo $tahun == $year['tahun'] ? 'selected' : ''; ?>>
                                <?php echo $year['tahun']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="bi bi-tags me-1"></i>Kategori</label>
                        <select name="kategori_id" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php while ($cat = pg_fetch_assoc($cat_result)): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $kategori_id == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter me-1"></i>Filter
                        </button>
                    </div>
                </form>
                <?php if ($search || $tahun || $kategori_id): ?>
                <div class="mt-2">
                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Reset Filter
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (pg_num_rows($result) > 0): ?>
        
        <div class="row g-4">
            <?php while ($item = pg_fetch_assoc($result)): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    
                    <!-- Image -->
                    <?php if ($item['gambar']): ?>
                    <img src="<?php echo BASE_URL; ?>/public/uploads/produk/<?php echo htmlspecialchars($item['gambar']); ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($item['nama_produk']); ?>"
                         style="height: 200px; object-fit: cover;"
                         onerror="this.src='https://picsum.photos/seed/<?php echo $item['id']; ?>/600/400'">
                    <?php else: ?>
                    <!-- Auto Generated Image -->
                    <img src="https://picsum.photos/seed/<?php echo $item['id']; ?>/600/400" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($item['nama_produk']); ?>"
                         style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <!-- Meta Info -->
                        <div class="mb-2 d-flex gap-2 flex-wrap">
                            <span class="badge bg-primary">
                                <i class="bi bi-calendar3 me-1"></i><?php echo $item['tahun']; ?>
                            </span>
                            <?php if ($item['kat_nama']): ?>
                            <span class="badge" style="background-color: <?php echo htmlspecialchars($item['kat_warna'] ?? '#0d6efd'); ?>">
                                <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($item['kat_nama']); ?>
                            </span>
                            <?php elseif ($item['kategori']): ?>
                            <span class="badge bg-secondary">
                                <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($item['kategori']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Title -->
                        <h5 class="card-title"><?php echo htmlspecialchars($item['nama_produk']); ?></h5>
                        
                        <!-- Description -->
                        <p class="card-text text-muted small">
                            <?php 
                            $deskripsi = strip_tags($item['deskripsi']);
                            echo strlen($deskripsi) > 100 ? substr($deskripsi, 0, 100) . '...' : $deskripsi; 
                            ?>
                        </p>
                        
                        <!-- Teknologi -->
                        <?php if ($item['teknologi']): ?>
                        <div class="mb-2">
                            <small class="text-muted"><i class="bi bi-gear me-1"></i><?php echo htmlspecialchars($item['teknologi']); ?></small>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Meta -->
                        <div class="mt-auto">
                            <?php if ($item['personil_nama']): ?>
                            <div class="d-flex align-items-center text-muted small mb-3">
                                <i class="bi bi-person me-1"></i>
                                <span><?php echo htmlspecialchars($item['personil_nama']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex gap-2">
                                <!-- Detail Button -->
                                <a href="detail.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-primary flex-grow-1">
                                    Lihat Detail <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                                
                                <!-- Demo Button -->
                                <?php if ($item['link_demo']): ?>
                                <a href="<?php echo htmlspecialchars($item['link_demo']); ?>" 
                                   class="btn btn-outline-success" target="_blank" title="Demo">
                                    <i class="bi bi-play-circle"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&tahun=<?php echo $tahun; ?>&kategori_id=<?php echo $kategori_id; ?>">
                        <i class="bi bi-chevron-left"></i> Sebelumnya
                    </a>
                </li>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&tahun=<?php echo $tahun; ?>&kategori_id=<?php echo $kategori_id; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&tahun=<?php echo $tahun; ?>&kategori_id=<?php echo $kategori_id; ?>">
                        Selanjutnya <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php else: ?>
        
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 5rem;"></i>
            <h4 class="mt-3 text-muted">
                <?php echo ($search || $tahun || $kategori_id) ? 'Tidak Ada Hasil' : 'Belum Ada Produk'; ?>
            </h4>
            <p class="text-muted">
                <?php echo ($search || $tahun || $kategori_id) ? 'Coba ubah filter pencarian Anda' : 'Produk akan ditampilkan di sini'; ?>
            </p>
        </div>
        
        <?php endif; ?>
        
    </div>
</section>

<style>
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.card-img-top {
    transition: transform 0.3s ease;
}

.hover-card:hover .card-img-top {
    transform: scale(1.05);
}
</style>

<?php
pg_close($conn);
require_once __DIR__ . '/../../includes/footer.php';
?>
