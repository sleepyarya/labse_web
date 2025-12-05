-- Database: labse
-- Drop tables if exist
DROP TABLE IF EXISTS artikel CASCADE;
DROP TABLE IF EXISTS mahasiswa CASCADE;
DROP TABLE IF EXISTS personil CASCADE;
DROP TABLE IF EXISTS lab_profile CASCADE;
DROP TABLE IF EXISTS admin_users CASCADE;

-- Create table lab_profile
CREATE TABLE lab_profile (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    konten TEXT NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create table personil
CREATE TABLE personil (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255),
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create table mahasiswa
CREATE TABLE mahasiswa (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    nim VARCHAR(50) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    alasan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create table artikel
CREATE TABLE artikel (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    penulis VARCHAR(255) NOT NULL,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create table admin_users
CREATE TABLE admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Insert sample data into lab_profile
INSERT INTO lab_profile (judul, konten, kategori) VALUES
('Tentang Lab Software Engineering', 'Laboratorium Software Engineering adalah pusat keunggulan dalam pengembangan perangkat lunak yang berfokus pada penelitian, pengembangan, dan implementasi praktik terbaik dalam rekayasa perangkat lunak. Kami berkomitmen untuk menghasilkan lulusan yang kompeten dan siap menghadapi tantangan industri teknologi informasi.', 'tentang'),
('Visi', 'Menjadi laboratorium software engineering terkemuka yang menghasilkan inovasi dan praktisi berkualitas tinggi dalam bidang rekayasa perangkat lunak di tingkat nasional dan internasional pada tahun 2030.', 'visi'),
('Misi 1', 'Menyelenggarakan pendidikan dan pelatihan berkualitas tinggi dalam bidang software engineering dengan pendekatan praktis dan berbasis industri.', 'misi'),
('Misi 2', 'Melakukan penelitian dan pengembangan yang inovatif untuk memajukan ilmu pengetahuan dan teknologi di bidang rekayasa perangkat lunak.', 'misi'),
('Misi 3', 'Membangun kemitraan strategis dengan industri dan institusi pendidikan untuk meningkatkan kualitas pembelajaran dan penelitian.', 'misi'),
('Focus Area 1', 'Web Development dan Cloud Computing - Mengembangkan aplikasi web modern dengan arsitektur cloud-native dan scalable.', 'focus'),
('Focus Area 2', 'Mobile Application Development - Penelitian dan pengembangan aplikasi mobile cross-platform dengan performa optimal.', 'focus'),
('Focus Area 3', 'Software Quality Assurance - Implementasi metode testing dan quality assurance untuk menghasilkan software berkualitas tinggi.', 'focus'),
('Focus Area 4', 'DevOps dan CI/CD - Otomasi proses development dan deployment untuk meningkatkan efisiensi pengembangan software.', 'focus');

-- Insert sample data into personil
INSERT INTO personil (nama, jabatan, deskripsi, foto, email) VALUES
('Dr. Ahmad Fauzi, M.Kom', 'Kepala Laboratorium', 'Memiliki pengalaman lebih dari 15 tahun dalam bidang software engineering dan telah memimpin berbagai proyek penelitian skala nasional dan internasional.', 'ahmad-fauzi.jpg', 'ahmad.fauzi@university.ac.id'),
('Prof. Dr. Siti Nurhaliza, M.T', 'Koordinator Penelitian', 'Ahli dalam bidang software architecture dan design patterns. Telah mempublikasikan lebih dari 50 paper di jurnal internasional bereputasi.', 'siti-nurhaliza.jpg', 'siti.nurhaliza@university.ac.id'),
('Budi Santoso, Ph.D', 'Dosen Senior', 'Spesialisasi dalam web development dan cloud computing. Aktif membimbing mahasiswa dalam proyek-proyek inovatif.', 'budi-santoso.jpg', 'budi.santoso@university.ac.id'),
('Dr. Rina Wijaya, M.Sc', 'Dosen Senior', 'Fokus penelitian pada mobile application development dan user experience design. Memiliki sertifikasi internasional di bidang UX/UI.', 'rina-wijaya.jpg', 'rina.wijaya@university.ac.id'),
('Muhammad Rizki, M.Kom', 'Asisten Laboratorium', 'Mengelola operasional laboratorium dan memberikan support teknis kepada mahasiswa dalam kegiatan praktikum.', 'muhammad-rizki.jpg', 'muhammad.rizki@university.ac.id'),
('Dewi Lestari, M.T', 'Asisten Laboratorium', 'Membantu dalam kegiatan penelitian dan pengembangan serta membimbing mahasiswa dalam proyek akhir.', 'dewi-lestari.jpg', 'dewi.lestari@university.ac.id');

-- Insert sample data into mahasiswa
INSERT INTO mahasiswa (nama, nim, jurusan, email, alasan) VALUES
('Andi Prasetyo', '2021001', 'Teknik Informatika', 'andi.prasetyo@student.ac.id', 'Saya tertarik untuk mendalami software engineering karena passion saya dalam mengembangkan aplikasi yang bermanfaat untuk masyarakat.'),
('Fitri Rahmawati', '2021002', 'Sistem Informasi', 'fitri.rahmawati@student.ac.id', 'Ingin mengembangkan skill dalam web development dan berkontribusi dalam proyek-proyek penelitian di lab.'),
('Dimas Adiputra', '2021003', 'Teknik Informatika', 'dimas.adiputra@student.ac.id', 'Tertarik dengan teknologi cloud computing dan ingin belajar best practices dalam software development.'),
('Sarah Amelia', '2021004', 'Sistem Informasi', 'sarah.amelia@student.ac.id', 'Memiliki minat kuat dalam mobile app development dan ingin mengaplikasikan teori ke dalam praktik nyata.'),
('Reza Pratama', '2021005', 'Teknik Informatika', 'reza.pratama@student.ac.id', 'Ingin mendalami DevOps dan CI/CD untuk meningkatkan efisiensi dalam pengembangan software.');

-- Insert sample data into artikel
INSERT INTO artikel (judul, isi, penulis, gambar) VALUES
('Penerapan Design Patterns dalam Pengembangan Aplikasi Enterprise', 'Design patterns adalah solusi umum yang dapat digunakan kembali untuk masalah yang sering terjadi dalam desain software. Dalam pengembangan aplikasi enterprise, penerapan design patterns seperti Singleton, Factory, dan Observer sangat penting untuk menciptakan kode yang maintainable dan scalable. Artikel ini membahas bagaimana menerapkan berbagai design patterns dalam konteks aplikasi enterprise modern, termasuk contoh implementasi praktis dan best practices yang perlu diperhatikan.', 'Dr. Ahmad Fauzi, M.Kom', 'design-patterns.jpg'),
('Optimasi Performance Aplikasi Web dengan Caching Strategy', 'Performance adalah faktor krusial dalam kesuksesan aplikasi web modern. Salah satu teknik yang paling efektif adalah implementasi caching strategy yang tepat. Artikel ini mengeksplorasi berbagai jenis caching mulai dari browser caching, CDN caching, hingga application-level caching menggunakan Redis atau Memcached. Kami juga membahas kapan dan bagaimana mengimplementasikan masing-masing strategi untuk hasil optimal.', 'Budi Santoso, Ph.D', 'web-optimization.jpg'),
('Microservices Architecture: Keuntungan dan Tantangan Implementasinya', 'Microservices architecture telah menjadi pilihan populer untuk aplikasi skala besar. Arsitektur ini menawarkan fleksibilitas, scalability, dan kemudahan dalam deployment. Namun, implementasinya juga membawa tantangan tersendiri seperti distributed system complexity dan data consistency. Artikel ini memberikan panduan komprehensif tentang bagaimana merancang dan mengimplementasikan microservices architecture dengan efektif.', 'Prof. Dr. Siti Nurhaliza, M.T', 'microservices.jpg'),
('Mobile App Development: Flutter vs React Native di Tahun 2024', 'Memilih framework yang tepat untuk mobile app development adalah keputusan penting yang akan mempengaruhi seluruh lifecycle proyek. Flutter dan React Native adalah dua framework cross-platform paling populer saat ini. Artikel ini membandingkan keduanya dari berbagai aspek seperti performance, developer experience, ecosystem, dan community support berdasarkan pengalaman praktis kami dalam berbagai proyek.', 'Dr. Rina Wijaya, M.Sc', 'mobile-dev.jpg'),
('Implementasi CI/CD Pipeline untuk Meningkatkan Produktivitas Tim', 'Continuous Integration dan Continuous Deployment (CI/CD) adalah praktik essential dalam modern software development. Dengan CI/CD pipeline yang baik, tim dapat melakukan deployment lebih cepat dengan risiko yang lebih rendah. Artikel ini menjelaskan step-by-step implementasi CI/CD pipeline menggunakan tools seperti Jenkins, GitLab CI, dan GitHub Actions, lengkap dengan contoh konfigurasi dan best practices.', 'Muhammad Rizki, M.Kom', 'cicd-pipeline.jpg'),
('Software Testing Strategy: Unit, Integration, dan E2E Testing', 'Testing adalah komponen vital dalam software development lifecycle. Strategi testing yang komprehensif meliputi unit testing, integration testing, dan end-to-end testing. Artikel ini membahas kapan menggunakan masing-masing jenis testing, tools yang dapat digunakan, dan bagaimana mencapai test coverage yang optimal tanpa mengorbankan development velocity.', 'Dewi Lestari, M.T', 'software-testing.jpg');

-- Insert sample data into admin_users
-- Password disimpan di tabel users, bukan di admin_users
INSERT INTO admin_users (username, nama_lengkap, email) VALUES
('admin', 'Administrator', 'admin@labse.ac.id'),
('superadmin', 'Super Administrator', 'superadmin@labse.ac.id');