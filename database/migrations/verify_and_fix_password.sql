-- ============================================
-- SCRIPT VERIFIKASI DAN PERBAIKAN PASSWORD
-- ============================================
-- Script ini untuk:
-- 1. Mengecek apakah kolom password sudah dihapus dari admin_users
-- 2. Memastikan password admin ada di tabel users
-- 3. Menyinkronkan password jika diperlukan
-- ============================================

-- STEP 1: Cek struktur tabel admin_users
SELECT 'STEP 1: Struktur tabel admin_users' AS info;
SELECT column_name, data_type, is_nullable 
FROM information_schema.columns 
WHERE table_name = 'admin_users'
ORDER BY ordinal_position;

-- STEP 2: Cek data di tabel users untuk admin
SELECT 'STEP 2: Data users dengan role admin' AS info;
SELECT u.id, u.username, u.email, u.role, u.reference_id,
       CASE WHEN u.password IS NULL THEN 'NULL - TIDAK ADA PASSWORD!' ELSE 'Password ada' END as password_status
FROM users u
WHERE u.role = 'admin';

-- STEP 3: Cek data di tabel admin_users
SELECT 'STEP 3: Data admin_users' AS info;
SELECT id, username, email, nama_lengkap, created_at
FROM admin_users;

-- STEP 4: Cek relasi antara users dan admin_users
SELECT 'STEP 4: Relasi users dan admin_users' AS info;
SELECT u.id as user_id, u.username, u.email, u.role, 
       au.id as admin_id, au.nama_lengkap,
       CASE WHEN u.password IS NULL THEN 'TIDAK ADA PASSWORD!' ELSE 'OK' END as password_check
FROM users u
LEFT JOIN admin_users au ON u.reference_id = au.id AND u.role = 'admin';

-- ============================================
-- JIKA PASSWORD TIDAK ADA, UNCOMMENT SCRIPT DI BAWAH INI
-- ============================================

-- Set password admin123 untuk user admin
-- UPDATE users 
-- SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'  -- password: admin123
-- WHERE role = 'admin' AND username = 'admin';

-- Set password admin123 untuk user superadmin
-- UPDATE users 
-- SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'  -- password: admin123
-- WHERE role = 'admin' AND username = 'superadmin';

-- Verifikasi setelah update
-- SELECT username, email, 
--        CASE WHEN password IS NULL THEN 'NULL!' ELSE 'OK' END as password_status
-- FROM users WHERE role = 'admin';
