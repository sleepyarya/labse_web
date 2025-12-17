# Upload Gambar Troubleshooting Guide

## 🔧 Masalah yang Diperbaiki

### 1. **Folder Upload Tidak Ada**
- **Masalah:** Folder `uploads/artikel/` tidak ada
- **Solusi:** Dibuat otomatis dengan `mkdir('../uploads/artikel/', 0777, true)`

### 2. **Error Handling Kurang**
- **Masalah:** Tidak ada validasi ukuran file dan error handling
- **Solusi:** Ditambahkan validasi lengkap

### 3. **Update Database Tanpa Cek Error**
- **Masalah:** Database diupdate meski upload gagal
- **Solusi:** Ditambahkan `if (empty($error))` sebelum update

---

## 🧪 Testing Steps

### 1. **Cek PHP Settings**
Buka: `http://localhost/labse_web/member/php_info.php`

Pastikan:
- `file_uploads = On`
- `upload_max_filesize >= 2M`
- `post_max_size >= 2M`
- Directory writable

### 2. **Test Upload**
Buka: `http://localhost/labse_web/member/test_upload.php`

Test dengan file gambar kecil (< 2MB)

### 3. **Debug Console**
1. Buka Developer Tools (F12)
2. Ke tab Console
3. Edit artikel dengan gambar baru
4. Submit form
5. Lihat console log

---

## 🔍 Debugging

### **Console Log akan menampilkan:**
```javascript
Form submitted with: {
    judul: "Judul artikel...",
    isi: "Isi artikel...",
    hasFile: true,
    fileName: "gambar.jpg"
}
```

### **PHP Error Messages:**
- "Ukuran file terlalu besar! Maksimal 2MB"
- "Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF"
- "Gagal mengupload gambar. Silakan coba lagi"

---

## 📁 File Structure

```
uploads/
├── artikel/          ← Untuk gambar artikel
└── personil/         ← Untuk foto profil
```

---

## ✅ Validation Rules

### **File Upload:**
- **Format:** JPG, JPEG, PNG, GIF
- **Ukuran:** Maksimal 2MB
- **Nama:** Unique dengan `uniqid()`

### **Form:**
- **Judul:** Wajib diisi
- **Isi:** Wajib diisi, minimal 100 karakter

---

## 🚀 Test Scenario

### **Edit Artikel dengan Gambar Baru:**

1. **Login member**
2. **Ke "Artikel Saya"**
3. **Klik Edit pada artikel**
4. **Upload gambar baru (JPG, < 2MB)**
5. **Submit form**
6. **Cek hasil:**
   - Redirect ke my_articles.php?success=edit
   - Gambar berubah di list artikel
   - File lama terhapus, file baru tersimpan

### **Expected Behavior:**
- ✅ Gambar lama dihapus dari server
- ✅ Gambar baru tersimpan dengan nama unique
- ✅ Database terupdate dengan nama file baru
- ✅ Preview gambar berubah

---

## 🐛 Common Issues

### **Issue 1: Gambar tidak berubah**
**Cause:** Folder tidak ada atau tidak writable
**Solution:** Cek php_info.php, pastikan folder writable

### **Issue 2: File terlalu besar**
**Cause:** PHP upload limit
**Solution:** Edit php.ini:
```ini
upload_max_filesize = 2M
post_max_size = 2M
```

### **Issue 3: Form tidak submit**
**Cause:** JavaScript error atau validation
**Solution:** Cek browser console untuk error

---

## 📞 Debug Commands

### **Cek folder permissions:**
```bash
ls -la uploads/artikel/
```

### **Cek PHP settings:**
```php
echo ini_get('upload_max_filesize');
echo ini_get('post_max_size');
```

### **Test file upload:**
```php
if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
    echo "Success";
} else {
    echo "Failed: " . error_get_last()['message'];
}
```

---

**Upload gambar sudah diperbaiki dengan error handling lengkap!** 🚀📸
