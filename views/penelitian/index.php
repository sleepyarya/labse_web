<?php
// Public view - List all hasil penelitian
$page_title = 'Hasil Penelitian';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : '';
$kategori_id = isset($_GET['kategori_id']) ? (int)$_GET['kategori_id'] : '';

// Pagination configuration
$items_per_page = 6;
// Initial load offset 0
$offset = 0;

// Build WHERE clause
$where_conditions = [];
$params = [];
$param_count = 1;

if ($search) {
    // Search in judul, deskripsi, abstrak, and category name
    $where_conditions[] = "(hp.judul ILIKE $" . $param_count . " OR hp.deskripsi ILIKE $" . $param_count . " OR hp.abstrak ILIKE $" . $param_count . " OR kp.nama_kategori ILIKE $" . $param_count . ")";
    $params[] = "%$search%";
    $param_count++;
}

if ($tahun) {
    $where_conditions[] = "hp.tahun = $" . $param_count;
    $params[] = $tahun;
    $param_count++;
}

if ($kategori_id) {
    $where_conditions[] = "hp.kategori_id = $" . $param_count;
    $params[] = $kategori_id;
    $param_count++;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total (need join for search by category name)
$count_query = "SELECT COUNT(*) as total 
                FROM hasil_penelitian hp 
                LEFT JOIN kategori_penelitian kp ON hp.kategori_id = kp.id 
                $where_clause";
$count_result = empty($params) ? pg_query($conn, $count_query) : pg_query_params($conn, $count_query, $params);
$total_items = pg_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_items / $items_per_page);

// Get data
$query = "SELECT hp.*, p.nama as personil_nama, kp.nama_kategori as kat_nama, kp.warna as kat_warna
          FROM hasil_penelitian hp
          LEFT JOIN personil p ON hp.personil_id = p.id
          LEFT JOIN kategori_penelitian kp ON hp.kategori_id = kp.id
          $where_clause 
          ORDER BY hp.tahun DESC, hp.created_at DESC 
          LIMIT $items_per_page OFFSET $offset";
$result = empty($params) ? pg_query($conn, $query) : pg_query_params($conn, $query, $params);

// Get available years for filter
$years_query = "SELECT DISTINCT tahun FROM hasil_penelitian ORDER BY tahun DESC";
$years_result = pg_query($conn, $years_query);

// Get categories for filter
$cat_query = "SELECT * FROM kategori_penelitian WHERE is_active = TRUE ORDER BY nama_kategori ASC";
$cat_result = pg_query($conn, $cat_query);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Hasil Penelitian</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Publikasi hasil penelitian yang telah dilakukan oleh Lab Software Engineering</p>
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
                        <label class="form-label"><i class="bi bi-search me-1"></i>Cari Penelitian</label>
                        <input type="text" name="search" class="form-control" placeholder="Judul, deskripsi, atau abstrak..." value="<?php echo htmlspecialchars($search); ?>">
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
        
        <div class="row g-4" id="penelitian-container">
            <?php while ($item = pg_fetch_assoc($result)): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    
                    <!-- Image -->
                    <?php if ($item['gambar']): ?>
                    <img src="<?php echo BASE_URL; ?>/public/uploads/penelitian/<?php echo htmlspecialchars($item['gambar']); ?>"  
                         class="card-img-top" alt="<?php echo htmlspecialchars($item['judul']); ?>"
                         style="height: 200px; object-fit: cover;"
                         onerror="this.src='<?php echo BASE_URL; ?>/public/img/no-image.png'">
                    <?php else: ?>
                    <div class="bg-gradient d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-journal-text text-white" style="font-size: 3rem;"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <!-- Meta Info -->
                        <div class="mb-2 d-flex gap-2 flex-wrap">
                            <span class="badge bg-primary">
                                <i class="bi bi-calendar3 me-1"></i><?php echo $item['tahun']; ?>
                            </span>
                            <?php if ($item['kat_nama']): ?>
                            <span class="badge" style="background-color: <?php echo htmlspecialchars($item['kat_warna'] ?? '#17a2b8'); ?>">
                                <?php echo htmlspecialchars($item['kat_nama']); ?>
                            </span>
                            <?php elseif ($item['kategori']): ?>
                            <span class="badge bg-secondary">
                                <?php echo htmlspecialchars($item['kategori']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Title -->
                        <h5 class="card-title"><?php echo htmlspecialchars($item['judul']); ?></h5>
                        
                        <!-- Description -->
                        <p class="card-text text-muted small">
                            <?php 
                            $deskripsi = strip_tags($item['deskripsi']);
                            echo strlen($deskripsi) > 120 ? substr($deskripsi, 0, 120) . '...' : $deskripsi; 
                            ?>
                        </p>
                        
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
                                
                                <!-- PDF Button -->
                                <?php if ($item['file_pdf']): ?>
                                <a href="<?php echo BASE_URL; ?>/public/uploads/penelitian/<?php echo htmlspecialchars($item['file_pdf']); ?>" 
                                   class="btn btn-outline-danger" target="_blank" title="Download PDF">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <!-- Load More Section -->
        <?php if ($total_items > $items_per_page): ?>
        <div class="text-center mt-5" id="load-more-container">
            <button id="load-more-btn" class="btn btn-primary btn-lg load-more-btn" data-offset="<?php echo $items_per_page; ?>">
                <i class="bi bi-chevron-down me-2"></i>
                Muat Lebih Banyak
                <i class="bi bi-chevron-down ms-2"></i>
            </button>
            <p class="text-muted mt-2 small">
                <span id="loaded-count"><?php echo min($items_per_page, $total_items); ?></span> dari <span id="total-count"><?php echo $total_items; ?></span> penelitian
            </p>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 5rem;"></i>
            <h4 class="mt-3 text-muted">
                <?php echo ($search || $tahun || $kategori_id) ? 'Tidak Ada Hasil' : 'Belum Ada Penelitian'; ?>
            </h4>
            <p class="text-muted">
                <?php echo ($search || $tahun || $kategori_id) ? 'Coba ubah filter pencarian Anda' : 'Hasil penelitian akan ditampilkan di sini'; ?>
            </p>
        </div>
        
        <?php endif; ?>
        
    </div>
</section>

<style>
/* Load More Button Animation */
.load-more-btn {
    position: relative;
    overflow: hidden;
    animation: pulse 2s ease-in-out infinite;
    transition: all 0.3s ease;
}

.load-more-btn:hover {
    transform: scale(1.05);
    animation: none;
}

@keyframes pulse {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

/* Item Fade In Animation */
.penelitian-item {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Loading State */
.load-more-btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.load-more-btn.loading .bi-chevron-down {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const container = document.getElementById('penelitian-container');
    const loadedCount = document.getElementById('loaded-count');
    const loadMoreContainer = document.getElementById('load-more-container');
    
    // Get filter params from URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search') || '';
    const tahunParam = urlParams.get('tahun') || '';
    const kategoriParam = urlParams.get('kategori_id') || '';
    
    if (!loadMoreBtn) return;
    
    loadMoreBtn.addEventListener('click', function() {
        const offset = parseInt(this.getAttribute('data-offset'));
        const limit = 6; // Same as PHP items_per_page
        
        // Set loading state
        loadMoreBtn.classList.add('loading');
        loadMoreBtn.innerHTML = '<i class="bi bi-chevron-down me-2"></i> Memuat... <i class="bi bi-chevron-down ms-2"></i>';
        
        // Build URL
        let url = `load_more_penelitian.php?offset=${offset}&limit=${limit}`;
        if (searchParam) url += `&search=${encodeURIComponent(searchParam)}`;
        if (tahunParam) url += `&tahun=${encodeURIComponent(tahunParam)}`;
        if (kategoriParam) url += `&kategori_id=${encodeURIComponent(kategoriParam)}`;
        
        // AJAX request
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.html) {
                    // Create temporary container
                    const temp = document.createElement('div');
                    temp.innerHTML = data.html;
                    
                    // Append new items
                    while (temp.firstChild) {
                        container.appendChild(temp.firstChild);
                    }
                    
                    // Re-initialize AOS for new elements
                    if (typeof AOS !== 'undefined') {
                        AOS.refresh();
                    }
                    
                    // Update offset
                    loadMoreBtn.setAttribute('data-offset', offset + limit);
                    
                    // Update counter
                    loadedCount.textContent = data.loaded;
                    
                    // Remove loading state
                    loadMoreBtn.classList.remove('loading');
                    loadMoreBtn.innerHTML = '<i class="bi bi-chevron-down me-2"></i> Muat Lebih Banyak <i class="bi bi-chevron-down ms-2"></i>';
                    
                    // Hide button if no more items
                    if (!data.has_more) {
                        loadMoreContainer.innerHTML = '<p class="text-success mt-4"><i class="bi bi-check-circle me-2"></i>Semua penelitian telah ditampilkan</p>';
                    }
                } else {
                    alert('Gagal memuat penelitian');
                    loadMoreBtn.classList.remove('loading');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat penelitian');
                loadMoreBtn.classList.remove('loading');
                loadMoreBtn.innerHTML = '<i class="bi bi-chevron-down me-2"></i> Muat Lebih Banyak <i class="bi bi-chevron-down ms-2"></i>';
            });
    });
});
</script>

<?php
pg_close($conn);
require_once __DIR__ . '/../../includes/footer.php';
?>
