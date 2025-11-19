<?php
/**
 * DEBUG LOGIN SCRIPT
 * Script ini untuk debug masalah login
 * Jalankan dari browser: http://localhost/debug_login.php
 */

require_once 'includes/config.php';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Debug Login</title>";
echo "<style>
    body { font-family: monospace; padding: 20px; background: #f5f5f5; }
    .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #4A90E2; }
    .success { border-left-color: #28a745; }
    .error { border-left-color: #dc3545; }
    .warning { border-left-color: #ffc107; }
    h2 { color: #333; margin-top: 0; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #4A90E2; color: white; }
    .test-form { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; }
    input, button { padding: 8px; margin: 5px; }
    button { background: #4A90E2; color: white; border: none; cursor: pointer; border-radius: 3px; }
    button:hover { background: #357abd; }
</style>";
echo "</head><body>";

echo "<h1>🔍 Debug Login System</h1>";

// ============================================
// 1. CEK KONEKSI DATABASE
// ============================================
echo "<div class='box'>";
echo "<h2>1. Koneksi Database</h2>";
if ($conn) {
    echo "✅ <strong style='color: green;'>Koneksi berhasil!</strong>";
} else {
    echo "❌ <strong style='color: red;'>Koneksi gagal!</strong>";
    echo "<pre>" . pg_last_error() . "</pre>";
}
echo "</div>";

// ============================================
// 2. CEK TABEL USERS
// ============================================
echo "<div class='box'>";
echo "<h2>2. Tabel Users</h2>";

$check_table = "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'users')";
$result = pg_query($conn, $check_table);
$exists = pg_fetch_result($result, 0, 0);

if ($exists === 't') {
    echo "✅ <strong style='color: green;'>Tabel 'users' ada</strong><br><br>";
    
    // Cek jumlah data
    $count_query = "SELECT COUNT(*) as total FROM users";
    $count_result = pg_query($conn, $count_query);
    $total = pg_fetch_result($count_result, 0, 0);
    
    echo "Total users: <strong>{$total}</strong><br><br>";
    
    if ($total > 0) {
        // Tampilkan semua users
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Is Active</th><th>Password Hash (first 30 chars)</th></tr>";
        
        $users_query = "SELECT id, username, email, role, is_active, password FROM users ORDER BY role, id";
        $users_result = pg_query($conn, $users_query);
        
        while ($user = pg_fetch_assoc($users_result)) {
            $active_badge = $user['is_active'] === 't' ? '✅' : '❌';
            $password_preview = substr($user['password'], 0, 30) . '...';
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td><strong>{$user['username']}</strong></td>";
            echo "<td>{$user['email']}</td>";
            echo "<td><span style='background: #4A90E2; color: white; padding: 2px 6px; border-radius: 3px;'>{$user['role']}</span></td>";
            echo "<td>{$active_badge}</td>";
            echo "<td><code>{$password_preview}</code></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='box error'>";
        echo "⚠️ <strong>Tabel users kosong!</strong><br><br>";
        echo "Jalankan query berikut:<br>";
        echo "<pre>psql -U postgres -d labse -f database/insert_sample_users.sql</pre>";
        echo "Atau buka file <code>database/insert_sample_users.sql</code> di pgAdmin dan execute.";
        echo "</div>";
    }
} else {
    echo "<div class='box error'>";
    echo "❌ <strong style='color: red;'>Tabel 'users' tidak ada!</strong><br><br>";
    echo "Jalankan query berikut:<br>";
    echo "<pre>psql -U postgres -d labse -f database/create_users_table.sql</pre>";
    echo "</div>";
}
echo "</div>";

// ============================================
// 3. TEST PASSWORD VERIFY
// ============================================
echo "<div class='box'>";
echo "<h2>3. Test Password Verification</h2>";

$test_password = 'admin123';
$correct_hash = password_hash($test_password, PASSWORD_DEFAULT); // Generate fresh hash

echo "Password yang di-test: <strong>{$test_password}</strong><br>";
echo "Hash yang baru di-generate: <code>{$correct_hash}</code><br><br>";

if (password_verify($test_password, $correct_hash)) {
    echo "✅ <strong style='color: green;'>Password verification BERHASIL!</strong><br>";
    echo "Hash yang baru di-generate ini valid untuk password 'admin123'<br><br>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 10px;'>";
    echo "<strong>⚠️ Hash lama Anda mungkin berbeda!</strong><br>";
    echo "Jalankan <a href='quick_fix_admin.php' style='color: #007bff; font-weight: bold;'>quick_fix_admin.php</a> untuk update password dengan hash yang benar.";
    echo "</div>";
} else {
    echo "❌ <strong style='color: red;'>Password verification GAGAL!</strong><br>";
    echo "Ada masalah dengan password hash.";
}

echo "</div>";

// ============================================
// 4. CEK USER SPESIFIK
// ============================================
echo "<div class='box'>";
echo "<h2>4. Cek User Spesifik</h2>";

if (isset($_GET['check_user']) && !empty($_GET['check_user'])) {
    $check_username = $_GET['check_user'];
    
    echo "Mencari user: <strong>{$check_username}</strong><br><br>";
    
    $query = "SELECT * FROM users WHERE (username = $1 OR email = $1)";
    $result = pg_query_params($conn, $query, array($check_username));
    
    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        
        echo "✅ <strong style='color: green;'>User ditemukan!</strong><br><br>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        echo "<tr><td>ID</td><td>{$user['id']}</td></tr>";
        echo "<tr><td>Username</td><td><strong>{$user['username']}</strong></td></tr>";
        echo "<tr><td>Email</td><td>{$user['email']}</td></tr>";
        echo "<tr><td>Role</td><td>{$user['role']}</td></tr>";
        echo "<tr><td>Reference ID</td><td>{$user['reference_id']}</td></tr>";
        echo "<tr><td>Is Active</td><td>" . ($user['is_active'] === 't' ? '✅ TRUE' : '❌ FALSE') . "</td></tr>";
        echo "<tr><td>Password Hash</td><td><code>{$user['password']}</code></td></tr>";
        echo "</table>";
        
        // Test password
        if (isset($_GET['test_password']) && !empty($_GET['test_password'])) {
            $test_pwd = $_GET['test_password'];
            echo "<br><h3>Test Password: <code>{$test_pwd}</code></h3>";
            
            if (password_verify($test_pwd, $user['password'])) {
                echo "✅ <strong style='color: green;'>PASSWORD COCOK!</strong><br>";
                echo "User ini bisa login dengan password: <strong>{$test_pwd}</strong>";
            } else {
                echo "❌ <strong style='color: red;'>PASSWORD TIDAK COCOK!</strong><br>";
                echo "Password yang Anda masukkan salah untuk user ini.";
            }
        }
        
    } else {
        echo "<div class='box error'>";
        echo "❌ <strong style='color: red;'>User TIDAK ditemukan!</strong><br><br>";
        echo "Username/Email '<strong>{$check_username}</strong>' tidak ada di database.";
        echo "</div>";
    }
} else {
    echo "<p>Masukkan username/email untuk mengecek:</p>";
}

echo "<div class='test-form'>";
echo "<form method='GET'>";
echo "Username/Email: <input type='text' name='check_user' placeholder='admin atau admin@labse.ac.id' value='" . (isset($_GET['check_user']) ? htmlspecialchars($_GET['check_user']) : '') . "'><br>";
echo "Password (untuk test): <input type='text' name='test_password' placeholder='admin123' value='" . (isset($_GET['test_password']) ? htmlspecialchars($_GET['test_password']) : '') . "'><br>";
echo "<button type='submit'>🔍 Cek User & Test Password</button>";
echo "</form>";
echo "</div>";

echo "</div>";

// ============================================
// 5. CEK ADMIN USERS & PERSONIL
// ============================================
echo "<div class='box'>";
echo "<h2>5. Tabel Admin Users & Personil</h2>";

// Cek admin_users
$admin_count = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM admin_users"), 0, 0);
echo "<h3>Admin Users: {$admin_count} records</h3>";

if ($admin_count > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Nama Lengkap</th></tr>";
    
    $admin_query = "SELECT id, username, email, nama_lengkap FROM admin_users ORDER BY id";
    $admin_result = pg_query($conn, $admin_query);
    
    while ($admin = pg_fetch_assoc($admin_result)) {
        echo "<tr>";
        echo "<td>{$admin['id']}</td>";
        echo "<td><strong>{$admin['username']}</strong></td>";
        echo "<td>{$admin['email']}</td>";
        echo "<td>{$admin['nama_lengkap']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<br>";

// Cek personil
$personil_count = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM personil WHERE is_member = TRUE"), 0, 0);
echo "<h3>Personil (Members): {$personil_count} records</h3>";

if ($personil_count > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nama</th><th>Email</th><th>Jabatan</th><th>Is Member</th></tr>";
    
    $personil_query = "SELECT id, nama, email, jabatan, is_member FROM personil WHERE is_member = TRUE ORDER BY id";
    $personil_result = pg_query($conn, $personil_query);
    
    while ($personil = pg_fetch_assoc($personil_result)) {
        echo "<tr>";
        echo "<td>{$personil['id']}</td>";
        echo "<td><strong>{$personil['nama']}</strong></td>";
        echo "<td>{$personil['email']}</td>";
        echo "<td>{$personil['jabatan']}</td>";
        echo "<td>✅</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='box warning'>";
    echo "⚠️ Tidak ada personil yang di-set sebagai member.<br>";
    echo "Run SQL: <code>UPDATE personil SET is_member = TRUE, password = '...' WHERE id IN (...)</code>";
    echo "</div>";
}

echo "</div>";

// ============================================
// 6. QUICK FIX
// ============================================
echo "<div class='box warning'>";
echo "<h2>6. Quick Fix</h2>";
echo "<p>Jika login masih gagal, jalankan query ini di pgAdmin:</p>";
echo "<pre>";
echo "-- 1. Pastikan tabel users ada\n";
echo "SELECT * FROM users;\n\n";
echo "-- 2. Jika tabel kosong, insert sample users\n";
echo "-- Buka file: database/insert_sample_users.sql dan execute\n\n";
echo "-- 3. Atau insert manual admin:\n";
echo "INSERT INTO users (username, email, password, role, reference_id, is_active)\n";
echo "VALUES (\n";
echo "    'admin',\n";
echo "    'admin@labse.ac.id',\n";
echo "    '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',\n";
echo "    'admin',\n";
echo "    1,\n";
echo "    TRUE\n";
echo ") ON CONFLICT (username) DO NOTHING;\n\n";
echo "-- 4. Test login dengan:\n";
echo "-- Username: admin\n";
echo "-- Password: admin123\n";
echo "</pre>";
echo "</div>";

// ============================================
// FOOTER
// ============================================
echo "<div class='box'>";
echo "<h3>📝 Catatan:</h3>";
echo "<ul>";
echo "<li>Password default untuk semua user: <strong>admin123</strong></li>";
echo "<li>Hash password: <code>\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi</code></li>";
echo "<li>Pastikan field <strong>is_active = TRUE</strong> di tabel users</li>";
echo "<li>Username dan email case-sensitive</li>";
echo "</ul>";
echo "</div>";

pg_close($conn);

echo "</body></html>";
?>
