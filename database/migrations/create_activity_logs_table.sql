-- Migration: Create activity_logs table
-- Description: Tabel untuk menyimpan riwayat aktivitas personil
-- Created: 2025-12-03

-- Drop table if exists (untuk development)
DROP TABLE IF EXISTS activity_logs CASCADE;

-- Create activity_logs table
CREATE TABLE activity_logs (
    id SERIAL PRIMARY KEY,
    personil_id INTEGER NOT NULL,
    personil_nama VARCHAR(255) NOT NULL,
    action_type VARCHAR(100) NOT NULL,
    action_description TEXT NOT NULL,
    target_type VARCHAR(50),
    target_id INTEGER,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activity_personil FOREIGN KEY (personil_id) 
        REFERENCES personil(id) ON DELETE CASCADE
);

-- Create indexes for better query performance
CREATE INDEX idx_activity_logs_personil ON activity_logs(personil_id);
CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at DESC);
CREATE INDEX idx_activity_logs_action_type ON activity_logs(action_type);
CREATE INDEX idx_activity_logs_target ON activity_logs(target_type, target_id);

-- Add comment to table
COMMENT ON TABLE activity_logs IS 'Tabel untuk menyimpan riwayat aktivitas personil';
COMMENT ON COLUMN activity_logs.action_type IS 'Tipe aktivitas: LOGIN, LOGOUT, CREATE_ARTICLE, EDIT_ARTICLE, DELETE_ARTICLE, dll';
COMMENT ON COLUMN activity_logs.action_description IS 'Deskripsi detail aktivitas dalam bahasa Indonesia';
COMMENT ON COLUMN activity_logs.target_type IS 'Jenis target: artikel, penelitian, pengabdian, produk, profile';
COMMENT ON COLUMN activity_logs.target_id IS 'ID dari target yang dimanipulasi';
COMMENT ON COLUMN activity_logs.ip_address IS 'IP address saat aktivitas dilakukan';
