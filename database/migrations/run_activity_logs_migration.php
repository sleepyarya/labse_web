<?php
/**
 * Migration Runner for Activity Logs Table
 * Run this file once to create the activity_logs table
 * URL: http://localhost/labse_web/database/migrations/run_activity_logs_migration.php
 */

require_once '../../includes/config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migration: Activity Logs Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Migration: Activity Logs Table</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        // Read SQL file
                        $sql_file = __DIR__ . '/create_activity_logs_table.sql';
                        
                        if (!file_exists($sql_file)) {
                            echo '<div class="alert alert-danger">Error: File SQL tidak ditemukan!</div>';
                            exit;
                        }
                        
                        $sql = file_get_contents($sql_file);
                        
                        if ($sql === false) {
                            echo '<div class="alert alert-danger">Error: Gagal membaca file SQL!</div>';
                            exit;
                        }
                        
                        // Execute migration
                        echo '<div class="alert alert-info">Menjalankan migration...</div>';
                        
                        $result = pg_query($conn, $sql);
                        
                        if ($result) {
                            echo '<div class="alert alert-success">';
                            echo '<h5><i class="bi bi-check-circle"></i> Migration Berhasil!</h5>';
                            echo '<p>Tabel <code>activity_logs</code> telah berhasil dibuat.</p>';
                            echo '<hr>';
                            echo '<h6>Detail:</h6>';
                            echo '<ul>';
                            echo '<li>Tabel: <code>activity_logs</code></li>';
                            echo '<li>Indexes: 4 indexes untuk performance</li>';
                            echo '<li>Foreign Key: personil_id → personil(id)</li>';
                            echo '</ul>';
                            echo '</div>';
                            
                            // Verify table creation
                            $verify_query = "SELECT column_name, data_type 
                                           FROM information_schema.columns 
                                           WHERE table_name = 'activity_logs' 
                                           ORDER BY ordinal_position";
                            $verify_result = pg_query($conn, $verify_query);
                            
                            if ($verify_result && pg_num_rows($verify_result) > 0) {
                                echo '<div class="mt-3">';
                                echo '<h6>Struktur Tabel:</h6>';
                                echo '<table class="table table-sm table-bordered">';
                                echo '<thead><tr><th>Column</th><th>Type</th></tr></thead>';
                                echo '<tbody>';
                                while ($row = pg_fetch_assoc($verify_result)) {
                                    echo '<tr>';
                                    echo '<td><code>' . htmlspecialchars($row['column_name']) . '</code></td>';
                                    echo '<td>' . htmlspecialchars($row['data_type']) . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                                echo '</div>';
                            }
                            
                        } else {
                            $error = pg_last_error($conn);
                            echo '<div class="alert alert-danger">';
                            echo '<h5><i class="bi bi-exclamation-triangle"></i> Migration Gagal!</h5>';
                            echo '<p>Error: ' . htmlspecialchars($error) . '</p>';
                            echo '</div>';
                        }
                        
                        pg_close($conn);
                        ?>
                        
                        <div class="mt-4">
                            <a href="../../admin/index.php" class="btn btn-primary">Kembali ke Dashboard Admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
