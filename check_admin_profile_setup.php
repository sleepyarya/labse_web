<?php
/**
 * CHECK ADMIN PROFILE SETUP
 * Script untuk mengecek dan setup kolom yang diperlukan untuk fitur edit profil admin
 */

require_once 'includes/config.php';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Check Admin Profile Setup</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { border-left: 4px solid #28a745; }
    .error { border-left: 4px solid #dc3545; }
    .warning { border-left: 4px solid #ffc107; }
    .info { border-left: 4px solid #17a2b8; }
    h1 { color: #333; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f8f9fa; }
    .btn { padding: 8px 16px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-primary { background: #007bff; color: white; }
    .btn-success { background: #28a745; color: white; }
    .btn-warning { background: #ffc107; color: black; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
</style>";
echo "</head><body>";

echo "<h1>🔍 Check Admin Profile Setup</h1>";

// Check if admin_users table exists
echo "<div class='box info'>";
echo "<h3>📋 Table Information</h3>";

$table_check = "SELECT EXISTS (
    SELECT FROM information_schema.tables 
    WHERE table_schema = 'public' 
    AND table_name = 'admin_users'
)";
$result = pg_query($conn, $table_check);
$table_exists = pg_fetch_result($result, 0, 0) === 't';

if ($table_exists) {
    echo "<p>✅ <strong>Table 'admin_users' exists</strong></p>";
} else {
    echo "<p>❌ <strong>Table 'admin_users' does not exist!</strong></p>";
    echo "</div>";
    echo "<p><a href='admin/views/edit_profile.php' class='btn btn-primary'>← Back to Admin</a></p>";
    echo "</body></html>";
    exit;
}
echo "</div>";

// Get table structure
echo "<div class='box info'>";
echo "<h3>🏗️ Current Table Structure</h3>";

$structure_query = "SELECT 
    column_name, 
    data_type, 
    is_nullable, 
    column_default
FROM information_schema.columns 
WHERE table_name = 'admin_users' 
ORDER BY ordinal_position";

$structure_result = pg_query($conn, $structure_query);

echo "<table>";
echo "<tr><th>Column Name</th><th>Data Type</th><th>Nullable</th><th>Default</th></tr>";

$required_columns = ['foto', 'updated_at'];
$existing_columns = [];

while ($row = pg_fetch_assoc($structure_result)) {
    $is_required_col = in_array($row['column_name'], $required_columns);
    if ($is_required_col) {
        $existing_columns[] = $row['column_name'];
        echo "<tr style='background: #d4edda;'>";
    } else {
        echo "<tr>";
    }
    
    echo "<td>" . htmlspecialchars($row['column_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['data_type']) . "</td>";
    echo "<td>" . htmlspecialchars($row['is_nullable']) . "</td>";
    echo "<td>" . htmlspecialchars($row['column_default'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// Check required columns
echo "<div class='box " . (count($existing_columns) === count($required_columns) ? 'success' : 'warning') . "'>";
echo "<h3>🔐 Profile Feature Columns</h3>";

$missing_columns = array_diff($required_columns, $existing_columns);

if (empty($missing_columns)) {
    echo "<p>✅ <strong>All required columns exist!</strong></p>";
    echo "<ul>";
    foreach ($required_columns as $col) {
        echo "<li>✅ <strong>{$col}</strong></li>";
    }
    echo "</ul>";
} else {
    echo "<p>⚠️ <strong>Missing required columns:</strong></p>";
    echo "<ul>";
    foreach ($required_columns as $col) {
        if (in_array($col, $existing_columns)) {
            echo "<li>✅ <strong>{$col}</strong></li>";
        } else {
            echo "<li>❌ <strong>{$col}</strong></li>";
        }
    }
    echo "</ul>";
    
    echo "<p><strong>Action needed:</strong> Add missing columns to enable profile features.</p>";
    
    // Show SQL to add missing columns
    if (!empty($missing_columns)) {
        echo "<h4>📝 SQL to Add Missing Columns:</h4>";
        echo "<pre>";
        foreach ($missing_columns as $col) {
            if ($col === 'foto') {
                echo "ALTER TABLE admin_users ADD COLUMN foto VARCHAR(255);\n";
            } elseif ($col === 'updated_at') {
                echo "ALTER TABLE admin_users ADD COLUMN updated_at TIMESTAMP;\n";
            }
        }
        echo "</pre>";
        
        // Auto-fix button
        echo "<form method='POST' style='display: inline;'>";
        echo "<button type='submit' name='auto_fix' class='btn btn-success'>🔧 Auto-Fix Missing Columns</button>";
        echo "</form>";
    }
}
echo "</div>";

// Handle auto-fix
if (isset($_POST['auto_fix'])) {
    echo "<div class='box info'>";
    echo "<h3>🔧 Auto-Fix Results</h3>";
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($missing_columns as $col) {
        if ($col === 'foto') {
            $sql = "ALTER TABLE admin_users ADD COLUMN foto VARCHAR(255)";
            $result = pg_query($conn, $sql);
            if ($result) {
                echo "<p>✅ Added column: <strong>foto</strong></p>";
                $success_count++;
            } else {
                echo "<p>❌ Failed to add column: <strong>foto</strong> - " . pg_last_error($conn) . "</p>";
                $error_count++;
            }
        } elseif ($col === 'updated_at') {
            $sql = "ALTER TABLE admin_users ADD COLUMN updated_at TIMESTAMP";
            $result = pg_query($conn, $sql);
            if ($result) {
                echo "<p>✅ Added column: <strong>updated_at</strong></p>";
                $success_count++;
            } else {
                echo "<p>❌ Failed to add column: <strong>updated_at</strong> - " . pg_last_error($conn) . "</p>";
                $error_count++;
            }
        }
    }
    
    if ($success_count > 0) {
        echo "<p><strong>✅ Successfully added {$success_count} column(s)!</strong></p>";
        echo "<p><a href='" . $_SERVER['PHP_SELF'] . "' class='btn btn-primary'>🔄 Refresh Page</a></p>";
    }
    
    if ($error_count > 0) {
        echo "<p><strong>❌ Failed to add {$error_count} column(s).</strong></p>";
    }
    
    echo "</div>";
}

// Check sample data
echo "<div class='box info'>";
echo "<h3>📊 Sample Admin Data</h3>";

$sample_query = "SELECT id, nama_lengkap, username, email";
if (in_array('foto', $existing_columns)) {
    $sample_query .= ", foto";
}
if (in_array('updated_at', $existing_columns)) {
    $sample_query .= ", updated_at";
}
$sample_query .= " FROM admin_users ORDER BY id LIMIT 5";

$sample_result = pg_query($conn, $sample_query);

if (pg_num_rows($sample_result) > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nama</th><th>Username</th><th>Email</th>";
    if (in_array('foto', $existing_columns)) echo "<th>Foto</th>";
    if (in_array('updated_at', $existing_columns)) echo "<th>Updated At</th>";
    echo "</tr>";
    
    while ($row = pg_fetch_assoc($sample_result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        if (in_array('foto', $existing_columns)) {
            echo "<td>" . htmlspecialchars($row['foto'] ?? 'NULL') . "</td>";
        }
        if (in_array('updated_at', $existing_columns)) {
            echo "<td>" . htmlspecialchars($row['updated_at'] ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>📭 No admin data found.</p>";
}
echo "</div>";

// Check uploads directory
echo "<div class='box info'>";
echo "<h3>📁 Uploads Directory</h3>";

$uploads_dir = __DIR__ . '/uploads/admin/';
if (is_dir($uploads_dir)) {
    echo "<p>✅ <strong>Uploads directory exists:</strong> <code>{$uploads_dir}</code></p>";
    if (is_writable($uploads_dir)) {
        echo "<p>✅ <strong>Directory is writable</strong></p>";
    } else {
        echo "<p>⚠️ <strong>Directory is not writable</strong> - Photo uploads may fail</p>";
    }
} else {
    echo "<p>❌ <strong>Uploads directory does not exist:</strong> <code>{$uploads_dir}</code></p>";
    echo "<p><strong>Action needed:</strong> Create the uploads directory manually or photo uploads will fail.</p>";
}
echo "</div>";

// Action buttons
echo "<div class='box info'>";
echo "<h3>🚀 Actions</h3>";
echo "<p>";
echo "<a href='admin/views/edit_profile.php' class='btn btn-primary'>👤 Go to Edit Profile</a>";
echo "<a href='admin/views/manage_users.php' class='btn btn-success'>👥 Manage Users</a>";
echo "</p>";
echo "</div>";

pg_close($conn);
echo "</body></html>";
?>
