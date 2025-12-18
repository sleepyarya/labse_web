<?php
// Controller: Artikel Controller (Admin)
// Fix: Jalur upload folder, Keamanan SQL Injection, dan Sinkronisasi Key

require_once __DIR__ . '/../../includes/config.php'; // Pastikan koneksi DB tersedia lewat config atau database.php

class ArtikelController {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    public function getKategoriList() {
        $query = "SELECT id, nama_kategori, warna FROM kategori_artikel WHERE is_active = TRUE ORDER BY nama_kategori ASC";
        $result = pg_query($this->conn, $query);
        $list = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $list[] = $row;
            }
        }
        return $list;
    }

    public function getPersonilList() {
        $query = "SELECT id, nama FROM personil ORDER BY nama ASC";
        $result = pg_query($this->conn, $query);
        $personil = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $personil[] = $row;
            }
        }
        return $personil;
    }

    // FUNGSI ADD
    public function add() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = trim($_POST['judul'] ?? '');
            $isi = trim($_POST['isi'] ?? '');
            $penulis = trim($_POST['penulis'] ?? '');
            $kategori_id = !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            $personil_id = !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : null;
            
            if (empty($judul) || empty($isi)) {
                $error = 'Judul dan isi artikel wajib diisi!';
            } else {
                $gambar = '';
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $new_filename = 'artikel_' . time() . '_' . uniqid() . '.' . $ext;
                        
                        // FIX PATH: Naik 2 tingkat dari admin/controllers/ ke root
                        $upload_dir = __DIR__ . '/../../public/uploads/artikel/';
                        
                        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
                        
                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                            $gambar = $new_filename;
                        }
                    }
                }
                
                $query = "INSERT INTO artikel (judul, isi, penulis, gambar, personil_id, kategori_id, created_at, updated_at)
                          VALUES ($1, $2, $3, $4, $5, $6, NOW(), NOW())";

                $result = pg_query_params($this->conn, $query, array($judul, $isi, $penulis, $gambar, $personil_id, $kategori_id));
                
                if ($result) {
                    header('Location: manage_artikel.php?success=add');
                    exit();
                } else {
                    $error = 'Gagal menyimpan ke database.';
                }
            }
        }
        return ['error' => $error];
    }
    
    // FUNGSI EDIT (FIXED LOGIC)
    public function edit($id) {
        $error = '';
        
        // Ambil data lama dulu
        $query_old = "SELECT * FROM artikel WHERE id = $1";
        $res_old = pg_query_params($this->conn, $query_old, array($id));
        $artikel = pg_fetch_assoc($res_old);

        if (!$artikel) return ['error' => 'Data tidak ditemukan!', 'artikel' => null];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = trim($_POST['judul'] ?? '');
            $isi = trim($_POST['isi'] ?? '');
            $penulis = trim($_POST['penulis'] ?? '');
            $kategori_id = !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
            $personil_id = !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : null;
            
            $gambar = $artikel['gambar']; // Default gunakan gambar lama

            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $new_filename = 'artikel_' . time() . '_' . uniqid() . '.' . $ext;
                    $upload_dir = __DIR__ . '/../../public/uploads/artikel/';

                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                        // Hapus gambar lama jika ada
                        if ($gambar && file_exists($upload_dir . $gambar)) {
                            unlink($upload_dir . $gambar);
                        }
                        $gambar = $new_filename; // Update ke nama file baru
                    }
                }
            }
            
            $query = "UPDATE artikel SET judul = $1, isi = $2, penulis = $3, gambar = $4, 
                      personil_id = $5, kategori_id = $6, updated_at = NOW() WHERE id = $7";
                      
            $result = pg_query_params($this->conn, $query, array($judul, $isi, $penulis, $gambar, $personil_id, $kategori_id, $id));
            
            if ($result) {
                header('Location: manage_artikel.php?success=edit');
                exit();
            } else {
                $error = 'Gagal memperbarui database.';
            }
        }
        
        return ['error' => $error, 'artikel' => $artikel];
    }

    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        
        // Gunakan parameter binding untuk search agar aman
        $params = [];
        $where = "";
        if ($search) {
            $where = "WHERE a.judul ILIKE $1 OR a.isi ILIKE $1 OR a.penulis ILIKE $1";
            $params[] = "%$search%";
        }
        
        $count_query = "SELECT COUNT(*) as total FROM artikel a $where";
        $count_res = pg_query_params($this->conn, $count_query, $params);
        $total_records = pg_fetch_assoc($count_res)['total'];
        $total_pages = ceil($total_records / $limit);
        
        $query = "SELECT a.*, p.nama as personil_nama, k.nama_kategori as kat_nama, k.warna as kat_warna 
                  FROM artikel a
                  LEFT JOIN personil p ON a.personil_id = p.id
                  LEFT JOIN kategori_artikel k ON a.kategori_id = k.id
                  $where 
                  ORDER BY a.created_at DESC 
                  LIMIT $limit OFFSET $offset";
                  
        $result = pg_query_params($this->conn, $query, $params);
        $items = [];
        while ($row = pg_fetch_assoc($result)) {
            $items[] = $row;
        }
        
        return [
            'articles' => $items, // Penting: Gunakan 'articles' agar cocok dengan View
            'total_records' => $total_records, 
            'total_pages' => $total_pages, 
            'current_page' => $page
        ];
    }
    
    public function delete($id) {
        $q_img = "SELECT gambar FROM artikel WHERE id = $1";
        $res_img = pg_query_params($this->conn, $q_img, array($id));
        $data = pg_fetch_assoc($res_img);

        $q = "DELETE FROM artikel WHERE id = $1";
        if (pg_query_params($this->conn, $q, array($id))) {
            if ($data['gambar']) {
                $path = __DIR__ . '/../../public/uploads/artikel/' . $data['gambar'];
                if (file_exists($path)) unlink($path);
            }
            header('Location: manage_artikel.php?success=delete');
            exit();
        }
    }
}