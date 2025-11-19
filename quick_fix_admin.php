<?php
/**
 * QUICK FIX - INSERT ADMIN
 * Script ini akan memastikan admin bisa login
 * Jalankan dari browser: http://localhost/quick_fix_admin.php
 */

require_once 'includes/config.php';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Quick Fix Admin</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { background: #d4edda; border-left: 4px solid #28a745; }
    .error { background: #f8d7da; border-left: 4px solid #dc3545; }
    .info { background: #d1ecf1; border-left: 4px solid #17a2b8; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    h1 { color: #333; }
</style>";
echo "</head><body>";

echo "<h1>⚡ Quick Fix Admin Login</h1>";

// Password default
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Generate fresh hash

echo "<div class='box info'>";
echo "<strong>Password yang akan di-set:</strong> {$password}<br>";
echo "<strong>Hash:</strong> <code>{$hashed_password}</code>";
echo "</div>";

// ============================================
// 1. CEK & INSERT KE ADMIN_USERS
// ============================================
echo "<div class='box'>";
echo "<h2>1. Tabel admin_users</h2>";

$check_admin = "SELECT * FROM admin_users WHERE username = 'admin'";
$result = pg_query($conn, $check_admin);

if (pg_num_rows($result) > 0) {
    $admin = pg_fetch_assoc($result);
    echo "✅ Admin sudah ada di tabel admin_users<br>";
    echo "ID: {$admin['id']}, Username: {$admin['username']}, Email: {$admin['email']}<br><br>";
    $admin_id = $admin['id'];
} else {
    echo "❌ Admin belum ada, creating...<br>";
    
    $insert_admin = "INSERT INTO admin_users (username, nama_lengkap, email) 
                     VALUES ($1, $2, $3) RETURNING id";
    $result = pg_query_params($conn, $insert_admin, array(
        'admin',
        'Administrator',
        'admin@labse.ac.id'
    ));
    
    if ($result) {
        $admin_id = pg_fetch_result($result, 0, 0);
        echo "<div class='success'>";
        echo "✅ Admin berhasil ditambahkan ke admin_users!<br>";
        echo "ID: {$admin_id}";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "❌ Gagal insert admin: " . pg_last_error($conn);
        echo "</div>";
        exit;
    }
}
echo "</div>";

// ============================================
// 2. CEK & INSERT KE USERS
// ============================================
echo "<div class='box'>";
echo "<h2>2. Tabel users</h2>";

$check_user = "SELECT * FROM users WHERE username = 'admin' AND role = 'admin'";
$result = pg_query($conn, $check_user);

if (pg_num_rows($result) > 0) {
    $user = pg_fetch_assoc($result);
    echo "✅ Admin sudah ada di tabel users<br>";
    echo "ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}, Active: " . ($user['is_active'] === 't' ? 'YES' : 'NO') . "<br><br>";
    
    // Update jika perlu
    $update_user = "UPDATE users SET 
                    password = $1,
                    email = $2,
                    is_active = TRUE,
                    reference_id = $3
                    WHERE username = 'admin' AND role = 'admin'";
    pg_query_params($conn, $update_user, array(
        $hashed_password,
        'admin@labse.ac.id',
        $admin_id
    ));
    echo "🔄 Data user di-update<br>";
    
} else {
    echo "❌ Admin belum ada di tabel users, creating...<br>";
    
    $insert_user = "INSERT INTO users (username, email, password, role, reference_id, is_active)
                    VALUES ($1, $2, $3, 'admin', $4, TRUE)
                    ON CONFLICT (username) DO UPDATE SET
                        password = EXCLUDED.password,
                        email = EXCLUDED.email,
                        is_active = TRUE,
                        reference_id = EXCLUDED.reference_id";
    
    $result = pg_query_params($conn, $insert_user, array(
        'admin',
        'admin@labse.ac.id',
        $hashed_password,
        $admin_id
    ));
    
    if ($result) {
        echo "<div class='success'>";
        echo "✅ Admin berhasil ditambahkan ke tabel users!";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "❌ Gagal insert user: " . pg_last_error($conn);
        echo "</div>";
    }
}
echo "</div>";

// ============================================
// 3. VERIFIKASI FINAL
// ============================================
echo "<div class='box success'>";
echo "<h2>3. Verifikasi Final</h2>";

$verify = "SELECT 
            u.id,
            u.username,
            u.email,
            u.role,
            u.is_active,
            au.nama_lengkap
           FROM users u
           LEFT JOIN admin_users au ON u.reference_id = au.id
           WHERE u.username = 'admin' AND u.role = 'admin'";

$result = pg_query($conn, $verify);

if ($result && pg_num_rows($result) > 0) {
    $data = pg_fetch_assoc($result);
    
    echo "✅ <strong style='color: green; font-size: 1.2em;'>ADMIN SIAP DIGUNAKAN!</strong><br><br>";
    echo "<table style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Username</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>{$data['username']}</td></tr>";
    echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Email</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>{$data['email']}</td></tr>";
    echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Password</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>{$password}</td></tr>";
    echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Role</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>{$data['role']}</td></tr>";
    echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Active</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>" . ($data['is_active'] === 't' ? '✅ YES' : '❌ NO') . "</td></tr>";
    echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Nama Lengkap</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>{$data['nama_lengkap']}</td></tr>";
    echo "</table>";
    
    echo "<br><br>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
    echo "<h3 style='margin-top: 0;'>🔐 Silakan Login:</h3>";
    echo "<p>Buka: <a href='login.php' style='color: #007bff; font-weight: bold;'>http://localhost/login.php</a></p>";
    echo "<ul style='font-size: 1.1em; line-height: 1.8;'>";
    echo "<li><strong>Username:</strong> admin</li>";
    echo "<li><strong>atau Email:</strong> admin@labse.ac.id</li>";
    echo "<li><strong>Password:</strong> {$password}</li>";
    echo "</ul>";
    echo "</div>";
    
} else {
    echo "<div class='error'>";
    echo "❌ Masih ada masalah. Coba refresh halaman ini atau cek database manual.";
    echo "</div>";
}

echo "</div>";

pg_close($conn);

echo "</body></html>";
?>
