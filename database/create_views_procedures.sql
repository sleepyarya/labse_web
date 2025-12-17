-- 1. View: view_student_research_status
-- Memudahkan melihat status penelitian mahasiswa beserta nama pembimbingnya
CREATE OR REPLACE VIEW view_student_research_status AS
SELECT 
    p.id AS penelitian_id,
    p.judul,
    p.status,
    p.created_at,
    m.nama AS nama_mahasiswa,
    m.nim,
    per.nama AS nama_pembimbing
FROM penelitian p
JOIN mahasiswa m ON p.mahasiswa_id = m.id
LEFT JOIN personil per ON m.dosen_pembimbing_id = per.id;

-- 2. View: view_lab_profile_summary
-- Ringkasan jumlah konten profil per kategori
CREATE OR REPLACE VIEW view_lab_profile_summary AS
SELECT 
    kategori,
    COUNT(*) as total_item,
    MAX(updated_at) as last_update
FROM lab_profile
GROUP BY kategori;

-- 3. Function: get_student_statistics
-- Menghitung statistik untuk dashboard mahasiswa
CREATE OR REPLACE FUNCTION get_student_statistics(student_id INT)
RETURNS TABLE (
    total_submissions BIGINT,
    approved_submissions BIGINT,
    pending_submissions BIGINT
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        COUNT(*) as total,
        COUNT(*) FILTER (WHERE status = 'approved') as approved,
        COUNT(*) FILTER (WHERE status = 'pending') as pending
    FROM penelitian
    WHERE mahasiswa_id = student_id;
END;
$$ LANGUAGE plpgsql;

-- 4. Function: approve_student_research
-- Procedure untuk menyetujui penelitian dan mencatat log (simulasi log dengan notice)
CREATE OR REPLACE FUNCTION approve_student_research(research_id INT, reviewer_id INT)
RETURNS VOID AS $$
BEGIN
    -- Update status penelitian
    UPDATE penelitian 
    SET status = 'approved', 
        updated_at = NOW()
    WHERE id = research_id;
    
    -- Bisa ditambahkan logika lain di sini, misal insert ke tabel log notifikasi
    -- Untuk saat ini kita raise notice saja
    RAISE NOTICE 'Research ID % approved by Reviewer ID %', research_id, reviewer_id;
END;
$$ LANGUAGE plpgsql;
