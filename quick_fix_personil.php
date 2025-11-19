<?php
/**
 * QUICK FIX - PERSONIL/MEMBER LOGIN
 * Script untuk fix login personil/member
 * Jalankan dari browser: http://localhost/labse_web/quick_fix_personil.php
 */

require_once 'includes/config.php';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Quick Fix Personil Login</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { background: #d4edda; border-left: 4px solid #28a745; }
    .error { background: #f8d7da; border-left: 4px solid #dc3545; }
    .info { background: #d1ecf1; border-left: 4px solid #17a2b8; }
    .warning { background: #fff3cd; border-left: 4px solid #ffc107; }
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #4A90E2; color: white; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    h1 { color: #333; }
</style>";
echo "</head><body>";

echo "<h1>⚡ Quick Fix Personil/Member Login</h1>";

// Password default untuk personil
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "<div class='box info'>";
echo "<strong>Password yang akan di-set untuk semua personil:</strong> {$password}<br>";
echo "<strong>Hash:</strong> <code style='font-size: 11px;'>" . substr($hashed_password, 0, 50) . "...</code>";
echo "</div>";

// ============================================
// 1. CEK SEMUA PERSONIL
// ============================================
echo "<div class='box'>";
echo "<h2>1. Data Personil di Database</h2>";

$personil_query = "SELECT id, nama, email, jabatan, is_member
                   FROM personil 
                   ORDER BY id";
$personil_result = pg_query($conn, $personil_query);

if (pg_num_rows($personil_result) > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nama</th><th>Email</th><th>Jabatan</th><th>Is Member</th></tr>";
    
    $personil_list = [];
    while ($p = pg_fetch_assoc($personil_result)) {
        $personil_list[] = $p;
        $is_member_badge = ($p['is_member'] === 't') ? '✅' : '❌';
        
        echo "<tr>";
        echo "<td>{$p['id']}</td>";
        echo "<td><strong>{$p['nama']}</strong></td>";
        echo "<td>{$p['email']}</td>";
        echo "<td>{$p['jabatan']}</td>";
        echo "<td>{$is_member_badge}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>❌ Tidak ada data personil!</div>";
    exit;
}
echo "</div>";

// ============================================
// 2. UPDATE SEMUA PERSONIL
// ============================================
echo "<div class='box'>";
echo "<h2>2. Update Personil (Set as Member & Password)</h2>";

$updated_count = 0;

foreach ($personil_list as $personil) {
    $update_query = "UPDATE personil 
                     SET is_member = TRUE
                     WHERE id = $1";
    
    $result = pg_query_params($conn, $update_query, array($personil['id']));
    
    if ($result) {
        $updated_count++;
        echo "✅ {$personil['nama']} - Updated<br>";
    } else {
        echo "❌ {$personil['nama']} - Failed<br>";
    }
}

echo "<div class='success' style='margin-top: 15px;'>";
echo "<strong>Total personil di-update: {$updated_count}</strong>";
echo "</div>";

echo "</div>";

// ============================================
// 3. SYNC KE TABEL USERS
// ============================================
echo "<div class='box'>";
echo "<h2>3. Sinkronisasi ke Tabel Users</h2>";

$synced_count = 0;
$credentials = [];

foreach ($personil_list as $personil) {
    // Generate username dari email
    $email = $personil['email'];
    $username = strstr($email, '@', true); // Ambil bagian sebelum @
    
    // Cek apakah sudah ada di users
    $check_query = "SELECT id FROM users WHERE reference_id = $1 AND role = 'personil'";
    $check_result = pg_query_params($conn, $check_query, array($personil['id']));
    
    if (pg_num_rows($check_result) > 0) {
        // Update existing
        $update_user_query = "UPDATE users 
                             SET username = $1, 
                                 email = $2, 
                                 password = $3,
                                 is_active = TRUE
                             WHERE reference_id = $4 AND role = 'personil'";
        
        $result = pg_query_params($conn, $update_user_query, array(
            $username,
            $email,
            $hashed_password,
            $personil['id']
        ));
        
        if ($result) {
            echo "🔄 {$personil['nama']} - Updated in users table<br>";
            $synced_count++;
        }
    } else {
        // Insert new
        $insert_user_query = "INSERT INTO users (username, email, password, role, reference_id, is_active)
                             VALUES ($1, $2, $3, 'personil', $4, TRUE)";
        
        $result = pg_query_params($conn, $insert_user_query, array(
            $username,
            $email,
            $hashed_password,
            $personil['id']
        ));
        
        if ($result) {
            echo "✅ {$personil['nama']} - Added to users table<br>";
            $synced_count++;
        } else {
            echo "❌ {$personil['nama']} - Failed to add: " . pg_last_error($conn) . "<br>";
        }
    }
    
    // Simpan credentials
    $credentials[] = [
        'nama' => $personil['nama'],
        'username' => $username,
        'email' => $email
    ];
}

echo "<div class='success' style='margin-top: 15px;'>";
echo "<strong>Total personil di-sync ke users: {$synced_count}</strong>";
echo "</div>";

echo "</div>";

// ============================================
// 4. VERIFIKASI FINAL
// ============================================
echo "<div class='box success'>";
echo "<h2>4. ✅ Verifikasi Final - Personil Siap Login!</h2>";

$verify_query = "SELECT 
                    u.id as user_id,
                    u.username,
                    u.email,
                    u.role,
                    u.is_active,
                    p.nama,
                    p.jabatan,
                    p.is_member
                 FROM users u
                 JOIN personil p ON u.reference_id = p.id
                 WHERE u.role = 'personil'
                 ORDER BY p.id";

$verify_result = pg_query($conn, $verify_query);

if (pg_num_rows($verify_result) > 0) {
    echo "<p style='font-size: 1.1em; color: green; font-weight: bold;'>🎉 Semua personil siap login!</p>";
    
    echo "<table>";
    echo "<tr><th>Nama</th><th>Username</th><th>Email</th><th>Jabatan</th><th>Active</th></tr>";
    
    while ($data = pg_fetch_assoc($verify_result)) {
        $active_badge = ($data['is_active'] === 't') ? '✅ YES' : '❌ NO';
        echo "<tr>";
        echo "<td><strong>{$data['nama']}</strong></td>";
        echo "<td>{$data['username']}</td>";
        echo "<td>{$data['email']}</td>";
        echo "<td>{$data['jabatan']}</td>";
        echo "<td>{$active_badge}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} else {
    echo "<div class='error'>❌ Tidak ada personil di tabel users!</div>";
}

echo "</div>";

// ============================================
// 5. CREDENTIALS UNTUK LOGIN
// ============================================
echo "<div class='box warning'>";
echo "<h2>🔐 Login Credentials</h2>";

echo "<p><strong>Password untuk semua personil:</strong> <code style='background: #fff; padding: 5px 10px; border-radius: 3px; font-size: 1.2em;'>{$password}</code></p>";

echo "<p>Silakan login dengan salah satu dari credentials berikut:</p>";

echo "<table>";
echo "<tr><th>Nama</th><th>Username</th><th>Email</th><th>Password</th></tr>";

foreach ($credentials as $cred) {
    echo "<tr>";
    echo "<td><strong>{$cred['nama']}</strong></td>";
    echo "<td><code>{$cred['username']}</code></td>";
    echo "<td><code>{$cred['email']}</code></td>";
    echo "<td><code>{$password}</code></td>";
    echo "</tr>";
}
echo "</table>";

echo "<br>";
echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 5px; border-left: 4px solid #2196F3;'>";
echo "<h3 style='margin-top: 0;'>📝 Cara Login:</h3>";
echo "<ol style='line-height: 2;'>";
echo "<li>Buka: <a href='login.php' style='color: #007bff; font-weight: bold;'>http://localhost/labse_web/login.php</a></li>";
echo "<li>Masukkan <strong>Username</strong> ATAU <strong>Email</strong> dari tabel di atas</li>";
echo "<li>Masukkan password: <strong>{$password}</strong></li>";
echo "<li>Klik Login</li>";
echo "<li>Otomatis masuk ke Member Dashboard (/member/index.php)</li>";
echo "</ol>";
echo "</div>";

echo "</div>";

pg_close($conn);

echo "</body></html>";
?>
