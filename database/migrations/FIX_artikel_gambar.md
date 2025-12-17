# Fix: Gambar Artikel Tidak Muncul di Landing Page

## 🔴 Masalah
Saat mengganti gambar artikel di admin/personil, perubahannya tidak muncul di halaman landing page blog artikel.

## 🔍 Penyebab

Terdapat **inkonsistensi path upload gambar** antara berbagai bagian aplikasi:

### **Sebelum Perbaikan:**

1. **Admin Controller** (`admin/controllers/artikelController.php`):
   - Upload ke: `../public/uploads/artikel/` ✅

2. **Member** (`member/add_article.php`, `member/edit_article.php`):
   - Upload ke: `../uploads/artikel/` ❌ (path berbeda!)

3. **Landing Page** (`views/blog/index.php`):
   - Menampilkan dari: `/public/uploads/artikel/` ✅

4. **Blog Detail** (`views/blog/detail.php`):
   - Menampilkan dari: `/uploads/artikel/` ❌ (path berbeda!)

**Akibatnya:**
- Gambar yang di-upload oleh **admin** muncul di blog index, tapi upload oleh **personil** tidak muncul
- Gambar tidak muncul di halaman detail artikel

---

## ✅ Solusi yang Sudah Diterapkan

### 1. **Standardisasi Path Upload** ✅

Semua upload gambar artikel sekarang ke satu lokasi: **`/public/uploads/artikel/`**

**File yang Diperbaiki:**
- ✅ `member/add_article.php` - Upload path diubah ke `/public/uploads/artikel/`
- ✅ `member/edit_article.php` - Upload path diubah ke `/public/uploads/artikel/`
- ✅ `views/blog/detail.php` - Display path diubah ke `/public/uploads/artikel/`
- ✅ `admin/controllers/artikelController.php` - Sudah benar (tidak perlu diubah)
- ✅ `views/blog/index.php` - Sudah benar (tidak perlu diubah)

### 2. **Script Migrasi Gambar** ✅

Dibuat script untuk memindahkan gambar yang sudah ada di lokasi lama ke lokasi baru:
- File: [`admin/migrate_artikel_images.php`](file:///C:/laragon/www/labse_web/admin/migrate_artikel_images.php)

---

## 🚀 Cara Menjalankan Migrasi

### **Langkah 1: Jalankan Script Migrasi**

Buka browser dan akses:
```
http://localhost/labse_web/admin/migrate_artikel_images.php
```

Script ini akan:
1. ✅ Membuat folder `/public/uploads/artikel/` jika belum ada
2. ✅ Memindahkan semua gambar dari `/uploads/artikel/` ke `/public/uploads/artikel/`
3. ✅ Menghapus gambar lama setelah berhasil dipindahkan
4. ✅ Menampilkan laporan hasil migrasi

### **Langkah 2: Verifikasi**

1. Cek apakah gambar sudah ada di folder `/public/uploads/artikel/`
2. Refresh halaman blog (`http://localhost/ labse_web/views/blog/`)
3. Gambar artikel seharusnya sudah muncul dengan benar ✅

---

## 📂 Struktur Folder Setelah Fix

```
labse_web/
├── public/
│   └── uploads/
│       └── artikel/           ✅ SEMUA gambar artikel di sini
│           ├── artikel_123.jpg
│           ├── artikel_456.png
│           └── ...
├── uploads/
│   └── artikel/               ❌ Tidak dipakai lagi (akan kosong/dihapus)
```

---

## 🎯 Test Setelah Fix

### **Test 1: Upload Gambar Baru (Admin)**
1. Login sebagai admin
2. Buka **Kelola Artikel**
3. **Edit** artikel atau **Tambah** artikel baru
4. Upload gambar baru
5. Lihat di landing page blog → gambar harus muncul ✅

### **Test 2: Upload Gambar Baru (Personil)**
1. Login sebagai personil/member
2. Buka **Artikel Saya**
3. **Edit** artikel atau **Tambah** artikel baru
4. Upload gambar baru
5. Lihat di landing page blog → gambar harus muncul ✅

### **Test 3: Lihat Detail Artikel**
1. Buka halaman blog
2. Klik artikel yang ada gambarnya
3. Di halaman detail, gambar artikel terkait harus muncul ✅

---

## 📝 Path Upload & Display yang Benar

### **Upload (dari Controller/Member)**
```php
// ✅ BENAR - Semua upload ke /public/uploads/artikel/
$upload_dir = '../public/uploads/artikel/';
```

### **Display (dari Views)**
```php
// ✅ BENAR - Semua display dari /public/uploads/artikel/
$img_url = BASE_URL . '/public/uploads/artikel/' . $gambar;

// Cek file exists
file_exists('../../public/uploads/artikel/' . $gambar)
```

---

## ⚠️ Catatan Penting

### **Setelah Migrasi:**
- ✅ Folder `/uploads/artikel/` akan kosong atau dihapus
- ✅ Semua gambar baru akan otomatis masuk ke `/public/uploads/artikel/`
- ✅ Gambar lama yang sudah dipindahkan akan tetap berfungsi

### **Jika Gambar Masih Tidak Muncul:**
1. **Clear browser cache** (Ctrl + Shift + R)
2. **Cek permission folder:**
   ```bash
   chmod 777 public/uploads/artikel/
   ```
3. **Verifikasi gambar ada di database:**
   ```sql
   SELECT id, judul, gambar FROM artikel WHERE gambar IS NOT NULL;
   ```
4. **Cek path gambar di file system:**
   - Pastikan file ada di: `C:\laragon\www\labse_web\public\uploads\artikel\`

---

## 🔧 File yang Diubah

1. ✅ `member/add_article.php` - Path upload
2. ✅ `member/edit_article.php` - Path upload & display
3. ✅ `views/blog/detail.php` - Path display
4. ✅ `admin/migrate_artikel_images.php` - Script migrasi (NEW)

---

## ✅ Selesai!

Sekarang **semua gambar artikel** akan tersimpan dan ditampilkan dari lokasi yang sama: `/public/uploads/artikel/`

Tidak akan ada lagi masalah gambar tidak muncul saat upload dari admin maupun personil! 😊
