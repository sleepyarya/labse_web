-- Migration: Create recruitment_settings table
-- Purpose: Store recruitment open/close status and custom message
-- Date: 2025-12-02

-- Drop table if exists
DROP TABLE IF EXISTS recruitment_settings CASCADE;

-- Create table recruitment_settings
CREATE TABLE recruitment_settings (
    id SERIAL PRIMARY KEY,
    is_open BOOLEAN NOT NULL DEFAULT TRUE,
    message TEXT DEFAULT 'Maaf, Lab SE sedang tidak membuka recruitment saat ini. Silakan cek kembali nanti.',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by VARCHAR(255) DEFAULT 'System'
);

-- Insert default data (recruitment is open by default)
INSERT INTO recruitment_settings (is_open, message, updated_by) 
VALUES (TRUE, 'Maaf, Lab SE sedang tidak membuka recruitment saat ini. Silakan cek kembali nanti.', 'System');

-- Create index for faster queries
CREATE INDEX idx_recruitment_settings_is_open ON recruitment_settings(is_open);

-- Add comment to table
COMMENT ON TABLE recruitment_settings IS 'Tabel untuk mengatur status recruitment (buka/tutup) dan pesan custom';
COMMENT ON COLUMN recruitment_settings.is_open IS 'Status recruitment: TRUE = buka, FALSE = tutup';
COMMENT ON COLUMN recruitment_settings.message IS 'Pesan yang ditampilkan ketika recruitment ditutup';
COMMENT ON COLUMN recruitment_settings.updated_at IS 'Waktu terakhir update setting';
COMMENT ON COLUMN recruitment_settings.updated_by IS 'Admin yang melakukan update terakhir';
