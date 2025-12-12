# Fix: Error Column "updated_at" Does Not Exist

## 🔴 Error yang Terjadi

```
ERROR: column "updated_at" of relation "personil" does not exist
LINE 2: is_member = $6, updated_at = N...
```

File: `C:\laragon\www\labse_web\admin\controllers\personilController.php`

---

## 🔍 Penyebab

Beberapa tabel di database **belum memiliki kolom `updated_at`** yang dibutuhkan oleh controller untuk tracking kapan data terakhir diupdate.

Tabel yang membutuhkan kolom `updated_at`:
- ✅ `personil` - **BELUM ADA** (penyebab error)
- ✅ `admin_users` - **BELUM ADA**  
- ✅ `artikel` - **BELUM ADA**
- ✅ `mahasiswa` - **BELUM ADA**
- ✅ `lab_profile` - **BELUM ADA**

Tabel yang sudah punya:
- ✅ `penelitian` - sudah ada
- ✅ `pengabdian` - sudah ada
- ✅ `produk` - sudah ada
- ✅ `users` - sudah ada

---

## ✅ Solusi

### Jalankan Migration Script

**Cara 1: Menggunakan pgAdmin (Recommended)**

1. Buka **pgAdmin**
2. Connect ke database `labse`
3. Klik kanan pada database → pilih **Query Tool**
4. Buka file: [`database/migrations/add_updated_at_columns.sql`](file:///C:/laragon/www/labse_web/database/migrations/add_updated_at_columns.sql)
5. Copy semua isi file ke Query Tool
6. Klik **Execute** (F5)

**Cara 2: Menggunakan psql (Command Line)**

```bash
psql -U postgres -d labse -f "database/migrations/add_updated_at_columns.sql"
```

**Cara 3: Manual Query (Quick Fix)**

Jika Anda ingin cepat, jalankan query ini di pgAdmin:

```sql
-- Tambah kolom updated_at ke semua tabel yang butuh
ALTER TABLE personil ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE artikel ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE mahasiswa ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE lab_profile ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Buat trigger function untuk auto-update
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Buat trigger untuk setiap tabel
DROP TRIGGER IF EXISTS update_personil_updated_at ON personil;
CREATE TRIGGER update_personil_updated_at BEFORE UPDATE ON personil
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_admin_users_updated_at ON admin_users;
CREATE TRIGGER update_admin_users_updated_at BEFORE UPDATE ON admin_users
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_artikel_updated_at ON artikel;
CREATE TRIGGER update_artikel_updated_at BEFORE UPDATE ON artikel
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_mahasiswa_updated_at ON mahasiswa;
CREATE TRIGGER update_mahasiswa_updated_at BEFORE UPDATE ON mahasiswa
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_lab_profile_updated_at ON lab_profile;
CREATE TRIGGER update_lab_profile_updated_at BEFORE UPDATE ON lab_profile
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
```

---

## ✅ Verifikasi

Setelah menjalankan migration, verifikasi dengan query:

```sql
-- Cek apakah kolom updated_at sudah ada
SELECT table_name, column_name, data_type
FROM information_schema.columns 
WHERE table_name IN ('personil', 'admin_users', 'artikel', 'mahasiswa', 'lab_profile')
  AND column_name = 'updated_at'
ORDER BY table_name;
```

Hasil yang diharapkan:

```
table_name    | column_name | data_type
--------------+-------------+-------------------
admin_users   | updated_at  | timestamp without time zone
artikel       | updated_at  | timestamp without time zone
lab_profile   | updated_at  | timestamp without time zone
mahasiswa     | updated_at  | timestamp without time zone
personil      | updated_at  | timestamp without time zone
```

---

## 🎯 Test Setelah Fix

1. Buka halaman **Manage Personil**: `http://localhost/labse_web/admin/views/manage_personil.php`
2. Coba **Edit** salah satu personil
3. Update data dan klik **Simpan**
4. Seharusnya **tidak ada error lagi** ✅

---

## 📝 Apa yang Dilakukan Migration?

1. **Menambahkan kolom `updated_at`** ke tabel: `personil`, `admin_users`, `artikel`, `mahasiswa`, `lab_profile`
2. **Membuat trigger function** `update_updated_at_column()` untuk auto-update waktu
3. **Membuat trigger** di setiap tabel agar setiap kali ada UPDATE, kolom `updated_at` otomatis diisi dengan waktu sekarang

---

## 🔧 Manfaat Kolom `updated_at`

- **Tracking**: Tahu kapan terakhir kali data diupdate
- **Audit**: Berguna untuk audit trail
- **Sorting**: Bisa sort data berdasarkan yang terakhir diupdate
- **Cache**: Bisa dipakai untuk invalidate cache

---

## ⚠️ Catatan

- Migration ini menggunakan `IF NOT EXISTS` sehingga **aman dijalankan berulang kali**
- Trigger akan otomatis mengisi `updated_at` setiap kali ada UPDATE
- Kolom `created_at` tetap tidak berubah (hanya diisi saat INSERT)

---

## 🆘 Jika Masih Error

Jika setelah menjalankan migration masih ada error, coba:

1. **Restart PHP Service** (Laragon → Stop → Start)
2. **Clear Opcache** (restart web server)
3. **Check PostgreSQL Connection** pastikan service berjalan
4. **Verify Column Exists** jalankan query verifikasi di atas

---

## 📚 File yang Diubah/Dibuat

1. ✅ `database/migrations/add_updated_at_columns.sql` - Migration script
2. ✅ `database/labse.sql` - Update struktur tabel dengan kolom `updated_at`
3. ✅ `admin/controllers/personilController.php` - Tetap menggunakan `updated_at` (sudah benar)

Semua file lain yang menggunakan `updated_at` sudah benar dan tidak perlu diubah.
