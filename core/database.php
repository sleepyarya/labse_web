<?php
// Core: Database Connection Handler

// --- KONFIGURASI DOCKER ---
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_PORT')) define('DB_PORT', '5432');
if (!defined('DB_NAME')) define('DB_NAME', 'labse');
if (!defined('DB_USER')) define('DB_USER', 'postgres');
if (!defined('DB_PASS')) define('DB_PASS', '123');

// Base URL (Port 8888 sesuai Nginx di Docker)
// Base URL (Port 8888 sesuai Nginx di Docker)
if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost:8888/labse_web');
function getConnection()
{
    $conn_string = "host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS;

    // Menggunakan @ untuk menyembunyikan warning php standar
    $conn = @pg_connect($conn_string);

    if (!$conn) {
        // FIX: Gunakan error_get_last() karena pg_last_error() error jika tidak ada koneksi
        $error = error_get_last();
        $error_msg = isset($error['message']) ? $error['message'] : 'Koneksi gagal tanpa pesan error spesifik.';

        // Bersihkan pesan error agar lebih mudah dibaca (opsional)
        $error_msg = str_replace("pg_connect(): ", "", $error_msg);

        die("<div style='font-family: sans-serif; padding: 20px; border: 1px solid #f5c6cb; background: #f8d7da; color: #721c24; border-radius: 5px;'>
                <h3>🚫 Koneksi Database Gagal!</h3>
                <p>Sistem tidak bisa terhubung ke database PostgreSQL di Docker.</p>
                <hr>
                <strong>Penyebab Error:</strong><br>
                <code>" . $error_msg . "</code>
                <br><br>
                <strong>Solusi:</strong>
                <ul>
                    <li>Pastikan container database bernama <b>db_postgres</b> sudah jalan (cek Docker Desktop).</li>
                    <li>Pastikan username/password di <b>docker-compose.yml</b> sama dengan di sini.</li>
                </ul>
             </div>");
    }
    return $conn;
}

$conn = getConnection();
date_default_timezone_set('Asia/Jakarta');
