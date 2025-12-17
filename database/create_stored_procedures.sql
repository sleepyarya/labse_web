-- =====================================================
-- STORED PROCEDURES untuk Database LABSE
-- Created: 2025-12-03
-- Description: Stored procedures dan functions untuk operasi CRUD dan query kompleks
-- =====================================================

-- =====================================================
-- HELPER FUNCTIONS
-- =====================================================

-- Function: Sanitize Input (Basic SQL Injection Prevention)
CREATE OR REPLACE FUNCTION fn_sanitize_input(input_text TEXT)
RETURNS TEXT AS $$
BEGIN
    -- Remove dangerous characters
    RETURN regexp_replace(input_text, '[;<>''"]', '', 'g');
END;
$$ LANGUAGE plpgsql IMMUTABLE;

COMMENT ON FUNCTION fn_sanitize_input IS 'Sanitasi input untuk keamanan dasar';

-- =====================================================
-- Function: Calculate Personil Score
CREATE OR REPLACE FUNCTION fn_calculate_personil_score(p_personil_id INTEGER)
RETURNS INTEGER AS $$
DECLARE
    v_score INTEGER := 0;
    v_penelitian INTEGER;
    v_pengabdian INTEGER;
    v_produk INTEGER;
BEGIN
    -- Count contributions
    SELECT COUNT(*) INTO v_penelitian FROM hasil_penelitian WHERE personil_id = p_personil_id;
    SELECT COUNT(*) INTO v_pengabdian FROM pengabdian WHERE personil_id = p_personil_id;
    SELECT COUNT(*) INTO v_produk FROM produk WHERE personil_id = p_personil_id;
    
    -- Calculate weighted score
    v_score := (v_penelitian * 3) + (v_pengabdian * 2) + (v_produk * 2);
    
    RETURN v_score;
END;
$$ LANGUAGE plpgsql STABLE;

COMMENT ON FUNCTION fn_calculate_personil_score IS 'Hitung score kontribusi personil (penelitian=3, pengabdian=2, produk=2)';

-- =====================================================
-- Function: Get Latest Activities for a Personil
CREATE OR REPLACE FUNCTION fn_get_latest_activities(p_personil_id INTEGER, p_limit INTEGER DEFAULT 10)
RETURNS TABLE (
    activity_type VARCHAR,
    item_id INTEGER,
    title VARCHAR,
    created_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    (
        SELECT 
            'penelitian'::VARCHAR,
            id::INTEGER,
            judul::VARCHAR,
            hasil_penelitian.created_at
        FROM hasil_penelitian
        WHERE personil_id = p_personil_id
    )
    UNION ALL
    (
        SELECT 
            'pengabdian'::VARCHAR,
            id::INTEGER,
            judul::VARCHAR,
            pengabdian.created_at
        FROM pengabdian
        WHERE personil_id = p_personil_id
    )
    UNION ALL
    (
        SELECT 
            'produk'::VARCHAR,
            id::INTEGER,
            nama_produk::VARCHAR,
            produk.created_at
        FROM produk
        WHERE personil_id = p_personil_id
    )
    ORDER BY created_at DESC
    LIMIT p_limit;
END;
$$ LANGUAGE plpgsql STABLE;

COMMENT ON FUNCTION fn_get_latest_activities IS 'Get aktivitas terbaru untuk satu personil';

-- =====================================================
-- Function: Refresh All Materialized Views
CREATE OR REPLACE FUNCTION fn_refresh_all_materialized_views()
RETURNS TEXT AS $$
DECLARE
    v_start_time TIMESTAMP;
    v_end_time TIMESTAMP;
    v_duration INTERVAL;
BEGIN
    v_start_time := clock_timestamp();
    
    -- Refresh all materialized views
    REFRESH MATERIALIZED VIEW CONCURRENTLY mv_dashboard_statistics;
    REFRESH MATERIALIZED VIEW CONCURRENTLY mv_personil_contributions;
    REFRESH MATERIALIZED VIEW mv_recent_activities;
    REFRESH MATERIALIZED VIEW CONCURRENTLY mv_yearly_research_summary;
    REFRESH MATERIALIZED VIEW mv_popular_content;
    
    v_end_time := clock_timestamp();
    v_duration := v_end_time - v_start_time;
    
    RETURN format('All materialized views refreshed successfully in %s', v_duration);
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION fn_refresh_all_materialized_views IS 'Refresh semua materialized views sekaligus';

-- =====================================================
-- STORED PROCEDURES
-- =====================================================

-- =====================================================
-- SP: Create Article
CREATE OR REPLACE FUNCTION sp_create_article(
    p_judul VARCHAR,
    p_isi TEXT,
    p_penulis VARCHAR,
    p_gambar VARCHAR DEFAULT NULL
)
RETURNS INTEGER AS $$
DECLARE
    v_new_id INTEGER;
BEGIN
    -- Validasi input
    IF p_judul IS NULL OR trim(p_judul) = '' THEN
        RAISE EXCEPTION 'Judul artikel tidak boleh kosong';
    END IF;
    
    IF p_isi IS NULL OR trim(p_isi) = '' THEN
        RAISE EXCEPTION 'Isi artikel tidak boleh kosong';
    END IF;
    
    -- Insert artikel
    INSERT INTO artikel (judul, isi, penulis, gambar, created_at)
    VALUES (p_judul, p_isi, p_penulis, p_gambar, NOW())
    RETURNING id INTO v_new_id;
    
    RETURN v_new_id;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION sp_create_article IS 'Insert artikel baru dengan validasi';

-- =====================================================
-- SP: Update Article
CREATE OR REPLACE FUNCTION sp_update_article(
    p_id INTEGER,
    p_judul VARCHAR,
    p_isi TEXT,
    p_penulis VARCHAR,
    p_gambar VARCHAR DEFAULT NULL
)
RETURNS BOOLEAN AS $$
DECLARE
    v_exists BOOLEAN;
BEGIN
    -- Check if article exists
    SELECT EXISTS(SELECT 1 FROM artikel WHERE id = p_id) INTO v_exists;
    
    IF NOT v_exists THEN
        RAISE EXCEPTION 'Artikel dengan ID % tidak ditemukan', p_id;
    END IF;
    
    -- Update artikel
    UPDATE artikel
    SET judul = p_judul,
        isi = p_isi,
        penulis = p_penulis,
        gambar = COALESCE(p_gambar, gambar)
    WHERE id = p_id;
    
    RETURN TRUE;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION sp_update_article IS 'Update artikel dengan validasi';

-- =====================================================
-- SP: Delete Article (Soft Delete simulation - actual delete for now)
CREATE OR REPLACE FUNCTION sp_delete_article(p_id INTEGER)
RETURNS BOOLEAN AS $$
DECLARE
    v_exists BOOLEAN;
BEGIN
    -- Check if article exists
    SELECT EXISTS(SELECT 1 FROM artikel WHERE id = p_id) INTO v_exists;
    
    IF NOT v_exists THEN
        RAISE EXCEPTION 'Artikel dengan ID % tidak ditemukan', p_id;
    END IF;
    
    -- Delete artikel
    DELETE FROM artikel WHERE id = p_id;
    
    RETURN TRUE;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION sp_delete_article IS 'Hapus artikel dengan validasi';

-- =====================================================
-- SP: Create Penelitian
CREATE OR REPLACE FUNCTION sp_create_penelitian(
    p_judul VARCHAR,
    p_deskripsi TEXT,
    p_tahun INTEGER,
    p_kategori VARCHAR DEFAULT NULL,
    p_abstrak TEXT DEFAULT NULL,
    p_gambar VARCHAR DEFAULT NULL,
    p_file_pdf VARCHAR DEFAULT NULL,
    p_link_publikasi TEXT DEFAULT NULL,
    p_personil_id INTEGER DEFAULT NULL
)
RETURNS INTEGER AS $$
DECLARE
    v_new_id INTEGER;
BEGIN
    -- Validasi
    IF p_judul IS NULL OR trim(p_judul) = '' THEN
        RAISE EXCEPTION 'Judul penelitian tidak boleh kosong';
    END IF;
    
    IF p_tahun < 1900 OR p_tahun > EXTRACT(YEAR FROM CURRENT_DATE) + 10 THEN
        RAISE EXCEPTION 'Tahun tidak valid';
    END IF;
    
    -- Insert
    INSERT INTO hasil_penelitian (
        judul, deskripsi, tahun, kategori, abstrak, 
        gambar, file_pdf, link_publikasi, personil_id, 
        created_at, updated_at
    )
    VALUES (
        p_judul, p_deskripsi, p_tahun, p_kategori, p_abstrak,
        p_gambar, p_file_pdf, p_link_publikasi, p_personil_id,
        NOW(), NOW()
    )
    RETURNING id INTO v_new_id;
    
    RETURN v_new_id;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION sp_create_penelitian IS 'Insert penelitian baru dengan validasi';

-- =====================================================
-- SP: Create Pengabdian
CREATE OR REPLACE FUNCTION sp_create_pengabdian(
    p_judul VARCHAR,
    p_deskripsi TEXT,
    p_tanggal DATE,
    p_lokasi VARCHAR,
    p_penyelenggara VARCHAR,
    p_gambar VARCHAR DEFAULT NULL,
    p_personil_id INTEGER DEFAULT NULL
)
RETURNS INTEGER AS $$
DECLARE
    v_new_id INTEGER;
BEGIN
    -- Validasi
    IF p_judul IS NULL OR trim(p_judul) = '' THEN
        RAISE EXCEPTION 'Judul pengabdian tidak boleh kosong';
    END IF;
    
    IF p_tanggal IS NULL THEN
        RAISE EXCEPTION 'Tanggal pengabdian tidak boleh kosong';
    END IF;
    
    -- Insert
    INSERT INTO pengabdian (
        judul, deskripsi, tanggal, lokasi, penyelenggara,
        gambar, personil_id, created_at, updated_at
    )
    VALUES (
        p_judul, p_deskripsi, p_tanggal, p_lokasi, p_penyelenggara,
        p_gambar, p_personil_id, NOW(), NOW()
    )
    RETURNING id INTO v_new_id;
    
    RETURN v_new_id;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION sp_create_pengabdian IS 'Insert pengabdian baru dengan validasi';

-- =====================================================
-- SP: Create Produk
CREATE OR REPLACE FUNCTION sp_create_produk(
    p_nama_produk VARCHAR,
    p_deskripsi TEXT,
    p_kategori VARCHAR,
    p_tahun INTEGER,
    p_teknologi TEXT DEFAULT NULL,
    p_gambar VARCHAR DEFAULT NULL,
    p_link_demo TEXT DEFAULT NULL,
    p_link_repository TEXT DEFAULT NULL,
    p_personil_id INTEGER DEFAULT NULL
)
RETURNS INTEGER AS $$
DECLARE
    v_new_id INTEGER;
BEGIN
    -- Validasi
    IF p_nama_produk IS NULL OR trim(p_nama_produk) = '' THEN
        RAISE EXCEPTION 'Nama produk tidak boleh kosong';
    END IF;
    
    IF p_tahun < 1900 OR p_tahun > EXTRACT(YEAR FROM CURRENT_DATE) + 10 THEN
        RAISE EXCEPTION 'Tahun tidak valid';
    END IF;
    
    -- Insert
    INSERT INTO produk (
        nama_produk, deskripsi, kategori, tahun, teknologi,
        gambar, link_demo, link_repository, personil_id,
        created_at, updated_at
    )
    VALUES (
        p_nama_produk, p_deskripsi, p_kategori, p_tahun, p_teknologi,
        p_gambar, p_link_demo, p_link_repository, p_personil_id,
        NOW(), NOW()
    )
    RETURNING id INTO v_new_id;
    
    RETURN v_new_id;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION sp_create_produk IS 'Insert produk baru dengan validasi';

-- =====================================================
-- SP: Get Personil Detail with All Contributions
CREATE OR REPLACE FUNCTION sp_get_personil_detail(p_personil_id INTEGER)
RETURNS TABLE (
    personil_info JSON,
    penelitian_list JSON,
    pengabdian_list JSON,
    produk_list JSON,
    statistics JSON
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        -- Personil Info
        row_to_json(p.*) as personil_info,
        
        -- Penelitian List
        (SELECT json_agg(row_to_json(hp.*)) 
         FROM hasil_penelitian hp 
         WHERE hp.personil_id = p_personil_id) as penelitian_list,
        
        -- Pengabdian List
        (SELECT json_agg(row_to_json(pg.*)) 
         FROM pengabdian pg 
         WHERE pg.personil_id = p_personil_id) as pengabdian_list,
        
        -- Produk List
        (SELECT json_agg(row_to_json(pr.*)) 
         FROM produk pr 
         WHERE pr.personil_id = p_personil_id) as produk_list,
        
        -- Statistics
        json_build_object(
            'total_penelitian', (SELECT COUNT(*) FROM hasil_penelitian WHERE personil_id = p_personil_id),
            'total_pengabdian', (SELECT COUNT(*) FROM pengabdian WHERE personil_id = p_personil_id),
            'total_produk', (SELECT COUNT(*) FROM produk WHERE personil_id = p_personil_id),
            'contribution_score', fn_calculate_personil_score(p_personil_id)
        ) as statistics
    FROM personil p
    WHERE p.id = p_personil_id;
END;
$$ LANGUAGE plpgsql STABLE;

COMMENT ON FUNCTION sp_get_personil_detail IS 'Get detail lengkap personil dengan semua kontribusi dalam format JSON';

-- =====================================================
-- SP: Get Dashboard Stats
CREATE OR REPLACE FUNCTION sp_get_dashboard_stats()
RETURNS TABLE (
    stats_json JSON
) AS $$
BEGIN
    RETURN QUERY
    SELECT json_build_object(
        'total_personil', (SELECT COUNT(*) FROM personil),
        'total_mahasiswa', (SELECT COUNT(*) FROM mahasiswa),
        'total_artikel', (SELECT COUNT(*) FROM artikel),
        'total_penelitian', (SELECT COUNT(*) FROM hasil_penelitian),
        'total_pengabdian', (SELECT COUNT(*) FROM pengabdian),
        'total_produk', (SELECT COUNT(*) FROM produk),
        'penelitian_tahun_ini', (SELECT COUNT(*) FROM hasil_penelitian WHERE tahun = EXTRACT(YEAR FROM CURRENT_DATE)),
        'produk_tahun_ini', (SELECT COUNT(*) FROM produk WHERE tahun = EXTRACT(YEAR FROM CURRENT_DATE)),
        'top_contributor', (
            SELECT json_build_object('nama', nama, 'score', contribution_score)
            FROM mv_personil_contributions
            ORDER BY contribution_score DESC
            LIMIT 1
        ),
        'last_update', NOW()
    ) as stats_json;
END;
$$ LANGUAGE plpgsql STABLE;

COMMENT ON FUNCTION sp_get_dashboard_stats IS 'Get statistik dashboard dalam format JSON (menggunakan materialized view)';

-- =====================================================
-- SP: Search Content (Full-text search across all tables)
CREATE OR REPLACE FUNCTION sp_search_content(
    p_keyword VARCHAR,
    p_limit INTEGER DEFAULT 20
)
RETURNS TABLE (
    content_type VARCHAR,
    item_id INTEGER,
    title VARCHAR,
    description TEXT,
    created_at TIMESTAMP,
    relevance NUMERIC
) AS $$
BEGIN
    RETURN QUERY
    (
        SELECT 
            'artikel'::VARCHAR as content_type,
            id::INTEGER,
            judul::VARCHAR as title,
            LEFT(isi, 200)::TEXT as description,
            artikel.created_at,
            1.0::NUMERIC as relevance
        FROM artikel
        WHERE judul ILIKE '%' || p_keyword || '%' 
           OR isi ILIKE '%' || p_keyword || '%'
           OR penulis ILIKE '%' || p_keyword || '%'
    )
    UNION ALL
    (
        SELECT 
            'penelitian'::VARCHAR,
            id::INTEGER,
            judul::VARCHAR,
            LEFT(deskripsi, 200)::TEXT,
            hasil_penelitian.created_at,
            1.0::NUMERIC
        FROM hasil_penelitian
        WHERE judul ILIKE '%' || p_keyword || '%' 
           OR deskripsi ILIKE '%' || p_keyword || '%'
           OR abstrak ILIKE '%' || p_keyword || '%'
    )
    UNION ALL
    (
        SELECT 
            'pengabdian'::VARCHAR,
            id::INTEGER,
            judul::VARCHAR,
            LEFT(deskripsi, 200)::TEXT,
            pengabdian.created_at,
            1.0::NUMERIC
        FROM pengabdian
        WHERE judul ILIKE '%' || p_keyword || '%' 
           OR deskripsi ILIKE '%' || p_keyword || '%'
           OR lokasi ILIKE '%' || p_keyword || '%'
    )
    UNION ALL
    (
        SELECT 
            'produk'::VARCHAR,
            id::INTEGER,
            nama_produk::VARCHAR,
            LEFT(deskripsi, 200)::TEXT,
            produk.created_at,
            1.0::NUMERIC
        FROM produk
        WHERE nama_produk ILIKE '%' || p_keyword || '%' 
           OR deskripsi ILIKE '%' || p_keyword || '%'
           OR teknologi ILIKE '%' || p_keyword || '%'
    )
    ORDER BY created_at DESC
    LIMIT p_limit;
END;
$$ LANGUAGE plpgsql STABLE;

COMMENT ON FUNCTION sp_search_content IS 'Search konten di semua tabel (artikel, penelitian, pengabdian, produk)';

-- =====================================================
-- SP: Bulk Delete with Transaction
CREATE OR REPLACE FUNCTION sp_bulk_delete(
    p_table_name VARCHAR,
    p_ids INTEGER[]
)
RETURNS INTEGER AS $$
DECLARE
    v_deleted_count INTEGER := 0;
    v_query TEXT;
BEGIN
    -- Validasi table name (whitelist)
    IF p_table_name NOT IN ('artikel', 'hasil_penelitian', 'pengabdian', 'produk') THEN
        RAISE EXCEPTION 'Invalid table name: %', p_table_name;
    END IF;
    
    -- Build and execute dynamic query
    v_query := format('DELETE FROM %I WHERE id = ANY($1)', p_table_name);
    EXECUTE v_query USING p_ids;
    
    GET DIAGNOSTICS v_deleted_count = ROW_COUNT;
    
    RETURN v_deleted_count;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION sp_bulk_delete IS 'Bulk delete dengan transaction safety (artikel, penelitian, pengabdian, produk)';

-- =====================================================
-- Success Message
-- =====================================================
SELECT 'Semua Stored Procedures dan Functions berhasil dibuat!' as status,
       14 as total_functions,
       'Check COMMENT untuk detail masing-masing function' as note;
