# 📦 Archived Migration Files

## Tentang Folder Ini

Folder ini berisi migration files yang **sudah dijalankan** dan tidak diperlukan lagi untuk development aktif. File-file ini diarsipkan untuk:
- Dokumentasi historical changes
- Reference untuk troubleshooting di masa depan
- Audit trail perubahan database

> [!WARNING]
> **JANGAN jalankan file-file ini lagi** kecuali Anda sedang melakukan restore database dari awal atau troubleshooting masalah tertentu.

---

## 📋 Daftar File yang Diarsipkan

### PHP Migration Scripts (2 file)

#### 1. `migrate_to_users.php`
- **Ukuran:** 7,776 bytes
- **Fungsi:** Migrasi dari sistem user lama ke tabel `users` yang baru
- **Status:** ✅ Sudah dijalankan
- **Tanggal Arsip:** 2025-12-02

#### 2. `run_update.php`
- **Ukuran:** 426 bytes
- **Fungsi:** Script untuk menjalankan update schema
- **Status:** ✅ Sudah dijalankan
- **Tanggal Arsip:** 2025-12-02

---

### SQL Schema Updates (5 file)

#### 3. `add_admin_profile_columns.sql`
- **Ukuran:** 902 bytes
- **Fungsi:** Menambah kolom profil ke tabel admin
- **Status:** ✅ Sudah dijalankan
- **Tanggal Arsip:** 2025-12-02

#### 4. `add_mahasiswa_approval.sql`
- **Ukuran:** 1,393 bytes
- **Fungsi:** Menambah sistem approval untuk mahasiswa
- **Status:** ✅ Sudah dijalankan
- **Tanggal Arsip:** 2025-12-02

#### 5. `add_updated_at_to_lab_profile.sql`
- **Ukuran:** 98 bytes
- **Fungsi:** Menambah kolom `updated_at` ke tabel lab_profile
- **Status:** ✅ Sudah dijalankan
- **Tanggal Arsip:** 2025-12-02

#### 6. `remove_dosen_pembimbing.sql`
- **Ukuran:** 1,697 bytes
- **Fungsi:** Menghapus field dosen pembimbing dari schema
- **Status:** ✅ Sudah dijalankan
- **Tanggal Arsip:** 2025-12-02

#### 7. `update_schema_dashboard.sql`
- **Ukuran:** 1,303 bytes
- **Fungsi:** Update schema untuk dashboard
- **Status:** ✅ Sudah dijalankan
- **Tanggal Arsip:** 2025-12-02

---

## 🔄 Kapan Menggunakan File Ini?

### ✅ Gunakan Jika:
1. Melakukan **fresh installation** dari database
2. **Troubleshooting** masalah historical
3. Ingin **melihat perubahan** yang pernah dilakukan
4. Membuat **documentation** perubahan database

### ❌ JANGAN Gunakan Jika:
1. Database sudah running dengan baik
2. Ingin membuat perubahan baru (buat migration file baru)
3. Tidak yakin dengan efek sampingnya

---

## 📚 Struktur Database Saat Ini

File-file migration ini sudah terintegrasi ke dalam schema utama:
- **`database/labse.sql`** - Schema lengkap terbaru

> [!TIP]
> Untuk fresh installation, cukup gunakan `database/labse.sql` yang sudah mencakup semua perubahan dari migration files yang diarsipkan.

---

## 🗂️ Struktur Folder Database

```
database/
├── migrations/
│   └── archived/          # ← Migration files yang sudah dijalankan (Anda di sini)
│       ├── migrate_to_users.php
│       ├── run_update.php
│       ├── add_admin_profile_columns.sql
│       ├── add_mahasiswa_approval.sql
│       ├── add_updated_at_to_lab_profile.sql
│       ├── remove_dosen_pembimbing.sql
│       └── update_schema_dashboard.sql
├── labse.sql              # Schema utama (gunakan ini untuk fresh install)
├── create_*.sql           # Table creation scripts
├── seed_*.sql             # Data seeding scripts
└── insert_sample_users.sql
```

---

## ⚠️ Catatan Penting

> [!IMPORTANT]
> File-file ini **TIDAK BOLEH DIHAPUS** karena merupakan bagian dari dokumentasi historical changes. Namun, file-file ini **TIDAK PERLU DIJALANKAN** lagi dalam development normal.

> [!NOTE]
> Jika Anda membuat migration baru di masa depan, simpan di folder `database/migrations/` (bukan di archived).
