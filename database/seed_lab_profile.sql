-- Seed data for Lab Profile (Visi, Misi, Roadmap)

-- Visi
INSERT INTO lab_profile (kategori, judul, konten) VALUES
('visi', 'Visi Kami', 'Menjadi pusat unggulan dalam pendidikan dan penelitian rekayasa perangkat lunak yang diakui secara internasional, menghasilkan inovasi teknologi yang bermanfaat bagi masyarakat luas.')
ON CONFLICT DO NOTHING;

-- Misi
INSERT INTO lab_profile (kategori, judul, konten) VALUES
('misi', 'Pendidikan Berkualitas', 'Menyelenggarakan pendidikan berkualitas tinggi di bidang software engineering dengan kurikulum yang relevan dengan kebutuhan industri.'),
('misi', 'Penelitian Inovatif', 'Melaksanakan penelitian inovatif yang berkontribusi pada kemajuan teknologi informasi dan komunikasi.'),
('misi', 'Kolaborasi Global', 'Membangun kerjasama strategis dengan industri, pemerintah, dan institusi pendidikan global untuk meningkatkan daya saing.'),
('misi', 'Pengabdian Masyarakat', 'Menerapkan solusi teknologi tepat guna untuk memecahkan permasalahan di masyarakat.')
ON CONFLICT DO NOTHING;

-- Roadmap
INSERT INTO lab_profile (kategori, judul, konten) VALUES
('roadmap', '2024 - Q1', 'Modernisasi Infrastruktur: Upgrade peralatan laboratorium dan implementasi cloud infrastructure untuk mendukung pembelajaran dan penelitian yang lebih efektif.'),
('roadmap', '2024 - Q2', 'Program Sertifikasi Internasional: Meluncurkan program sertifikasi AWS, Azure, dan Google Cloud untuk meningkatkan kompetensi mahasiswa dan dosen.'),
('roadmap', '2024 - Q3', 'Kemitraan Industri: Membangun kemitraan strategis dengan perusahaan teknologi untuk proyek kolaborasi dan program magang.'),
('roadmap', '2024 - Q4', 'Penelitian AI & Machine Learning: Inisiasi program penelitian fokus pada Artificial Intelligence dan Machine Learning dengan publikasi di jurnal internasional.'),
('roadmap', '2025 - Q1', 'Peluncuran Inkubator Startup: Membuka inkubator untuk mendukung mahasiswa mengembangkan ide bisnis teknologi mereka.'),
('roadmap', '2025 - Q2', 'Konferensi Internasional: Menyelenggarakan konferensi internasional tentang Software Engineering dan mengundang pembicara dari berbagai negara.'),
('roadmap', '2025 - Q3', 'Expansion Program: Perluasan fasilitas laboratorium dan penambahan program penelitian baru dalam bidang Blockchain dan IoT.'),
('roadmap', '2026 - 2030', 'Menjadi Center of Excellence: Mewujudkan Lab SE sebagai Center of Excellence di tingkat nasional dan internasional dengan kontribusi signifikan dalam riset dan industri.')
ON CONFLICT DO NOTHING;

-- Ensure Tentang exists (if not already)
INSERT INTO lab_profile (kategori, judul, konten) VALUES
('tentang', 'Tentang Lab SE', 'Lab Software Engineering adalah pusat inovasi dan pembelajaran yang berdedikasi untuk mencetak talenta terbaik di bidang rekayasa perangkat lunak. Kami menggabungkan kurikulum akademis yang ketat dengan pengalaman praktis melalui proyek-proyek nyata.

Fasilitas kami dilengkapi dengan teknologi terkini untuk mendukung eksplorasi mahasiswa dalam berbagai bidang seperti Web Development, Mobile Apps, Cloud Computing, dan Artificial Intelligence. Kami percaya bahwa kolaborasi dan inovasi adalah kunci untuk menciptakan solusi teknologi masa depan.')
ON CONFLICT DO NOTHING;
