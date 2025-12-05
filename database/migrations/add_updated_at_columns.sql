-- ============================================
-- MIGRATION: Tambah kolom updated_at ke tabel-tabel yang belum punya
-- ============================================
-- Tabel-tabel yang membutuhkan kolom updated_at:
-- - personil
-- - admin_users
-- - artikel
-- - mahasiswa
-- - lab_profile (mungkin sudah ada)
-- ============================================

-- Add updated_at to personil
ALTER TABLE personil ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Add updated_at to admin_users
ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Add updated_at to artikel
ALTER TABLE artikel ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Add updated_at to mahasiswa
ALTER TABLE mahasiswa ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Add updated_at to lab_profile (if not exists)
ALTER TABLE lab_profile ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Create trigger function for auto-update updated_at (if not exists)
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create triggers for auto-update updated_at on each table

-- Trigger for personil
DROP TRIGGER IF EXISTS update_personil_updated_at ON personil;
CREATE TRIGGER update_personil_updated_at 
BEFORE UPDATE ON personil
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Trigger for admin_users
DROP TRIGGER IF EXISTS update_admin_users_updated_at ON admin_users;
CREATE TRIGGER update_admin_users_updated_at 
BEFORE UPDATE ON admin_users
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Trigger for artikel
DROP TRIGGER IF EXISTS update_artikel_updated_at ON artikel;
CREATE TRIGGER update_artikel_updated_at 
BEFORE UPDATE ON artikel
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Trigger for mahasiswa
DROP TRIGGER IF EXISTS update_mahasiswa_updated_at ON mahasiswa;
CREATE TRIGGER update_mahasiswa_updated_at 
BEFORE UPDATE ON mahasiswa
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Trigger for lab_profile
DROP TRIGGER IF EXISTS update_lab_profile_updated_at ON lab_profile;
CREATE TRIGGER update_lab_profile_updated_at 
BEFORE UPDATE ON lab_profile
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Verifikasi kolom sudah ditambahkan
SELECT table_name, column_name, data_type, is_nullable
FROM information_schema.columns 
WHERE table_name IN ('personil', 'admin_users', 'artikel', 'mahasiswa', 'lab_profile')
  AND column_name = 'updated_at'
ORDER BY table_name;
