<?php
/**
 * GENERATE PASSWORD HASH
 * Script untuk generate hash password yang benar
 */

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Generate Password Hash</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { background: #d4edda; border-left: 4px solid #28a745; }
    code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    h1 { color: #333; }
    input, button { padding: 10px; margin: 5px; font-size: 14px; }
    button { background: #4A90E2; color: white; border: none; cursor: pointer; border-radius: 3px; }
    button:hover { background: #357abd; }
</style>";
echo "</head><body>";

echo "<h1>🔐 Generate Password Hash</h1>";

// Password yang akan di-hash
$passwords = [
    'admin123',
    'password123',
    'labse2024'
];

echo "<div class='box'>";
echo "<h2>Hash untuk Password Umum:</h2>";

foreach ($passwords as $pwd) {
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    echo "<div style='margin: 15px 0; padding: 10px; background: #f8f9fa; border-radius: 5px;'>";
    echo "<strong>Password:</strong> <code>{$pwd}</code><br>";
    echo "<strong>Hash:</strong><br>";
    echo "<code style='word-break: break-all; display: block; margin-top: 5px;'>{$hash}</code><br><br>";
    
    // Test verify
    if (password_verify($pwd, $hash)) {
        echo "<span style='color: green;'>✅ Verified: Hash ini cocok untuk password '{$pwd}'</span>";
    }
    echo "</div>";
}

echo "</div>";

// Form untuk custom password
echo "<div class='box'>";
echo "<h2>Generate Hash untuk Password Custom:</h2>";

if (isset($_POST['custom_password']) && !empty($_POST['custom_password'])) {
    $custom_pwd = $_POST['custom_password'];
    $custom_hash = password_hash($custom_pwd, PASSWORD_DEFAULT);
    
    echo "<div class='success'>";
    echo "<h3>✅ Hash Berhasil Dibuat!</h3>";
    echo "<strong>Password:</strong> <code>{$custom_pwd}</code><br><br>";
    echo "<strong>Hash:</strong><br>";
    echo "<textarea readonly style='width: 100%; height: 60px; font-family: monospace; padding: 10px;'>{$custom_hash}</textarea><br><br>";
    
    // SQL Query
    echo "<strong>SQL Query untuk Update:</strong><br>";
    echo "<pre style='background: #263238; color: #aed581; padding: 15px;'>";
    echo "-- Update admin_users\n";
    echo "UPDATE admin_users \n";
    echo "SET password = '{$custom_hash}'\n";
    echo "WHERE username = 'admin';\n\n";
    echo "-- Update users\n";
    echo "UPDATE users \n";
    echo "SET password = '{$custom_hash}'\n";
    echo "WHERE username = 'admin' AND role = 'admin';";
    echo "</pre>";
    echo "</div>";
}

echo "<form method='POST'>";
echo "<input type='text' name='custom_password' placeholder='Masukkan password...' required style='width: 300px;'>";
echo "<button type='submit'>Generate Hash</button>";
echo "</form>";

echo "</div>";

// Generate hash khusus untuk admin123
echo "<div class='box success'>";
echo "<h2>🎯 Quick Fix untuk Password 'admin123':</h2>";

$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);

echo "<p>Copy query ini dan jalankan di pgAdmin:</p>";
echo "<pre style='background: #263238; color: #aed581; padding: 15px;'>";
echo "-- Update password admin menjadi 'admin123'\n\n";
echo "-- Step 1: Update di admin_users\n";
echo "UPDATE admin_users \n";
echo "SET password = '{$admin_hash}'\n";
echo "WHERE username = 'admin';\n\n";

echo "-- Step 2: Update di users\n";
echo "UPDATE users \n";
echo "SET password = '{$admin_hash}'\n";
echo "WHERE username = 'admin' AND role = 'admin';\n\n";

echo "-- Step 3: Verifikasi\n";
echo "SELECT username, email, role FROM users WHERE username = 'admin';";
echo "</pre>";

echo "<p><strong>Password:</strong> <code>admin123</code></p>";
echo "<p><strong>Hash baru:</strong></p>";
echo "<textarea readonly style='width: 100%; height: 60px; font-family: monospace; padding: 10px;'>{$admin_hash}</textarea>";

// Test the hash
if (password_verify('admin123', $admin_hash)) {
    echo "<p style='color: green; font-weight: bold;'>✅ Hash ini VALID untuk password 'admin123'</p>";
}

echo "</div>";

echo "</body></html>";
?>
