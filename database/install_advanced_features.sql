-- =====================================================
-- MANUAL INSTALLATION SCRIPT
-- Jalankan file ini di psql jika migration PHP gagal
-- =====================================================

-- Step 1: Create Materialized Views
\echo '===== STEP 1: Creating Materialized Views ====='
\i create_materialized_views.sql

-- Step 2: Create Stored Procedures
\echo '===== STEP 2: Creating Stored Procedures ====='
\i create_stored_procedures.sql

-- Step 3: Create Trigger Functions
\echo '===== STEP 3: Creating Trigger Functions ====='
\i create_trigger_functions.sql

-- Step 4: Verify Installation
\echo '===== VERIFICATION ====='

-- Check materialized views
\echo 'Materialized Views:'
SELECT matviewname FROM pg_matviews WHERE schemaname = 'public' ORDER BY matviewname;

-- Check functions
\echo 'Custom Functions:'
SELECT routine_name, routine_type 
FROM information_schema.routines 
WHERE routine_schema = 'public' 
  AND (routine_name LIKE 'sp_%' OR routine_name LIKE 'fn_%' OR routine_name LIKE 'trigger_%')
ORDER BY routine_name;

-- Check triggers
\echo 'Triggers:'
SELECT trigger_name, event_object_table 
FROM information_schema.triggers 
WHERE trigger_schema = 'public'
ORDER BY trigger_name;

\echo '===== INSTALLATION COMPLETE ====='
\echo 'Next: Test dengan query SELECT * FROM mv_dashboard_statistics;'
