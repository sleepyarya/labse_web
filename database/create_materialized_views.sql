-- =====================================================
-- MATERIALIZED VIEWS untuk Database LABSE
-- Created: 2025-12-03
-- Description: Materialized views untuk meningkatkan performa query
-- =====================================================

-- =====================================================
-- 1. MATERIALIZED VIEW: Dashboard Statistics
-- Menyediakan statistik lengkap untuk dashboard admin
-- =====================================================
DROP MATERIALIZED VIEW IF EXISTS mv_dashboard_statistics CASCADE;

CREATE MATERIALIZED VIEW mv_dashboard_statistics AS
SELECT 
    -- Count dari setiap tabel
    (SELECT COUNT(*) FROM personil) as total_personil,
    (SELECT COUNT(*) FROM mahasiswa) as total_mahasiswa,
    (SELECT COUNT(*) FROM artikel) as total_artikel,
    (SELECT COUNT(*) FROM hasil_penelitian) as total_penelitian,
    (SELECT COUNT(*) FROM pengabdian) as total_pengabdian,
    (SELECT COUNT(*) FROM produk) as total_produk,
    (SELECT COUNT(*) FROM admin_users) as total_admin,
    (SELECT COUNT(*) FROM activity_logs) as total_activities,
    
    -- Last update dari setiap modul
    (SELECT MAX(created_at) FROM artikel) as last_artikel_date,
    (SELECT MAX(created_at) FROM hasil_penelitian) as last_penelitian_date,
    (SELECT MAX(created_at) FROM pengabdian) as last_pengabdian_date,
    (SELECT MAX(created_at) FROM produk) as last_produk_date,
    
    -- Statistik tahun ini
    (SELECT COUNT(*) FROM hasil_penelitian WHERE tahun = EXTRACT(YEAR FROM CURRENT_DATE)) as penelitian_tahun_ini,
    (SELECT COUNT(*) FROM produk WHERE tahun = EXTRACT(YEAR FROM CURRENT_DATE)) as produk_tahun_ini,
    
    -- Timestamp refresh
    NOW() as last_refresh_at;

-- Create unique index
CREATE UNIQUE INDEX ON mv_dashboard_statistics ((1));

COMMENT ON MATERIALIZED VIEW mv_dashboard_statistics IS 'Statistik dashboard untuk performa tinggi';

-- =====================================================
-- 2. MATERIALIZED VIEW: Personil Contributions
-- Tracking kontribusi setiap personil
-- =====================================================
DROP MATERIALIZED VIEW IF EXISTS mv_personil_contributions CASCADE;

CREATE MATERIALIZED VIEW mv_personil_contributions AS
SELECT 
    p.id as personil_id,
    p.nama,
    p.jabatan,
    p.email,
    COALESCE(COUNT(DISTINCT hp.id), 0) as total_penelitian,
    COALESCE(COUNT(DISTINCT pg.id), 0) as total_pengabdian,
    COALESCE(COUNT(DISTINCT pr.id), 0) as total_produk,
    -- Calculate contribution score (weighted)
    (COALESCE(COUNT(DISTINCT hp.id), 0) * 3 + 
     COALESCE(COUNT(DISTINCT pg.id), 0) * 2 + 
     COALESCE(COUNT(DISTINCT pr.id), 0) * 2) as contribution_score,
    MAX(GREATEST(
        COALESCE(hp.created_at, '1970-01-01'::timestamp),
        COALESCE(pg.created_at, '1970-01-01'::timestamp),
        COALESCE(pr.created_at, '1970-01-01'::timestamp)
    )) as last_contribution_date,
    NOW() as last_refresh_at
FROM personil p
LEFT JOIN hasil_penelitian hp ON p.id = hp.personil_id
LEFT JOIN pengabdian pg ON p.id = pg.personil_id
LEFT JOIN produk pr ON p.id = pr.personil_id
GROUP BY p.id, p.nama, p.jabatan, p.email
ORDER BY contribution_score DESC;

-- Create unique index
CREATE UNIQUE INDEX idx_mv_personil_contributions_id ON mv_personil_contributions (personil_id);

COMMENT ON MATERIALIZED VIEW mv_personil_contributions IS 'Kontribusi dan ranking personil berdasarkan penelitian, pengabdian, dan produk';

-- =====================================================
-- 3. MATERIALIZED VIEW: Recent Activities
-- Timeline aktivitas terbaru dari semua modul
-- =====================================================
DROP MATERIALIZED VIEW IF EXISTS mv_recent_activities CASCADE;

CREATE MATERIALIZED VIEW mv_recent_activities AS
(
    SELECT 
        'artikel' as activity_type,
        id as item_id,
        judul as title,
        penulis as author,
        NULL::varchar as location,
        created_at,
        created_at as activity_date
    FROM artikel
)
UNION ALL
(
    SELECT 
        'penelitian' as activity_type,
        hp.id as item_id,
        hp.judul as title,
        p.nama as author,
        hp.kategori as location,
        hp.created_at,
        hp.created_at as activity_date
    FROM hasil_penelitian hp
    LEFT JOIN personil p ON hp.personil_id = p.id
)
UNION ALL
(
    SELECT 
        'pengabdian' as activity_type,
        pg.id as item_id,
        pg.judul as title,
        p.nama as author,
        pg.lokasi as location,
        pg.created_at,
        pg.tanggal as activity_date
    FROM pengabdian pg
    LEFT JOIN personil p ON pg.personil_id = p.id
)
UNION ALL
(
    SELECT 
        'produk' as activity_type,
        pr.id as item_id,
        pr.nama_produk as title,
        p.nama as author,
        pr.kategori as location,
        pr.created_at,
        pr.created_at as activity_date
    FROM produk pr
    LEFT JOIN personil p ON pr.personil_id = p.id
)
UNION ALL
(
    SELECT 
        'mahasiswa' as activity_type,
        id as item_id,
        nama as title,
        nim as author,
        jurusan as location,
        created_at,
        created_at as activity_date
    FROM mahasiswa
)
ORDER BY created_at DESC
LIMIT 100;

-- Create index
CREATE INDEX idx_mv_recent_activities_type ON mv_recent_activities (activity_type);
CREATE INDEX idx_mv_recent_activities_date ON mv_recent_activities (created_at DESC);

COMMENT ON MATERIALIZED VIEW mv_recent_activities IS '100 aktivitas terbaru dari semua modul untuk timeline dashboard';

-- =====================================================
-- 4. MATERIALIZED VIEW: Yearly Research Summary
-- Ringkasan penelitian per tahun dan kategori
-- =====================================================
DROP MATERIALIZED VIEW IF EXISTS mv_yearly_research_summary CASCADE;

CREATE MATERIALIZED VIEW mv_yearly_research_summary AS
SELECT 
    tahun,
    kategori,
    COUNT(*) as total_penelitian,
    COUNT(*) FILTER (WHERE link_publikasi IS NOT NULL) as total_terpublikasi,
    COUNT(*) FILTER (WHERE file_pdf IS NOT NULL) as total_dengan_pdf,
    array_agg(judul ORDER BY created_at DESC) as daftar_judul,
    MIN(created_at) as first_created,
    MAX(created_at) as last_created,
    NOW() as last_refresh_at
FROM hasil_penelitian
GROUP BY tahun, kategori
ORDER BY tahun DESC, kategori;

-- Create unique index
CREATE UNIQUE INDEX idx_mv_yearly_research_tahun_kategori ON mv_yearly_research_summary (tahun, kategori);

COMMENT ON MATERIALIZED VIEW mv_yearly_research_summary IS 'Ringkasan statistik penelitian per tahun dan kategori';

-- =====================================================
-- 5. MATERIALIZED VIEW: Popular Content
-- Konten yang paling banyak diakses (berdasarkan created_at untuk simulasi)
-- =====================================================
DROP MATERIALIZED VIEW IF EXISTS mv_popular_content CASCADE;

CREATE MATERIALIZED VIEW mv_popular_content AS
(
    SELECT 
        'artikel' as content_type,
        id,
        judul as title,
        penulis as creator,
        created_at,
        EXTRACT(EPOCH FROM (NOW() - created_at)) / 86400 as age_days,
        -- Popularity score (newer = more popular in this simulation)
        100 - LEAST(100, EXTRACT(EPOCH FROM (NOW() - created_at)) / 86400) as popularity_score
    FROM artikel
    ORDER BY created_at DESC
    LIMIT 10
)
UNION ALL
(
    SELECT 
        'penelitian' as content_type,
        hp.id,
        hp.judul as title,
        p.nama as creator,
        hp.created_at,
        EXTRACT(EPOCH FROM (NOW() - hp.created_at)) / 86400 as age_days,
        100 - LEAST(100, EXTRACT(EPOCH FROM (NOW() - hp.created_at)) / 86400) as popularity_score
    FROM hasil_penelitian hp
    LEFT JOIN personil p ON hp.personil_id = p.id
    ORDER BY hp.created_at DESC
    LIMIT 10
)
UNION ALL
(
    SELECT 
        'pengabdian' as content_type,
        pg.id,
        pg.judul as title,
        p.nama as creator,
        pg.created_at,
        EXTRACT(EPOCH FROM (NOW() - pg.created_at)) / 86400 as age_days,
        100 - LEAST(100, EXTRACT(EPOCH FROM (NOW() - pg.created_at)) / 86400) as popularity_score
    FROM pengabdian pg
    LEFT JOIN personil p ON pg.personil_id = p.id
    ORDER BY pg.created_at DESC
    LIMIT 10
)
ORDER BY popularity_score DESC;

-- Create index
CREATE INDEX idx_mv_popular_content_type ON mv_popular_content (content_type);
CREATE INDEX idx_mv_popular_content_score ON mv_popular_content (popularity_score DESC);

COMMENT ON MATERIALIZED VIEW mv_popular_content IS 'Top 30 konten populer (10 dari setiap kategori)';

-- =====================================================
-- Success Message
-- =====================================================
SELECT 'Semua Materialized Views berhasil dibuat!' as status,
       5 as total_views,
       'mv_dashboard_statistics, mv_personil_contributions, mv_recent_activities, mv_yearly_research_summary, mv_popular_content' as view_names;
