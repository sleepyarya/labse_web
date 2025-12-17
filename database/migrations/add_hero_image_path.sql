-- Migration: Add hero_image_path to landing_page_content
-- Purpose: Add entry for hero section image path
-- Date: 2025-12-03

-- Insert hero image path entry
INSERT INTO landing_page_content (section_name, key_name, content_value, content_type) 
VALUES ('hero', 'image_path', '/public/img/logo-se.png', 'image')
ON CONFLICT (section_name, key_name) DO NOTHING;

-- Add comment
COMMENT ON TABLE landing_page_content IS 'Stores landing page content including hero section image';
