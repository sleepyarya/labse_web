-- =====================================================
-- Migration: Create hasil_penelitian table
-- Created: 2025-11-29
-- Description: Table untuk mengelola hasil penelitian
-- =====================================================

-- Drop table if exists
DROP TABLE IF EXISTS hasil_penelitian CASCADE;

-- Create hasil_penelitian table
CREATE TABLE hasil_penelitian (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    tahun INTEGER NOT NULL,
    kategori VARCHAR(100),               -- Kategori: Fundamental, Terapan, Pengembangan
    abstrak TEXT,
    gambar VARCHAR(255),
    file_pdf VARCHAR(255),               -- File PDF hasil penelitian
    link_publikasi TEXT,                 -- Link ke publikasi jurnal/conference
    personil_id INTEGER REFERENCES personil(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add indexes for better query performance
CREATE INDEX idx_penelitian_personil_id ON hasil_penelitian(personil_id);
CREATE INDEX idx_penelitian_tahun ON hasil_penelitian(tahun);
CREATE INDEX idx_penelitian_kategori ON hasil_penelitian(kategori);

-- Insert sample data
INSERT INTO hasil_penelitian (judul, deskripsi, tahun, kategori, abstrak, gambar, link_publikasi) VALUES
('Sistem Deteksi Objek Menggunakan YOLO v8', 
 'Implementasi algoritma YOLO v8 untuk deteksi objek real-time dengan akurasi tinggi pada berbagai kondisi pencahayaan.',
 2024,
 'Terapan',
 'Penelitian ini mengimplementasikan algoritma YOLO v8 untuk sistem deteksi objek secara real-time. Hasil penelitian menunjukkan akurasi deteksi mencapai 94.5% pada dataset COCO dengan kecepatan processing 60 FPS.',
 NULL,
 'https://ieeexplore.ieee.org/document/example1'),

('Optimasi Algoritma Machine Learning untuk Prediksi Cuaca',
 'Penelitian pengembangan model prediksi cuaca menggunakan ensemble learning dengan kombinasi Random Forest dan Neural Networks.',
 2024,
 'Fundamental',
 'Studi ini mengembangkan model hybrid untuk prediksi cuaca jangka pendek dengan menggabungkan Random Forest dan Deep Neural Networks. Model yang dikembangkan mampu meningkatkan akurasi prediksi hingga 15% dibandingkan model konvensional.',
 NULL,
 NULL),

('Pengembangan Framework IoT untuk Smart Home',
 'Framework open-source untuk integrasi berbagai perangkat IoT dalam sistem smart home yang efisien dan scalable.',
 2023,
 'Pengembangan',
 'Penelitian ini menghasilkan framework IoT yang memudahkan integrasi berbagai device smart home. Framework mendukung protokol MQTT, HTTP, dan WebSocket dengan arsitektur microservices.',
 NULL,
 'https://github.com/example/smart-home-framework'),

('Analisis Sentimen Media Sosial Menggunakan BERT',
 'Implementasi model BERT untuk analisis sentimen bahasa Indonesia dengan fine-tuning pada dataset lokal.',
 2023,
 'Terapan',
 'Penelitian menggunakan pre-trained model BERT yang di-fine-tune dengan dataset sentimen bahasa Indonesia. Hasil menunjukkan akurasi 89.7% dalam klasifikasi sentimen positif, negatif, dan netral.',
 NULL,
 'https://aclanthology.org/example2');

-- Verification query
SELECT COUNT(*) as total_penelitian FROM hasil_penelitian;
