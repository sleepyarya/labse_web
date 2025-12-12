<?php
require_once 'auth_check.php';
require_once '../includes/config.php';
require_once '../includes/activity_logger.php';

$page_title = 'Riwayat Aktivitas Personil';

// Pagination settings
$limit = 50;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Filter parameters
$filter_personil = isset($_GET['personil']) && $_GET['personil'] !== '' ? (int)$_GET['personil'] : null;
$filter_action = isset($_GET['action']) && $_GET['action'] !== '' ? $_GET['action'] : null;
$filter_date_from = isset($_GET['date_from']) && $_GET['date_from'] !== '' ? $_GET['date_from'] : null;
$filter_date_to = isset($_GET['date_to']) && $_GET['date_to'] !== '' ? $_GET['date_to'] : null;
$search_query = isset($_GET['search']) && $_GET['search'] !== '' ? trim($_GET['search']) : null;

// Build WHERE clause
$where_conditions = [];
$params = [];
$param_count = 1;

if ($filter_personil) {
    $where_conditions[] = "personil_id = $$param_count";
    $params[] = $filter_personil;
    $param_count++;
}

if ($filter_action) {
    $where_conditions[] = "action_type = $$param_count";
    $params[] = $filter_action;
    $param_count++;
}

if ($filter_date_from) {
    $where_conditions[] = "created_at >= $$param_count";
    $params[] = $filter_date_from . ' 00:00:00';
    $param_count++;
}

if ($filter_date_to) {
    $where_conditions[] = "created_at <= $$param_count";
    $params[] = $filter_date_to . ' 23:59:59';
    $param_count++;
}

if ($search_query) {
    $where_conditions[] = "action_description ILIKE $$param_count";
    $params[] = "%$search_query%";
    $param_count++;
}

$where_clause = count($where_conditions) > 0 ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_query = "SELECT COUNT(*) as total FROM activity_logs $where_clause";
if (count($params) > 0) {
    $count_result = pg_query_params($conn, $count_query, $params);
} else {
    $count_result = pg_query($conn, $count_query);
}
$total_records = pg_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Get activity logs
$query = "SELECT * FROM activity_logs $where_clause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
if (count($params) > 0) {
    $result = pg_query_params($conn, $query, $params);
} else {
    $result = pg_query($conn, $query);
}

// Get all personil for filter dropdown
$personil_query = "SELECT id, nama FROM personil ORDER BY nama";
$personil_result = pg_query($conn, $personil_query);

// Get action types for filter
$action_types = get_action_type_labels();

include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Aktivitas Personil</h4>
            <p class="text-muted mb-0 small">Monitor semua aktivitas personil secara real-time</p>
        </div>
    </div>
    
    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-3" data-aos="fade-up">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter & Pencarian</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="" id="filterForm">
                <div class="row g-3">
                    <!-- Personil Filter -->
                    <div class="col-md-3">
                        <label class="form-label small">Personil</label>
                        <select name="personil" class="form-select form-select-sm">
                            <option value="">Semua Personil</option>
                            <?php while ($personil = pg_fetch_assoc($personil_result)): ?>
                                <option value="<?php echo $personil['id']; ?>" 
                                    <?php echo $filter_personil == $personil['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($personil['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <!-- Action Type Filter -->
                    <div class="col-md-3">
                        <label class="form-label small">Jenis Aktivitas</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">Semua Aktivitas</option>
                            <optgroup label="Login/Logout">
                                <option value="LOGIN" <?php echo $filter_action == 'LOGIN' ? 'selected' : ''; ?>>Login</option>
                                <option value="LOGOUT" <?php echo $filter_action == 'LOGOUT' ? 'selected' : ''; ?>>Logout</option>
                            </optgroup>
                            <optgroup label="Artikel">
                                <option value="CREATE_ARTICLE" <?php echo $filter_action == 'CREATE_ARTICLE' ? 'selected' : ''; ?>>Tambah Artikel</option>
                                <option value="EDIT_ARTICLE" <?php echo $filter_action == 'EDIT_ARTICLE' ? 'selected' : ''; ?>>Edit Artikel</option>
                                <option value="DELETE_ARTICLE" <?php echo $filter_action == 'DELETE_ARTICLE' ? 'selected' : ''; ?>>Hapus Artikel</option>
                            </optgroup>
                            <optgroup label="Penelitian">
                                <option value="CREATE_PENELITIAN" <?php echo $filter_action == 'CREATE_PENELITIAN' ? 'selected' : ''; ?>>Tambah Penelitian</option>
                                <option value="EDIT_PENELITIAN" <?php echo $filter_action == 'EDIT_PENELITIAN' ? 'selected' : ''; ?>>Edit Penelitian</option>
                                <option value="DELETE_PENELITIAN" <?php echo $filter_action == 'DELETE_PENELITIAN' ? 'selected' : ''; ?>>Hapus Penelitian</option>
                            </optgroup>
                            <optgroup label="Pengabdian">
                                <option value="CREATE_PENGABDIAN" <?php echo $filter_action == 'CREATE_PENGABDIAN' ? 'selected' : ''; ?>>Tambah Pengabdian</option>
                                <option value="EDIT_PENGABDIAN" <?php echo $filter_action == 'EDIT_PENGABDIAN' ? 'selected' : ''; ?>>Edit Pengabdian</option>
                                <option value="DELETE_PENGABDIAN" <?php echo $filter_action == 'DELETE_PENGABDIAN' ? 'selected' : ''; ?>>Hapus Pengabdian</option>
                            </optgroup>
                            <optgroup label="Produk">
                                <option value="CREATE_PRODUK" <?php echo $filter_action == 'CREATE_PRODUK' ? 'selected' : ''; ?>>Tambah Produk</option>
                                <option value="EDIT_PRODUK" <?php echo $filter_action == 'EDIT_PRODUK' ? 'selected' : ''; ?>>Edit Produk</option>
                                <option value="DELETE_PRODUK" <?php echo $filter_action == 'DELETE_PRODUK' ? 'selected' : ''; ?>>Hapus Produk</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <!-- Date From -->
                    <div class="col-md-2">
                        <label class="form-label small">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" 
                               value="<?php echo htmlspecialchars($filter_date_from ?? ''); ?>">
                    </div>
                    
                    <!-- Date To -->
                    <div class="col-md-2">
                        <label class="form-label small">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" 
                               value="<?php echo htmlspecialchars($filter_date_to ?? ''); ?>">
                    </div>
                    
                    <!-- Search -->
                    <div class="col-md-2">
                        <label class="form-label small">Cari</label>
                        <input type="text" name="search" class="form-control form-control-sm" 
                               placeholder="Cari aktivitas..." 
                               value="<?php echo htmlspecialchars($search_query ?? ''); ?>">
                    </div>
                </div>
                
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="activity_logs.php" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Activity Logs Table -->
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>Daftar Aktivitas
                <span class="badge bg-primary"><?php echo $total_records; ?> Total</span>
            </h6>
            <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
        </div>
        <div class="card-body p-0">
            <?php if (pg_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 180px;">Waktu</th>
                                <th style="width: 200px;">Personil</th>
                                <th style="width: 150px;">Aktivitas</th>
                                <th>Detail</th>
                                <th style="width: 120px;">IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($log = pg_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-3">
                                        <small class="text-muted">
                                            <?php 
                                            $date = new DateTime($log['created_at']);
                                            echo $date->format('d M Y'); 
                                            ?>
                                            <br>
                                            <strong><?php echo $date->format('H:i:s'); ?></strong>
                                        </small>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($log['personil_nama']); ?></strong>
                                        <br>
                                        <small class="text-muted">ID: <?php echo $log['personil_id']; ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $action_type = $log['action_type'];
                                        $badge_class = 'bg-secondary';
                                        
                                        if (strpos($action_type, 'LOGIN') !== false) {
                                            $badge_class = 'bg-success';
                                        } elseif (strpos($action_type, 'LOGOUT') !== false) {
                                            $badge_class = 'bg-warning';
                                        } elseif (strpos($action_type, 'CREATE') !== false) {
                                            $badge_class = 'bg-primary';
                                        } elseif (strpos($action_type, 'EDIT') !== false) {
                                            $badge_class = 'bg-info';
                                        } elseif (strpos($action_type, 'DELETE') !== false) {
                                            $badge_class = 'bg-danger';
                                        }
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo get_action_label($action_type); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($log['action_description']); ?>
                                        <?php if ($log['target_type'] && $log['target_id']): ?>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-tag"></i> 
                                                <?php echo ucfirst($log['target_type']); ?> ID: <?php echo $log['target_id']; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="font-monospace"><?php echo htmlspecialchars($log['ip_address']); ?></small></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $total_records); ?> 
                                dari <?php echo $total_records; ?> aktivitas
                            </div>
                            <nav>
                                <ul class="pagination pagination-sm mb-0">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php 
                                                echo $filter_personil ? '&personil=' . $filter_personil : '';
                                                echo $filter_action ? '&action=' . $filter_action : '';
                                                echo $filter_date_from ? '&date_from=' . $filter_date_from : '';
                                                echo $filter_date_to ? '&date_to=' . $filter_date_to : '';
                                                echo $search_query ? '&search=' . urlencode($search_query) : '';
                                            ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?><?php 
                                                echo $filter_personil ? '&personil=' . $filter_personil : '';
                                                echo $filter_action ? '&action=' . $filter_action : '';
                                                echo $filter_date_from ? '&date_from=' . $filter_date_from : '';
                                                echo $filter_date_to ? '&date_to=' . $filter_date_to : '';
                                                echo $search_query ? '&search=' . urlencode($search_query) : '';
                                            ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php 
                                                echo $filter_personil ? '&personil=' . $filter_personil : '';
                                                echo $filter_action ? '&action=' . $filter_action : '';
                                                echo $filter_date_from ? '&date_from=' . $filter_date_from : '';
                                                echo $filter_date_to ? '&date_to=' . $filter_date_to : '';
                                                echo $search_query ? '&search=' . urlencode($search_query) : '';
                                            ?>">Next</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="mt-3 text-muted">Tidak ada aktivitas yang ditemukan.</p>
                    <a href="activity_logs.php" class="btn btn-sm btn-primary">Reset Filter</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
</div>
<!-- End Admin Content -->

<style>
    .font-monospace {
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.85em;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table td {
        vertical-align: middle;
        font-size: 0.875rem;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
    }
</style>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>
