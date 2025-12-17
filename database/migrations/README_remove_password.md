# Migrasi: Hapus Kolom Password dari Tabel admin_users

## Latar Belakang

Sebelumnya sistem menggunakan dua tabel untuk menyimpan password:
1. **Tabel `admin_users`** - menyimpan password admin
2. **Tabel `users`** - tabel pusat autentikasi untuk semua user (admin, personil, mahasiswa)

Hal ini menyebabkan:
- **Duplikasi data** - password tersimpan di dua tempat
- **Inkonsistensi** - password bisa berbeda antara kedua tabel
- **Error** - saat menambah user admin baru, terjadi error "null value in column password of relation admin_users violates not-null constraint"

## Solusi

Menghapus kolom `password` dari tabel `admin_users` dan hanya menggunakan tabel `users` sebagai pusat autentikasi.

## Struktur Setelah Migrasi

### Tabel `admin_users`
```sql
CREATE TABLE admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabel `users` (pusat autentikasi)
```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Password disimpan di sini
    role VARCHAR(20) NOT NULL,
    reference_id INTEGER NOT NULL,   -- ID dari admin_users/personil/mahasiswa
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Cara Menjalankan Migrasi

### Opsi 1: Menggunakan psql (Command Line)

```bash
psql -U postgres -d labse -f "database/migrations/remove_password_from_admin_users.sql"
```

### Opsi 2: Menggunakan pgAdmin

1. Buka pgAdmin
2. Connect ke database `labse`
3. Klik kanan pada database → pilih "Query Tool"
4. Buka file `database/migrations/remove_password_from_admin_users.sql`
5. Copy-paste isi file ke Query Tool
6. Klik Execute (F5)

### Opsi 3: Manual via SQL

Jalankan query berikut di pgAdmin atau psql:

```sql
-- Hapus kolom password dari admin_users
ALTER TABLE admin_users DROP COLUMN IF EXISTS password;
```

## Verifikasi

Setelah menjalankan migrasi, verifikasi dengan query berikut:

```sql
-- Cek struktur tabel admin_users (kolom password tidak ada)
SELECT column_name, data_type, is_nullable 
FROM information_schema.columns 
WHERE table_name = 'admin_users';

-- Cek data users yang sudah ada
SELECT u.id, u.username, u.email, u.role, au.nama_lengkap
FROM users u
LEFT JOIN admin_users au ON u.reference_id = au.id AND u.role = 'admin';
```

## File yang Diubah

1. **database/labse.sql** - Update struktur tabel admin_users
2. **database/migrations/remove_password_from_admin_users.sql** - Script migrasi
3. **admin/generate_password.php** - Update query untuk update password di tabel users
4. **admin/views/manage_users.php** - Sudah benar, tidak mengirim password ke admin_users

## Catatan Penting

⚠️ **Setelah migrasi, semua operasi password harus dilakukan di tabel `users`, BUKAN di `admin_users`**

### Login
- Login tetap menggunakan tabel `users`
- Tidak ada perubahan pada alur login

### Update Password
```sql
-- ✅ BENAR - Update di tabel users
UPDATE users SET password = 'hash_password' WHERE username = 'admin';

-- ❌ SALAH - Jangan update di admin_users (kolom sudah tidak ada)
UPDATE admin_users SET password = 'hash_password' WHERE username = 'admin';
```

### Tambah User Admin Baru
File `admin/views/manage_users.php` sudah benar:
- Insert ke `admin_users` tanpa password (line 151-157)
- Insert ke `users` dengan password (line 194-203)

## Rollback (Jika Diperlukan)

Jika ingin rollback migrasi:

```sql
-- Tambahkan kembali kolom password (nullable terlebih dahulu)
ALTER TABLE admin_users ADD COLUMN password VARCHAR(255);

-- Sync password dari tabel users ke admin_users
UPDATE admin_users au
SET password = u.password
FROM users u
WHERE u.reference_id = au.id AND u.role = 'admin';

-- Set kolom password menjadi NOT NULL jika diperlukan
ALTER TABLE admin_users ALTER COLUMN password SET NOT NULL;
```

## Pertanyaan & Jawaban

**Q: Apakah data password yang sudah ada akan hilang?**
A: Tidak. Password tetap tersimpan di tabel `users`. Hanya kolom password di `admin_users` yang dihapus.

**Q: Apakah user admin masih bisa login?**
A: Ya, login tetap menggunakan tabel `users` yang tidak berubah.

**Q: Apakah user lama perlu reset password?**
A: Tidak perlu. Semua password sudah tersimpan di tabel `users`.

**Q: Bagaimana jika saya sudah menjalankan migration ini dan masih error?**
A: Jalankan query verifikasi untuk memastikan kolom password sudah terhapus. Jika masih ada error, periksa error message dan file mana yang mengakses kolom password di admin_users.
