-- ============================================
-- MIGRATION: Hapus kolom password dari admin_users
-- ============================================
-- Alasan: 
-- - Password sudah disimpan di tabel users (sebagai pusat autentikasi)
-- - admin_users hanya menyimpan data profil admin
-- - Menghindari duplikasi data dan inkonsistensi
-- ============================================

-- Backup data terlebih dahulu (opsional, untuk safety)
-- Anda bisa uncomment jika ingin backup
-- CREATE TABLE admin_users_backup AS SELECT * FROM admin_users;

-- Hapus kolom password dari admin_users
ALTER TABLE admin_users 
DROP COLUMN IF EXISTS password;

-- Verifikasi struktur tabel
-- SELECT column_name, data_type, is_nullable 
-- FROM information_schema.columns 
-- WHERE table_name = 'admin_users';
