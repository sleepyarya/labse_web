<?php
session_start();

// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $role = $_SESSION['user_role'];
    if ($role === 'admin') {
        header('Location: admin/index.php');
    } elseif ($role === 'personil') {
        header('Location: member/index.php');
    } elseif ($role === 'mahasiswa') {
        header('Location: student/index.php');
    }
    exit();
}

require_once 'includes/config.php';
$page_title = 'Login';

$error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Username/Email dan password harus diisi!';
    } else {
        // Query ke tabel users berdasarkan username atau email
        $query = "SELECT * FROM users WHERE (username = $1 OR email = $1) AND is_active = TRUE";
        $result = pg_query_params($conn, $query, array($username));
        
        if ($result && pg_num_rows($result) > 0) {
            $user = pg_fetch_assoc($result);
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID untuk keamanan
                session_regenerate_id(true);
                
                // Generate unique session token
                $session_token = bin2hex(random_bytes(32));
                
                // Set session berdasarkan role
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['reference_id'] = $user['reference_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['session_token'] = $session_token;
                $_SESSION['last_activity'] = time();
                $_SESSION['fresh_login'] = true;
                
                // Ambil data detail dari tabel asli
                if ($user['role'] === 'admin') {
                    $detail_query = "SELECT * FROM admin_users WHERE id = $1";
                    $detail_result = pg_query_params($conn, $detail_query, array($user['reference_id']));
                    $detail = pg_fetch_assoc($detail_result);
                    
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $detail['id'];
                    $_SESSION['admin_username'] = $detail['username'];
                    $_SESSION['admin_nama'] = $detail['nama_lengkap'];
                    $_SESSION['admin_email'] = $detail['email'];
                    $_SESSION['admin_session_token'] = $session_token;
                    $_SESSION['admin_last_activity'] = time();
                    
                    $redirect_url = 'admin/index.php';
                    
                } elseif ($user['role'] === 'personil') {
                    $detail_query = "SELECT * FROM personil WHERE id = $1";
                    $detail_result = pg_query_params($conn, $detail_query, array($user['reference_id']));
                    $detail = pg_fetch_assoc($detail_result);
                    
                    $_SESSION['member_logged_in'] = true;
                    $_SESSION['member_id'] = $detail['id'];
                    $_SESSION['member_nama'] = $detail['nama'];
                    $_SESSION['member_email'] = $detail['email'];
                    $_SESSION['member_jabatan'] = $detail['jabatan'];
                    $_SESSION['member_foto'] = $detail['foto'];
                    $_SESSION['member_session_token'] = $session_token;
                    $_SESSION['member_last_activity'] = time();
                    
                    $redirect_url = 'member/index.php';
                    
                } elseif ($user['role'] === 'mahasiswa') {
                    $detail_query = "SELECT * FROM mahasiswa WHERE id = $1";
                    $detail_result = pg_query_params($conn, $detail_query, array($user['reference_id']));
                    $detail = pg_fetch_assoc($detail_result);
                    
                    $_SESSION['student_logged_in'] = true;
                    $_SESSION['student_id'] = $detail['id'];
                    $_SESSION['student_nama'] = $detail['nama'];
                    $_SESSION['student_nim'] = $detail['nim'];
                    $_SESSION['student_email'] = $detail['email'];
                    
                    $redirect_url = 'student/index.php';
                }
                
                // Update last login di tabel users
                $update_query = "UPDATE users SET last_login = NOW() WHERE id = $1";
                pg_query_params($conn, $update_query, array($user['id']));
                
                // Redirect sesuai role
                header('Location: ' . $redirect_url);
                exit();
            } else {
                $error = 'Username/Email atau password salah!';
            }
        } else {
            $error = 'Username/Email atau password salah!';
        }
    }
}

pg_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Lab SE</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #68BBE3;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .login-logo h3 {
            color: #2C3E50;
            font-weight: 700;
            margin: 0;
        }
        
        .login-logo p {
            color: #7F8C8D;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(74, 144, 226, 0.3);
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-link a:hover {
            opacity: 0.8;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        
        .form-control {
            border-left: none;
        }
        
        .form-control:focus + .input-group-text {
            border-color: var(--primary-color);
        }
        
        #togglePassword {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        #togglePassword:hover {
            background-color: #e9ecef;
            color: var(--primary-color);
        }
        
        #togglePassword:active,
        #togglePassword:focus {
            box-shadow: none;
            background-color: #e9ecef;
        }
        
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="bi bi-shield-lock"></i>
                <h3>Login</h3>
                <p>Lab Software Engineering</p>
            </div>
            
            <?php if (isset($_GET['logout']) && $_GET['logout'] == 'success' && !$error): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>Anda telah berhasil logout.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['timeout']) && $_GET['timeout'] == '1' && !$error): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-clock-history me-2"></i>Sesi Anda telah berakhir. Silakan login kembali.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['direct']) && $_GET['direct'] == '1' && !$error): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>Silakan login untuk mengakses halaman tersebut.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="" autocomplete="off">
                <div class="mb-3">
                    <label for="username" class="form-label">Username / Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required 
                               placeholder="Masukkan username atau email" autofocus autocomplete="off">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Masukkan password" autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-left: none;">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </div>
            </form>
            
            <div class="back-link">
                <a href="<?php echo BASE_URL; ?>">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle show/hide password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'password') {
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            } else {
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            }
        });
        
        // Clear form on load
        window.addEventListener('load', function() {
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
            
            // Clean URL after 3 seconds
            if (window.location.search.includes('logout=success')) {
                setTimeout(function() {
                    const url = new URL(window.location);
                    url.searchParams.delete('logout');
                    window.history.replaceState({}, '', url);
                }, 3000);
            }
        });
        
        // Clear form when back button is pressed
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                document.getElementById('username').value = '';
                document.getElementById('password').value = '';
            }
        });
    </script>
</body>
</html>
