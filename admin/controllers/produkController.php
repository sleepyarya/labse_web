<?php
// Admin Controller: Produk Controller - FULL ACCESS
// Description: Admin can manage ALL produk without ownership restrictions

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';

class ProdukController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    public function getKategoriList() {
        // Perhatikan nama tabelnya: kategori_produk
        $query = "SELECT id, nama_kategori FROM kategori_produk WHERE is_active = TRUE ORDER BY nama_kategori ASC";
        $result = pg_query($this->conn, $query);
        
        $list = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $list[] = $row;
            }
        }
        return $list;
    }
    
    // Add produk - admin can assign to any personil
    public function add() {
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_produk = pg_escape_string($this->conn, trim($_POST['nama_produk']));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi']));
            $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
            $kategori = pg_escape_string($this->conn, trim($_POST['kategori']));
            $teknologi = pg_escape_string($this->conn, trim($_POST['teknologi']));
            $link_demo = pg_escape_string($this->conn, trim($_POST['link_demo']));
            $link_repository = pg_escape_string($this->conn, trim($_POST['link_repository']));
            $personil_id = isset($_POST['personil_id']) && !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : null;
            
            if (empty($nama_produk) || empty($deskripsi)) {
                $error = 'Nama produk dan deskripsi wajib diisi!';
            } else {
                $gambar = '';
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'produk_' . time() . '_' . uniqid() . '.' . $ext;
                        $upload_dir = '../uploads/produk/';
                        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
                        
                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                            $gambar = $new_filename;
                        }
                    }
                }
                
                if (empty($error)) {
                   // Menggunakan Function Database
                   // Urutan parameter: nama_produk, deskripsi, kategori, tahun, teknologi, gambar, link_demo, link_repository, personil_id
                    $query = "SELECT sp_create_produk($1, $2, $3, $4, $5, $6, $7, $8, $9)";

                $params = array(
                    $nama_produk, 
                    $deskripsi, 
                    $kategori, 
                            (int)$tahun, 
                            $teknologi, 
                            $gambar, 
                            $link_demo, 
                            $link_repository, 
                            $personil_id
                        );

                    $result = pg_query_params($this->conn, $query, $params);
                    
                    if ($result) {
                        header('Location: ' . BASE_URL . '/admin/manage_produk.php?success=add');
                        exit();
                    } else {
                        $error = 'Gagal menambahkan produk!';
                    }
                }
            }
        }
        
        return ['error' => $error];
    }
    
    // Edit produk - FULL ACCESS
    public function edit($id) {
        $error = '';
        $produk = null;
        
        if ($id) {
            $query = "SELECT * FROM produk WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $produk = pg_fetch_assoc($result);
            
            if (!$produk) {
                return ['error' => 'Produk tidak ditemukan!', 'produk' => null];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_produk = pg_escape_string($this->conn, trim($_POST['nama_produk']));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi']));
            $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
            $kategori = pg_escape_string($this->conn, trim($_POST['kategori']));
            $teknologi = pg_escape_string($this->conn, trim($_POST['teknologi']));
            $link_demo = pg_escape_string($this->conn, trim($_POST['link_demo']));
            $link_repository = pg_escape_string($this->conn, trim($_POST['link_repository']));
            $personil_id = isset($_POST['personil_id']) && !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : null;
            
            $gambar = $produk['gambar'];
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $new_filename = 'produk_' . time() . '_' . uniqid() . '.' . $ext;
                    $upload_dir = '../uploads/produk/';
                    
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                        if ($produk['gambar'] && file_exists($upload_dir . $produk['gambar'])) {
                            unlink($upload_dir . $produk['gambar']);
                        }
                        $gambar = $new_filename;
                    }
                }
            }
            
            $query = "UPDATE produk SET nama_produk = $1, deskripsi = $2, tahun = $3, kategori = $4, teknologi = $5, 
                      gambar = $6, link_demo = $7, link_repository = $8, personil_id = $9, updated_at = NOW() 
                      WHERE id = $10";
            $result = pg_query_params($this->conn, $query, array(
                $nama_produk, $deskripsi, $tahun, $kategori, $teknologi, $gambar, $link_demo, $link_repository, $personil_id, $id
            ));
            
            if ($result) {
                header('Location: ' . BASE_URL . '/admin/manage_produk.php?success=edit');
                exit();
            } else {
                $error = 'Gagal mengupdate produk!';
            }
        }
        
        return ['error' => $error, 'produk' => $produk];
    }
    
    // Delete produk - FULL ACCESS
    public function delete($id) {
        if ($id) {
            $query = "SELECT gambar FROM produk WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $produk = pg_fetch_assoc($result);
            
            if ($produk) {
                $delete_query = "DELETE FROM produk WHERE id = $1";
                $delete_result = pg_query_params($this->conn, $delete_query, array($id));
                
                if ($delete_result) {
                    if ($produk['gambar'] && file_exists('../uploads/produk/' . $produk['gambar'])) {
                        unlink('../uploads/produk/' . $produk['gambar']);
                    }
                    header('Location: ' . BASE_URL . '/admin/manage_produk.php?success=delete');
                } else {
                    header('Location: ' . BASE_URL . '/admin/manage_produk.php?error=delete');
                }
            } else {
                header('Location: ' . BASE_URL . '/admin/manage_produk.php?error=notfound');
            }
        } else {
            header('Location: ' . BASE_URL . '/admin/manage_produk.php?error=invalid');
        }
        exit();
    }
    
    // Get all personil for dropdown
    public function getAllPersonil() {
        $query = "SELECT id, nama FROM personil ORDER BY nama ASC";
        $result = pg_query($this->conn, $query);
        
        $personil = [];
        while ($row = pg_fetch_assoc($result)) {
            $personil[] = $row;
        }
        return $personil;
    }
}
?>
