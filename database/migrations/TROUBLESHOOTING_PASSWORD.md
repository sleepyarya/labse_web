# Panduan Mengatasi Masalah Password Admin

## 🔴 Masalah
Saat mencoba mengganti password admin, muncul alert:
```
"Password saat ini tidak benar!"
```

Padahal password admin yang seharusnya adalah: `admin123`

## 🔍 Penyebab

Ada 2 kemungkinan penyebab:

1. **Kolom password di admin_users belum dihapus** (migration belum dijalankan)
2. **Password di tabel users belum di-set** atau berbeda dengan yang Anda kira

## ✅ Solusi Lengkap

### Langkah 1: Verifikasi Struktur Database

Buka **pgAdmin** atau **psql**, lalu jalankan script verifikasi:

```bash
# Via psql
psql -U postgres -d labse -f "database/migrations/verify_and_fix_password.sql"
```

Atau copy-paste isi file [`verify_and_fix_password.sql`](file:///C:/laragon/www/labse_web/database/migrations/verify_and_fix_password.sql) ke Query Tool pgAdmin.

### Langkah 2: Jalankan Migration (Jika Belum)

Jika dari verifikasi ternyata kolom `password` masih ada di tabel `admin_users`, jalankan:

```sql
-- Hapus kolom password dari admin_users
ALTER TABLE admin_users DROP COLUMN IF EXISTS password;
```

### Langkah 3: Set Password Admin di Tabel Users

Jalankan query berikut untuk set password `admin123`:

```sql
-- Set password admin123 untuk user admin
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE role = 'admin' AND username = 'admin';

-- Set password admin123 untuk superadmin
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE role = 'admin' AND username = 'superadmin';
```

> **Catatan:** Hash di atas adalah hasil dari `password_hash('admin123', PASSWORD_DEFAULT)`

### Langkah 4: Verifikasi Password Tersimpan

```sql
SELECT username, email, role,
       CASE WHEN password IS NULL THEN '❌ TIDAK ADA PASSWORD!' 
            ELSE '✅ Password ada' 
       END as status
FROM users 
WHERE role = 'admin';
```

### Langkah 5: Test Login & Ganti Password

1. **Logout** dari admin dashboard
2. **Login** dengan:
   - Username: `admin`
   - Password: `admin123`
3. Setelah login berhasil, coba **ganti password** di halaman Edit Profile

---

## 🔧 Alternatif: Generate Password Baru

Jika tetap tidak bisa, Anda bisa generate password baru:

1. Buka: `http://localhost/labse_web/admin/generate_password.php`
2. Akan muncul hash password untuk `admin123`
3. Atau input password custom yang Anda inginkan
4. Copy SQL query yang muncul
5. Jalankan di pgAdmin

---

## 📋 Checklist Troubleshooting

Pastikan hal-hal berikut sudah benar:

- [ ] Kolom `password` sudah **TIDAK ADA** di tabel `admin_users`
- [ ] Password admin ada di tabel `users` (bukan NULL)
- [ ] File `profileController.php` sudah diupdate (mengambil password dari `users`)
- [ ] File `generate_password.php` sudah diupdate (update password ke `users`)
- [ ] Clear browser cache/cookies setelah update

---

## 🆘 Jika Masih Bermasalah

Jalankan script reset password ini di pgAdmin:

```sql
-- Full reset untuk memastikan semuanya bersih
BEGIN;

-- 1. Hapus kolom password dari admin_users (jika ada)
ALTER TABLE admin_users DROP COLUMN IF EXISTS password;

-- 2. Cek apakah user admin ada di tabel users
SELECT * FROM users WHERE role = 'admin' AND username = 'admin';

-- 3. Jika tidak ada, insert data user admin
-- (Uncomment jika diperlukan)
-- INSERT INTO users (username, email, password, role, reference_id, is_active, created_at)
-- SELECT au.username, au.email, 
--        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
--        'admin', au.id, TRUE, NOW()
-- FROM admin_users au
-- WHERE au.username = 'admin'
-- ON CONFLICT (username) DO NOTHING;

-- 4. Jika sudah ada, update password
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE role = 'admin' AND username = 'admin';

-- 5. Verifikasi
SELECT u.username, u.email, au.nama_lengkap,
       CASE WHEN u.password IS NULL THEN '❌ NULL' ELSE '✅ OK' END as pwd_status
FROM users u
LEFT JOIN admin_users au ON u.reference_id = au.id
WHERE u.role = 'admin';

COMMIT;
```

---

## 📝 Catatan Penting

- **Password default:** `admin123`
- **Hash algorithm:** `PASSWORD_DEFAULT` (bcrypt)
- **Password disimpan di:** Tabel `users` kolom `password`
- **Password TIDAK di:** Tabel `admin_users` (kolom sudah dihapus)

Setelah berhasil login dengan `admin123`, **segera ganti password** Anda dengan yang lebih kuat!
