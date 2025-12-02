-- =====================================================
-- Migration: Remove dosen_pembimbing_id from mahasiswa
-- Created: 2025-11-29
-- Description: Hapus fitur dosen pembimbing dari sistem
-- =====================================================

-- Step 1: Drop views yang menggunakan kolom dosen_pembimbing_id
DROP VIEW IF EXISTS penelitian_view;
DROP VIEW IF EXISTS view_student_research_status;

-- Step 2: Drop functions/procedures yang menggunakan kolom tersebut
DROP FUNCTION IF EXISTS approve_penelitian(INTEGER, INTEGER);

-- Step 3: Drop index untuk performa
DROP INDEX IF EXISTS idx_mahasiswa_dosen;

-- Step 4: Drop foreign key constraint (jika ada nama eksplisit)
-- Jika constraint dibuat dengan nama otomatis, PostgreSQL akan handle saat DROP COLUMN
-- Uncomment jika ada constraint dengan nama eksplisit
-- ALTER TABLE mahasiswa DROP CONSTRAINT IF EXISTS mahasiswa_dosen_pembimbing_id_fkey;

-- Step 5: Drop kolom dosen_pembimbing_id dengan CASCADE untuk dependencies
-- CASCADE akan otomatis drop semua objects yang bergantung pada kolom ini
ALTER TABLE mahasiswa DROP COLUMN IF EXISTS dosen_pembimbing_id CASCADE;

-- Verification: Check if column is removed
-- Run this query to verify:
-- SELECT column_name FROM information_schema.columns WHERE table_name = 'mahasiswa';

-- =====================================================
-- NOTES:
-- 1. Backup database sebelum menjalankan migration ini
-- 2. Data dosen pembimbing akan hilang permanen
-- 3. View dan function yang terhapus tidak akan bisa digunakan lagi
-- 4. Pastikan tidak ada aplikasi lain yang menggunakan kolom ini
-- =====================================================
