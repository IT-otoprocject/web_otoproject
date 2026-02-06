# Configuration Module

Modul untuk mengelola master data konfigurasi sistem.

## 📁 Struktur File

### Models
```
app/Models/Configuration/
├── MasterDivisi.php          # Model untuk master divisi
├── MasterUserLevel.php       # Model untuk master user level
└── MasterGarage.php          # Model untuk master garage
```

### Controllers
```
app/Http/Controllers/Admin/Configuration/
├── ConfigurationController.php      # Dashboard configuration
├── MasterDivisiController.php       # CRUD divisi
├── MasterUserLevelController.php    # CRUD user level
└── MasterGarageController.php       # CRUD garage
```

### Views
```
resources/views/admin/configuration/
├── index.blade.php                  # Dashboard utama
├── divisi/
│   ├── index.blade.php             # List divisi
│   ├── create.blade.php            # Form tambah divisi
│   └── edit.blade.php              # Form edit divisi
├── user-level/
│   ├── index.blade.php             # List user level
│   ├── create.blade.php            # Form tambah user level
│   └── edit.blade.php              # Form edit user level
└── garage/
    ├── index.blade.php             # List garage
    ├── create.blade.php            # Form tambah garage
    └── edit.blade.php              # Form edit garage
```

## 🚀 Fitur

- ✅ CRUD lengkap untuk 3 master data (Divisi, User Level, Garage)
- ✅ Search dan pagination
- ✅ Status aktif/non-aktif
- ✅ Delete protection (tidak bisa hapus jika sedang digunakan)
- ✅ Usage counter (menampilkan jumlah user yang menggunakan)
- ✅ Integrasi dengan User Management
- ✅ Professional UI dengan Tailwind CSS

## 📝 Dokumentasi

Untuk dokumentasi lengkap, lihat [CONFIGURATION_MASTER_DATA.md](./CONFIGURATION_MASTER_DATA.md)

## 🔗 Routes

| Method | URI | Deskripsi |
|--------|-----|-----------|
| GET | `/admin/configuration` | Dashboard configuration |
| GET | `/admin/configuration/divisi` | List divisi |
| GET | `/admin/configuration/user-level` | List user level |
| GET | `/admin/configuration/garage` | List garage |

Semua route menggunakan pattern resource controller standar Laravel.

## 🔐 Access Control

- Middleware: `auth` + `system_access:user_management`
- Hanya admin dengan akses user management yang bisa mengakses

## 📊 Database

Tables:
- `master_divisi`
- `master_user_level`
- `master_garage`

Migration: `database/migrations/2026_01_29_134155_create_master_data_tables.php`
