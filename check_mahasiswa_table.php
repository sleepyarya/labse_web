<?php
/**
 * CHECK MAHASISWA TABLE STRUCTURE
 * Script untuk mengecek struktur tabel mahasiswa dan kolom approval
 */

require_once 'includes/config.php';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Check Mahasiswa Table</title>";
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
</style>";
echo "</head><body>";

echo "<h1>🔍 Check Mahasiswa Table Structure</h1>";

// Check if table exists
echo "<div class='box info'>";
echo "<h3>📋 Table Information</h3>";

$table_check = "SELECT EXISTS (
    SELECT FROM information_schema.tables 
    WHERE table_schema = 'public' 
    AND table_name = 'mahasiswa'
)";
$result = pg_query($conn, $table_check);
$table_exists = pg_fetch_result($result, 0, 0) === 't';

if ($table_exists) {
    echo "<p>✅ <strong>Table 'mahasiswa' exists</strong></p>";
} else {
    echo "<p>❌ <strong>Table 'mahasiswa' does not exist!</strong></p>";
    echo "</div>";
    echo "<p><a href='admin/views/manage_mahasiswa.php' class='btn btn-primary'>← Back to Admin</a></p>";
    echo "</body></html>";
    exit;
}
echo "</div>";

// Get table structure
echo "<div class='box info'>";
echo "<h3>🏗️ Table Structure</h3>";

$structure_query = "SELECT 
    column_name, 
    data_type, 
    is_nullable, 
    column_default
FROM information_schema.columns 
WHERE table_name = 'mahasiswa' 
ORDER BY ordinal_position";

$structure_result = pg_query($conn, $structure_query);

echo "<table>";
echo "<tr><th>Column Name</th><th>Data Type</th><th>Nullable</th><th>Default</th></tr>";

$approval_columns = ['status_approval', 'approved_by', 'approved_at', 'rejection_reason'];
$existing_approval_columns = [];

while ($row = pg_fetch_assoc($structure_result)) {
    $is_approval_col = in_array($row['column_name'], $approval_columns);
    if ($is_approval_col) {
        $existing_approval_columns[] = $row['column_name'];
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

// Check approval columns
echo "<div class='box " . (count($existing_approval_columns) === count($approval_columns) ? 'success' : 'warning') . "'>";
echo "<h3>🔐 Approval System Columns</h3>";

$missing_columns = array_diff($approval_columns, $existing_approval_columns);

if (empty($missing_columns)) {
    echo "<p>✅ <strong>All approval columns exist!</strong></p>";
    echo "<ul>";
    foreach ($approval_columns as $col) {
        echo "<li>✅ <strong>{$col}</strong></li>";
    }
    echo "</ul>";
} else {
    echo "<p>⚠️ <strong>Missing approval columns:</strong></p>";
    echo "<ul>";
    foreach ($missing_columns as $col) {
        echo "<li>❌ <strong>{$col}</strong></li>";
    }
    echo "</ul>";
    
    echo "<p><strong>Action needed:</strong> Run the SQL script to add missing columns.</p>";
    echo "<a href='database/add_mahasiswa_approval.sql' class='btn btn-success' target='_blank'>📄 View SQL Script</a>";
}
echo "</div>";

// Check sample data
echo "<div class='box info'>";
echo "<h3>📊 Sample Data</h3>";

$sample_query = "SELECT id, nama, nim, status_approval, approved_by, approved_at 
                 FROM mahasiswa 
                 ORDER BY created_at DESC 
                 LIMIT 5";
$sample_result = pg_query($conn, $sample_query);

if (pg_num_rows($sample_result) > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nama</th><th>NIM</th><th>Status</th><th>Approved By</th><th>Approved At</th></tr>";
    
    while ($row = pg_fetch_assoc($sample_result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
        echo "<td>";
        $status = $row['status_approval'] ?? 'NULL';
        if ($status === 'pending') {
            echo "<span style='color: orange;'>🟡 Pending</span>";
        } elseif ($status === 'approved') {
            echo "<span style='color: green;'>✅ Approved</span>";
        } elseif ($status === 'rejected') {
            echo "<span style='color: red;'>❌ Rejected</span>";
        } else {
            echo "<span style='color: gray;'>❓ " . htmlspecialchars($status) . "</span>";
        }
        echo "</td>";
        echo "<td>" . htmlspecialchars($row['approved_by'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['approved_at'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>📭 No data found in mahasiswa table.</p>";
}
echo "</div>";

// Action buttons
echo "<div class='box info'>";
echo "<h3>🚀 Actions</h3>";
echo "<p>";
echo "<a href='admin/views/manage_mahasiswa.php' class='btn btn-primary'>👥 Go to Manage Mahasiswa</a>";
echo "<a href='views/recruitment/form.php' class='btn btn-success'>📝 Test Registration Form</a>";
echo "</p>";
echo "</div>";

pg_close($conn);
echo "</body></html>";
?>
