<?php
/**
 * Activity Logger Helper
 * 
 * Helper function untuk mencatat aktivitas personil ke database
 * File: includes/activity_logger.php
 */

/**
 * Log aktivitas personil ke database
 * 
 * @param resource $conn Database connection
 * @param int $personil_id ID personil
 * @param string $personil_nama Nama personil
 * @param string $action_type Tipe aktivitas (LOGIN, LOGOUT, CREATE_ARTICLE, dll)
 * @param string $description Deskripsi aktivitas dalam bahasa Indonesia
 * @param string|null $target_type Tipe target (artikel, penelitian, pengabdian, produk, profile)
 * @param int|null $target_id ID target
 * @return bool True jika berhasil, false jika gagal
 */
function log_activity($conn, $personil_id, $personil_nama, $action_type, $description, $target_type = null, $target_id = null) {
    // Get IP address
    $ip_address = 'Unknown';
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip_address = $_SERVER['REMOTE_ADDR'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    
    // Sanitize parameters
    $personil_id = (int)$personil_id;
    $personil_nama = trim($personil_nama);
    $action_type = strtoupper(trim($action_type));
    $description = trim($description);
    $target_id = $target_id ? (int)$target_id : null;
    
    // Validate required fields
    if ($personil_id <= 0 || empty($personil_nama) || empty($action_type) || empty($description)) {
        error_log("Activity Logger: Invalid parameters - personil_id: $personil_id, nama: $personil_nama, action: $action_type");
        return false;
    }
    
    // Prepare query
    $query = "INSERT INTO activity_logs 
              (personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address) 
              VALUES ($1, $2, $3, $4, $5, $6, $7)";
    
    // Execute query
    try {
        $result = pg_query_params($conn, $query, array(
            $personil_id, 
            $personil_nama, 
            $action_type, 
            $description, 
            $target_type, 
            $target_id, 
            $ip_address
        ));
        
        if ($result === false) {
            error_log("Activity Logger: Database error - " . pg_last_error($conn));
            return false;
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Activity Logger: Exception - " . $e->getMessage());
        return false;
    }
}

/**
 * Get action type labels dalam bahasa Indonesia
 * 
 * @return array Mapping action types ke label Indonesia
 */
function get_action_type_labels() {
    return array(
        'LOGIN' => 'Login',
        'LOGOUT' => 'Logout',
        'CREATE_ARTICLE' => 'Tambah Artikel',
        'EDIT_ARTICLE' => 'Edit Artikel',
        'DELETE_ARTICLE' => 'Hapus Artikel',
        'CREATE_PENELITIAN' => 'Tambah Penelitian',
        'EDIT_PENELITIAN' => 'Edit Penelitian',
        'DELETE_PENELITIAN' => 'Hapus Penelitian',
        'CREATE_PENGABDIAN' => 'Tambah Pengabdian',
        'EDIT_PENGABDIAN' => 'Edit Pengabdian',
        'DELETE_PENGABDIAN' => 'Hapus Pengabdian',
        'CREATE_PRODUK' => 'Tambah Produk',
        'EDIT_PRODUK' => 'Edit Produk',
        'DELETE_PRODUK' => 'Hapus Produk',
        'EDIT_PROFILE' => 'Edit Profil'
    );
}

/**
 * Get action type label
 * 
 * @param string $action_type Action type code
 * @return string Label dalam bahasa Indonesia
 */
function get_action_label($action_type) {
    $labels = get_action_type_labels();
    return isset($labels[$action_type]) ? $labels[$action_type] : $action_type;
}
?>
