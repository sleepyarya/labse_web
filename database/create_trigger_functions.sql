-- =====================================================
-- TRIGGER FUNCTIONS untuk Database LABSE
-- Created: 2025-12-03
-- Description: Trigger functions untuk auto-update dan logging
-- =====================================================

-- =====================================================
-- TRIGGER FUNCTION: Update Timestamp
-- Auto-update updated_at field pada setiap UPDATE
-- =====================================================
CREATE OR REPLACE FUNCTION trigger_update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION trigger_update_timestamp IS 'Trigger function untuk auto-update updated_at field';

-- =====================================================
-- TRIGGER FUNCTION: Log Activity
-- Auto-log aktivitas CRUD ke activity_logs
-- =====================================================
CREATE OR REPLACE FUNCTION trigger_log_activity()
RETURNS TRIGGER AS $$
DECLARE
    v_action_type VARCHAR(100);
    v_action_desc TEXT;
    v_personil_id INTEGER;
    v_personil_nama VARCHAR(255);
BEGIN
    -- Determine action type
    IF TG_OP = 'INSERT' THEN
        v_action_type := 'CREATE_' || upper(TG_TABLE_NAME);
        v_action_desc := 'Membuat ' || TG_TABLE_NAME || ' baru: ' || 
                        COALESCE(NEW.judul, NEW.nama_produk, NEW.nama, 'Unknown');
        v_personil_id := COALESCE(NEW.personil_id, 0);
    ELSIF TG_OP = 'UPDATE' THEN
        v_action_type := 'EDIT_' || upper(TG_TABLE_NAME);
        v_action_desc := 'Mengedit ' || TG_TABLE_NAME || ': ' || 
                        COALESCE(NEW.judul, NEW.nama_produk, NEW.nama, 'Unknown');
        v_personil_id := COALESCE(NEW.personil_id, OLD.personil_id, 0);
    ELSIF TG_OP = 'DELETE' THEN
        v_action_type := 'DELETE_' || upper(TG_TABLE_NAME);
        v_action_desc := 'Menghapus ' || TG_TABLE_NAME || ': ' || 
                        COALESCE(OLD.judul, OLD.nama_produk, OLD.nama, 'Unknown');
        v_personil_id := COALESCE(OLD.personil_id, 0);
    END IF;
    
    -- Get personil name if exists
    IF v_personil_id > 0 THEN
        SELECT nama INTO v_personil_nama FROM personil WHERE id = v_personil_id;
    ELSE
        v_personil_nama := 'System';
    END IF;
    
    -- Insert log (only if personil_id exists and is valid)
    IF v_personil_id > 0 THEN
        INSERT INTO activity_logs (
            personil_id,
            personil_nama,
            action_type,
            action_description,
            target_type,
            target_id,
            created_at
        ) VALUES (
            v_personil_id,
            v_personil_nama,
            v_action_type,
            v_action_desc,
            TG_TABLE_NAME,
            COALESCE(NEW.id, OLD.id),
            NOW()
        );
    END IF;
    
    -- Return appropriate record
    IF TG_OP = 'DELETE' THEN
        RETURN OLD;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION trigger_log_activity IS 'Trigger function untuk auto-logging aktivitas CRUD ke activity_logs';

-- =====================================================
-- TRIGGER FUNCTION: Refresh Materialized View on Change
-- Optionally refresh materialized views saat ada perubahan data
-- NOTE: Dinonaktifkan by default karena bisa mempengaruhi performa
-- =====================================================
CREATE OR REPLACE FUNCTION trigger_refresh_mv_on_change()
RETURNS TRIGGER AS $$
BEGIN
    -- Uncomment line below to enable auto-refresh (WARNING: May impact performance)
    -- PERFORM fn_refresh_all_materialized_views();
    
    -- For now, just return
    IF TG_OP = 'DELETE' THEN
        RETURN OLD;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION trigger_refresh_mv_on_change IS 'Trigger function untuk auto-refresh materialized views (DISABLED by default)';

-- =====================================================
-- CREATE TRIGGERS ON TABLES
-- =====================================================

-- Drop existing triggers if any
DROP TRIGGER IF EXISTS trigger_artikel_update_timestamp ON artikel;
DROP TRIGGER IF EXISTS trigger_penelitian_update_timestamp ON hasil_penelitian;
DROP TRIGGER IF EXISTS trigger_pengabdian_update_timestamp ON pengabdian;
DROP TRIGGER IF EXISTS trigger_produk_update_timestamp ON produk;
DROP TRIGGER IF EXISTS trigger_personil_update_timestamp ON personil;

DROP TRIGGER IF EXISTS trigger_artikel_log_activity ON artikel;
DROP TRIGGER IF EXISTS trigger_penelitian_log_activity ON hasil_penelitian;
DROP TRIGGER IF EXISTS trigger_pengabdian_log_activity ON pengabdian;
DROP TRIGGER IF EXISTS trigger_produk_log_activity ON produk;

-- =====================================================
-- TRIGGERS: Update Timestamp
-- =====================================================

-- Artikel table
CREATE TRIGGER trigger_artikel_update_timestamp
    BEFORE UPDATE ON artikel
    FOR EACH ROW
    EXECUTE FUNCTION trigger_update_timestamp();

-- Hasil Penelitian table (already has updated_at)
CREATE TRIGGER trigger_penelitian_update_timestamp
    BEFORE UPDATE ON hasil_penelitian
    FOR EACH ROW
    EXECUTE FUNCTION trigger_update_timestamp();

-- Pengabdian table (already has updated_at)
CREATE TRIGGER trigger_pengabdian_update_timestamp
    BEFORE UPDATE ON pengabdian
    FOR EACH ROW
    EXECUTE FUNCTION trigger_update_timestamp();

-- Produk table (already has updated_at)
CREATE TRIGGER trigger_produk_update_timestamp
    BEFORE UPDATE ON produk
    FOR EACH ROW
    EXECUTE FUNCTION trigger_update_timestamp();

-- Personil table
CREATE TRIGGER trigger_personil_update_timestamp
    BEFORE UPDATE ON personil
    FOR EACH ROW
    EXECUTE FUNCTION trigger_update_timestamp();

-- =====================================================
-- TRIGGERS: Log Activity (OPTIONAL - Can be enabled later)
-- NOTE: These are commented out by default
-- Uncomment to enable auto-logging
-- =====================================================

/*
-- Artikel table
CREATE TRIGGER trigger_artikel_log_activity
    AFTER INSERT OR UPDATE OR DELETE ON artikel
    FOR EACH ROW
    EXECUTE FUNCTION trigger_log_activity();

-- Hasil Penelitian table
CREATE TRIGGER trigger_penelitian_log_activity
    AFTER INSERT OR UPDATE OR DELETE ON hasil_penelitian
    FOR EACH ROW
    EXECUTE FUNCTION trigger_log_activity();

-- Pengabdian table
CREATE TRIGGER trigger_pengabdian_log_activity
    AFTER INSERT OR UPDATE OR DELETE ON pengabdian
    FOR EACH ROW
    EXECUTE FUNCTION trigger_log_activity();

-- Produk table
CREATE TRIGGER trigger_produk_log_activity
    AFTER INSERT OR UPDATE OR DELETE ON produk
    FOR EACH ROW
    EXECUTE FUNCTION trigger_log_activity();
*/

-- =====================================================
-- ADD updated_at COLUMN if not exists
-- (Safe to run, will only add if column doesn't exist)
-- =====================================================

DO $$
BEGIN
    -- Add updated_at to artikel if not exists
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'artikel' AND column_name = 'updated_at'
    ) THEN
        ALTER TABLE artikel ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
    END IF;
    
    -- Add updated_at to personil if not exists
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'personil' AND column_name = 'updated_at'
    ) THEN
        ALTER TABLE personil ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
    END IF;
    
    -- Add updated_at to mahasiswa if not exists
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'mahasiswa' AND column_name = 'updated_at'
    ) THEN
        ALTER TABLE mahasiswa ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
    END IF;
    
    -- Add updated_at to lab_profile if not exists (probably already has it)
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'lab_profile' AND column_name = 'updated_at'
    ) THEN
        ALTER TABLE lab_profile ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
    END IF;
END $$;

-- =====================================================
-- Success Message
-- =====================================================
SELECT 'Trigger Functions dan Triggers berhasil dibuat!' as status,
       3 as total_trigger_functions,
       5 as total_active_triggers,
       'Update timestamp triggers ACTIVE, Activity logging triggers DISABLED (uncomment to enable)' as note;
