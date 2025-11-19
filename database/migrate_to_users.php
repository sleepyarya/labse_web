<?php
/**
 * Script Migrasi Data ke Tabel Users
 * 
 * Script ini akan:
 * 1. Membaca semua data dari admin_users
 * 2. Membaca semua data dari personil yang is_member = TRUE
 * 3. Membaca semua data dari mahasiswa (opsional)
 * 4. Insert ke tabel users dengan role yang sesuai
 * 
 * PENTING: Backup database sebelum menjalankan script ini!
 */

require_once '../includes/config.php';

// Fungsi untuk log proses
function logMessage($message, $type = 'info') {
    $colors = [
        'info' => "\033[0;36m",    // Cyan
        'success' => "\033[0;32m", // Green
        'warning' => "\033[0;33m", // Yellow
        'error' => "\033[0;31m",   // Red
        'reset' => "\033[0m"       // Reset
    ];
    
    $timestamp = date('Y-m-d H:i:s');
    echo $colors[$type] . "[{$timestamp}] [{$type}] {$message}" . $colors['reset'] . "\n";
}

// Default password for migrated users (used only in users table)
$default_password = 'admin123';
$default_hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

// Header
echo "\n";
echo "====================================================\n";
echo "  MIGRASI DATA KE TABEL USERS\n";
echo "  Lab Software Engineering - Politeknik Negeri Malang\n";
echo "====================================================\n\n";

// Step 1: Cek apakah tabel users sudah ada
logMessage("Memeriksa tabel users...", 'info');
$check_table = "SELECT EXISTS (
    SELECT FROM information_schema.tables 
    WHERE table_name = 'users'
)";
$result = pg_query($conn, $check_table);
$exists = pg_fetch_result($result, 0, 0);

if ($exists === 'f') {
    logMessage("Tabel users belum ada. Jalankan create_users_table.sql terlebih dahulu!", 'error');
    exit(1);
}
logMessage("Tabel users ditemukan ✓", 'success');

// Step 2: Hitung total data yang akan dimigrasi
logMessage("Menghitung data yang akan dimigrasi...", 'info');

$count_admin = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM admin_users"), 0, 0);
$count_personil = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM personil WHERE is_member = TRUE"), 0, 0);
$count_mahasiswa = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM mahasiswa"), 0, 0);

logMessage("Admin: {$count_admin} records", 'info');
logMessage("Personil (Member): {$count_personil} records", 'info');
logMessage("Mahasiswa: {$count_mahasiswa} records (akan diabaikan untuk saat ini)", 'info');

$total_to_migrate = $count_admin + $count_personil;
logMessage("Total yang akan dimigrasi: {$total_to_migrate} records\n", 'info');

// Konfirmasi
echo "Apakah Anda ingin melanjutkan migrasi? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim($line) != 'y' && trim($line) != 'Y') {
    logMessage("Migrasi dibatalkan oleh user", 'warning');
    exit(0);
}
fclose($handle);

echo "\n";

// Step 3: Mulai transaksi
pg_query($conn, "BEGIN");

try {
    $migrated_count = 0;
    $skipped_count = 0;
    $error_count = 0;
    
    // Step 4: Migrasi data dari admin_users
    logMessage("Memigrasikan data dari admin_users...", 'info');
    
    $query_admin = "SELECT id, username, email, nama_lengkap FROM admin_users ORDER BY id";
    $result_admin = pg_query($conn, $query_admin);
    
    while ($admin = pg_fetch_assoc($result_admin)) {
        // Cek apakah sudah ada di users
        $check_query = "SELECT id FROM users WHERE email = $1 OR username = $2";
        $check_result = pg_query_params($conn, $check_query, array($admin['email'], $admin['username']));
        
        if (pg_num_rows($check_result) > 0) {
            logMessage("Admin '{$admin['username']}' sudah ada di tabel users, skip", 'warning');
            $skipped_count++;
            continue;
        }
        
        // Insert ke users (gunakan default hashed password)
        $insert_query = "INSERT INTO users (username, email, password, role, reference_id, is_active, last_login) 
                        VALUES ($1, $2, $3, 'admin', $4, TRUE, NULL)";
        $insert_result = pg_query_params($conn, $insert_query, array(
            $admin['username'],
            $admin['email'],
            $default_hashed_password,
            $admin['id']
        ));
        
        if ($insert_result) {
            logMessage("✓ Admin '{$admin['username']}' berhasil dimigrasi", 'success');
            $migrated_count++;
        } else {
            logMessage("✗ Gagal migrasi admin '{$admin['username']}': " . pg_last_error($conn), 'error');
            $error_count++;
        }
    }
    
    // Step 5: Migrasi data dari personil (member)
    logMessage("\nMemigrasikan data dari personil (member)...", 'info');
    
    $query_personil = "SELECT id, nama, email FROM personil 
                      WHERE is_member = TRUE 
                      ORDER BY id";
    $result_personil = pg_query($conn, $query_personil);
    
    while ($personil = pg_fetch_assoc($result_personil)) {
        // Buat username dari email (bagian sebelum @)
        $username = strstr($personil['email'], '@', true);
        
        // Cek apakah sudah ada di users
        $check_query = "SELECT id FROM users WHERE email = $1";
        $check_result = pg_query_params($conn, $check_query, array($personil['email']));
        
        if (pg_num_rows($check_result) > 0) {
            logMessage("Personil '{$personil['nama']}' sudah ada di tabel users, skip", 'warning');
            $skipped_count++;
            continue;
        }
        
        // Jika username sudah ada, tambahkan suffix
        $check_username = "SELECT id FROM users WHERE username = $1";
        $check_username_result = pg_query_params($conn, $check_username, array($username));
        if (pg_num_rows($check_username_result) > 0) {
            $username = $username . '_' . $personil['id'];
        }
        
        // Insert ke users (gunakan default hashed password)
        $insert_query = "INSERT INTO users (username, email, password, role, reference_id, is_active, last_login) 
                        VALUES ($1, $2, $3, 'personil', $4, TRUE, NULL)";
        $insert_result = pg_query_params($conn, $insert_query, array(
            $username,
            $personil['email'],
            $default_hashed_password,
            $personil['id']
        ));
        
        if ($insert_result) {
            logMessage("✓ Personil '{$personil['nama']}' (username: {$username}) berhasil dimigrasi", 'success');
            $migrated_count++;
        } else {
            logMessage("✗ Gagal migrasi personil '{$personil['nama']}': " . pg_last_error($conn), 'error');
            $error_count++;
        }
    }
    
    // Commit transaksi
    pg_query($conn, "COMMIT");
    
    // Summary
    echo "\n";
    echo "====================================================\n";
    echo "  RINGKASAN MIGRASI\n";
    echo "====================================================\n";
    logMessage("Total berhasil dimigrasi: {$migrated_count}", 'success');
    logMessage("Total dilewati (sudah ada): {$skipped_count}", 'warning');
    logMessage("Total error: {$error_count}", $error_count > 0 ? 'error' : 'info');
    echo "====================================================\n\n";
    
    if ($error_count === 0 && $migrated_count > 0) {
        logMessage("Migrasi selesai dengan sukses! ✓", 'success');
    } elseif ($migrated_count === 0 && $skipped_count > 0) {
        logMessage("Tidak ada data baru yang perlu dimigrasi", 'info');
    } else {
        logMessage("Migrasi selesai dengan beberapa error", 'warning');
    }
    
} catch (Exception $e) {
    // Rollback jika ada error
    pg_query($conn, "ROLLBACK");
    logMessage("\nMigrasi dibatalkan karena error: " . $e->getMessage(), 'error');
    exit(1);
}

pg_close($conn);

echo "\n";
logMessage("Script selesai dijalankan", 'info');
echo "\n";
?>
