-- ============================================
-- CONTOH PENGGUNAAN VIEWS DAN STORED PROCEDURES
-- ============================================

-- 1. MENGGUNAKAN VIEW: view_student_research_status
-- Melihat semua penelitian mahasiswa beserta pembimbingnya
SELECT * FROM view_student_research_status;

-- Melihat penelitian dengan status tertentu
SELECT * FROM view_student_research_status
WHERE status = 'pending';

-- Melihat penelitian dari mahasiswa tertentu
SELECT * FROM view_student_research_status
WHERE nama_mahasiswa LIKE '%Budi%';

-- 2. MENGGUNAKAN VIEW: view_lab_profile_summary
-- Melihat ringkasan profil lab (berapa banyak item per kategori)
SELECT * FROM view_lab_profile_summary;

-- Melihat kategori yang paling banyak itemnya
SELECT * FROM view_lab_profile_summary
ORDER BY total_item DESC;

-- 3. MENGGUNAKAN FUNCTION: get_student_statistics
-- Contoh: Lihat statistik mahasiswa dengan ID 1
SELECT * FROM get_student_statistics(1);

-- Contoh hasil:
-- total_submissions | approved_submissions | pending_submissions
-- ------------------+---------------------+--------------------
--                 5 |                   3 |                  2

-- 4. MENGGUNAKAN FUNCTION: approve_student_research
-- Contoh: Approve penelitian ID 1 oleh reviewer ID 5
SELECT approve_student_research(1, 5);

-- Setelah memanggil approve_student_research, cek hasilnya:
SELECT * FROM penelitian WHERE id = 1;

-- ============================================
-- CONTOH PENGGUNAAN DALAM PHP
-- ============================================

/*
// 1. Menggunakan View di PHP
$query = "SELECT * FROM view_student_research_status WHERE status = 'pending'";
$result = pg_query($conn, $query);
while ($row = pg_fetch_assoc($result)) {
    echo $row['nama_mahasiswa'] . " - " . $row['judul'] . "\n";
}

// 2. Memanggil Function get_student_statistics
$student_id = 1;
$query = "SELECT * FROM get_student_statistics($1)";
$result = pg_query_params($conn, $query, array($student_id));
$stats = pg_fetch_assoc($result);
echo "Total: " . $stats['total_submissions'];

// 3. Memanggil Function approve_student_research
$research_id = 1;
$reviewer_id = 5;
$query = "SELECT approve_student_research($1, $2)";
pg_query_params($conn, $query, array($research_id, $reviewer_id));
*/
