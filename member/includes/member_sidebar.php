<!-- Mobile Toggle Button -->
<button class="mobile-toggle" id="sidebarToggle">
    <i class="bi bi-list"></i>
</button>

<!-- Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="member-sidebar" id="memberSidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <button class="sidebar-close" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
        <a href="<?php echo BASE_URL; ?>/member/index.php" class="sidebar-brand" style="align-items: flex-start;">
            <img src="<?php echo BASE_URL; ?>/assets/img/logo-pnm.png" alt="Logo PNM" style="width: 55px; height: 55px; object-fit: contain; margin-right: 12px; margin-top: -2px;">
            <div style="padding-top: 3px;">
                <div style="font-size: 1rem; line-height: 1.3;">JTI - POLINEMA</div>
                <small style="font-size: 0.7rem; opacity: 0.7; line-height: 1.2;">Member Panel</small>
            </div>
        </a>
    </div>
    
    <!-- Main Menu -->
    <div class="sidebar-menu">
        <div class="menu-label">Main Menu</div>
        
        <a href="<?php echo BASE_URL; ?>/member/index.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="menu-label">Content Management</div>
        
        <a href="<?php echo BASE_URL; ?>/member/my_articles.php" class="menu-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['my_articles.php', 'edit_article.php']) ? 'active' : ''; ?>">
            <i class="bi bi-file-text"></i>
            <span>Artikel Saya</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/member/add_article.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'add_article.php' ? 'active' : ''; ?>">
            <i class="bi bi-plus-circle"></i>
            <span>Buat Artikel</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/member/edit_profile.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'edit_profile.php' ? 'active' : ''; ?>">
            <i class="bi bi-person-circle"></i>
            <span>Edit Profil</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/member/logout.php" class="menu-item">
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
                    <?php echo htmlspecialchars($_SESSION['member_nama'] ?? 'Member'); ?>
                </div>
                <div style="font-size: 0.75rem; opacity: 0.7;">
                    <?php echo htmlspecialchars($_SESSION['member_jabatan'] ?? ''); ?>
                </div>
            </div>
        </div>
    </div>
</div>
