# 🚀 MEMBER DASHBOARD - SETUP GUIDE

## ✅ COMPLETED FEATURES

Saya telah berhasil membuat **Member Dashboard** yang lengkap dengan semua fitur yang diminta!

---

## 📦 YANG SUDAH DIBUAT

### 1. **Database Update Script**
📁 `database/member_update.sql`
- Menambah kolom `password`, `is_member`, `last_login` ke tabel `personil`
- Menambah kolom `personil_id` ke tabel `artikel` (foreign key)
- Set password default `member123` untuk semua personil
- Link artikel existing ke personil berdasarkan nama penulis

### 2. **Login System**
📁 `member/login.php`
- Desain sama persis dengan admin login
- Login menggunakan **email** (bukan username)
- Terintegrasi dengan tabel `personil`
- Session management dengan timeout 30 menit
- Beautiful responsive UI

### 3. **Authentication & Security**
📁 `member/auth_check.php` - Session validation
📁 `member/logout.php` - Logout handler
- Session timeout automatic
- Protected routes
- Security tokens

### 4. **Dashboard Layout (Responsive)**
📁 `member/includes/member_header.php` - Header & CSS
📁 `member/includes/member_sidebar.php` - Sidebar & mobile toggle
📁 `member/includes/member_footer.php` - Footer & JavaScript

**Features:**
- ✅ Sidebar dengan gradient blue (sama seperti admin)
- ✅ Hamburger menu untuk mobile
- ✅ Sidebar slide-in animation
- ✅ Backdrop overlay
- ✅ Responsive di semua device

### 5. **Dashboard Pages**

#### A. Dashboard Index
📁 `member/index.php`
- Welcome card dengan info member
- Statistik total artikel
- Quick actions (buat artikel, edit profil)
- List artikel terbaru (5 artikel)
- Tips untuk member

#### B. My Articles
📁 `member/my_articles.php`
- List **semua artikel milik member**
- Preview thumbnail gambar
- Action buttons: Lihat, Edit, Hapus
- Pagination (10 artikel per page)
- **Member hanya lihat artikelnya sendiri!**

#### C. Add Article
📁 `member/add_article.php`
- Form buat artikel baru
- Penulis otomatis terisi (dari session)
- Upload gambar artikel
- Preview gambar sebelum upload
- Validasi client-side & server-side
- **Artikel otomatis linked ke `personil_id`**

#### D. Edit Article
📁 `member/edit_article.php`
- Form edit artikel
- Preview gambar lama
- Upload gambar baru (optional)
- **Hanya bisa edit artikel sendiri!**
- Validasi ownership (cek `personil_id`)

#### E. Delete Article
📁 `member/delete_article.php`
- Hapus artikel
- Hapus file gambar di server
- **Hanya bisa hapus artikel sendiri!**
- Validasi ownership

#### F. Edit Profile
📁 `member/edit_profile.php`
- Edit nama, jabatan, email, bio
- Upload foto profil
- Ubah password (opsional)
- Validasi email uniqueness
- Profile preview card

### 6. **Documentation**
📁 `member/README.md`
- Complete documentation
- Installation guide
- User accounts list
- Security features
- Troubleshooting

---

## 🎯 KEY FEATURES

### ✅ Separation of Concerns
```
MEMBER dapat:
✅ Buat artikel baru
✅ Lihat artikel sendiri
✅ Edit artikel sendiri
✅ Hapus artikel sendiri
✅ Edit profil sendiri

MEMBER TIDAK dapat:
❌ Lihat artikel member lain
❌ Edit artikel member lain
❌ Hapus artikel member lain
❌ Manage personil/mahasiswa
```

### ✅ Security
- **Password hashing** dengan bcrypt
- **Session management** dengan timeout
- **Authorization check** pada setiap operasi
- **SQL injection protection** (prepared statements)
- **File upload validation** (type, size)

### ✅ Responsive Design
```
Desktop  (>1024px) : Full sidebar visible
Tablet   (768-1024): Optimized layout
Mobile   (<768px)  : Hamburger menu + slide-in sidebar
Small    (<480px)  : Extra compact
```

### ✅ User Experience
- Smooth animations (AOS)
- Loading indicators
- Success/error alerts
- Form validation
- Image preview
- Breadcrumb navigation
- Pagination
- Touch-friendly buttons

---

## 📝 LANGKAH INSTALASI

### Step 1: Update Database
Buka **pgAdmin** atau terminal PostgreSQL:

```sql
-- Copy isi file database/member_update.sql
-- Paste dan jalankan di pgAdmin
```

Atau via command line:
```bash
psql -U postgres -d labse -f c:/laragon/www/labse_web/database/member_update.sql
```

### Step 2: Buat Folder Upload
Buka Command Prompt di `c:/laragon/www/labse_web/`:

```bash
# Buat folder jika belum ada
mkdir uploads\artikel
mkdir uploads\personil

# Set permissions (Windows biasanya sudah OK)
```

### Step 3: Test Login
1. Buka browser: `http://localhost/labse_web/member/login.php`
2. Login dengan:
   - **Email:** `ahmad.fauzi@university.ac.id`
   - **Password:** `member123`
3. Seharusnya masuk ke dashboard!

---

## 👥 AKUN MEMBER YANG TERSEDIA

Setelah menjalankan update database, 6 akun berikut tersedia:

| No | Nama | Email | Password | Jabatan |
|----|------|-------|----------|---------|
| 1 | Dr. Ahmad Fauzi, M.Kom | ahmad.fauzi@university.ac.id | member123 | Kepala Laboratorium |
| 2 | Prof. Dr. Siti Nurhaliza, M.T | siti.nurhaliza@university.ac.id | member123 | Koordinator Penelitian |
| 3 | Budi Santoso, Ph.D | budi.santoso@university.ac.id | member123 | Dosen Senior |
| 4 | Dr. Rina Wijaya, M.Sc | rina.wijaya@university.ac.id | member123 | Dosen Senior |
| 5 | Muhammad Rizki, M.Kom | muhammad.rizki@university.ac.id | member123 | Asisten Laboratorium |
| 6 | Dewi Lestari, M.T | dewi.lestari@university.ac.id | member123 | Asisten Laboratorium |

**Note:** Artikel existing sudah otomatis linked ke personil yang sesuai!

---

## 🧪 TESTING WORKFLOW

### Test 1: Login & Dashboard
```
1. Buka: http://localhost/labse_web/member/login.php
2. Login dengan salah satu akun
3. ✅ Lihat dashboard dengan statistik
4. ✅ Lihat artikel terbaru (jika ada)
```

### Test 2: Buat Artikel
```
1. Klik "Buat Artikel" atau menu sidebar
2. Isi judul dan konten artikel
3. Upload gambar (optional)
4. ✅ Klik "Publikasikan Artikel"
5. ✅ Artikel muncul di "Artikel Saya"
```

### Test 3: Edit & Hapus
```
1. Di "Artikel Saya", klik Edit pada artikel
2. Ubah judul atau konten
3. ✅ Simpan perubahan
4. Klik Hapus pada artikel
5. ✅ Artikel terhapus
```

### Test 4: Separation Test (PENTING!)
```
1. Login sebagai Member A (misal: Ahmad Fauzi)
2. Buat 1-2 artikel
3. Logout
4. Login sebagai Member B (misal: Siti Nurhaliza)
5. ✅ Cek "Artikel Saya" → HANYA artikel Member B yang muncul
6. ✅ Member B TIDAK bisa lihat artikel Member A
```

### Test 5: Edit Profil
```
1. Klik menu "Edit Profil"
2. Ubah nama, bio, atau upload foto
3. (Optional) Ubah password
4. ✅ Simpan perubahan
5. ✅ Nama terupdate di topbar
```

### Test 6: Responsive
```
1. Buka di desktop → ✅ Sidebar visible
2. Resize browser ke mobile size
3. ✅ Hamburger menu muncul
4. ✅ Klik hamburger → Sidebar slide in
5. ✅ Klik backdrop → Sidebar close
6. ✅ Table bisa scroll horizontal
```

---

## 🎨 DESIGN COMPARISON

### Member vs Admin Dashboard

| Aspect | Member | Admin |
|--------|--------|-------|
| **Color Scheme** | Blue gradient | Same |
| **Layout** | Sidebar + Content | Same |
| **Responsive** | Mobile toggle | Same |
| **Components** | Cards, Tables, Forms | Same |
| **Animation** | AOS smooth | Same |

**Design 100% konsisten!** ✅

---

## 📊 DATABASE SCHEMA

### Relasi Baru
```
artikel
├── id (PK)
├── judul
├── isi
├── penulis (text - nama personil)
├── gambar
├── personil_id (FK → personil.id) ← BARU!
└── created_at

personil
├── id (PK)
├── nama
├── jabatan
├── email
├── deskripsi
├── foto
├── password ← BARU!
├── is_member ← BARU!
├── last_login ← BARU!
└── created_at
```

### Query Example
```sql
-- Artikel milik Ahmad Fauzi (personil_id = 1)
SELECT * FROM artikel WHERE personil_id = 1;

-- Hitung total artikel per personil
SELECT p.nama, COUNT(a.id) as total_artikel
FROM personil p
LEFT JOIN artikel a ON p.id = a.personil_id
WHERE p.is_member = TRUE
GROUP BY p.id, p.nama;
```

---

## 🔐 SECURITY FEATURES

### 1. Authentication
```php
// Login dengan email + password
// Password di-hash dengan bcrypt
password_verify($password, $hashed_password)
```

### 2. Authorization
```php
// Setiap operasi artikel HARUS cek ownership
WHERE id = $artikel_id AND personil_id = $member_id
```

### 3. Session Management
```php
// Timeout 30 menit
$timeout_duration = 1800; // seconds
// Auto logout jika idle
```

### 4. File Upload
```php
// Validasi type & size
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$max_size = 2 * 1024 * 1024; // 2MB
```

---

## 🐛 TROUBLESHOOTING

### Problem: "Email atau password salah"
**Solusi:**
1. Pastikan sudah jalankan `member_update.sql`
2. Cek tabel personil:
   ```sql
   SELECT id, nama, email, is_member FROM personil WHERE is_member = TRUE;
   ```
3. Password default: `member123`

### Problem: Gambar tidak ter-upload
**Solusi:**
1. Cek folder exists: `uploads/artikel/` dan `uploads/personil/`
2. Cek permissions (Windows biasanya OK)
3. Max size 2MB, format: JPG/PNG/GIF

### Problem: Artikel member lain muncul
**Solusi:**
1. Cek query di `my_articles.php`
2. Pastikan ada `WHERE personil_id = $member_id`
3. Cek session `$_SESSION['member_id']`

### Problem: Sidebar tidak muncul di mobile
**Solusi:**
1. Clear browser cache
2. Cek JavaScript console untuk error
3. Pastikan `member_footer.php` ter-include

---

## 📱 RESPONSIVE BREAKPOINTS

```css
/* Desktop */
@media (min-width: 1024px) {
    .member-sidebar { display: block; }
    .mobile-toggle { display: none; }
}

/* Tablet */
@media (max-width: 1024px) {
    .member-content { padding: 1.5rem; }
}

/* Mobile */
@media (max-width: 768px) {
    .mobile-toggle { display: flex; }
    .member-sidebar { transform: translateX(-100%); }
    .member-sidebar.active { transform: translateX(0); }
}

/* Small Mobile */
@media (max-width: 480px) {
    .member-sidebar { width: 100vw; }
    .member-content { padding: 0.75rem; }
}
```

---

## 📚 FILE STRUCTURE

```
labse_web/
├── member/                          ← FOLDER BARU
│   ├── login.php                    ← Login page
│   ├── logout.php                   ← Logout handler
│   ├── auth_check.php               ← Auth middleware
│   ├── index.php                    ← Dashboard
│   ├── my_articles.php              ← List artikel
│   ├── add_article.php              ← Buat artikel
│   ├── edit_article.php             ← Edit artikel
│   ├── delete_article.php           ← Hapus artikel
│   ├── edit_profile.php             ← Edit profil
│   ├── README.md                    ← Full documentation
│   └── includes/
│       ├── member_header.php        ← Header & CSS
│       ├── member_sidebar.php       ← Sidebar & toggle
│       └── member_footer.php        ← Footer & JS
│
├── database/
│   └── member_update.sql            ← Update script
│
├── uploads/
│   ├── artikel/                     ← Artikel images
│   └── personil/                    ← Profile photos
│
└── MEMBER_SETUP_GUIDE.md            ← Setup guide ini
```

---

## ✨ HIGHLIGHTS

### 🎯 Perfect Separation
- Member **HANYA** bisa manage artikel sendiri
- Authorization check di **setiap operasi**
- Database relation dengan `personil_id`

### 🎨 Consistent Design
- Same color scheme dengan admin
- Same layout structure
- Same responsive behavior
- Same animation effects

### 📱 Fully Responsive
- Desktop: Full experience
- Tablet: Optimized
- Mobile: Hamburger menu + slide-in
- Touch-friendly

### 🔐 Secure
- Password hashing
- Session management
- Authorization checks
- File upload validation
- SQL injection protection

### 💯 Complete Features
- ✅ Login/Logout
- ✅ Dashboard
- ✅ CRUD Artikel (hanya milik sendiri)
- ✅ Edit Profil
- ✅ Upload gambar
- ✅ Responsive design

---

## 🎉 READY TO USE!

Sistem **Member Dashboard** sudah **100% siap digunakan**!

### Quick Start:
```bash
1. Jalankan: member_update.sql
2. Buka: http://localhost/labse_web/member/login.php
3. Login: ahmad.fauzi@university.ac.id / member123
4. Explore!
```

---

## 📞 NOTES

- **Password default:** `member123` (untuk semua member)
- **Ubah password:** Di menu "Edit Profil"
- **Member baru:** Tambah manual di tabel `personil`, set `is_member = TRUE` dan `password`
- **Artikel existing:** Sudah otomatis linked ke personil

---

**Dashboard Member berhasil dibuat dengan sempurna!** ✅🎉

**Semua fitur yang diminta sudah terimplementasi:**
- ✅ Login terintegrasi dengan personil
- ✅ Bisa membedakan admin dan member
- ✅ CRUD artikel (hanya artikel sendiri)
- ✅ Edit profil
- ✅ Design konsisten dengan admin
- ✅ Fully responsive
- ✅ Artikel linked ke personil_id

**Silakan test dan jika ada yang perlu disesuaikan, saya siap membantu!** 🚀
