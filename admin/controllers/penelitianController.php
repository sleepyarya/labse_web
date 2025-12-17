<?php
// Controller: Penelitian Controller (Admin)
// Fix: Hybrid Saving (Simpan ID + Simpan Teks Legacy agar pasti tampil)

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';

class PenelitianController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Ambil daftar kategori untuk dropdown
    public function getKategoriList() {
        $query = "SELECT id, nama_kategori FROM kategori_penelitian WHERE is_active = TRUE ORDER BY nama_kategori ASC";
        $result = pg_query($this->conn, $query);
        $list = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $list[] = $row;
            }
        }
        return $list;
    }

    public function getAllPersonil() {
        $query = "SELECT id, nama FROM personil ORDER BY nama ASC";
        $result = pg_query($this->conn, $query);
        $personil = [];
        while ($row = pg_fetch_assoc($result)) {
            $personil[] = $row;
        }
        return $personil;
    }

    // Helper: Ambil Nama Kategori berdasarkan ID
    private function getNamaKategoriById($id) {
        if (!$id) return '';
        $query = "SELECT nama_kategori FROM kategori_penelitian WHERE id = $1";
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
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi'] ?? ''));
            $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
            
            // LOGIKA BARU: Ambil ID DAN Nama Kategorinya
            $kategori_id = isset($_POST['kategori_id']) && !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            // Kita cari namanya di database lalu simpan juga sebagai teks (Legacy Support)
            $kategori_text = $this->getNamaKategoriById($kategori_id);
            
            $abstrak = pg_escape_string($this->conn, trim($_POST['abstrak'] ?? ''));
            $link_publikasi = pg_escape_string($this->conn, trim($_POST['link_publikasi'] ?? ''));
            $personil_id = isset($_POST['personil_id']) && !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : null;
            
            if (empty($judul) || empty($deskripsi) || empty($tahun)) {
                $error = 'Judul, deskripsi, dan tahun wajib diisi!';
            } else {
                // Upload Gambar
                $gambar = '';
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $new_filename = 'penelitian_' . time() . '_' . uniqid() . '.' . $ext;
                        $upload_dir = '../public/uploads/penelitian/';
                        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
                        move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename);
                        $gambar = $new_filename;
                    }
                }
                
                // Upload PDF
                $file_pdf = '';
                if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
                    $ext = strtolower(pathinfo($_FILES['file_pdf']['name'], PATHINFO_EXTENSION));
                    if ($ext == 'pdf') {
                        $new_filename = 'penelitian_' . time() . '_' . uniqid() . '.pdf';
                        $upload_dir = '../public/uploads/penelitian/';
                        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
                        move_uploaded_file($_FILES['file_pdf']['tmp_name'], $upload_dir . $new_filename);
                        $file_pdf = $new_filename;
                    }
                }
                
                // Query INSERT (Simpan ID dan TEXT sekaligus)
                $query = "INSERT INTO hasil_penelitian 
                          (judul, deskripsi, tahun, kategori, abstrak, gambar, file_pdf, link_publikasi, personil_id, kategori_id, created_at, updated_at)
                          VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, NOW(), NOW())";

                $result = pg_query_params($this->conn, $query, array(
                    $judul, $deskripsi, $tahun, $kategori_text, $abstrak, 
                    $gambar, $file_pdf, $link_publikasi, $personil_id, 
                    $kategori_id
                ));
                
                if ($result) {
                    header('Location: manage_penelitian.php?success=add');
                    exit();
                } else {
                    $error = 'Gagal menambahkan penelitian: ' . pg_last_error($this->conn);
                }
            }
        }
        return ['error' => $error];
    }
    
    // EDIT DATA
    public function edit($id) {
        $error = '';
        $penelitian = null;
        
        if ($id) {
            $query = "SELECT * FROM hasil_penelitian WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $penelitian = pg_fetch_assoc($result);
            if (!$penelitian) {
                return ['error' => 'Data tidak ditemukan!', 'penelitian' => null];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = pg_escape_string($this->conn, trim($_POST['judul'] ?? ''));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi'] ?? ''));
            $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
            
            // LOGIKA BARU: Update ID dan TEXT
            $kategori_id = isset($_POST['kategori_id']) && !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            // Ambil nama kategori dari database berdasarkan ID yang dipilih
            $kategori_text = $this->getNamaKategoriById($kategori_id);
            // Jika user tidak pilih kategori, biarkan teks lama (opsional) atau kosongkan
            if (!$kategori_text && isset($penelitian['kategori'])) {
               // $kategori_text = $penelitian['kategori']; // Uncomment jika ingin mempertahankan teks lama saat unselect
            }
            
            $abstrak = pg_escape_string($this->conn, trim($_POST['abstrak'] ?? ''));
            $link_publikasi = pg_escape_string($this->conn, trim($_POST['link_publikasi'] ?? ''));
            $personil_id = isset($_POST['personil_id']) && !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : null;
            
            if (empty($judul)) {
                $error = 'Judul wajib diisi!';
            } else {
                $gambar = $penelitian['gambar'];
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $new_filename = 'penelitian_' . time() . '_' . uniqid() . '.' . $ext;
                        $upload_dir = '../public/uploads/penelitian/';
                        move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename);
                        $gambar = $new_filename;
                    }
                }
                
                $file_pdf = $penelitian['file_pdf'];
                if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
                    if (strtolower(pathinfo($_FILES['file_pdf']['name'], PATHINFO_EXTENSION)) == 'pdf') {
                        $new_filename = 'penelitian_' . time() . '_' . uniqid() . '.pdf';
                        $upload_dir = '../public/uploads/penelitian/';
                        move_uploaded_file($_FILES['file_pdf']['tmp_name'], $upload_dir . $new_filename);
                        $file_pdf = $new_filename;
                    }
                }
                
                // Update Query: Simpan TEXT ($4) dan ID ($10)
                $query = "UPDATE hasil_penelitian 
                          SET judul = $1, deskripsi = $2, tahun = $3, kategori = $4, abstrak = $5, 
                              gambar = $6, file_pdf = $7, link_publikasi = $8, personil_id = $9, 
                              kategori_id = $10, updated_at = NOW() 
                          WHERE id = $11";
                          
                $result = pg_query_params($this->conn, $query, array(
                    $judul, $deskripsi, $tahun, $kategori_text, $abstrak, 
                    $gambar, $file_pdf, $link_publikasi, $personil_id, 
                    $kategori_id, $id
                ));
                
                if ($result) {
                    header('Location: manage_penelitian.php?success=edit');
                    exit();
                } else {
                    $error = 'Gagal update: ' . pg_last_error($this->conn);
                }
            }
        }
        
        return ['error' => $error, 'penelitian' => $penelitian];
    }

    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        $where = $search ? "WHERE hp.judul ILIKE '%$search%'" : '';
        
        $count_query = "SELECT COUNT(*) as total FROM hasil_penelitian hp $where";
        $total_records = pg_fetch_assoc(pg_query($this->conn, $count_query))['total'];
        $total_pages = ceil($total_records / $limit);
        
        $query = "SELECT hp.*, p.nama as personil_nama, kp.nama_kategori as kat_nama 
                  FROM hasil_penelitian hp
                  LEFT JOIN personil p ON hp.personil_id = p.id
                  LEFT JOIN kategori_penelitian kp ON hp.kategori_id = kp.id
                  $where 
                  ORDER BY hp.created_at DESC 
                  LIMIT $limit OFFSET $offset";
        $result = pg_query($this->conn, $query);
        
        $items = [];
        while ($row = pg_fetch_assoc($result)) {
            $items[] = $row;
        }
        
        return [
            'items' => $items, 
            'total_records' => $total_records, 
            'total_pages' => $total_pages, 
            'current_page' => $page
        ];
    }
    
    public function delete($id) {
        if ($id) {
            $q = "DELETE FROM hasil_penelitian WHERE id = $1";
            if (pg_query_params($this->conn, $q, array($id))) {
                header('Location: manage_penelitian.php?success=delete');
            } else {
                header('Location: manage_penelitian.php?error=delete');
            }
            exit();
        }
    }
    
    public function getById($id) {
        $query = "SELECT * FROM hasil_penelitian WHERE id = $1";
        $result = pg_query_params($this->conn, $query, array($id));
        return pg_fetch_assoc($result);
    }
}
?>