<?php
require_once __DIR__ . '/../../includes/config.php';

// Get parameters
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : '';
$kategori_id = isset($_GET['kategori_id']) ? (int)$_GET['kategori_id'] : '';

// Build WHERE clause
$where_conditions = [];
$params = [];
$param_count = 1;

if ($search) {
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

// Get total count
$count_query = "SELECT COUNT(*) as total 
                FROM hasil_penelitian hp 
                LEFT JOIN kategori_penelitian kp ON hp.kategori_id = kp.id 
                $where_clause";
$count_result = empty($params) ? pg_query($conn, $count_query) : pg_query_params($conn, $count_query, $params);
$total = pg_fetch_assoc($count_result)['total'];
$has_more = ($offset + $limit) < $total;

// Get data
$query = "SELECT hp.*, p.nama as personil_nama, kp.nama_kategori as kat_nama, kp.warna as kat_warna
          FROM hasil_penelitian hp
          LEFT JOIN personil p ON hp.personil_id = p.id
          LEFT JOIN kategori_penelitian kp ON hp.kategori_id = kp.id
          $where_clause 
          ORDER BY hp.tahun DESC, hp.created_at DESC 
          LIMIT $limit OFFSET $offset";
$result = empty($params) ? pg_query($conn, $query) : pg_query_params($conn, $query, $params);

// Generate HTML
$html = '';

while ($item = pg_fetch_assoc($result)) {
    $html .= '<div class="col-md-6 col-lg-4 penelitian-item" data-aos="fade-up">';
    $html .= '    <div class="card h-100 shadow-sm border-0 hover-card">';
    
    // Image
    if ($item['gambar']) {
        $html .= '<img src="' . BASE_URL . '/public/uploads/penelitian/' . htmlspecialchars($item['gambar']) . '" ';
        $html .= '     class="card-img-top" alt="' . htmlspecialchars($item['judul']) . '"';
        $html .= '     style="height: 200px; object-fit: cover;"';
        $html .= '     onerror="this.src=\'' . BASE_URL . '/public/img/no-image.png\'">';
    } else {
        $html .= '<div class="bg-gradient d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">';
        $html .= '    <i class="bi bi-journal-text text-white" style="font-size: 3rem;"></i>';
        $html .= '</div>';
    }
    
    $html .= '    <div class="card-body d-flex flex-column">';
    
    // Meta Info
    $html .= '        <div class="mb-2 d-flex gap-2 flex-wrap">';
    $html .= '            <span class="badge bg-primary">';
    $html .= '                <i class="bi bi-calendar3 me-1"></i>' . $item['tahun'];
    $html .= '            </span>';
    
    if ($item['kat_nama']) {
        $warna = $item['kat_warna'] ?? '#17a2b8';
        $html .= '        <span class="badge" style="background-color: ' . htmlspecialchars($warna) . ';">' . htmlspecialchars($item['kat_nama']) . '</span>';
    } elseif ($item['kategori']) {
        $html .= '        <span class="badge bg-secondary">' . htmlspecialchars($item['kategori']) . '</span>';
    }
    
    $html .= '        </div>';
    
    // Title
    $html .= '        <h5 class="card-title">' . htmlspecialchars($item['judul']) . '</h5>';
    
    // Description
    $deskripsi = strip_tags($item['deskripsi']);
    $html .= '        <p class="card-text text-muted small">';
    $html .= strlen($deskripsi) > 120 ? substr($deskripsi, 0, 120) . '...' : $deskripsi;
    $html .= '        </p>';
    
    // Meta
    $html .= '        <div class="mt-auto">';
    
    if ($item['personil_nama']) {
        $html .= '        <div class="d-flex align-items-center text-muted small mb-3">';
        $html .= '            <i class="bi bi-person me-1"></i>';
        $html .= '            <span>' . htmlspecialchars($item['personil_nama']) . '</span>';
        $html .= '        </div>';
    }
    
    $html .= '            <div class="d-flex gap-2">';
    $html .= '                <a href="detail.php?id=' . $item['id'] . '" class="btn btn-outline-primary flex-grow-1">';
    $html .= '                    Lihat Detail <i class="bi bi-arrow-right ms-2"></i>';
    $html .= '                </a>';
    
    if ($item['file_pdf']) {
        $html .= '            <a href="' . BASE_URL . '/public/uploads/penelitian/' . htmlspecialchars($item['file_pdf']) . '" ';
        $html .= '               class="btn btn-outline-danger" target="_blank" title="Download PDF">';
        $html .= '                <i class="bi bi-file-pdf"></i>';
        $html .= '            </a>';
    }
    
    $html .= '            </div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '</div>';
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
