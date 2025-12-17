-- Migration: Create table for managing article categories
-- This allows admin to categorize articles

-- Create kategori_artikel table
CREATE TABLE IF NOT EXISTS kategori_artikel (
    id SERIAL PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100),
    deskripsi TEXT,
    warna VARCHAR(20) DEFAULT '#0d6efd',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add kategori_id column to artikel table if not exists
DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                   WHERE table_name = 'artikel' AND column_name = 'kategori_id') THEN
        ALTER TABLE artikel ADD COLUMN kategori_id INTEGER REFERENCES kategori_artikel(id) ON DELETE SET NULL;
    END IF;
END $$;

-- Create index for better performance
CREATE INDEX IF NOT EXISTS idx_artikel_kategori_id ON artikel(kategori_id);
CREATE INDEX IF NOT EXISTS idx_kategori_artikel_is_active ON kategori_artikel(is_active);

-- Seed initial data for kategori_artikel
INSERT INTO kategori_artikel (nama_kategori, slug, deskripsi, warna, is_active) VALUES
('Teknologi', 'teknologi', 'Artikel tentang teknologi, software, dan hardware terbaru', '#0d6efd', TRUE),
('Penelitian', 'penelitian', 'Artikel tentang hasil penelitian dan akademik', '#198754', TRUE),
('Tutorial', 'tutorial', 'Panduan dan tutorial programming', '#ffc107', TRUE),
('Berita', 'berita', 'Berita terbaru seputar lab dan kampus', '#dc3545', TRUE),
('Tips & Trik', 'tips-trik', 'Tips dan trik seputar IT dan pengembangan', '#6f42c1', TRUE)
ON CONFLICT (nama_kategori) DO NOTHING;
