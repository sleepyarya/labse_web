<?php
// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah admin sedang preview website (bukan hanya login)
// Hanya muncul jika admin klik "Lihat Website" dari dashboard
$is_viewing_from_admin = isset($_SESSION['viewing_from_admin']) && $_SESSION['viewing_from_admin'] === true;

// Deteksi halaman aktif berdasarkan URL
$current_page = '';
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Ambil path relatif dari BASE_URL
$base_path = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
$current_path = str_replace($base_path, '', $request_uri);

// Hapus query string jika ada
$current_path = strtok($current_path, '?');

// Tentukan halaman aktif
if ($current_path == '/' || $current_path == '/index.php' || $current_path == '' || $current_path == '/views/index.php') {
    $current_page = 'home';
} elseif (strpos($current_path, '/views/blog/') !== false || strpos($current_path, 'blog') !== false) {
    $current_page = 'blog';
} elseif (strpos($current_path, '/views/pengabdian/') !== false || strpos($current_path, 'pengabdian') !== false) {
    $current_page = 'pengabdian';
} elseif (strpos($current_path, '/views/penelitian/') !== false || strpos($current_path, 'penelitian') !== false) {
    $current_page = 'penelitian';
} elseif (strpos($current_path, '/views/produk/') !== false || strpos($current_path, 'produk') !== false) {
    $current_page = 'produk';
} elseif (strpos($current_path, '/views/personil/') !== false || strpos($current_path, 'personil') !== false) {
    $current_page = 'personil';
} elseif (strpos($current_path, '/views/recruitment/') !== false || strpos($current_path, 'recruitment') !== false) {
    $current_page = 'recruitment';
} elseif (strpos($current_path, '/views/tentang.php') !== false || 
          strpos($current_path, '/views/visi_misi.php') !== false || 
          strpos($current_path, '/views/roadmap.php') !== false || 
          strpos($current_path, '/views/focus_scope.php') !== false ||
          strpos($current_path, 'tentang') !== false ||
          strpos($current_path, 'visi_misi') !== false ||
          strpos($current_path, 'roadmap') !== false ||
          strpos($current_path, 'focus_scope') !== false) {
    $current_page = 'profil';
}
?>
<!-- Debug: Current page = <?php echo $current_page; ?>, Path = <?php echo $current_path; ?> -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-start" href="<?php echo BASE_URL; ?>/" style="padding-top: 0.5rem;">
            <img src="<?php echo BASE_URL . get_content('navbar', 'logo_path', '/public/img/logo-pnm.png'); ?>" alt="Logo Politeknik Negeri Malang" class="navbar-logo me-3">
            <div class="brand-text">
                <div class="fw-bold text-primary" style="font-size: 1rem; line-height: 1.3;"><?php echo htmlspecialchars(get_content('navbar', 'brand_title', 'Jurusan Teknologi Informasi')); ?></div>
                <div class="text-muted" style="font-size: 0.85rem; line-height: 1.3;"><?php echo htmlspecialchars(get_content('navbar', 'brand_subtitle', 'Politeknik Negeri Malang')); ?></div>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'home' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo $current_page == 'profil' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        Profil
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/tentang.php">Tentang</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/visi_misi.php">Visi & Misi</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/roadmap.php">Roadmap</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/focus_scope.php">Focus & Scope</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'personil' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/personil/">Personil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'blog' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/blog/">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'pengabdian' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/pengabdian/">Pengabdian</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'penelitian' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/penelitian/">Penelitian</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'produk' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/produk/">Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'recruitment' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/recruitment/">Recruitment</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php if ($is_viewing_from_admin): ?>
<!-- Floating Admin Buttons - Hanya muncul jika admin klik "Lihat Website" dari dashboard -->
<div class="floating-admin-controls">
    <a href="<?php echo BASE_URL; ?>/admin/index.php" class="floating-btn floating-btn-primary" title="Kembali ke Admin Dashboard" onclick="clearPreviewMode()">
        <i class="bi bi-speedometer2"></i>
    </a>
    <button class="floating-btn floating-btn-secondary" title="Tutup Mode Preview" onclick="closePreviewMode()">
        <i class="bi bi-x-lg"></i>
    </button>
</div>

<script>
// Debug: Confirm viewing from admin
console.log('Admin Preview Mode: ACTIVE ✓');
console.log('Floating controls rendered');
console.log('Username: <?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'N/A'; ?>');

// Function to close preview mode
function closePreviewMode() {
    if (confirm('Tutup mode preview admin?')) {
        fetch('<?php echo BASE_URL; ?>/admin/close_preview.php')
            .then(() => {
                location.reload();
            });
    }
}

// Function to clear preview mode when going back to dashboard
function clearPreviewMode() {
    fetch('<?php echo BASE_URL; ?>/admin/close_preview.php');
    // Continue with navigation
    return true;
}
</script>
<?php else: ?>
<script>
// Debug: Confirm not in preview mode
console.log('Admin Preview Mode: INACTIVE');
<?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
console.log('Note: Admin is logged in, but NOT viewing from dashboard');
<?php endif; ?>
</script>
<?php endif; ?>

<style>
    /* Override container padding untuk logo mepet */
    .navbar .container {
        padding-left: 0 !important;
    }
    
    .navbar-logo {
        height: 70px;
        width: auto;
        object-fit: contain;
        margin-top: -5px;
        margin-left: -100px;
    }
    
    .brand-text {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding-top: 2px;
    }
    
    .navbar-brand {
        gap: 0;
        margin-left: 0 !important;
        padding-left: 0 !important;
    }
    
    @media (max-width: 768px) {
        .navbar-logo {
            height: 50px;
            margin-left: 8px;
        }
        
        .brand-text {
            font-size: 0.85rem;
        }
        
        .brand-text .fw-bold {
            font-size: 0.9rem !important;
        }
        
        .brand-text div:last-child {
            font-size: 0.75rem !important;
        }
    }
    
    @media (max-width: 576px) {
        .navbar-logo {
            height: 45px;
            margin-left: 5px;
        }
        
        .brand-text .fw-bold {
            font-size: 0.8rem !important;
        }
        
        .brand-text div:last-child {
            font-size: 0.7rem !important;
        }
    }
    
    /* Floating Admin Controls */
    .floating-admin-controls {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999 !important;
        display: flex;
        flex-direction: column;
        gap: 10px;
        animation: fadeInUp 0.5s ease-in-out;
    }
    
    .floating-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: all 0.3s ease;
        border: 3px solid #fff !important;
        cursor: pointer;
        text-decoration: none;
        color: white;
    }
    
    .floating-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4) !important;
    }
    
    .floating-btn-primary:hover {
        transform: scale(1.15) rotate(10deg);
        box-shadow: 0 8px 30px rgba(102, 126, 234, 0.6) !important;
        color: white;
    }
    
    .floating-btn-secondary {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4) !important;
    }
    
    .floating-btn-secondary:hover {
        transform: scale(1.15) rotate(-10deg);
        box-shadow: 0 8px 30px rgba(245, 87, 108, 0.6) !important;
        color: white;
    }
    
    .floating-btn:active {
        transform: scale(0.95);
    }
    
    /* Pulse animation for attention */
    .floating-admin-controls::before {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        opacity: 0.2;
        animation: pulse 2s infinite;
        z-index: -1;
        pointer-events: none;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.2; }
        50% { transform: scale(1.1); opacity: 0.3; }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Tooltip untuk floating buttons */
    .floating-btn::after {
        content: attr(title);
        position: absolute;
        right: 70px;
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        white-space: nowrap;
        font-size: 0.875rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        font-weight: 500;
    }
    
    .floating-btn:hover::after {
        opacity: 1;
    }
    
    @media (max-width: 768px) {
        .floating-admin-controls {
            bottom: 20px;
            right: 20px;
            gap: 8px;
        }
        
        .floating-btn {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }
        
        .floating-btn::after {
            display: none; /* Hide tooltip on mobile */
        }
    }
</style>