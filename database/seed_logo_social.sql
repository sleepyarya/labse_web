-- Seed data for Navbar Logo and Social Media
INSERT INTO landing_page_content (section_name, key_name, content_value, content_type) VALUES
('navbar', 'logo_path', '/public/img/logo-pnm.png', 'image'),
('footer', 'social_facebook', '#', 'link'),
('footer', 'social_twitter', '#', 'link'),
('footer', 'social_instagram', '#', 'link'),
('footer', 'social_linkedin', '#', 'link'),
('footer', 'social_youtube', '#', 'link')
ON CONFLICT (section_name, key_name) DO NOTHING;
