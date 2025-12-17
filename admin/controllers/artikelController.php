<?php
// Controller: Artikel Controller (Admin)
// Fix: Sinkronisasi nama fungsi getPersonilList dan Return Key 'articles'

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';

class ArtikelController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Ambil daftar kategori
    public function getKategoriList() {
        $query = "SELECT id, nama_kategori FROM kategori_artikel WHERE is_active = TRUE ORDER BY nama_kategori ASC";
        $result = pg_query($this->conn, $query);
        $list = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $list[] = $row;
            }
        }
        return $list;
    }

    // PERBAIKAN: Ganti nama fungsi jadi 'getPersonilList' agar cocok dengan artikel_form.php
    public function getPersonilList() {
        $query = "SELECT id, nama FROM personil ORDER BY nama ASC";
        $result = pg_query($this->conn, $query);
        $personil = [];
        while ($row = pg_fetch_assoc($result)) {
            $personil[] = $row;
        }
        return $personil;
    }

    // Helper: Cari Nama Kategori berdasarkan ID
    private function getNamaKategoriById($id) {
        if (!$id) return '';
        $query = "SELECT nama_kategori FROM kategori_artikel WHERE id = $1";
        $result = pg_query_params($this->conn, $query, array($id));
        if ($result && pg_num_rows($result) > 0) {
            return pg_fetch_result($result, 0, 0);
        }
        return '';
    }

    // ADD DATA
    public function add() {
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = pg_escape_string($this->conn, trim($_POST['judul'] ?? ''));
            $isi = pg_escape_string($this->conn, trim($_POST['isi'] ?? ''));
            $penulis = pg_escape_string($this->conn, trim($_POST['penulis'] ?? ''));
            
            // LOGIKA HYBRID
            $kategori_id = isset($_POST['kategori_id']) && !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            // $kategori_text = $this->getNamaKategoriById($kategori_id); // Opsional jika tabel artikel punya kolom teks kategori
            
            $personil_id = isset($_POST['personil_id']) && !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : null;
            
            if (empty($judul) || empty($isi)) {
                $error = 'Judul dan isi artikel wajib diisi!';
            } else {
                $gambar = '';
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $new_filename = 'artikel_' . time() . '_' . uniqid() . '.' . $ext;
                        $upload_dir = '../public/uploads/artikel/';
                        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
                        move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename);
                        $gambar = $new_filename;
                    }
                }
                
                // Query INSERT
                $query = "INSERT INTO artikel 
                          (judul, isi, penulis, gambar, personil_id, kategori_id, created_at, updated_at)
                          VALUES ($1, $2, $3, $4, $5, $6, NOW(), NOW())";

                $result = pg_query_params($this->conn, $query, array(
                    $judul, $isi, $penulis, $gambar, $personil_id, $kategori_id
                ));
                
                if ($result) {
                    header('Location: manage_artikel.php?success=add');
                    exit();
                } else {
                    $error = 'Gagal menambahkan artikel: ' . pg_last_error($this->conn);
                }
            }
        }
        return ['error' => $error];
    }
    
    // EDIT DATA
    public function edit($id) {
        $error = '';
        $artikel = null;
        
        if ($id) {
            $query = "SELECT * FROM artikel WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $artikel = pg_fetch_assoc($result);
            if (!$artikel) {
                return ['error' => 'Data tidak ditemukan!', 'artikel' => null];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = pg_escape_string($this->conn, trim($_POST['judul'] ?? ''));
            $isi = pg_escape_string($this->conn, trim($_POST['isi'] ?? ''));
            $penulis = pg_escape_string($this->conn, trim($_POST['penulis'] ?? ''));
            
            $kategori_id = isset($_POST['kategori_id']) && !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            $personil_id = isset($_POST['personil_id']) && !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : null;
            
            if (empty($judul)) {
                $error = 'Judul wajib diisi!';
            } else {
                $gambar = $artikel['gambar'];
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $new_filename = 'artikel_' . time() . '_' . uniqid() . '.' . $ext;
                        $upload_dir = '../public/uploads/artikel/';
                        move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename);
                        if ($gambar && file_exists($upload_dir . $gambar)) unlink($upload_dir . $gambar);
                        $gambar = $new_filename;
                    }
                }
                
                $query = "UPDATE artikel 
                          SET judul = $1, isi = $2, penulis = $3, gambar = $4, 
                              personil_id = $5, kategori_id = $6, updated_at = NOW() 
                          WHERE id = $7";
                          
                $result = pg_query_params($this->conn, $query, array(
                    $judul, $isi, $penulis, $gambar, $personil_id, $kategori_id, $id
                ));
                
                if ($result) {
                    header('Location: manage_artikel.php?success=edit');
                    exit();
                } else {
                    $error = 'Gagal update: ' . pg_last_error($this->conn);
                }
            }
        }
        
        return ['error' => $error, 'artikel' => $artikel];
    }

    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        $where = $search ? "WHERE a.judul ILIKE '%$search%'" : '';
        
        $count_query = "SELECT COUNT(*) as total FROM artikel a $where";
        $total_records = pg_fetch_assoc(pg_query($this->conn, $count_query))['total'];
        $total_pages = ceil($total_records / $limit);
        
        $query = "SELECT a.*, p.nama as personil_nama, k.nama_kategori as kat_nama, k.warna as kat_warna 
                  FROM artikel a
                  LEFT JOIN personil p ON a.personil_id = p.id
                  LEFT JOIN kategori_artikel k ON a.kategori_id = k.id
                  $where 
                  ORDER BY a.created_at DESC 
                  LIMIT $limit OFFSET $offset";
        $result = pg_query($this->conn, $query);
        
        $items = [];
        while ($row = pg_fetch_assoc($result)) {
            $items[] = $row;
        }
        
        // PERBAIKAN: Menggunakan key 'articles' agar sesuai View
        return [
            'articles' => $items, 
            'total_records' => $total_records, 
            'total_pages' => $total_pages, 
            'current_page' => $page
        ];
    }
    
    public function delete($id) {
        if ($id) {
            $q_img = "SELECT gambar FROM artikel WHERE id = $1";
            $res_img = pg_query_params($this->conn, $q_img, array($id));
            $img = pg_fetch_result($res_img, 0, 0);

            $q = "DELETE FROM artikel WHERE id = $1";
            if (pg_query_params($this->conn, $q, array($id))) {
                if ($img && file_exists('../public/uploads/artikel/' . $img)) {
                    unlink('../public/uploads/artikel/' . $img);
                }
                header('Location: manage_artikel.php?success=delete');
            } else {
                header('Location: manage_artikel.php?error=delete');
            }
            exit();
        }
    }
    
    public function getById($id) {
        $query = "SELECT * FROM artikel WHERE id = $1";
        $result = pg_query_params($this->conn, $query, array($id));
        return pg_fetch_assoc($result);
    }
}
?>