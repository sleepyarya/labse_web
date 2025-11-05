<div class="admin-sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="sidebar-brand" style="align-items: flex-start;">
            <img src="<?php echo BASE_URL; ?>/assets/img/logo-pnm.png" alt="Logo PNM" style="width: 55px; height: 55px; object-fit: contain; margin-right: 12px; margin-top: -2px;">
            <div style="padding-top: 3px;">
                <div style="font-size: 1rem; line-height: 1.3;">JTI - POLINEMA</div>
                <small style="font-size: 0.7rem; opacity: 0.7; line-height: 1.2;">Admin Panel</small>
            </div>
        </a>
    </div>
    
    <!-- Main Menu -->
    <div class="sidebar-menu">
        <div class="menu-label">Main Menu</div>
        
        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="menu-label">Content Management</div>
        
        <a href="<?php echo BASE_URL; ?>/admin/manage_personil.php" class="menu-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['manage_personil.php', 'add_personil.php', 'edit_personil.php']) ? 'active' : ''; ?>">
            <i class="bi bi-people"></i>
            <span>Kelola Personil</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/admin/manage_artikel.php" class="menu-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['manage_artikel.php', 'add_artikel.php', 'edit_artikel.php']) ? 'active' : ''; ?>">
            <i class="bi bi-file-text"></i>
            <span>Kelola Artikel</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/admin/manage_mahasiswa.php" class="menu-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['manage_mahasiswa.php', 'add_mahasiswa.php', 'edit_mahasiswa.php']) ? 'active' : ''; ?>">
            <i class="bi bi-person-badge"></i>
            <span>Kelola Mahasiswa</span>
        </a>
        
        <div class="menu-label">Website</div>
        
        <a href="<?php echo BASE_URL; ?>/admin/view_website.php" class="menu-item">
            <i class="bi bi-globe"></i>
            <span>Lihat Website</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/admin/logout.php" class="menu-item">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
    
    <!-- User Info -->
    <div class="user-info">
        <div class="d-flex align-items-center">
            <div class="user-avatar">
                <i class="bi bi-person"></i>
            </div>
            <div class="flex-grow-1">
                <div style="font-size: 0.9rem; font-weight: 600;">
                    <?php echo htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?>
                </div>
                <div style="font-size: 0.75rem; opacity: 0.7;">
                    <?php echo htmlspecialchars($_SESSION['admin_username'] ?? ''); ?>
                </div>
            </div>
        </div>
    </div>
</div>
