-- Add approval system to mahasiswa table
-- Run this SQL to add approval functionality

-- Add approval status column
ALTER TABLE mahasiswa ADD COLUMN IF NOT EXISTS status_approval VARCHAR(20) DEFAULT 'pending';
ALTER TABLE mahasiswa ADD COLUMN IF NOT EXISTS approved_by INTEGER;
ALTER TABLE mahasiswa ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP;
ALTER TABLE mahasiswa ADD COLUMN IF NOT EXISTS rejection_reason TEXT;

-- Add foreign key for approved_by (references admin_users)
ALTER TABLE mahasiswa ADD CONSTRAINT fk_mahasiswa_approved_by 
    FOREIGN KEY (approved_by) REFERENCES admin_users(id) ON DELETE SET NULL;

-- Update existing records to pending status
UPDATE mahasiswa SET status_approval = 'pending' WHERE status_approval IS NULL;

-- Create index for better performance
CREATE INDEX IF NOT EXISTS idx_mahasiswa_status ON mahasiswa(status_approval);
CREATE INDEX IF NOT EXISTS idx_mahasiswa_approved_by ON mahasiswa(approved_by);

-- Add comments for documentation
COMMENT ON COLUMN mahasiswa.status_approval IS 'Status persetujuan: pending, approved, rejected';
COMMENT ON COLUMN mahasiswa.approved_by IS 'ID admin yang menyetujui/menolak';
COMMENT ON COLUMN mahasiswa.approved_at IS 'Waktu persetujuan/penolakan';
COMMENT ON COLUMN mahasiswa.rejection_reason IS 'Alasan penolakan (jika ditolak)';

SELECT 'Mahasiswa approval system has been added successfully!' as message;
