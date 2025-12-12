-- Migration: Create table for managing jurusan (departments)
-- This allows admin to add, edit, and delete departments for student registration form

-- Create jurusan table
CREATE TABLE IF NOT EXISTS jurusan (
    id SERIAL PRIMARY KEY,
    nama_jurusan VARCHAR(255) NOT NULL UNIQUE,
    kode_jurusan VARCHAR(20),
    deskripsi TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add jurusan_id column to mahasiswa table if not exists
DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                   WHERE table_name = 'mahasiswa' AND column_name = 'jurusan_id') THEN
        ALTER TABLE mahasiswa ADD COLUMN jurusan_id INTEGER REFERENCES jurusan(id) ON DELETE SET NULL;
    END IF;
END $$;

-- Create index for better performance
CREATE INDEX IF NOT EXISTS idx_mahasiswa_jurusan_id ON mahasiswa(jurusan_id);
CREATE INDEX IF NOT EXISTS idx_jurusan_is_active ON jurusan(is_active);

-- Seed initial data for jurusan (common departments)
INSERT INTO jurusan (nama_jurusan, kode_jurusan, deskripsi, is_active) VALUES
('Teknik Informatika', 'TI', 'Program studi yang mempelajari tentang pengembangan perangkat lunak, algoritma, dan pemrograman', TRUE),
('Sistem Informasi', 'SI', 'Program studi yang mempelajari tentang sistem informasi bisnis dan manajemen teknologi informasi', TRUE),
('Teknik Komputer', 'TK', 'Program studi yang mempelajari tentang hardware, embedded systems, dan arsitektur komputer', TRUE),
('Teknologi Informasi', 'TIN', 'Program studi yang mempelajari tentang infrastruktur TI dan administrasi jaringan', TRUE),
('Manajemen Informatika', 'MI', 'Program studi yang mempelajari tentang manajemen sistem informasi dan e-business', TRUE)
ON CONFLICT (nama_jurusan) DO NOTHING;

-- Update existing mahasiswa data to link with jurusan table
UPDATE mahasiswa m
SET jurusan_id = j.id
FROM jurusan j
WHERE m.jurusan = j.nama_jurusan AND m.jurusan_id IS NULL;
