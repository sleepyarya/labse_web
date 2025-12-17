-- Create kategori_produk table
CREATE TABLE IF NOT EXISTS kategori_produk (
    id SERIAL PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    deskripsi TEXT,
    warna VARCHAR(20) DEFAULT '#0d6efd',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO kategori_produk (nama_kategori, slug, deskripsi, warna) VALUES
('Web Application', 'web-application', 'Aplikasi berbasis web', '#0d6efd'), -- Blue
('Mobile Application', 'mobile-application', 'Aplikasi mobile (Android/iOS)', '#6610f2'), -- Purple
('IoT System', 'iot-system', 'Sistem Internet of Things dan Hardware', '#198754'), -- Green
('AI & Machine Learning', 'ai-ml', 'Implementasi kecerdasan buatan', '#ffc107'), -- Yellow
('Desktop Application', 'desktop-application', 'Aplikasi desktop', '#dc3545') -- Red
ON CONFLICT (nama_kategori) DO NOTHING;

-- Add kategori_id column to produk table
ALTER TABLE produk 
ADD COLUMN IF NOT EXISTS kategori_id INTEGER REFERENCES kategori_produk(id) ON DELETE SET NULL;

-- Migrate existing data (match case-insensitive)
UPDATE produk p
SET kategori_id = kp.id
FROM kategori_produk kp
WHERE LOWER(p.kategori) = LOWER(kp.nama_kategori);

-- Create index for performance
CREATE INDEX IF NOT EXISTS idx_produk_kategori_id ON produk(kategori_id);
