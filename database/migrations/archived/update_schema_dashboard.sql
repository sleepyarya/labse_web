-- Add dosen_pembimbing_id to mahasiswa table
ALTER TABLE mahasiswa 
ADD COLUMN IF NOT EXISTS dosen_pembimbing_id INTEGER REFERENCES personil(id);

-- Create penelitian table
CREATE TABLE IF NOT EXISTS penelitian (
    id SERIAL PRIMARY KEY,
    mahasiswa_id INTEGER NOT NULL REFERENCES mahasiswa(id),
    judul VARCHAR(255) NOT NULL,
    file_path VARCHAR(255), -- For PDF/Word/Image
    link_drive TEXT, -- For Google Drive link
    keterangan TEXT,
    status VARCHAR(50) DEFAULT 'submitted', -- submitted, reviewed, approved
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create komentar_penelitian table
CREATE TABLE IF NOT EXISTS komentar_penelitian (
    id SERIAL PRIMARY KEY,
    penelitian_id INTEGER NOT NULL REFERENCES penelitian(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id), -- Who commented (Dosen or Mahasiswa)
    isi TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index for performance
CREATE INDEX IF NOT EXISTS idx_mahasiswa_dosen ON mahasiswa(dosen_pembimbing_id);
CREATE INDEX IF NOT EXISTS idx_penelitian_mahasiswa ON penelitian(mahasiswa_id);
CREATE INDEX IF NOT EXISTS idx_komentar_penelitian ON komentar_penelitian(penelitian_id);
