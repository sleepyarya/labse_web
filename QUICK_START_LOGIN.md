# 🚀 Quick Start: Sistem Login Terpusat

## ⚡ Implementasi dalam 5 Menit

### **Step 1: Backup Database** ⏱️ 1 menit
```bash
pg_dump -U postgres labse > backup_labse.sql
```

### **Step 2: Create Table Users** ⏱️ 1 menit
```bash
cd c:\laragon\www\labse_web\database
psql -U postgres -d labse -f create_users_table.sql
```

Atau dari **pgAdmin**:
- Buka database `labse` → Query Tool
- Open file `create_users_table.sql`
- Execute (F5)

### **Step 3: Migrasi Data** ⏱️ 1 menit
```bash
cd c:\laragon\www\labse_web\database
php migrate_to_users.php
```

Ketik `y` untuk konfirmasi migrasi.

### **Step 4: Test Login** ⏱️ 1 menit
1. Buka: `http://localhost/login.php`
2. Login dengan:
   - **Username**: `admin`
   - **Password**: `admin123`

### **Step 5: Done!** ✅
- Admin bisa login dari `/login.php`
- Member bisa login dari `/login.php?type=personil`
- Semua dashboard tetap sama, hanya sistem login yang berubah

---

## 📖 Apa yang Berubah?

### ✅ **Yang Baru**
- Satu halaman login untuk semua role
- Password terenkripsi dengan `password_hash()`
- Tabel `users` sebagai pusat autentikasi
- Auto-sync saat tambah/edit admin atau personil

### ✅ **Yang Tetap**
- Dashboard admin tetap sama
- Dashboard member tetap sama
- Semua fitur CRUD tetap berfungsi
- UI/UX tidak berubah

---

## 🔑 Default Login

### Admin:
```
Username: admin
Password: admin123
URL: http://localhost/login.php
```

### Member (Personil):
```
Email: [email personil yang sudah di-set sebagai member]
Password: [password yang sudah di-set]
URL: http://localhost/login.php?type=personil
```

---

## 📝 Cara Menambah Admin Baru

### Dari Panel Admin:
1. Login sebagai admin
2. Navigasi ke "Manage Admin" (perlu dibuat view-nya)
3. Klik "Tambah Admin"
4. Isi form dan submit

### Manual via SQL:
```sql
-- Insert ke admin_users
INSERT INTO admin_users (username, nama_lengkap, email, password)
VALUES (
    'newadmin',
    'New Administrator',
    'newadmin@labse.ac.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' -- password: admin123
) RETURNING id;

-- Insert ke users (ganti <ID> dengan ID yang dikembalikan query di atas)
INSERT INTO users (username, email, password, role, reference_id)
VALUES (
    'newadmin',
    'newadmin@labse.ac.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    <ID>
);
```

---

## 📝 Cara Menambah Personil sebagai Member

### Dari Panel Admin:
1. Login sebagai admin
2. Navigasi ke "Manage Personil"
3. Tambah atau Edit Personil
4. Centang checkbox "Set sebagai Member"
5. Isi password
6. Submit

**Otomatis tersinkron ke tabel `users`!**

---

## 🔒 Generate Password Hash

Gunakan script ini untuk generate password hash:

```php
<?php
$password = 'password_baru_123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hash: " . $hash;
?>
```

Atau via command line:
```bash
php -r "echo password_hash('password_baru_123', PASSWORD_DEFAULT);"
```

---

## ❓ FAQ

### Q: Apakah password lama masih bisa digunakan?
**A**: Tidak. Semua password harus di-hash menggunakan `password_hash()`. Password plain text tidak akan berfungsi.

### Q: Bagaimana cara reset password admin?
**A**: Update manual di database:
```sql
UPDATE admin_users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';

-- Jangan lupa update di users juga
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE username = 'admin' AND role = 'admin';
```

### Q: Bagaimana cara rollback jika ada masalah?
**A**: 
1. Restore database dari backup:
   ```bash
   psql -U postgres -d labse < backup_labse.sql
   ```
2. Hapus file-file baru yang ditambahkan
3. Restore file `admin/login.php` dan `member/login.php` dari backup

### Q: Apakah tabel lama (admin_users, personil) masih digunakan?
**A**: Ya! Tabel lama tetap digunakan untuk menyimpan data detail. Tabel `users` hanya untuk autentikasi.

---

## 🆘 Troubleshooting

### Login gagal?
1. Cek apakah user ada di tabel `users`
2. Pastikan `is_active = TRUE`
3. Verifikasi password menggunakan `password_verify()`

### Personil tidak bisa login?
1. Pastikan `is_member = TRUE` di tabel `personil`
2. Pastikan `password` tidak NULL
3. Cek apakah ada di tabel `users` dengan role `personil`

### Session tidak tersimpan?
1. Cek folder `c:\laragon\tmp` ada dan writable
2. Cek `php.ini` untuk `session.save_path`

---

## 📚 Dokumentasi Lengkap

Lihat file **`IMPLEMENTATION_GUIDE.md`** untuk dokumentasi lengkap dan troubleshooting detail.

---

**Need Help?** Hubungi developer atau buat issue di repository.

---

**Last Updated**: 13 November 2024
