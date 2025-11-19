<!-- Mobile Toggle Button -->
<button class="mobile-toggle" id="sidebarToggle">
    <i class="bi bi-list"></i>
</button>

<!-- Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="admin-sidebar" id="adminSidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <button class="sidebar-close" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
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
        
        <div class="menu-label">System</div>
        
        <a href="<?php echo BASE_URL; ?>/admin/views/manage_users.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>">
            <i class="bi bi-people-fill"></i>
            <span>Manajemen User</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/admin/views/edit_profile.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'edit_profile.php' ? 'active' : ''; ?>">
            <i class="bi bi-person-gear"></i>
            <span>Edit Profil</span>
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
            <?php
            // Get admin photo (with error handling for missing column)
            $admin_photo = '';
            if (isset($_SESSION['admin_id'])) {
                try {
                    // Check if foto column exists first
                    $column_check = "SELECT column_name FROM information_schema.columns 
                                   WHERE table_name = 'admin_users' AND column_name = 'foto'";
                    $column_result = pg_query($conn, $column_check);
                    
                    if ($column_result && pg_num_rows($column_result) > 0) {
                        // Column exists, safe to query
                        $photo_query = "SELECT foto FROM admin_users WHERE id = $1";
                        $photo_result = pg_query_params($conn, $photo_query, array($_SESSION['admin_id']));
                        if ($photo_result && pg_num_rows($photo_result) > 0) {
                            $photo_data = pg_fetch_assoc($photo_result);
                            $admin_photo = $photo_data['foto'];
                        }
                    }
                } catch (Exception $e) {
                    // Silently handle error - column doesn't exist yet
                    $admin_photo = '';
                }
            }
            ?>
            <div class="user-avatar">
                <?php if (!empty($admin_photo) && file_exists(__DIR__ . '/../../uploads/admin/' . $admin_photo)): ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/admin/<?php echo htmlspecialchars($admin_photo); ?>" 
                         alt="Admin Photo" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                <?php else: ?>
                    <i class="bi bi-person"></i>
                <?php endif; ?>
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
