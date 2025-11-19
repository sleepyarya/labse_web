-- ============================================
-- TABEL USERS - PUSAT AUTENTIKASI
-- ============================================
-- Tabel ini menjadi pusat autentikasi untuk semua user
-- Setiap user dari admin_users, personil (member), dan mahasiswa
-- akan memiliki record di tabel ini

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL CHECK (role IN ('admin', 'personil', 'mahasiswa')),
    reference_id INTEGER NOT NULL, -- ID dari tabel asli (admin_users, personil, atau mahasiswa)
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index untuk meningkatkan performa query
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_reference ON users(role, reference_id);

-- Trigger untuk auto-update updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- ============================================
-- UPDATE TABEL PERSONIL 
-- ============================================
-- Menambahkan kolom yang diperlukan untuk member login

ALTER TABLE personil 
ADD COLUMN IF NOT EXISTS password VARCHAR(255),
ADD COLUMN IF NOT EXISTS is_member BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP;

-- ============================================
-- KOMENTAR UNTUK DOKUMENTASI
-- ============================================
COMMENT ON TABLE users IS 'Tabel pusat autentikasi untuk semua pengguna sistem';
COMMENT ON COLUMN users.role IS 'Role pengguna: admin, personil, atau mahasiswa';
COMMENT ON COLUMN users.reference_id IS 'ID referensi ke tabel asli sesuai role';
COMMENT ON COLUMN users.is_active IS 'Status aktif user, untuk soft delete';
