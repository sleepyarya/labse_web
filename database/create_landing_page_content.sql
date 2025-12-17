-- Create landing_page_content table
CREATE TABLE IF NOT EXISTS landing_page_content (
    id SERIAL PRIMARY KEY,
    section_name VARCHAR(50) NOT NULL,
    key_name VARCHAR(50) NOT NULL,
    content_value TEXT,
    content_type VARCHAR(20) DEFAULT 'text',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(section_name, key_name)
);

-- Seed data for Hero Section
INSERT INTO landing_page_content (section_name, key_name, content_value) VALUES
('hero', 'title', 'Laboratorium Software Engineering'),
('hero', 'subtitle', 'Berinovasi, Berkolaborasi, dan Berkembang bersama Teknologi Masa Depan')
ON CONFLICT (section_name, key_name) DO NOTHING;

-- Seed data for About Section
INSERT INTO landing_page_content (section_name, key_name, content_value) VALUES
('about', 'title', 'Tentang Lab SE'),
('about', 'subtitle', 'Pusat Keunggulan Pengembangan Perangkat Lunak'),
('about', 'card1_title', 'Unggul dalam Penelitian'),
('about', 'card1_desc', 'Melakukan penelitian inovatif dalam bidang rekayasa perangkat lunak dan teknologi informasi terkini.'),
('about', 'card2_title', 'Tim Berkualitas'),
('about', 'card2_desc', 'Didukung oleh dosen dan peneliti berpengalaman dengan sertifikasi internasional.'),
('about', 'card3_title', 'Inovasi Berkelanjutan'),
('about', 'card3_desc', 'Menghasilkan solusi software inovatif yang memberikan dampak nyata bagi masyarakat.')
ON CONFLICT (section_name, key_name) DO NOTHING;

-- Seed data for Navbar
INSERT INTO landing_page_content (section_name, key_name, content_value) VALUES
('navbar', 'brand_title', 'Jurusan Teknologi Informasi'),
('navbar', 'brand_subtitle', 'Politeknik Negeri Malang')
ON CONFLICT (section_name, key_name) DO NOTHING;

-- Seed data for Footer
INSERT INTO landing_page_content (section_name, key_name, content_value) VALUES
('footer', 'description', 'Pusat keunggulan dalam pengembangan perangkat lunak dan penelitian teknologi informasi.'),
('footer', 'email', 'labse@university.ac.id'),
('footer', 'phone', '+62 21 1234 5678'),
('footer', 'copyright', 'Lab Software Engineering. All rights reserved.')
ON CONFLICT (section_name, key_name) DO NOTHING;
