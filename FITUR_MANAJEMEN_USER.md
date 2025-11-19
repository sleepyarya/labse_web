# 👥 Fitur Manajemen User - Admin Dashboard

## 📋 Overview

Fitur ini memungkinkan admin untuk mengelola semua user dalam sistem (Admin, Personil, Mahasiswa) dari satu tempat terpusat. Manajemen dilakukan pada tabel `users` yang menjadi pusat autentikasi sistem.

---

## ✨ Fitur Utama

### **1. Dashboard Statistik**
- 📊 Total Users
- ✅ Active Users
- 🛡️ Jumlah Admin
- 👤 Jumlah Personil
- 🎓 Jumlah Mahasiswa (jika ada)

### **2. Filter & Pencarian**
- 🔍 **Search**: Cari berdasarkan username atau email
- 🎯 **Filter by Role**: Filter berdasarkan role (Admin/Personil/Mahasiswa)
- 🔄 **Reset Filter**: Kembali ke tampilan semua user

### **3. Daftar User dengan Info Lengkap**
Setiap user ditampilkan dengan informasi:
- ✅ ID User
- ✅ Username
- ✅ Email
- ✅ Nama Lengkap (dari tabel asli)
- ✅ Role (Admin/Personil/Mahasiswa)
- ✅ Status (Aktif/Nonaktif)
- ✅ Last Login
- ✅ Info Tambahan (Jabatan untuk Personil, NIM untuk Mahasiswa)

### **4. Aksi Management**

#### **a. Toggle Active/Inactive** 🔌
- Aktifkan atau nonaktifkan user
- User nonaktif tidak bisa login
- Tidak bisa menonaktifkan akun sendiri

#### **b. Reset Password** 🔑
- Reset password user
- Input password baru manual
- Generate password random otomatis
- Password minimal 6 karakter
- Password ter-update di tabel `users` DAN tabel asli

#### **c. Delete User** 🗑️
- Hapus user dari sistem
- **Proteksi**:
  - Tidak bisa hapus akun sendiri
  - Tidak bisa hapus admin terakhir
- Menghapus user akan menghapus akses login

### **5. Pagination**
- Tampilan 10 user per halaman
- Navigasi pagination lengkap
- Retain filter saat pindah halaman

---

## 📁 File yang Dibuat

### **1. Controller**
```
admin/controllers/userController.php
```

**Class UserController** dengan methods:
- `getAll()` - Get semua users dengan filter & pagination
- `getById()` - Get user by ID dengan detail
- `toggleActive()` - Toggle status aktif/nonaktif
- `resetPassword()` - Reset password user
- `delete()` - Hapus user
- `getStatistics()` - Get statistik users

### **2. View**
```
admin/views/manage_users.php
```

Halaman lengkap dengan:
- Statistics cards
- Filter & search form
- Users table
- Action buttons
- Reset password modal
- Pagination

### **3. Update Sidebar**
```
admin/includes/admin_sidebar.php
```

Menambahkan menu baru:
- Section "System"
- Link ke "Manajemen User"

---

## 🔗 Akses

### **URL**
```
http://localhost/labse_web/admin/views/manage_users.php
```

### **Menu Sidebar**
```
Dashboard Admin → System → Manajemen User
```

---

## 🎯 Cara Penggunaan

### **1. Melihat Semua User**
1. Login sebagai admin
2. Klik menu **"Manajemen User"** di sidebar
3. Lihat daftar semua user dengan statistik di atas

### **2. Mencari User**
1. Gunakan search box
2. Ketik username atau email
3. Klik tombol "Cari"

### **3. Filter by Role**
1. Pilih role dari dropdown (Admin/Personil/Mahasiswa)
2. Klik tombol "Cari"
3. Atau kombinasikan dengan search

### **4. Mengaktifkan/Menonaktifkan User**
1. Klik tombol **power** (🔌) pada user
2. Konfirmasi perubahan
3. Status user akan berubah
4. User nonaktif tidak bisa login

### **5. Reset Password User**
1. Klik tombol **key** (🔑) pada user
2. Modal reset password akan muncul
3. **Option 1**: Ketik password baru manual
4. **Option 2**: Klik "Generate Password Random"
5. Klik "Reset Password"
6. Password ter-update di database

### **6. Menghapus User**
1. Klik tombol **trash** (🗑️) pada user
2. Konfirmasi penghapusan
3. User akan dihapus dari tabel `users`

**Catatan**: 
- ⚠️ User yang dihapus akan kehilangan akses login
- ⚠️ Tidak bisa hapus akun sendiri
- ⚠️ Tidak bisa hapus admin terakhir

---

## 🔒 Proteksi & Keamanan

### **Proteksi yang Diterapkan:**

1. **✅ Session Check**
   - Hanya admin yang login yang bisa akses
   - Redirect ke login jika belum login

2. **✅ Self-Protection**
   - Tidak bisa nonaktifkan akun sendiri
   - Tidak bisa hapus akun sendiri

3. **✅ Last Admin Protection**
   - Tidak bisa hapus admin terakhir
   - Sistem minimal harus punya 1 admin aktif

4. **✅ Password Security**
   - Password di-hash dengan `password_hash()`
   - Update sync ke tabel asli

5. **✅ Confirmation Prompts**
   - Konfirmasi sebelum toggle active
   - Konfirmasi sebelum delete
   - Warning message yang jelas

---

## 💡 Fitur Khusus

### **1. Generate Random Password**
Modal reset password punya tombol "Generate Password Random":
- Generate password 12 karakter
- Kombinasi huruf besar, kecil, angka, dan simbol
- Aman dan random
- Langsung terisi di input field

```javascript
function generatePassword() {
    const length = 12;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    document.getElementById('new_password').value = password;
}
```

### **2. Badge Warna untuk Role**
Setiap role punya warna badge berbeda:
- 🔴 **Admin**: Red badge (`bg-danger`)
- 🔵 **Personil**: Blue badge (`bg-primary`)
- 🟢 **Mahasiswa**: Green badge (`bg-success`)

### **3. Status Indicator**
- ✅ **Aktif**: Green badge dengan icon check
- ⚫ **Nonaktif**: Gray badge dengan icon x

### **4. Additional Info Display**
- **Personil**: Tampil jabatan di bawah nama
- **Mahasiswa**: Tampil NIM di bawah nama

---

## 📊 Database Query

### **Get All Users with Details**
```sql
SELECT 
    u.id,
    u.username,
    u.email,
    u.role,
    u.reference_id,
    u.is_active,
    u.last_login,
    u.created_at,
    CASE 
        WHEN u.role = 'admin' THEN au.nama_lengkap
        WHEN u.role = 'personil' THEN p.nama
        WHEN u.role = 'mahasiswa' THEN m.nama
    END as full_name,
    CASE 
        WHEN u.role = 'personil' THEN p.jabatan
        WHEN u.role = 'mahasiswa' THEN m.nim
    END as additional_info
FROM users u
LEFT JOIN admin_users au ON u.role = 'admin' AND u.reference_id = au.id
LEFT JOIN personil p ON u.role = 'personil' AND u.reference_id = p.id
LEFT JOIN mahasiswa m ON u.role = 'mahasiswa' AND u.reference_id = m.id
ORDER BY u.created_at DESC
```

### **Toggle Active Status**
```sql
UPDATE users 
SET is_active = NOT is_active 
WHERE id = $1
```

### **Reset Password**
```sql
-- Update users table
UPDATE users 
SET password = $1 
WHERE id = $2

-- Update original table (admin_users/personil)
UPDATE admin_users 
SET password = $1 
WHERE id = $2
```

---

## 🎨 UI Design

### **Color Scheme:**
- **Primary**: Blue (`#4A90E2`)
- **Success**: Green (`#28a745`)
- **Warning**: Yellow (`#ffc107`)
- **Danger**: Red (`#dc3545`)
- **Info**: Cyan (`#17a2b8`)

### **Icons (Bootstrap Icons):**
- 👥 `bi-people-fill` - Main icon
- 🔌 `bi-power` - Toggle active
- 🔑 `bi-key` - Reset password
- 🗑️ `bi-trash` - Delete
- 🔍 `bi-search` - Search
- 🎯 `bi-funnel` - Filter

### **Responsive Design:**
- ✅ Mobile friendly
- ✅ Tablet optimized
- ✅ Desktop full feature

---

## ✅ Testing Checklist

### **Functionality:**
- [ ] Tampil semua users dengan benar
- [ ] Filter by role berfungsi
- [ ] Search by username/email berfungsi
- [ ] Toggle active/inactive berfungsi
- [ ] Reset password berfungsi
- [ ] Generate random password berfungsi
- [ ] Delete user berfungsi
- [ ] Pagination berfungsi
- [ ] Statistics cards update real-time

### **Security:**
- [ ] Tidak bisa nonaktifkan akun sendiri
- [ ] Tidak bisa hapus akun sendiri
- [ ] Tidak bisa hapus admin terakhir
- [ ] Password ter-hash dengan benar
- [ ] Session validation berfungsi

### **UI/UX:**
- [ ] Responsive di mobile
- [ ] Modal reset password muncul
- [ ] Confirmation prompts muncul
- [ ] Success/Error messages tampil
- [ ] Loading states (jika ada)

---

## 🐛 Troubleshooting

### **Problem 1: Menu tidak muncul di sidebar**
**Solution**: Refresh browser atau clear cache

### **Problem 2: Error "Permission denied"**
**Solution**: Login ulang sebagai admin

### **Problem 3: Delete tidak bisa**
**Kemungkinan**: 
- Mencoba delete akun sendiri
- Mencoba delete admin terakhir
- User tidak ditemukan

### **Problem 4: Reset password gagal**
**Kemungkinan**:
- Password kurang dari 6 karakter
- Database connection error
- Reference ID tidak valid

---

## 📝 Future Improvements

Fitur yang bisa ditambahkan:
- [ ] Bulk actions (activate/deactivate multiple users)
- [ ] Export users to CSV/Excel
- [ ] Import users from CSV
- [ ] User activity log
- [ ] Email notification saat reset password
- [ ] 2FA (Two-Factor Authentication)
- [ ] Role permissions management
- [ ] User groups/categories

---

## 🎉 Kesimpulan

Fitur Manajemen User memberikan admin kontrol penuh atas semua user dalam sistem dengan interface yang modern, aman, dan mudah digunakan.

**Key Benefits:**
- ✅ Satu tempat untuk manage semua user
- ✅ Filter & search yang powerful
- ✅ Aksi cepat dengan proteksi keamanan
- ✅ UI/UX yang intuitif
- ✅ Fully integrated dengan sistem login terpusat

---

**Last Updated**: 13 November 2024  
**Version**: 1.0.0  
**Author**: Lab SE Development Team
