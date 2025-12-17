-- Create table pengabdian for community service/training activities
-- Similar structure to artikel table

DROP TABLE IF EXISTS pengabdian CASCADE;

CREATE TABLE pengabdian (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    tanggal DATE NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    penyelenggara VARCHAR(255) NOT NULL,
    gambar VARCHAR(255),
    personil_id INTEGER REFERENCES personil(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add index for better query performance
CREATE INDEX idx_pengabdian_personil_id ON pengabdian(personil_id);
CREATE INDEX idx_pengabdian_tanggal ON pengabdian(tanggal);

-- Insert sample data
INSERT INTO pengabdian (judul, deskripsi, tanggal, lokasi, penyelenggara, gambar) VALUES
('Pelatihan Web Development untuk UMKM', 'Kegiatan pelatihan pembuatan website untuk pelaku UMKM di wilayah Semarang. Peserta diajarkan cara membuat website sederhana menggunakan WordPress dan dasar-dasar digital marketing.', '2024-10-15', 'Aula Kelurahan Tembalang, Semarang', 'Lab Software Engineering', 'pelatihan-umkm.jpg'),
('Workshop IoT untuk Siswa SMA', 'Workshop pengenalan Internet of Things (IoT) untuk siswa SMA di Jawa Tengah. Materi meliputi pengenalan Arduino, sensor, dan pembuatan prototype smart home sederhana.', '2024-09-20', 'SMA Negeri 1 Semarang', 'Lab Software Engineering', 'workshop-iot.jpg'),
('Pengabdian Masyarakat: Digitalisasi Desa', 'Program pengabdian masyarakat untuk membantu digitalisasi administrasi desa. Termasuk pembuatan sistem informasi desa dan pelatihan penggunaan untuk aparat desa.', '2024-08-10', 'Desa Mangunsari, Ungaran', 'Lab Software Engineering', 'digitalisasi-desa.jpg'),
('Pelatihan Mobile App Development', 'Pelatihan pengembangan aplikasi mobile menggunakan Flutter untuk mahasiswa dan masyarakat umum. Peserta membuat aplikasi sederhana dari nol hingga publish ke Play Store.', '2024-11-05', 'Lab Software Engineering, Kampus UNDIP', 'Lab Software Engineering', 'pelatihan-flutter.jpg');
