-- ============================================
-- SQL Migration: Create Produk Table
-- Description: Table untuk manajemen produk Lab Software Engineering
-- ============================================

-- Drop table if exists (untuk re-run migration)
DROP TABLE IF EXISTS produk CASCADE;

-- Create produk table
CREATE TABLE produk (
    id SERIAL PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    kategori VARCHAR(100),  -- Hardware atau Software
    tahun INTEGER NOT NULL,
    gambar VARCHAR(255),
    link_demo TEXT,
    link_repository TEXT,
    teknologi TEXT,  -- Stack teknologi yang digunakan (comma separated)
    personil_id INTEGER REFERENCES personil(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX idx_produk_personil_id ON produk(personil_id);
CREATE INDEX idx_produk_tahun ON produk(tahun);
CREATE INDEX idx_produk_kategori ON produk(kategori);

-- Insert sample data
INSERT INTO produk (nama_produk, deskripsi, kategori, tahun, teknologi, personil_id) VALUES
('Sistem Informasi Akademik', 'Aplikasi untuk mengelola data akademik mahasiswa, dosen, dan jadwal perkuliahan dengan fitur lengkap.', 'Software', 2023, 'PHP, Laravel, MySQL, Bootstrap', 1),
('Smart Home Controller', 'Perangkat hardware untuk mengontrol peralatan rumah tangga menggunakan IoT dan aplikasi mobile.', 'Hardware', 2023, 'Arduino, ESP32, Android, Firebase', 2),
('E-Learning Platform', 'Platform pembelajaran online dengan fitur video conference, quiz, dan assignment management.', 'Software', 2024, 'Node.js, React, MongoDB, WebRTC', 1),
('Traffic Monitoring System', 'Sistem monitoring lalu lintas berbasis AI dengan kamera CCTV dan dashboard real-time.', 'Hardware', 2024, 'Raspberry Pi, Python, OpenCV, TensorFlow', 2);

-- Success message
SELECT 'Tabel produk berhasil dibuat!' as status;
