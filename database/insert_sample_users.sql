-- ============================================
-- INSERT SAMPLE USERS
-- ============================================
-- Query ini untuk insert data user sample ke tabel users
-- berdasarkan data admin dan personil yang ada
-- Password untuk semua user: "admin123"
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

-- ============================================
-- 1. INSERT ADMIN USERS
-- ============================================
-- Admin 1: Administrator
INSERT INTO users (username, email, password, role, reference_id, is_active)
VALUES (
    'admin',
    'admin@labse.ac.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    1, -- Sesuaikan dengan ID dari admin_users
    TRUE
) ON CONFLICT (username) DO NOTHING;

-- Admin 2: Super Administrator
INSERT INTO users (username, email, password, role, reference_id, is_active)
VALUES (
    'superadmin',
    'superadmin@labse.ac.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    2, -- Sesuaikan dengan ID dari admin_users
    TRUE
) ON CONFLICT (username) DO NOTHING;

-- ============================================
-- 2. INSERT PERSONIL/MEMBER USERS
-- ============================================
-- Catatan: Hanya personil yang di-set sebagai member yang bisa login
-- Pastikan kolom is_member = TRUE dan password sudah di-set di tabel personil

-- Personil 1: Dr. Ahmad Fauzi
INSERT INTO users (username, email, password, role, reference_id, is_active)
VALUES (
    'ahmad.fauzi',
    'ahmad.fauzi@university.ac.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'personil',
    1, -- Sesuaikan dengan ID dari personil
    TRUE
) ON CONFLICT (email) DO NOTHING;

-- Personil 2: Prof. Dr. Siti Nurhaliza
INSERT INTO users (username, email, password, role, reference_id, is_active)
VALUES (
    'siti.nurhaliza',
    'siti.nurhaliza@university.ac.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'personil',
    2, -- Sesuaikan dengan ID dari personil
    TRUE
) ON CONFLICT (email) DO NOTHING;

-- Personil 3: Budi Santoso
INSERT INTO users (username, email, password, role, reference_id, is_active)
VALUES (
    'budi.santoso',
    'budi.santoso@university.ac.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'personil',
    3, -- Sesuaikan dengan ID dari personil
    TRUE
) ON CONFLICT (email) DO NOTHING;

-- Personil 4: Dr. Rina Wijaya
INSERT INTO users (username, email, password, role, reference_id, is_active)
VALUES (
    'rina.wijaya',
    'rina.wijaya@university.ac.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'personil',
    4, -- Sesuaikan dengan ID dari personil
    TRUE
) ON CONFLICT (email) DO NOTHING;

-- Personil 5: Muhammad Rizki
INSERT INTO users (username, email, password, role, reference_id, is_active)
VALUES (
    'muhammad.rizki',
    'muhammad.rizki@university.ac.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'personil',
    5, -- Sesuaikan dengan ID dari personil
    TRUE
) ON CONFLICT (email) DO NOTHING;

-- Personil 6: Dewi Lestari
INSERT INTO users (username, email, password, role, reference_id, is_active)
VALUES (
    'dewi.lestari',
    'dewi.lestari@university.ac.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'personil',
    6, -- Sesuaikan dengan ID dari personil
    TRUE
) ON CONFLICT (email) DO NOTHING;

-- ============================================
-- 3. UPDATE PERSONIL TABLE
-- ============================================
-- Set is_member = TRUE dan password untuk personil yang bisa login

UPDATE personil SET 
    is_member = TRUE,
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE id IN (1, 2, 3, 4, 5, 6);

-- ============================================
-- 4. VERIFIKASI DATA
-- ============================================
-- Query untuk cek data users yang sudah diinsert

-- Cek semua users
SELECT id, username, email, role, reference_id, is_active, created_at
FROM users
ORDER BY role, id;

-- Cek admin users
SELECT 
    u.id as user_id,
    u.username,
    u.email,
    u.role,
    au.nama_lengkap,
    u.is_active
FROM users u
JOIN admin_users au ON u.reference_id = au.id
WHERE u.role = 'admin'
ORDER BY u.id;

-- Cek personil members
SELECT 
    u.id as user_id,
    u.username,
    u.email,
    u.role,
    p.nama,
    p.jabatan,
    u.is_active
FROM users u
JOIN personil p ON u.reference_id = p.id
WHERE u.role = 'personil'
ORDER BY u.id;

-- ============================================
-- INFORMASI LOGIN
-- ============================================
-- Gunakan salah satu dari credential berikut untuk login:

-- ADMIN:
-- Username: admin (atau email: admin@labse.ac.id)
-- Password: admin123

-- Username: superadmin (atau email: superadmin@labse.ac.id)
-- Password: admin123

-- PERSONIL/MEMBER:
-- Username: ahmad.fauzi (atau email: ahmad.fauzi@university.ac.id)
-- Password: admin123

-- Username: siti.nurhaliza (atau email: siti.nurhaliza@university.ac.id)
-- Password: admin123

-- ... dan seterusnya untuk personil lainnya
-- Password untuk semua: admin123
