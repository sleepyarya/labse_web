-- Create kategori_penelitian table
CREATE TABLE IF NOT EXISTS kategori_penelitian (
    id SERIAL PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    deskripsi TEXT,
    warna VARCHAR(20) DEFAULT '#0d6efd',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default categories (commonly used in research)
INSERT INTO kategori_penelitian (nama_kategori, slug, deskripsi, warna) VALUES
('Fundamental', 'fundamental', 'Penelitian dasar untuk memperluas ilmu pengetahuan', '#0d6efd'), -- Primary Blue
('Terapan', 'terapan', 'Penelitian untuk memecahkan masalah praktis', '#198754'), -- Success Green
('Pengembangan', 'pengembangan', 'Penelitian dan pengembangan produk atau sistem', '#ffc107'), -- Warning Yellow
('Eksperimental', 'eksperimental', 'Penelitian berbasis eksperimen dan uji coba', '#6610f2'), -- Purple
('Studi Kasus', 'studi-kasus', 'Penelitian mendalam tentang suatu kasus spesifik', '#dc3545') -- Danger Red
ON CONFLICT (nama_kategori) DO NOTHING;

-- Add kategori_id column to hasil_penelitian table
ALTER TABLE hasil_penelitian 
ADD COLUMN IF NOT EXISTS kategori_id INTEGER REFERENCES kategori_penelitian(id) ON DELETE SET NULL;

-- Migrate existing data (assuming 'kategori' column exists and contains names matching above or similar)
-- Match case-insensitive just in case
UPDATE hasil_penelitian hp
SET kategori_id = kp.id
FROM kategori_penelitian kp
WHERE LOWER(hp.kategori) = LOWER(kp.nama_kategori);

-- Create index for performance
CREATE INDEX IF NOT EXISTS idx_penelitian_kategori_id ON hasil_penelitian(kategori_id);
