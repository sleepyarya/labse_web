# 🔐 Sistem Login Admin - Lab SE

## 📌 Konsep "Hidden Admin Access"

Sistem ini menggunakan konsep **hidden admin access** dimana:
- ✅ **TIDAK ADA tombol login** di halaman publik website
- ✅ Admin harus **mengetik URL secara manual**
- ✅ Halaman login **HANYA bisa diakses** via URL langsung
- ✅ Semua halaman admin **dilindungi dengan session**

## 🚪 Cara Akses Admin

### Step 1: Akses URL Login
Ketik secara manual di browser:
```
http://localhost/labse_web/admin/login.php
```

### Step 2: Login dengan Credentials
**Default Login:**
- **Username:** `admin`
- **Password:** `admin123`

atau

- **Username:** `superadmin`
- **Password:** `admin123`

### Step 3: Akses Dashboard
Setelah login berhasil, otomatis redirect ke:
```
http://localhost/labse_web/admin/index.php
```

## 🗂️ Struktur File Admin

```
admin/
├── login.php           # Halaman login (tidak dilindungi)
├── auth_check.php      # File proteksi session
├── logout.php          # Proses logout
├── index.php           # Dashboard utama (dilindungi)
├── add_personil.php    # Kelola personil (dilindungi)
├── add_artikel.php     # Kelola artikel (dilindungi)
└── add_mahasiswa.php   # Kelola mahasiswa (dilindungi)
```

## 🔒 Cara Kerja Sistem Keamanan

### 1. Login Process
```php
// File: login.php
- User input username & password
- Query database admin_users
- Verify password dengan password_verify()
- Jika cocok: buat session
- Redirect ke dashboard
```

### 2. Session Protection
```php
// File: auth_check.php
- Cek session admin_logged_in
- Jika tidak ada: redirect ke login.php
- Jika ada: lanjutkan ke halaman
```

### 3. Logout Process
```php
// File: logout.php
- Hapus semua session
- Redirect ke login.php dengan pesan success
```

## 🗄️ Database Admin

### Tabel: `admin_users`
```sql
CREATE TABLE admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,         -- Hash dengan password_hash()
    nama_lengkap VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    last_login TIMESTAMP,                   -- Auto update saat login
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Sample Data
```sql
-- Password: "admin123"
INSERT INTO admin_users (username, password, nama_lengkap, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@labse.ac.id'),
('superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Administrator', 'superadmin@labse.ac.id');
```

## 🔐 Menambah Admin Baru

### Via SQL Query
```sql
INSERT INTO admin_users (username, password, nama_lengkap, email) VALUES
('username_baru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nama Lengkap', 'email@example.com');
-- Password di atas adalah "admin123"
```

### Generate Password Hash Baru
Buat file PHP temporary:
```php
<?php
// generate_password.php
$password = 'password_baru_anda';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password Hash: " . $hash;
?>
```

Akses via browser, copy hash-nya, lalu masukkan ke database.

## 🛡️ Fitur Keamanan

### ✅ Yang Sudah Ada:
1. **Password Hashing** - Password tidak disimpan plain text
2. **Session Management** - Menggunakan PHP session
3. **SQL Injection Protection** - Menggunakan `pg_query_params()`
4. **XSS Protection** - Menggunakan `htmlspecialchars()`
5. **Hidden Admin URL** - Tidak ada link publik ke admin
6. **Auto Redirect** - Jika sudah login, tidak bisa akses login.php lagi

### 🔜 Yang Bisa Ditambahkan:
1. **CSRF Protection** - Token untuk form
2. **Brute Force Protection** - Limit login attempts
3. **2FA (Two Factor Auth)** - Via email/SMS
4. **Activity Log** - Track semua aktivitas admin
5. **IP Whitelist** - Hanya IP tertentu bisa akses
6. **Session Timeout** - Auto logout setelah idle

## 📱 Session Data yang Tersimpan

Setelah login berhasil, session berisi:
```php
$_SESSION['admin_logged_in'] = true;              // Status login
$_SESSION['admin_id'] = 1;                        // ID admin
$_SESSION['admin_username'] = 'admin';            // Username
$_SESSION['admin_nama'] = 'Administrator';        // Nama lengkap
$_SESSION['admin_email'] = 'admin@labse.ac.id';   // Email
```

## 🚀 Testing Login

### Test 1: Akses Dashboard Tanpa Login
1. Buka: `http://localhost/labse_web/admin/index.php`
2. **Expected:** Auto redirect ke `login.php`

### Test 2: Login dengan Credentials Salah
1. Buka: `http://localhost/labse_web/admin/login.php`
2. Input username/password salah
3. **Expected:** Error message muncul

### Test 3: Login Berhasil
1. Buka: `http://localhost/labse_web/admin/login.php`
2. Input: `admin` / `admin123`
3. **Expected:** Redirect ke dashboard, nama muncul di header

### Test 4: Logout
1. Klik tombol "Logout" di dashboard
2. **Expected:** Redirect ke login.php dengan pesan success
3. Coba akses dashboard lagi → harus redirect ke login

## 🔧 Troubleshooting

### ❌ Session Tidak Tersimpan
**Solusi:**
1. Cek `php.ini`: `session.save_path` harus writable
2. Pastikan `session_start()` dipanggil sebelum output apapun
3. Clear browser cookies

### ❌ Infinite Redirect Loop
**Solusi:**
1. Clear browser cache & cookies
2. Cek apakah ada multiple `session_start()`
3. Pastikan tidak ada output sebelum `header()`

### ❌ Password Tidak Match
**Solusi:**
1. Re-import database dengan SQL terbaru
2. Generate password hash baru dengan PHP
3. Pastikan menggunakan `password_verify()` bukan `==`

## 📋 Checklist Update Database

Setelah membuat sistem login, jangan lupa:
- [ ] Drop database lama (jika ada)
- [ ] Create database baru: `labse`
- [ ] Import file `database/labse.sql` yang sudah diupdate
- [ ] Verify tabel `admin_users` sudah ada
- [ ] Test login dengan credentials default

## 🎯 Next Steps

Sistem login sudah selesai! Anda bisa:
1. ✅ Login ke admin dashboard
2. ✅ Lihat statistik real-time
3. ✅ Navigasi ke halaman kelola data
4. 🔜 Implement CRUD untuk personil, artikel, mahasiswa

---

**Catatan Keamanan:**
> Untuk production, **WAJIB** ubah password default dan tambahkan HTTPS!
