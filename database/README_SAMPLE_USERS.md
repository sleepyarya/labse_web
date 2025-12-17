# 📝 Cara Insert Sample Users

## 🎯 Overview

File `insert_sample_users.sql` berisi query untuk insert data user sample ke tabel `users` berdasarkan data admin dan personil yang sudah ada.

**Password untuk semua user**: `admin123`

---

## 🚀 Cara Menggunakan

### **Option 1: Via pgAdmin (Recommended)**

1. Buka **pgAdmin**
2. Pilih database `labse`
3. Klik kanan → **Query Tool**
4. Buka file `insert_sample_users.sql`
5. Klik **Execute** (F5) atau klik tombol ▶️

### **Option 2: Via Command Line**

```bash
cd c:\laragon\www\labse_web\database
psql -U postgres -d labse -f insert_sample_users.sql
```

---

## 👥 User Sample yang Akan Dibuat

### **Admin Users**

| Username | Email | Password | Role | Nama Lengkap |
|----------|-------|----------|------|--------------|
| admin | admin@labse.ac.id | admin123 | admin | Administrator |
| superadmin | superadmin@labse.ac.id | admin123 | admin | Super Administrator |

### **Personil/Member Users**

| Username | Email | Password | Role | Nama |
|----------|-------|----------|------|------|
| ahmad.fauzi | ahmad.fauzi@university.ac.id | admin123 | personil | Dr. Ahmad Fauzi, M.Kom |
| siti.nurhaliza | siti.nurhaliza@university.ac.id | admin123 | personil | Prof. Dr. Siti Nurhaliza, M.T |
| budi.santoso | budi.santoso@university.ac.id | admin123 | personil | Budi Santoso, Ph.D |
| rina.wijaya | rina.wijaya@university.ac.id | admin123 | personil | Dr. Rina Wijaya, M.Sc |
| muhammad.rizki | muhammad.rizki@university.ac.id | admin123 | personil | Muhammad Rizki, M.Kom |
| dewi.lestari | dewi.lestari@university.ac.id | admin123 | personil | Dewi Lestari, M.T |

---

## 🔑 Cara Login

### **Akses Halaman Login**
```
http://localhost/login.php
```

### **Login sebagai Admin**

**Menggunakan Username:**
- Username: `admin`
- Password: `admin123`

**Menggunakan Email:**
- Email: `admin@labse.ac.id`
- Password: `admin123`

✅ **Hasil**: Otomatis redirect ke `/admin/index.php`

### **Login sebagai Personil/Member**

**Menggunakan Username:**
- Username: `ahmad.fauzi`
- Password: `admin123`

**Menggunakan Email:**
- Email: `ahmad.fauzi@university.ac.id`
- Password: `admin123`

✅ **Hasil**: Otomatis redirect ke `/member/index.php`

---

## 🔄 Apa yang Dilakukan Script Ini?

### **1. Insert Users ke Tabel `users`**
- Admin users → role: `admin`
- Personil users → role: `personil`

### **2. Update Tabel `personil`**
- Set `is_member = TRUE` untuk personil yang bisa login
- Set `password` dengan hash yang sama

### **3. Verifikasi Data**
Query di akhir file akan menampilkan:
- Semua users yang sudah diinsert
- Join dengan tabel asli untuk verifikasi

---

## ⚙️ Cara Kerja Login Baru

### **Tanpa Tab/Ikon Role**

Halaman login sekarang **tidak memerlukan pemilihan role** (Admin/Member). Sistem akan otomatis mendeteksi role berdasarkan data di database.

### **Flow Login:**

```
User input username/email + password
    ↓
Query ke tabel users
    ↓
Validasi password dengan password_verify()
    ↓
Deteksi role dari field users.role
    ↓
Redirect otomatis sesuai role:
    - role = 'admin' → /admin/index.php
    - role = 'personil' → /member/index.php
    - role = 'mahasiswa' → /student/index.php
```

### **Keuntungan:**

✅ **User Experience Lebih Baik**
- User tidak perlu tahu apakah dia admin atau member
- Cukup input username/email dan password
- Sistem otomatis redirect ke dashboard yang benar

✅ **Lebih Aman**
- Role tidak bisa dipilih manual oleh user
- Role ditentukan oleh data di database
- Mengurangi kemungkinan unauthorized access

---

## 🔍 Verifikasi Data

### **Cek Semua Users**

```sql
SELECT id, username, email, role, reference_id, is_active, created_at
FROM users
ORDER BY role, id;
```

### **Cek Admin dengan Detail**

```sql
SELECT 
    u.id as user_id,
    u.username,
    u.email,
    u.role,
    au.nama_lengkap,
    u.is_active
FROM users u
JOIN admin_users au ON u.reference_id = au.id
WHERE u.role = 'admin'
ORDER BY u.id;
```

### **Cek Personil dengan Detail**

```sql
SELECT 
    u.id as user_id,
    u.username,
    u.email,
    u.role,
    p.nama,
    p.jabatan,
    p.is_member,
    u.is_active
FROM users u
JOIN personil p ON u.reference_id = p.id
WHERE u.role = 'personil'
ORDER BY u.id;
```

---

## 🔒 Cara Generate Password Baru

Jika Anda ingin membuat password baru:

### **Via PHP Script:**

```php
<?php
$password = 'password_baru_123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hash: " . $hash . "\n";
?>
```

### **Via Command Line:**

```bash
php -r "echo password_hash('password_baru_123', PASSWORD_DEFAULT) . PHP_EOL;"
```

### **Insert User Baru dengan Password Custom:**

```sql
INSERT INTO users (username, email, password, role, reference_id, is_active)
VALUES (
    'newuser',
    'newuser@example.com',
    '$2y$10$...[hash dari password_hash]...',
    'admin', -- atau 'personil'
    1, -- ID dari tabel asli
    TRUE
);
```

---

## ❓ Troubleshooting

### **Q: Login gagal dengan "Username/Email atau password salah"**

**A**: Cek beberapa hal:

1. **Pastikan user ada di tabel users:**
   ```sql
   SELECT * FROM users WHERE username = 'admin' OR email = 'admin@labse.ac.id';
   ```

2. **Pastikan is_active = TRUE:**
   ```sql
   UPDATE users SET is_active = TRUE WHERE username = 'admin';
   ```

3. **Pastikan password hash benar:**
   ```sql
   -- Update dengan hash yang benar
   UPDATE users 
   SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
   WHERE username = 'admin';
   ```

### **Q: Setelah login langsung redirect kembali ke login**

**A**: Kemungkinan:
1. Session tidak tersimpan - cek folder `c:\laragon\tmp`
2. Role tidak sesuai - cek field `role` di tabel users
3. Reference_id tidak valid - cek apakah ID ada di tabel asli

### **Q: Personil tidak bisa login**

**A**: Pastikan:
1. Ada di tabel `users` dengan role `personil`
2. Di tabel `personil`: `is_member = TRUE`
3. Di tabel `personil`: `password` tidak NULL
4. Password hash sama di kedua tabel

### **Q: Duplicate key error saat insert**

**A**: User sudah ada. Gunakan:
```sql
-- Lihat user yang sudah ada
SELECT * FROM users WHERE username = 'admin' OR email = 'admin@labse.ac.id';

-- Atau hapus dulu
DELETE FROM users WHERE username = 'admin';
```

---

## 📚 Referensi Lengkap

- **Implementation Guide**: Lihat file `IMPLEMENTATION_GUIDE.md`
- **Quick Start**: Lihat file `QUICK_START_LOGIN.md`
- **Create Table Script**: Lihat file `create_users_table.sql`
- **Migration Script**: Lihat file `migrate_to_users.php`

---

## 🆘 Need Help?

Jika masih ada masalah, coba:
1. ✅ Baca troubleshooting di atas
2. ✅ Cek `IMPLEMENTATION_GUIDE.md` untuk detail lengkap
3. ✅ Verifikasi data dengan query di bagian "Verifikasi Data"
4. ✅ Hubungi developer jika masih error

---

**Last Updated**: 13 November 2024  
**Password Default**: `admin123` (untuk testing)  
**Hash**: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`
