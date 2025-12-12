<?php
require_once __DIR__ . '/../../includes/config.php';
$page_title = 'Blog & Artikel';
include '../../includes/header.php';
include '../../includes/navbar.php';

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$kategori_filter = isset($_GET['kategori']) ? (int)$_GET['kategori'] : '';

// Pagination configuration
$items_per_page = 6;
// Offset selalu 0 untuk initial load page pertama kali
$offset = 0;

// Build WHERE clause
$where_conditions = [];
$params = [];
$param_count = 1;

if ($search) {
    $where_conditions[] = "(a.judul ILIKE $" . $param_count . " OR a.isi ILIKE $" . $param_count . " OR a.penulis ILIKE $" . $param_count . ")";
    $params[] = "%$search%";
    $param_count++;
}

if ($kategori_filter) {
    $where_conditions[] = "a.kategori_id = $" . $param_count;
    $params[] = $kategori_filter;
    $param_count++;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_query = "SELECT COUNT(*) as total FROM artikel a $where_clause";
$count_result = empty($params) ? pg_query($conn, $count_query) : pg_query_params($conn, $count_query, $params);
$total_articles = pg_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_articles / $items_per_page);

// Get articles with kategori
$query = "SELECT a.*, k.nama_kategori, k.warna as kategori_warna 
          FROM artikel a
          LEFT JOIN kategori_artikel k ON a.kategori_id = k.id
          $where_clause 
          ORDER BY a.created_at DESC 
          LIMIT $items_per_page OFFSET $offset";
$result = empty($params) ? pg_query($conn, $query) : pg_query_params($conn, $query, $params);

// Get all categories for filter
$kategori_query = "SELECT ka.id, ka.nama_kategori, ka.warna, COUNT(a.id) as total_artikel 
                   FROM kategori_artikel ka 
                   LEFT JOIN artikel a ON ka.id = a.kategori_id 
                   WHERE ka.is_active = TRUE 
                   GROUP BY ka.id, ka.nama_kategori, ka.warna 
                   ORDER BY ka.nama_kategori ASC";
$kategori_result = pg_query($conn, $kategori_query);
$kategori_list = [];
while ($row = pg_fetch_assoc($kategori_result)) {
    $kategori_list[] = $row;
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Blog & Artikel</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Temukan artikel, penelitian, dan insight terbaru tentang software engineering</p>
    </div>
</div>

<!-- Blog Grid -->
<section class="content-section">
    <div class="container">
        
        <!-- Filter Section -->
        <div class="card mb-4" data-aos="fade-up">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-search me-1"></i>Cari Artikel</label>
                        <input type="text" name="search" class="form-control" placeholder="Judul, isi, atau penulis..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-tags me-1"></i>Kategori</label>
                        <select name="kategori" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($kategori_list as $kat): ?>
                            <option value="<?php echo $kat['id']; ?>" <?php echo $kategori_filter == $kat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter me-1"></i>Filter
                        </button>
                    </div>
                </form>
                <?php if ($search || $kategori_filter): ?>
                <div class="mt-2">
                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Reset Filter
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Articles Grid -->
        <?php if (pg_num_rows($result) > 0): ?>
        <div class="row g-4" id="articles-container">
            <?php
            $delay = 0;
            while ($row = pg_fetch_assoc($result)) {
                $delay += 100;
                // Use uploaded image if exists, otherwise use placeholder
                if (!empty($row['gambar']) && file_exists('../../public/uploads/artikel/' . $row['gambar'])) {
                    $img_url = BASE_URL . '/public/uploads/artikel/' . $row['gambar'];
                } else {
                    $img_url = "https://picsum.photos/seed/" . $row['id'] . "/600/400";
                }
                ?>
                <div class="col-md-6 col-lg-4 article-item" data-aos="fade-up" data-aos-delay="<?php echo min($delay, 300); ?>">
                    <div class="card h-100 hover-card border-0 shadow-sm">
                        <div class="position-relative overflow-hidden">
                            <img src="<?php echo $img_url; ?>" class="card-img-top blog-card-img" alt="<?php echo htmlspecialchars($row['judul']); ?>" style="height: 200px; object-fit: cover;">
                            <?php if (!empty($row['nama_kategori'])): ?>
                            <span class="badge position-absolute top-0 end-0 m-2" 
                                  style="background-color: <?php echo htmlspecialchars($row['kategori_warna'] ?? '#0d6efd'); ?>;">
                                <?php echo htmlspecialchars($row['nama_kategori']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['judul']); ?></h5>
                            <div class="mb-3">
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-calendar3 me-1"></i><?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                </span>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($row['penulis']); ?>
                            </p>
                            <p class="card-text text-muted mb-4"><?php echo substr(strip_tags($row['isi']), 0, 120); ?>...</p>
                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary mt-auto">
                                Baca Selengkapnya <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        
        <!-- Load More Section -->
        <?php if ($total_articles > $items_per_page): ?>
        <div class="text-center mt-5" id="load-more-container">
            <button id="load-more-btn" class="btn btn-primary btn-lg load-more-btn" data-offset="<?php echo $items_per_page; ?>">
                <i class="bi bi-chevron-down me-2"></i>
                Muat Lebih Banyak
                <i class="bi bi-chevron-down ms-2"></i>
            </button>
            <p class="text-muted mt-2 small">
                <span id="loaded-count"><?php echo min($items_per_page, $total_articles); ?></span> dari <span id="total-count"><?php echo $total_articles; ?></span> artikel
            </p>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="text-center py-5" data-aos="fade-up">
            <i class="bi bi-file-earmark-text text-muted" style="font-size: 5rem;"></i>
            <h3 class="mt-3">
                <?php echo ($search || $kategori_filter) ? 'Tidak ada artikel ditemukan' : 'Belum ada artikel'; ?>
            </h3>
            <p class="text-muted">
                <?php echo ($search || $kategori_filter) ? 'Coba ubah filter pencarian Anda' : 'Artikel akan segera ditambahkan. Silakan kembali lagi nanti.'; ?>
            </p>
            <?php if ($search || $kategori_filter): ?>
            <a href="index.php" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>Lihat Semua Artikel
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Styles for Blog Page -->
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

/* Article Fade In Animation */
.article-item {
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
    const articlesContainer = document.getElementById('articles-container');
    const loadedCount = document.getElementById('loaded-count');
    const loadMoreContainer = document.getElementById('load-more-container');
    
    // Get filter params from URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search') || '';
    const kategoriParam = urlParams.get('kategori') || '';
    
    if (!loadMoreBtn) return;
    
    loadMoreBtn.addEventListener('click', function() {
        const offset = parseInt(this.getAttribute('data-offset'));
        const limit = 6; // Same as PHP items_per_page
        
        // Set loading state
        loadMoreBtn.classList.add('loading');
        loadMoreBtn.innerHTML = '<i class="bi bi-chevron-down me-2"></i> Memuat... <i class="bi bi-chevron-down ms-2"></i>';
        
        // Build URL
        let url = `load_more_artikel.php?offset=${offset}&limit=${limit}`;
        if (searchParam) url += `&search=${encodeURIComponent(searchParam)}`;
        if (kategoriParam) url += `&kategori=${encodeURIComponent(kategoriParam)}`;
        
        // AJAX request
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.html) {
                    // Create temporary container
                    const temp = document.createElement('div');
                    temp.innerHTML = data.html;
                    
                    // Append new articles
                    while (temp.firstChild) {
                        articlesContainer.appendChild(temp.firstChild);
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
                    
                    // Hide button if no more articles
                    if (!data.has_more) {
                        loadMoreContainer.innerHTML = '<p class="text-success mt-4"><i class="bi bi-check-circle me-2"></i>Semua artikel telah ditampilkan</p>';
                    }
                } else {
                    alert('Gagal memuat artikel');
                    loadMoreBtn.classList.remove('loading');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat artikel');
                loadMoreBtn.classList.remove('loading');
                loadMoreBtn.innerHTML = '<i class="bi bi-chevron-down me-2"></i> Muat Lebih Banyak <i class="bi bi-chevron-down ms-2"></i>';
            });
    });
});
</script>

<?php
pg_close($conn);
include '../../includes/footer.php';
?>
