<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';

class PenelitianController {
    private $conn;
    private $upload_dir;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        // Folder penyimpanan fisik di Laragon
        $this->upload_dir = __DIR__ . '/../../public/uploads/penelitian/';
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }

    // Menghilangkan error 'getAll' di manage_penelitian.php
    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        $search_term = pg_escape_string($this->conn, $search);
        $where = $search ? "WHERE hp.judul ILIKE '%$search_term%'" : '';
        
        $query = "SELECT hp.*, p.nama as personil_nama, kp.nama_kategori as kat_nama, kp.warna as kat_warna 
                  FROM hasil_penelitian hp
                  LEFT JOIN personil p ON hp.personil_id = p.id
                  LEFT JOIN kategori_penelitian kp ON hp.kategori_id = kp.id
                  $where 
                  ORDER BY hp.created_at DESC LIMIT $limit OFFSET $offset";
        
        $result = pg_query($this->conn, $query);
        $items = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) { $items[] = $row; }
        }
        
        $total_res = pg_query($this->conn, "SELECT COUNT(*) FROM hasil_penelitian hp $where");
        $total = pg_fetch_result($total_res, 0, 0);
        
        return [
            'items' => $items, 
            'total_pages' => ceil($total / $limit), 
            'current_page' => $page
        ];
    }

    // Menghilangkan error 'delete' di delete_penelitian.php
    public function delete($id) {
        $res = pg_query_params($this->conn, "SELECT gambar, file_pdf FROM hasil_penelitian WHERE id = $1", [$id]);
        $data = pg_fetch_assoc($res);

        if ($data) {
            if (!empty($data['gambar']) && file_exists($this->upload_dir . $data['gambar'])) {
                unlink($this->upload_dir . $data['gambar']);
            }
            if (!empty($data['file_pdf']) && file_exists($this->upload_dir . $data['file_pdf'])) {
                unlink($this->upload_dir . $data['file_pdf']);
            }
        }

        $query = "DELETE FROM hasil_penelitian WHERE id = $1";
        if (pg_query_params($this->conn, $query, [$id])) {
            header('Location: manage_penelitian.php?success=delete');
            exit();
        }
        return false;
    }

    public function getAllPersonil() {
        $res = pg_query($this->conn, "SELECT id, nama FROM personil ORDER BY nama ASC");
        $data = [];
        if($res) while ($row = pg_fetch_assoc($res)) { $data[] = $row; }
        return $data;
    }

    public function getKategoriList() {
        $res = pg_query($this->conn, "SELECT id, nama_kategori FROM kategori_penelitian WHERE is_active = TRUE ORDER BY nama_kategori ASC");
        $data = [];
        if($res) while ($row = pg_fetch_assoc($res)) { $data[] = $row; }
        return $data;
    }

    public function add() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = trim($_POST['judul']);
            $kategori_id = !empty($_POST['kategori_id']) ? intval($_POST['kategori_id']) : null;
            
            $gambar = $this->uploadFile('gambar', ['jpg', 'jpeg', 'png'], 'penelitian_');
            $file_pdf = $this->uploadFile('file_pdf', ['pdf'], 'doc_');

            $query = "INSERT INTO hasil_penelitian (judul, deskripsi, tahun, abstrak, gambar, file_pdf, link_publikasi, personil_id, kategori_id, created_at, updated_at) 
                      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, NOW(), NOW())";
            
            $params = [
                $judul, trim($_POST['deskripsi']), intval($_POST['tahun']), 
                trim($_POST['abstrak']), $gambar, $file_pdf, trim($_POST['link_publikasi']), 
                !empty($_POST['personil_id']) ? intval($_POST['personil_id']) : null, $kategori_id
            ];

            if (pg_query_params($this->conn, $query, $params)) {
                header('Location: manage_penelitian.php?success=add');
                exit();
            } else { $error = "Gagal menyimpan data."; }
        }
        return ['error' => $error];
    }

    public function edit($id) {
        $error = '';
        $res = pg_query_params($this->conn, "SELECT * FROM hasil_penelitian WHERE id = $1", [$id]);
        $penelitian = pg_fetch_assoc($res);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $gambar = $penelitian['gambar'];
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $new_img = $this->uploadFile('gambar', ['jpg', 'jpeg', 'png'], 'penelitian_');
                if ($new_img) {
                    if ($gambar && file_exists($this->upload_dir . $gambar)) unlink($this->upload_dir . $gambar);
                    $gambar = $new_img;
                }
            }

            $file_pdf = $penelitian['file_pdf'];
            if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
                $new_pdf = $this->uploadFile('file_pdf', ['pdf'], 'doc_');
                if ($new_pdf) {
                    if ($file_pdf && file_exists($this->upload_dir . $file_pdf)) unlink($this->upload_dir . $file_pdf);
                    $file_pdf = $new_pdf;
                }
            }

            $query = "UPDATE hasil_penelitian SET judul=$1, deskripsi=$2, tahun=$3, abstrak=$4, gambar=$5, file_pdf=$6, link_publikasi=$7, personil_id=$8, kategori_id=$9, updated_at=NOW() WHERE id=$10";
            $params = [
                trim($_POST['judul']), trim($_POST['deskripsi']), intval($_POST['tahun']), 
                trim($_POST['abstrak']), $gambar, $file_pdf, trim($_POST['link_publikasi']), 
                !empty($_POST['personil_id']) ? intval($_POST['personil_id']) : null, 
                !empty($_POST['kategori_id']) ? intval($_POST['kategori_id']) : null, $id
            ];

            if (pg_query_params($this->conn, $query, $params)) {
                header('Location: manage_penelitian.php?success=edit');
                exit();
            } else { $error = "Gagal memperbarui data."; }
        }
        return ['error' => $error, 'penelitian' => $penelitian];
    }

    private function uploadFile($name, $allowed, $pref) {
        if (isset($_FILES[$name]) && $_FILES[$name]['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $filename = $pref . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES[$name]['tmp_name'], $this->upload_dir . $filename)) {
                    return $filename;
                }
            }
        }
        return '';
    }
}