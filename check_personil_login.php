<?php
/**
 * CHECK PERSONIL LOGIN
 * Script untuk mengecek kenapa personil tidak bisa login
 */

require_once 'includes/config.php';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Check Personil Login</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { border-left: 4px solid #28a745; }
    .error { border-left: 4px solid #dc3545; }
    .warning { border-left: 4px solid #ffc107; }
    .info { border-left: 4px solid #17a2b8; }
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #4A90E2; color: white; }
    h1 { color: #333; }
    .badge-yes { background: #28a745; color: white; padding: 3px 8px; border-radius: 3px; }
    .badge-no { background: #dc3545; color: white; padding: 3px 8px; border-radius: 3px; }
</style>";
echo "</head><body>";

echo "<h1>🔍 Check Personil Login Issue</h1>";

// ============================================
// 1. CEK TABEL PERSONIL
// ============================================
echo "<div class='box'>";
echo "<h2>1. Data Personil</h2>";

$personil_query = "SELECT 
                    id, 
                    nama, 
                    email, 
                    jabatan,
                    is_member
                   FROM personil 
                   ORDER BY id";

$result = pg_query($conn, $personil_query);
$total_personil = pg_num_rows($result);

echo "<p><strong>Total Personil:</strong> {$total_personil}</p>";

if ($total_personil > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nama</th><th>Email</th><th>Is Member</th></tr>";
    
    while ($p = pg_fetch_assoc($result)) {
        $is_member = ($p['is_member'] === 't') ? 
            "<span class='badge-yes'>✅ YES</span>" : 
            "<span class='badge-no'>❌ NO</span>";
        
        echo "<tr>";
        echo "<td>{$p['id']}</td>";
        echo "<td><strong>{$p['nama']}</strong></td>";
        echo "<td>{$p['email']}</td>";
        echo "<td>{$is_member}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "</div>";

// ============================================
// 2. CEK TABEL USERS (PERSONIL)
// ============================================
echo "<div class='box'>";
echo "<h2>2. Personil di Tabel Users</h2>";

$users_query = "SELECT 
                    u.id,
                    u.username,
                    u.email,
                    u.role,
                    u.reference_id,
                    u.is_active,
                    p.nama
                FROM users u
                LEFT JOIN personil p ON u.reference_id = p.id
                WHERE u.role = 'personil'
                ORDER BY u.id";

$users_result = pg_query($conn, $users_query);
$total_users = pg_num_rows($users_result);

echo "<p><strong>Total Personil di Users:</strong> {$total_users}</p>";

if ($total_users > 0) {
    echo "<table>";
    echo "<tr><th>User ID</th><th>Username</th><th>Email</th><th>Ref ID</th><th>Nama (from personil)</th><th>Active</th></tr>";
    
    while ($u = pg_fetch_assoc($users_result)) {
        $active = ($u['is_active'] === 't') ? 
            "<span class='badge-yes'>✅ YES</span>" : 
            "<span class='badge-no'>❌ NO</span>";
        
        $nama = $u['nama'] ?: "<span style='color: red;'>❌ NOT FOUND</span>";
        
        echo "<tr>";
        echo "<td>{$u['id']}</td>";
        echo "<td><strong>{$u['username']}</strong></td>";
        echo "<td>{$u['email']}</td>";
        echo "<td>{$u['reference_id']}</td>";
        echo "<td>{$nama}</td>";
        echo "<td>{$active}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='box error'>";
    echo "<h3>❌ MASALAH DITEMUKAN!</h3>";
    echo "<p>Tidak ada personil di tabel <code>users</code>!</p>";
    echo "<p><strong>Solusi:</strong> Jalankan <a href='quick_fix_personil.php'>quick_fix_personil.php</a></p>";
    echo "</div>";
}

echo "</div>";

// ============================================
// 3. CROSS CHECK
// ============================================
echo "<div class='box'>";
echo "<h2>3. Cross Check Personil vs Users</h2>";

$cross_query = "SELECT 
                    p.id as personil_id,
                    p.nama,
                    p.email,
                    p.is_member,
                    u.id as user_id,
                    u.username as user_username,
                    u.is_active as user_active
                FROM personil p
                LEFT JOIN users u ON p.id = u.reference_id AND u.role = 'personil'
                ORDER BY p.id";

$cross_result = pg_query($conn, $cross_query);

echo "<table>";
echo "<tr><th>Personil ID</th><th>Nama</th><th>Email</th><th>Is Member</th><th>In Users?</th><th>Username</th><th>Active</th></tr>";

$issues = [];

while ($row = pg_fetch_assoc($cross_result)) {
    $is_member = ($row['is_member'] === 't') ? '✅' : '❌';
    $in_users = $row['user_id'] ? '✅ YES' : '❌ NO';
    $username = $row['user_username'] ?: '-';
    $active = $row['user_active'] === 't' ? '✅' : ($row['user_active'] === 'f' ? '❌' : '-');
    
    // Detect issues
    $has_issue = false;
    $issue_reason = [];
    
    if ($row['is_member'] !== 't') {
        $has_issue = true;
        $issue_reason[] = "is_member = FALSE";
    }
    
    if (!$row['user_id']) {
        $has_issue = true;
        $issue_reason[] = "Not in users table";
    }
    
    if ($row['user_active'] === 'f') {
        $has_issue = true;
        $issue_reason[] = "User not active";
    }
    
    if ($has_issue) {
        $issues[] = [
            'nama' => $row['nama'],
            'email' => $row['email'],
            'reasons' => $issue_reason
        ];
    }
    
    $row_style = $has_issue ? "background: #fff3cd;" : "";
    
    echo "<tr style='{$row_style}'>";
    echo "<td>{$row['personil_id']}</td>";
    echo "<td><strong>{$row['nama']}</strong></td>";
    echo "<td>{$row['email']}</td>";
    echo "<td>{$is_member}</td>";
    echo "<td>{$in_users}</td>";
    echo "<td>{$username}</td>";
    echo "<td>{$active}</td>";
    echo "</tr>";
}

echo "</table>";

echo "</div>";

// ============================================
// 4. SUMMARY & SOLUTION
// ============================================
if (count($issues) > 0) {
    echo "<div class='box error'>";
    echo "<h2>❌ Issues Found: " . count($issues) . " personil(s)</h2>";
    
    foreach ($issues as $issue) {
        echo "<div style='margin: 10px 0; padding: 10px; background: #f8d7da; border-radius: 5px;'>";
        echo "<strong>{$issue['nama']}</strong> ({$issue['email']})<br>";
        echo "Problems: ";
        echo "<ul style='margin: 5px 0;'>";
        foreach ($issue['reasons'] as $reason) {
            echo "<li>{$reason}</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
    
    echo "<div class='box warning' style='margin-top: 20px;'>";
    echo "<h3>🔧 SOLUSI:</h3>";
    echo "<p style='font-size: 1.1em;'>Jalankan script berikut untuk fix semua masalah:</p>";
    echo "<a href='quick_fix_personil.php' style='display: inline-block; background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 1.1em;'>";
    echo "🚀 RUN QUICK FIX PERSONIL";
    echo "</a>";
    echo "</div>";
    
} else {
    echo "<div class='box success'>";
    echo "<h2>✅ All Good!</h2>";
    echo "<p>Semua personil sudah ter-konfigurasi dengan benar.</p>";
    echo "<p>Jika masih tidak bisa login, coba:</p>";
    echo "<ol>";
    echo "<li>Clear browser cache atau buka incognito</li>";
    echo "<li>Pastikan menggunakan password yang benar</li>";
    echo "<li>Test dengan username DAN email</li>";
    echo "</ol>";
    echo "</div>";
}

pg_close($conn);

echo "</body></html>";
?>
