<?php
require_once __DIR__ . '/../../includes/config.php';

// Get parameters
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : '';

// Build WHERE clause
$where_conditions = [];
$params = [];
$param_count = 1;

if ($search) {
    $where_conditions[] = "(a.judul ILIKE $" . $param_count . " OR a.isi ILIKE $" . $param_count . " OR a.penulis ILIKE $" . $param_count . ")";
    $params[] = "%$search%";
    $param_count++;
}

if ($kategori) {
    $where_conditions[] = "a.kategori_id = $" . $param_count;
    $params[] = $kategori;
    $param_count++;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count (with filters)
$count_query = "SELECT COUNT(*) as total FROM artikel a $where_clause";
$count_result = empty($params) ? pg_query($conn, $count_query) : pg_query_params($conn, $count_query, $params);
$total = pg_fetch_assoc($count_result)['total'];
$has_more = ($offset + $limit) < $total;

// Get articles
$query = "SELECT a.*, k.nama_kategori, k.warna as kategori_warna 
          FROM artikel a
          LEFT JOIN kategori_artikel k ON a.kategori_id = k.id
          $where_clause 
          ORDER BY a.created_at DESC 
          LIMIT $limit OFFSET $offset";

$result = empty($params) ? pg_query($conn, $query) : pg_query_params($conn, $query, $params);

// Generate HTML
$html = '';
$delay = 0;

while ($row = pg_fetch_assoc($result)) {
    $delay += 100;
    
    // Use uploaded image if exists, otherwise use placeholder
    if (!empty($row['gambar']) && file_exists('../../public/uploads/artikel/' . $row['gambar'])) {
        $img_url = BASE_URL . '/public/uploads/artikel/' . $row['gambar'];
    } else {
        $img_url = "https://picsum.photos/seed/" . $row['id'] . "/600/400";
    }
    
    $html .= '<div class="col-md-6 col-lg-4 article-item" data-aos="fade-up" data-aos-delay="' . min($delay, 300) . '">';
    $html .= '    <div class="card h-100 hover-card border-0 shadow-sm">';
    $html .= '        <div class="position-relative overflow-hidden">';
    $html .= '            <img src="' . $img_url . '" class="card-img-top blog-card-img" alt="' . htmlspecialchars($row['judul']) . '" style="height: 200px; object-fit: cover;">';
    
    if (!empty($row['nama_kategori'])) {
        $warna = $row['kategori_warna'] ?? '#0d6efd';
        $html .= '        <span class="badge position-absolute top-0 end-0 m-2" style="background-color: ' . htmlspecialchars($warna) . ';">';
        $html .=              htmlspecialchars($row['nama_kategori']);
        $html .= '        </span>';
    }
    
    $html .= '        </div>';
    $html .= '        <div class="card-body d-flex flex-column">';
    $html .= '            <h5 class="card-title">' . htmlspecialchars($row['judul']) . '</h5>';
    $html .= '            <div class="mb-3">';
    $html .= '                <span class="badge bg-light text-dark">';
    $html .= '                    <i class="bi bi-calendar3 me-1"></i>' . date('d M Y', strtotime($row['created_at']));
    $html .= '                </span>';
    $html .= '            </div>';
    $html .= '            <p class="text-muted small mb-2">';
    $html .= '                <i class="bi bi-person me-1"></i>' . htmlspecialchars($row['penulis']);
    $html .= '            </p>';
    $html .= '            <p class="card-text text-muted mb-4">' . substr(strip_tags($row['isi']), 0, 120) . '...</p>';
    $html .= '            <a href="detail.php?id=' . $row['id'] . '" class="btn btn-outline-primary mt-auto">';
    $html .= '                Baca Selengkapnya <i class="bi bi-arrow-right ms-2"></i>';
    $html .= '            </a>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '</div>';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'html' => $html,
    'has_more' => $has_more,
    'total' => $total,
    'loaded' => $offset + pg_num_rows($result)
]);

pg_close($conn);
?>
