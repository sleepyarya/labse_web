# 🔐 Panduan Implementasi Sistem Login Terpusat

## 📋 Daftar Isi
1. [Overview](#overview)
2. [Langkah-langkah Implementasi](#langkah-langkah-implementasi)
3. [Struktur Tabel Users](#struktur-tabel-users)
4. [Migrasi Data](#migrasi-data)
5. [Testing](#testing)
6. [Troubleshooting](#troubleshooting)

---

## 🎯 Overview

Sistem login telah diperbarui untuk menggunakan **tabel `users` sebagai pusat autentikasi**. Ini memungkinkan:

- ✅ Satu halaman login untuk semua role (admin, personil, mahasiswa)
- ✅ Password terenkripsi menggunakan `password_hash()`
- ✅ Sinkronisasi otomatis antara tabel users dan tabel asli
- ✅ Manajemen user yang lebih mudah dan aman
- ✅ Dashboard dan tampilan tetap sama, hanya sistem login yang berubah

### 📁 File-file Baru

```
labse_web/
├── login.php                              # Halaman login pusat (BARU)
├── core/
│   └── UserSyncService.php                # Service sinkronisasi users (BARU)
├── database/
│   ├── create_users_table.sql             # SQL create table users (BARU)
│   └── migrate_to_users.php               # Script migrasi data (BARU)
└── admin/
    └── controllers/
        └── adminController.php            # Controller untuk manage admin (BARU)
```

### 🔄 File-file yang Diupdate

```
labse_web/
├── admin/
│   ├── login.php                          # Redirect ke login pusat
│   └── controllers/
│       └── personilController.php         # Tambah sinkronisasi users
└── member/
    └── login.php                          # Redirect ke login pusat
```

---

## 🚀 Langkah-langkah Implementasi

### **Step 1: Backup Database**

**PENTING!** Backup database Anda terlebih dahulu:

```bash
# PostgreSQL
pg_dump -U postgres labse > backup_labse_$(date +%Y%m%d_%H%M%S).sql
```

### **Step 2: Jalankan SQL Create Table**

Buka pgAdmin atau psql dan jalankan file SQL:

```bash
cd c:\laragon\www\labse_web\database
psql -U postgres -d labse -f create_users_table.sql
```

Atau dari pgAdmin:
1. Buka database `labse`
2. Klik kanan → **Query Tool**
3. Buka file `create_users_table.sql`
4. Klik **Execute** (F5)

### **Step 3: Migrasi Data ke Tabel Users**

Jalankan script migrasi untuk memindahkan data dari `admin_users` dan `personil` ke tabel `users`:

```bash
cd c:\laragon\www\labse_web\database
php migrate_to_users.php
```

Script ini akan:
- ✅ Membaca semua admin dari `admin_users`
- ✅ Membaca semua personil yang `is_member = TRUE`
- ✅ Insert ke tabel `users` dengan role yang sesuai
- ✅ Menampilkan progress dan summary

**Output yang diharapkan:**
```
====================================================
  MIGRASI DATA KE TABEL USERS
====================================================

[INFO] Memeriksa tabel users...
[SUCCESS] Tabel users ditemukan ✓

[INFO] Menghitung data yang akan dimigrasi...
[INFO] Admin: 2 records
[INFO] Personil (Member): 3 records
[INFO] Total yang akan dimigrasi: 5 records

Apakah Anda ingin melanjutkan migrasi? (y/n): y

[SUCCESS] ✓ Admin 'admin' berhasil dimigrasi
[SUCCESS] ✓ Admin 'superadmin' berhasil dimigrasi
[SUCCESS] ✓ Personil 'Dr. Ahmad Fauzi' berhasil dimigrasi
...

====================================================
  RINGKASAN MIGRASI
====================================================
[SUCCESS] Total berhasil dimigrasi: 5
[SUCCESS] Migrasi selesai dengan sukses! ✓
```

### **Step 4: Update Password Admin yang Ada (Opsional)**

Jika password admin yang ada belum menggunakan `password_hash()`, update manual:

```sql
-- Update password admin menjadi "admin123"
UPDATE admin_users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE username = 'admin';

-- Atau buat script PHP untuk generate hash baru
<?php
echo password_hash('admin123', PASSWORD_DEFAULT);
?>
```

### **Step 5: Test Login**

1. Buka browser dan akses: `http://localhost/login.php`
2. Pilih tab **Admin** atau **Member**
3. Login dengan:
   - **Username**: `admin`
   - **Password**: `admin123` (atau password yang Anda set)

✅ Jika berhasil, Anda akan diarahkan ke dashboard sesuai role

---

## 📊 Struktur Tabel Users

```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,      -- Username untuk login
    email VARCHAR(255) UNIQUE NOT NULL,         -- Email untuk login
    password VARCHAR(255) NOT NULL,             -- Password ter-hash
    role VARCHAR(20) NOT NULL,                  -- admin, personil, mahasiswa
    reference_id INTEGER NOT NULL,              -- ID dari tabel asli
    is_active BOOLEAN DEFAULT TRUE,             -- Status aktif
    last_login TIMESTAMP,                       -- Last login time
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### **Relasi dengan Tabel Lain**

| Role | Tabel Asli | reference_id menunjuk ke |
|------|------------|--------------------------|
| admin | admin_users | admin_users.id |
| personil | personil | personil.id |
| mahasiswa | mahasiswa | mahasiswa.id |

---

## 🔄 Migrasi Data

### **Apa yang Dimigrasi?**

✅ **Admin Users**
- Semua record dari tabel `admin_users`
- Username, email, password (already hashed)
- Role = 'admin'

✅ **Personil (Member)**
- Record dari tabel `personil` dimana `is_member = TRUE` dan `password IS NOT NULL`
- Email → username (auto-generated dari email)
- Role = 'personil'

❌ **Mahasiswa**
- Saat ini tidak dimigrasi (akan ditambahkan nanti jika diperlukan)

### **Script Migrasi**

Script `migrate_to_users.php` memiliki fitur:
- ✅ Validasi tabel users ada
- ✅ Cek duplikasi sebelum insert
- ✅ Transaction support (rollback jika error)
- ✅ Colored output untuk memudahkan monitoring
- ✅ Summary report setelah selesai

---

## 🧪 Testing

### **Test 1: Login Admin**

```
URL: http://localhost/login.php?type=admin
Username: admin
Password: admin123

✅ Expected: Redirect ke /admin/index.php
✅ Session: admin_logged_in = true
```

### **Test 2: Login Personil/Member**

```
URL: http://localhost/login.php?type=personil
Username: [email personil]
Password: [password yang di-set]

✅ Expected: Redirect ke /member/index.php
✅ Session: member_logged_in = true
```

### **Test 3: Tambah Admin Baru**

Buat file `test_add_admin.php`:

```php
<?php
require_once 'core/database.php';
require_once 'admin/controllers/adminController.php';

$_POST = [
    'username' => 'testadmin',
    'nama_lengkap' => 'Test Administrator',
    'email' => 'testadmin@labse.ac.id',
    'password' => 'test123',
    'confirm_password' => 'test123'
];

$_SERVER['REQUEST_METHOD'] = 'POST';

$controller = new AdminController();
$result = $controller->add();

if (empty($result['error'])) {
    echo "✅ Admin berhasil ditambahkan!\n";
    echo "Cek tabel users dan admin_users\n";
} else {
    echo "❌ Error: " . $result['error'] . "\n";
}
?>
```

Jalankan: `php test_add_admin.php`

### **Test 4: Verifikasi Sinkronisasi**

Query untuk cek sinkronisasi:

```sql
-- Cek semua admin dan statusnya di users
SELECT 
    au.id as admin_id,
    au.username,
    au.email,
    u.id as user_id,
    u.role,
    u.is_active
FROM admin_users au
LEFT JOIN users u ON u.reference_id = au.id AND u.role = 'admin'
ORDER BY au.id;

-- Cek semua personil member dan statusnya di users
SELECT 
    p.id as personil_id,
    p.nama,
    p.email,
    p.is_member,
    u.id as user_id,
    u.username,
    u.role,
    u.is_active
FROM personil p
LEFT JOIN users u ON u.reference_id = p.id AND u.role = 'personil'
WHERE p.is_member = TRUE
ORDER BY p.id;
```

---

## 🔧 Troubleshooting

### **Problem 1: Login Gagal**

**Symptoms**: "Username/Email atau password salah!"

**Solutions**:
1. Cek apakah user ada di tabel `users`:
   ```sql
   SELECT * FROM users WHERE username = 'admin' OR email = 'admin@labse.ac.id';
   ```

2. Cek apakah `is_active = TRUE`:
   ```sql
   UPDATE users SET is_active = TRUE WHERE username = 'admin';
   ```

3. Cek password hash:
   ```php
   <?php
   // Verify password
   $password = 'admin123';
   $hash = '$2y$10$...'; // hash dari database
   var_dump(password_verify($password, $hash)); // Should return true
   ?>
   ```

### **Problem 2: Migrasi Gagal**

**Symptoms**: Error saat menjalankan `migrate_to_users.php`

**Solutions**:
1. Cek apakah tabel users sudah dibuat:
   ```sql
   SELECT * FROM information_schema.tables WHERE table_name = 'users';
   ```

2. Cek permission:
   ```sql
   GRANT ALL PRIVILEGES ON TABLE users TO postgres;
   ```

3. Lihat error log:
   ```bash
   tail -f c:\laragon\data\logs\postgresql.log
   ```

### **Problem 3: Session Tidak Tersimpan**

**Symptoms**: Setelah login, di-redirect kembali ke login

**Solutions**:
1. Cek session save path:
   ```php
   <?php
   echo "Session save path: " . session_save_path();
   echo "<br>Session name: " . session_name();
   ?>
   ```

2. Pastikan folder session writable:
   ```bash
   # Windows
   icacls c:\laragon\tmp /grant Users:F
   ```

3. Cek php.ini:
   ```ini
   session.save_handler = files
   session.save_path = "c:\laragon\tmp"
   session.gc_maxlifetime = 3600
   ```

### **Problem 4: Personil Tidak Bisa Login**

**Symptoms**: Personil yang sudah ada tidak bisa login

**Solutions**:
1. Pastikan personil memiliki password:
   ```sql
   SELECT id, nama, email, is_member, 
          CASE WHEN password IS NULL THEN 'NO PASSWORD' ELSE 'HAS PASSWORD' END as pwd_status
   FROM personil 
   WHERE is_member = TRUE;
   ```

2. Set password untuk personil:
   ```sql
   -- Set password untuk personil ID 1
   UPDATE personil 
   SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
       is_member = TRUE
   WHERE id = 1;
   ```

3. Re-run migrasi untuk personil tersebut:
   ```php
   php migrate_to_users.php
   ```

---

## 📝 Catatan Penting

### **Backward Compatibility**

- ✅ Tabel `admin_users` dan `personil` **tetap ada**
- ✅ Data di tabel asli **tidak berubah**
- ✅ Sistem bisa rollback jika ada masalah
- ✅ Dashboard dan UI **tidak berubah**

### **Password Management**

- ✅ Semua password baru menggunakan `password_hash()`
- ✅ Minimal password: 6 karakter
- ✅ Password lama (plain text) **tidak akan berfungsi**
- ✅ Admin perlu reset password user yang belum menggunakan hash

### **Auto-Sync**

Setiap kali admin melakukan:
- ✅ **Tambah Admin Baru** → Otomatis masuk ke tabel `users`
- ✅ **Edit Admin** → Otomatis update di tabel `users`
- ✅ **Hapus Admin** → Otomatis hapus dari tabel `users`
- ✅ **Tambah/Edit Personil sebagai Member** → Otomatis sinkron ke `users`
- ✅ **Hapus Personil** → Otomatis hapus dari `users`

---

## 🎉 Kesimpulan

Sistem login terpusat telah berhasil diimplementasikan dengan fitur:

✅ **Satu Login untuk Semua Role**
✅ **Password Terenkripsi (password_hash)**
✅ **Auto-Sync ke Tabel Users**
✅ **Backward Compatible**
✅ **Dashboard Tetap Modern dan Responsif**

Jika ada pertanyaan atau masalah, silakan hubungi developer atau buat issue di repository.

---

**Last Updated**: 13 November 2024
**Version**: 1.0.0
**Author**: Lab SE Development Team
