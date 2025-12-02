-- Add profile columns to admin_users table
-- Run this SQL to add profile management functionality

-- Add foto column for profile photo
ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS foto VARCHAR(255);

-- Add updated_at column for tracking profile updates
ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP;

-- Add comments for documentation
COMMENT ON COLUMN admin_users.foto IS 'Filename of admin profile photo';
COMMENT ON COLUMN admin_users.updated_at IS 'Last profile update timestamp';

-- Create index for better performance
CREATE INDEX IF NOT EXISTS idx_admin_users_foto ON admin_users(foto);
CREATE INDEX IF NOT EXISTS idx_admin_users_updated_at ON admin_users(updated_at);

-- Update existing records to have NULL values (which is fine)
-- No need to set default values as NULL is acceptable

SELECT 'Admin profile columns have been added successfully!' as message;
