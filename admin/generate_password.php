<?php
/**
 * Script untuk generate password hash
 * Jalankan file ini via browser untuk mendapatkan hash password
 */

// Password yang ingin di-hash
$password = 'admin123';

// Generate hash
$hash = password_hash($password, PASSWORD_DEFAULT);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">🔐 Password Hash Generator</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Password:</strong> <code><?php echo htmlspecialchars($password); ?></code>
                        </div>
                        
                        <div class="alert alert-success">
                            <strong>Hash:</strong><br>
                            <code style="word-break: break-all;"><?php echo $hash; ?></code>
                        </div>
                        
                        <hr>
                        
                        <h5>Cara Update Database:</h5>
                        <p>Copy SQL query di bawah ini dan jalankan di pgAdmin:</p>
                        
                        <div class="bg-dark text-white p-3 rounded">
                            <code style="color: #0f0;">
-- Update password untuk user admin di tabel users<br>
UPDATE users SET password = '<?php echo $hash; ?>' <br>
WHERE role = 'admin' AND username = 'admin';<br>
<br>
-- Update password untuk user superadmin di tabel users<br>
UPDATE users SET password = '<?php echo $hash; ?>' <br>
WHERE role = 'admin' AND username = 'superadmin';<br>
<br>
-- Verifikasi<br>
SELECT u.username, u.email, u.role, au.nama_lengkap <br>
FROM users u <br>
LEFT JOIN admin_users au ON u.reference_id = au.id AND u.role = 'admin';
                            </code>
                        </div>
                        
                        <hr>
                        
                        <h5>Custom Password:</h5>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Masukkan password baru:</label>
                                <input type="text" name="custom_password" class="form-control" placeholder="Masukkan password baru">
                            </div>
                            <button type="submit" class="btn btn-primary">Generate Hash</button>
                        </form>
                        
                        <?php if (isset($_POST['custom_password'])): ?>
                            <?php
                            $custom_pass = $_POST['custom_password'];
                            $custom_hash = password_hash($custom_pass, PASSWORD_DEFAULT);
                            ?>
                            <div class="alert alert-warning mt-3">
                                <strong>Password:</strong> <code><?php echo htmlspecialchars($custom_pass); ?></code><br>
                                <strong>Hash:</strong><br>
                                <code style="word-break: break-all;"><?php echo $custom_hash; ?></code>
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <div class="text-center">
                            <a href="login.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Login
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-3">
                    <strong>⚠️ Penting:</strong> Hapus file ini setelah selesai untuk keamanan!
                </div>
            </div>
        </div>
    </div>
</body>
</html>
