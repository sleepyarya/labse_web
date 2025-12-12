-- =====================================================
-- CONTOH PENGGUNAAN ADVANCED FEATURES
-- Database LABSE - Materialized Views & Stored Procedures
-- Created: 2025-12-03
-- =====================================================

-- =====================================================
-- SECTION 1: MENGGUNAKAN MATERIALIZED VIEWS
-- =====================================================

-- 1.1 Dashboard Statistics
-- Mendapatkan statistik lengkap untuk dashboard
SELECT * FROM mv_dashboard_statistics;

-- Output example:
-- total_personil | total_mahasiswa | total_artikel | total_penelitian | ...
-- 6              | 5               | 6             | 4                | ...

-- 1.2 Personil Contributions & Ranking
-- Melihat ranking personil berdasarkan kontribusi
SELECT 
    nama,
    jabatan,
    total_penelitian,
    total_pengabdian,
    total_produk,
    contribution_score
FROM mv_personil_contributions
ORDER BY contribution_score DESC
LIMIT 10;

-- Filter personil dengan kontribusi tinggi
SELECT * FROM mv_personil_contributions
WHERE contribution_score > 10;

-- 1.3 Recent Activities (Timeline)
-- Mendapatkan aktivitas terbaru untuk dashboard
SELECT 
    activity_type,
    title,
    author,
    location,
    TO_CHAR(created_at, 'DD Mon YYYY HH24:MI') as formatted_date
FROM mv_recent_activities
LIMIT 20;

-- Filter by activity type
SELECT * FROM mv_recent_activities
WHERE activity_type = 'penelitian';

-- 1.4 Yearly Research Summary
-- Statistik penelitian per tahun
SELECT 
    tahun,
    kategori,
    total_penelitian,
    total_terpublikasi
FROM mv_yearly_research_summary
ORDER BY tahun DESC;

-- 1.5 Popular Content
-- Top konten populer
SELECT 
    content_type,
    title,
    creator,
    ROUND(popularity_score, 2) as score
FROM mv_popular_content
ORDER BY popularity_score DESC
LIMIT 15;

-- =====================================================
-- SECTION 2: REFRESH MATERIALIZED VIEWS
-- =====================================================

-- 2.1 Refresh individual view
REFRESH MATERIALIZED VIEW mv_dashboard_statistics;
REFRESH MATERIALIZED VIEW CONCURRENTLY mv_personil_contributions;

-- 2.2 Refresh all views at once
SELECT fn_refresh_all_materialized_views();

-- Output: "All materialized views refreshed successfully in 00:00:00.123"

-- =====================================================
-- SECTION 3: MENGGUNAKAN HELPER FUNCTIONS
-- =====================================================

-- 3.1 Calculate Personil Score
-- Menghitung score kontribusi seorang personil
SELECT fn_calculate_personil_score(1) as score;

-- 3.2 Get Latest Activities for Personil
-- Mendapatkan 5 aktivitas terbaru dari personil ID 1
SELECT * FROM fn_get_latest_activities(1, 5);

-- 3.3 Sanitize Input (untuk keamanan)
SELECT fn_sanitize_input('Test<script>alert("xss")</script>') as clean_text;
-- Output: "Testscriptalertxssscript"

-- =====================================================
-- SECTION 4: MENGGUNAKAN STORED PROCEDURES (INSERT)
-- =====================================================

-- 4.1 Create Article
SELECT sp_create_article(
    'Judul Artikel Baru',
    'Ini adalah isi artikel yang lengkap dan informatif...',
    'Dr. Ahmad Fauzi',
    'artikel-baru.jpg'
) as new_article_id;

-- 4.2 Create Penelitian
SELECT sp_create_penelitian(
    'Penelitian Machine Learning Terbaru',          -- judul
    'Deskripsi lengkap penelitian...',              -- deskripsi
    2024,                                            -- tahun
    'Terapan',                                       -- kategori
    'Abstrak penelitian yang detail...',            -- abstrak
    'penelitian.jpg',                                -- gambar
    'penelitian.pdf',                                -- file_pdf
    'https://journal.com/paper123',                 -- link_publikasi
    1                                                -- personil_id
) as new_penelitian_id;

-- 4.3 Create Pengabdian
SELECT sp_create_pengabdian(
    'Workshop AI untuk Guru',                       -- judul
    'Pelatihan AI untuk guru-guru SMA...',          -- deskripsi
    '2024-12-15',                                    -- tanggal
    'Aula SMAN 1 Semarang',                         -- lokasi
    'Lab Software Engineering',                      -- penyelenggara
    'workshop.jpg',                                  -- gambar
    1                                                -- personil_id
) as new_pengabdian_id;

-- 4.4 Create Produk
SELECT sp_create_produk(
    'Sistem Monitoring IoT',                        -- nama_produk
    'Sistem monitoring berbasis IoT...',            -- deskripsi
    'Hardware',                                      -- kategori
    2024,                                            -- tahun
    'Arduino, ESP32, Firebase, React',              -- teknologi
    'produk.jpg',                                    -- gambar
    'https://demo.labse.com/iot',                   -- link_demo
    'https://github.com/labse/iot-monitor',         -- link_repository
    1                                                -- personil_id
) as new_produk_id;

-- =====================================================
-- SECTION 5: MENGGUNAKAN STORED PROCEDURES (UPDATE & DELETE)
-- =====================================================

-- 5.1 Update Article
SELECT sp_update_article(
    1,                                               -- id
    'Judul Artikel yang Diperbarui',               -- judul
    'Isi artikel yang sudah direvisi...',           -- isi
    'Dr. Ahmad Fauzi',                              -- penulis
    'artikel-updated.jpg'                            -- gambar
) as update_success;

-- 5.2 Delete Article
SELECT sp_delete_article(99) as delete_success;

-- =====================================================
-- SECTION 6: MENGGUNAKAN COMPLEX QUERIES
-- =====================================================

-- 6.1 Get Personil Detail (Complete Profile)
SELECT * FROM sp_get_personil_detail(1);

-- Output akan berupa JSON dengan struktur:
-- {
--   "personil_info": {...},
--   "penelitian_list": [...],
--   "pengabdian_list": [...],
--   "produk_list": [...],
--   "statistics": {...}
-- }

-- 6.2 Get Dashboard Stats (JSON format)
SELECT * FROM sp_get_dashboard_stats();

-- 6.3 Search Content (Full-text search)
SELECT 
    content_type,
    title,
    LEFT(description, 100) as preview,
    TO_CHAR(created_at, 'DD Mon YYYY') as date
FROM sp_search_content('machine learning', 10);

-- Search dengan keyword lain
SELECT * FROM sp_search_content('IoT', 15);

-- 6.4 Bulk Delete
-- Delete multiple items at once
SELECT sp_bulk_delete('artikel', ARRAY[10, 11, 12]) as deleted_count;
SELECT sp_bulk_delete('penelitian', ARRAY[1, 2]) as deleted_count;

-- =====================================================
-- SECTION 7: CONTOH PENGGUNAAN DALAM PHP
-- =====================================================

/*
<?php
// Include config
require_once '../includes/config.php';

// Example 1: Get Dashboard Statistics
$query = "SELECT * FROM mv_dashboard_statistics";
$result = pg_query($conn, $query);
$stats = pg_fetch_assoc($result);

echo "Total Personil: " . $stats['total_personil'] . "<br>";
echo "Total Penelitian: " . $stats['total_penelitian'] . "<br>";

// Example 2: Get Personil Contributions
$query = "SELECT * FROM mv_personil_contributions ORDER BY contribution_score DESC LIMIT 5";
$result = pg_query($conn, $query);

while ($row = pg_fetch_assoc($result)) {
    echo $row['nama'] . " - Score: " . $row['contribution_score'] . "<br>";
}

// Example 3: Create Article using Stored Procedure
$judul = pg_escape_string($conn, $_POST['judul']);
$isi = pg_escape_string($conn, $_POST['isi']);
$penulis = pg_escape_string($conn, $_POST['penulis']);
$gambar = pg_escape_string($conn, $_FILES['gambar']['name']);

$query = "SELECT sp_create_article($1, $2, $3, $4) as new_id";
$result = pg_query_params($conn, $query, array($judul, $isi, $penulis, $gambar));
$row = pg_fetch_assoc($result);

echo "New Article ID: " . $row['new_id'];

// Example 4: Get Personil Detail
$personil_id = 1;
$query = "SELECT * FROM sp_get_personil_detail($1)";
$result = pg_query_params($conn, $query, array($personil_id));
$data = pg_fetch_assoc($result);

// Parse JSON
$personil_info = json_decode($data['personil_info'], true);
$penelitian_list = json_decode($data['penelitian_list'], true);
$statistics = json_decode($data['statistics'], true);

echo "Nama: " . $personil_info['nama'] . "<br>";
echo "Total Penelitian: " . $statistics['total_penelitian'] . "<br>";

// Example 5: Search Content
$keyword = pg_escape_string($conn, $_GET['q']);
$query = "SELECT * FROM sp_search_content($1, 20)";
$result = pg_query_params($conn, $query, array($keyword));

while ($row = pg_fetch_assoc($result)) {
    echo "<div>";
    echo "<h3>" . $row['title'] . "</h3>";
    echo "<p>" . $row['description'] . "</p>";
    echo "<small>Type: " . $row['content_type'] . "</small>";
    echo "</div>";
}

// Example 6: Refresh Materialized Views (dapat dijadwalkan via cron)
$query = "SELECT fn_refresh_all_materialized_views() as result";
$result = pg_query($conn, $query);
$row = pg_fetch_assoc($result);

echo $row['result']; // "All materialized views refreshed successfully in ..."

// Example 7: Bulk Delete
$ids_to_delete = array(1, 2, 3, 4, 5);
$query = "SELECT sp_bulk_delete('artikel', $1) as deleted_count";
$result = pg_query_params($conn, $query, array('{' . implode(',', $ids_to_delete) . '}'));
$row = pg_fetch_assoc($result);

echo "Deleted " . $row['deleted_count'] . " items";
?>
*/

-- =====================================================
-- SECTION 8: PERFORMANCE TIPS
-- =====================================================

-- 8.1 Compare query performance
-- Before (tanpa materialized view)
EXPLAIN ANALYZE 
SELECT 
    (SELECT COUNT(*) FROM personil) as total_personil,
    (SELECT COUNT(*) FROM mahasiswa) as total_mahasiswa,
    (SELECT COUNT(*) FROM artikel) as total_artikel;

-- After (dengan materialized view)
EXPLAIN ANALYZE 
SELECT * FROM mv_dashboard_statistics;

-- 8.2 Refresh strategy
-- Option 1: Manual refresh saat ada perubahan besar
REFRESH MATERIALIZED VIEW mv_dashboard_statistics;

-- Option 2: Refresh via cron job (jalankan setiap 1 jam)
-- Setup cron job untuk menjalankan:
-- psql -U postgres -d labse -c "SELECT fn_refresh_all_materialized_views();"

-- Option 3: Refresh CONCURRENTLY (tidak mengunci view)
REFRESH MATERIALIZED VIEW CONCURRENTLY mv_personil_contributions;

-- =====================================================
-- SECTION 9: MONITORING & MAINTENANCE
-- =====================================================

-- 9.1 Check materialized view size
SELECT 
    schemaname,
    matviewname,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||matviewname)) as size
FROM pg_matviews
WHERE schemaname = 'public'
ORDER BY pg_total_relation_size(schemaname||'.'||matviewname) DESC;

-- 9.2 Check last refresh time
SELECT 
    'mv_dashboard_statistics' as view_name,
    last_refresh_at
FROM mv_dashboard_statistics
UNION ALL
SELECT 
    'mv_personil_contributions',
    last_refresh_at::timestamp
FROM mv_personil_contributions
LIMIT 1;

-- 9.3 List all custom functions
SELECT 
    routine_name,
    routine_type,
    data_type
FROM information_schema.routines
WHERE routine_schema = 'public'
  AND routine_name LIKE 'sp_%' OR routine_name LIKE 'fn_%'
ORDER BY routine_name;

-- 9.4 View function definition
SELECT pg_get_functiondef('sp_create_article'::regproc);

-- =====================================================
-- Success!
-- =====================================================
SELECT 'Dokumentasi penggunaan berhasil dibuat!' as status,
       'Lihat berbagai contoh di atas untuk penggunaan views dan procedures' as note;
