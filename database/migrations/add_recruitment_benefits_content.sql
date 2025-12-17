-- Migration: Add recruitment benefits section to landing_page_content
-- This allows admin to edit the 4 benefit cards on the recruitment page

-- Seed data for Recruitment Benefits Section
INSERT INTO landing_page_content (section_name, key_name, content_value, content_type) VALUES
-- Section Title
('recruitment', 'benefits_title', 'Apa yang Anda Dapatkan?', 'text'),

-- Benefit Card 1 (Pelatihan Berkualitas)
('recruitment', 'benefit1_icon', 'bi-book', 'text'),
('recruitment', 'benefit1_title', 'Pelatihan Berkualitas', 'text'),
('recruitment', 'benefit1_desc', 'Akses ke workshop, training, dan mentoring dari expert', 'text'),

-- Benefit Card 2 (Proyek Real)
('recruitment', 'benefit2_icon', 'bi-laptop', 'text'),
('recruitment', 'benefit2_title', 'Proyek Real', 'text'),
('recruitment', 'benefit2_desc', 'Terlibat dalam proyek nyata dengan industri dan penelitian', 'text'),

-- Benefit Card 3 (Sertifikasi)
('recruitment', 'benefit3_icon', 'bi-award', 'text'),
('recruitment', 'benefit3_title', 'Sertifikasi', 'text'),
('recruitment', 'benefit3_desc', 'Kesempatan mendapat sertifikasi internasional', 'text'),

-- Benefit Card 4 (Networking)
('recruitment', 'benefit4_icon', 'bi-people', 'text'),
('recruitment', 'benefit4_title', 'Networking', 'text'),
('recruitment', 'benefit4_desc', 'Membangun jaringan dengan profesional dan mahasiswa lain', 'text')

ON CONFLICT (section_name, key_name) DO NOTHING;
