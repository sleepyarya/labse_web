# Member Dashboard System - Lab Software Engineering

## 📋 Deskripsi
Dashboard khusus untuk member (dosen/personil) Lab SE untuk mengelola artikel mereka sendiri. Sistem ini terintegrasi dengan tabel `personil` yang sudah ada dan memiliki tampilan responsif yang konsisten dengan admin dashboard.

---

## 🎯 Fitur Utama

### 1. **Authentication System**
- ✅ Login menggunakan email dan password
- ✅ Session management dengan timeout (30 menit)
- ✅ Session token untuk keamanan
- ✅ Protected pages (harus login)

### 2. **Dashboard**
- ✅ Statistik artikel member
- ✅ Artikel terbaru member
- ✅ Quick actions (buat artikel, edit profil)
- ✅ Welcome card dengan info member

### 3. **Manajemen Artikel**
- ✅ **Buat Artikel** - Member bisa membuat artikel baru
- ✅ **Lihat Artikel** - List semua artikel milik member
- ✅ **Edit Artikel** - Edit artikel sendiri saja
- ✅ **Hapus Artikel** - Hapus artikel sendiri saja
- ✅ Upload gambar artikel
- ✅ Preview gambar sebelum upload

### 4. **Manajemen Profil**
- ✅ Edit informasi pribadi (nama, jabatan, email, bio)
- ✅ Upload foto profil
- ✅ Ubah password
- ✅ Validasi password lama

### 5. **Responsive Design**
- ✅ **Desktop** (> 1024px) - Full sidebar visible
- ✅ **Tablet** (768px - 1024px) - Optimized layout
- ✅ **Mobile** (< 768px) - Hamburger menu, sidebar slide-in
- ✅ Touch-friendly interface
- ✅ Smooth animations

---

## 📁 Struktur File

```
member/
├── login.php                 # Halaman login member
├── logout.php                # Proses logout
├── auth_check.php            # Session validation
├── index.php                 # Dashboard utama
├── my_articles.php           # List artikel member
├── add_article.php           # Form buat artikel
├── edit_article.php          # Form edit artikel
├── delete_article.php        # Proses hapus artikel
├── edit_profile.php          # Form edit profil
├── README.md                 # Dokumentasi ini
└── includes/
    ├── member_header.php     # Header & CSS
    ├── member_sidebar.php    # Sidebar & mobile toggle
    └── member_footer.php     # Footer & JavaScript
```

---

## 🗄️ Database Schema

### Update yang Diperlukan

Jalankan script `database/member_update.sql` untuk:

1. **Tabel `personil`** - Tambah kolom:
   - `password` VARCHAR(255) - Password hash untuk login
   - `is_member` BOOLEAN - Flag apakah personil adalah member
   - `last_login` TIMESTAMP - Waktu login terakhir

2. **Tabel `artikel`** - Tambah kolom:
   - `personil_id` INTEGER - Foreign key ke tabel personil
   - Index untuk optimasi query

### Relasi
```
artikel.personil_id → personil.id
```

Satu personil bisa punya banyak artikel.  
Artikel hanya bisa diedit/dihapus oleh penulis (personil) yang membuat.

---

## 🚀 Cara Install

### 1. Update Database
```bash
psql -U postgres -d labse -f database/member_update.sql
```

### 2. Buat Folder Upload
```bash
mkdir -p uploads/artikel
mkdir -p uploads/personil
chmod 777 uploads/artikel
chmod 777 uploads/personil
```

### 3. Default Login Credentials
**Email:** Gunakan email dari tabel personil (contoh: `ahmad.fauzi@university.ac.id`)  
**Password:** `member123` (untuk semua personil yang diupdate)

### 4. Akses Member Dashboard
```
http://localhost/labse_web/member/login.php
```

---

## 👥 User Accounts (Sample)

Setelah menjalankan update script, akun berikut tersedia:

| Nama | Email | Password | Jabatan |
|------|-------|----------|---------|
| Dr. Ahmad Fauzi, M.Kom | ahmad.fauzi@university.ac.id | member123 | Kepala Laboratorium |
| Prof. Dr. Siti Nurhaliza, M.T | siti.nurhaliza@university.ac.id | member123 | Koordinator Penelitian |
| Budi Santoso, Ph.D | budi.santoso@university.ac.id | member123 | Dosen Senior |
| Dr. Rina Wijaya, M.Sc | rina.wijaya@university.ac.id | member123 | Dosen Senior |
| Muhammad Rizki, M.Kom | muhammad.rizki@university.ac.id | member123 | Asisten Laboratorium |
| Dewi Lestari, M.T | dewi.lestari@university.ac.id | member123 | Asisten Laboratorium |

---

## 🎨 Design Consistency

Dashboard member menggunakan **design pattern yang sama** dengan admin dashboard:

### Color Scheme
- Primary: `#4A90E2` (Blue)
- Secondary: `#68BBE3` (Light Blue)
- Success: `#28a745`
- Danger: `#dc3545`
- Warning: `#ffc107`

### Layout Components
- ✅ Sidebar dengan menu navigation
- ✅ Top bar dengan breadcrumb
- ✅ Card-based content
- ✅ Responsive table
- ✅ Form dengan validation
- ✅ Modal alerts

### Responsive Breakpoints
```css
Desktop:  > 1024px
Tablet:   768px - 1024px
Mobile:   < 768px
Small:    < 480px
```

---

## 🔐 Security Features

### 1. **Session Management**
- Session timeout setelah 30 menit idle
- Session token untuk validasi
- Auto-logout pada timeout

### 2. **Authentication**
- Password hashing (bcrypt)
- Email validation
- Protected routes

### 3. **Authorization**
- Member hanya bisa akses artikel sendiri
- Validasi `personil_id` pada setiap operasi
- SQL injection protection (prepared statements)

### 4. **File Upload Security**
- Validasi tipe file (image only)
- Validasi ukuran file (max 2MB)
- Unique filename untuk mencegah overwrite
- Error handling untuk file operations

---

## 📊 Permissions Matrix

| Fitur | Member | Admin |
|-------|--------|-------|
| Lihat artikel sendiri | ✅ | ✅ |
| Lihat semua artikel | ❌ | ✅ |
| Buat artikel | ✅ | ✅ |
| Edit artikel sendiri | ✅ | ✅ |
| Edit artikel orang lain | ❌ | ✅ |
| Hapus artikel sendiri | ✅ | ✅ |
| Hapus artikel orang lain | ❌ | ✅ |
| Edit profil sendiri | ✅ | ✅ |
| Manage personil | ❌ | ✅ |
| Manage mahasiswa | ❌ | ✅ |

---

## 🧪 Testing Checklist

### Login System
- [ ] Login dengan email valid
- [ ] Login dengan email invalid
- [ ] Login dengan password salah
- [ ] Session timeout setelah 30 menit
- [ ] Logout berhasil

### CRUD Artikel
- [ ] Buat artikel baru
- [ ] Buat artikel dengan gambar
- [ ] List artikel milik member
- [ ] Edit artikel sendiri
- [ ] Hapus artikel sendiri
- [ ] Tidak bisa edit artikel member lain
- [ ] Tidak bisa hapus artikel member lain

### Edit Profil
- [ ] Update nama, jabatan, email
- [ ] Update bio/deskripsi
- [ ] Upload foto profil baru
- [ ] Ubah password
- [ ] Validasi password lama
- [ ] Email uniqueness validation

### Responsive
- [ ] Desktop view (sidebar visible)
- [ ] Tablet view (optimized)
- [ ] Mobile view (hamburger menu)
- [ ] Touch gestures (swipe, tap)
- [ ] Table horizontal scroll
- [ ] Form input pada mobile

---

## 🐛 Troubleshooting

### Problem: "Email atau password salah"
**Solution:** 
- Pastikan sudah menjalankan `member_update.sql`
- Cek apakah `is_member = TRUE` di tabel personil
- Password default: `member123`

### Problem: "Gambar tidak ter-upload"
**Solution:**
- Cek permissions folder `uploads/artikel/` (chmod 777)
- Pastikan ukuran file < 2MB
- Format harus JPG/JPEG/PNG/GIF

### Problem: "Sidebar tidak muncul di mobile"
**Solution:**
- Clear browser cache
- Pastikan JavaScript aktif
- Cek console untuk error

### Problem: "Session timeout terlalu cepat"
**Solution:**
- Edit `auth_check.php`, ubah `$timeout_duration = 1800;` (dalam detik)
- 1800 = 30 menit
- 3600 = 60 menit

---

## 🔄 Maintenance

### Backup Database
```bash
pg_dump -U postgres labse > backup_$(date +%Y%m%d).sql
```

### Clear Old Sessions
```bash
# Hapus session files (jika menggunakan file-based session)
find /tmp -name "sess_*" -mtime +1 -delete
```

### Monitor Disk Usage
```bash
du -sh uploads/artikel/
du -sh uploads/personil/
```

---

## 📈 Future Enhancements

- [ ] Statistik views per artikel
- [ ] Kategori artikel
- [ ] Tag artikel
- [ ] Draft artikel (belum publish)
- [ ] Rich text editor (WYSIWYG)
- [ ] Notifikasi email
- [ ] Export artikel ke PDF
- [ ] Sharing ke social media

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Cek dokumentasi ini terlebih dahulu
2. Lihat kode source untuk detail implementasi
3. Hubungi developer

---

## 📜 License

Copyright © 2024 Lab Software Engineering
All rights reserved.

---

**Created with ❤️ for Lab Software Engineering**
