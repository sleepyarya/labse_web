<?php
// Controller: Pengabdian Controller
// Description: Handles CRUD operations for pengabdian (community service/training) management

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/session.php';

class PengabdianController
{

    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // Add new pengabdian
    public function add()
    {
        $error = '';
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = pg_escape_string($this->conn, trim($_POST['judul']));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi']));
            $tanggal = pg_escape_string($this->conn, trim($_POST['tanggal']));
            $lokasi = pg_escape_string($this->conn, trim($_POST['lokasi']));
            $penyelenggara = pg_escape_string($this->conn, trim($_POST['penyelenggara']));
            $personil_id = isset($_POST['personil_id']) && !empty($_POST['personil_id']) ? (int)$_POST['personil_id'] : null;

            // Validation
            if (empty($judul) || empty($deskripsi) || empty($tanggal) || empty($lokasi) || empty($penyelenggara)) {
                $error = 'Semua field wajib diisi!';
            } else {
                // Handle file upload
                $gambar = '';
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $new_filename = 'pengabdian_' . time() . '_' . uniqid() . '.' . $ext;
                        
                        // FIXED: Gunakan path yang konsisten (tanpa /public/)
                        $upload_dir = '../uploads/pengabdian/';

                        // Create directory if not exists
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                            $gambar = $new_filename;
                        }
                    } else {
                        $error = 'Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
                    }
                }

                // Insert to database
                if (empty($error)) {
                    $query = "SELECT sp_create_pengabdian($1, $2, $3, $4, $5, $6, $7)";

                    $params = array(
                        $judul,           // $1
                        $deskripsi,       // $2
                        $tanggal,         // $3
                        $lokasi,          // $4
                        $penyelenggara,   // $5
                        $gambar,          // $6
                        $personil_id      // $7
                    );

                    $result = pg_query_params($this->conn, $query, $params);

                    if ($result) {
                        // FIXED: Redirect ke file sibling
                        header('Location: manage_pengabdian.php?success=add');
                        exit();
                    } else {
                        $error = 'Gagal menambahkan pengabdian: ' . pg_last_error($this->conn);
                    }
                }
            }
        }

        return ['error' => $error, 'success' => $success];
    }

    // Get all personil for dropdown
    public function getAllPersonil()
    {
        $query = "SELECT id, nama FROM personil ORDER BY nama ASC";
        $result = pg_query($this->conn, $query);

        $personil = [];
        while ($row = pg_fetch_assoc($result)) {
            $personil[] = $row;
        }
        return $personil;
    }

    // Edit pengabdian
    public function edit($id)
    {
        $error = '';
        $success = false;
        $pengabdian = null;

        // Get pengabdian data
        if ($id) {
            $query = "SELECT * FROM pengabdian WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $pengabdian = pg_fetch_assoc($result);

            if (!$pengabdian) {
                $error = 'Data pengabdian tidak ditemukan!';
                return ['error' => $error, 'pengabdian' => null];
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = pg_escape_string($this->conn, trim($_POST['judul']));
            $deskripsi = pg_escape_string($this->conn, trim($_POST['deskripsi']));
            $tanggal = pg_escape_string($this->conn, trim($_POST['tanggal']));
            $lokasi = pg_escape_string($this->conn, trim($_POST['lokasi']));
            $penyelenggara = pg_escape_string($this->conn, trim($_POST['penyelenggara']));

            // Validation
            if (empty($judul) || empty($deskripsi) || empty($tanggal) || empty($lokasi) || empty($penyelenggara)) {
                $error = 'Semua field wajib diisi!';
            } else {
                // Handle file upload
                $gambar = $pengabdian['gambar']; // Keep existing image by default
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $new_filename = 'pengabdian_' . time() . '_' . uniqid() . '.' . $ext;
                        
                        // FIXED: Path upload disamakan
                        $upload_dir = '../uploads/pengabdian/';

                        // Create directory if not exists
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                            // Delete old image
                            if ($pengabdian['gambar'] && file_exists($upload_dir . $pengabdian['gambar'])) {
                                unlink($upload_dir . $pengabdian['gambar']);
                            }
                            $gambar = $new_filename;
                        }
                    } else {
                        $error = 'Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
                    }
                }

                // Update database
                if (empty($error)) {
                    $query = "UPDATE pengabdian SET judul = $1, deskripsi = $2, tanggal = $3, lokasi = $4, penyelenggara = $5, gambar = $6, updated_at = NOW() 
                              WHERE id = $7";
                    $result = pg_query_params($this->conn, $query, array($judul, $deskripsi, $tanggal, $lokasi, $penyelenggara, $gambar, $id));

                    if ($result) {
                        // FIXED: Redirect ke file sibling
                        header('Location: manage_pengabdian.php?success=edit');
                        exit();
                    } else {
                        $error = 'Gagal mengupdate pengabdian: ' . pg_last_error($this->conn);
                    }
                }
            }
        }

        return ['error' => $error, 'success' => $success, 'pengabdian' => $pengabdian];
    }

    // Delete pengabdian
    public function delete($id)
    {
        if ($id) {
            // Get pengabdian data first to delete image
            $query = "SELECT gambar FROM pengabdian WHERE id = $1";
            $result = pg_query_params($this->conn, $query, array($id));
            $pengabdian = pg_fetch_assoc($result);

            if ($pengabdian) {
                // Delete from database
                $delete_query = "DELETE FROM pengabdian WHERE id = $1";
                $delete_result = pg_query_params($this->conn, $delete_query, array($id));

                if ($delete_result) {
                    // FIXED: Path hapus disamakan dengan path upload
                    if ($pengabdian['gambar'] && file_exists('../uploads/pengabdian/' . $pengabdian['gambar'])) {
                        unlink('../uploads/pengabdian/' . $pengabdian['gambar']);
                    }
                    
                    // FIXED: Redirect ke file sibling
                    header('Location: manage_pengabdian.php?success=delete');
                } else {
                    header('Location: manage_pengabdian.php?error=delete');
                }
            } else {
                header('Location: manage_pengabdian.php?error=notfound');
            }
        } else {
            header('Location: manage_pengabdian.php?error=invalid');
        }
        exit();
    }

    // Get all pengabdian with pagination
    public function getAll($page = 1, $limit = 10, $search = '')
    {
        $offset = ($page - 1) * $limit;

        // Search functionality
        $where = $search ? "WHERE judul ILIKE '%$search%' OR penyelenggara ILIKE '%$search%' OR lokasi ILIKE '%$search%' OR deskripsi ILIKE '%$search%'" : '';

        // Get total records
        $count_query = "SELECT COUNT(*) as total FROM pengabdian $where";
        $count_result = pg_query($this->conn, $count_query);
        $total_records = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_records / $limit);

        // Get pengabdian data
        $query = "SELECT * FROM pengabdian $where ORDER BY tanggal DESC, created_at DESC LIMIT $limit OFFSET $offset";
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
}