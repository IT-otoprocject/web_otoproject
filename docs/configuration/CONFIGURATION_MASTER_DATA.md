# Master Data Configuration - Implementation Guide

## Overview
Fitur Master Data Configuration telah berhasil ditambahkan ke sistem. Fitur ini memungkinkan admin untuk mengelola master data untuk:
- Master Divisi
- Master User Level  
- Master Garage

## File Structure (Professional Organization)

### Models
```
app/Models/Configuration/
├── MasterDivisi.php
├── MasterUserLevel.php
└── MasterGarage.php
```

### Controllers
```
app/Http/Controllers/Admin/Configuration/
├── ConfigurationController.php
├── MasterDivisiController.php
├── MasterUserLevelController.php
└── MasterGarageController.php
```

### Views
```
resources/views/admin/configuration/
├── index.blade.php
├── divisi/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── user-level/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── garage/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

### Documentation
```
docs/configuration/
└── CONFIGURATION_MASTER_DATA.md (this file)
```

## Database Structure

### Migration
Location: `database/migrations/2026_01_29_134155_create_master_data_tables.php`

### Tabel yang Dibuat:
1. **master_divisi** - Menyimpan data divisi perusahaan
2. **master_user_level** - Menyimpan data level user
3. **master_garage** - Menyimpan data garage/lokasi

Semua tabel memiliki struktur:
- `id` (primary key)
- `kode` (unique identifier)
- `nama` (display name)
- `deskripsi` (optional description)
- `is_active` (boolean status)
- `lokasi` (khusus untuk garage)
- `timestamps` (created_at, updated_at)

## Features Implemented

### 1. Master Data Models
Namespace: `App\Models\Configuration`

**MasterDivisi.php**
- Manages division master data
- Methods: `isUsedByUsers()`, `getUsersCount()`, `scopeActive()`

**MasterUserLevel.php**
- Manages user level master data
- Methods: `isUsedByUsers()`, `getUsersCount()`, `scopeActive()`

**MasterGarage.php**
- Manages garage master data with location field
- Methods: `isUsedByUsers()`, `getUsersCount()`, `scopeActive()`

### 2. Controllers
Namespace: `App\Http\Controllers\Admin\Configuration`

All controllers include full CRUD operations:
- `index()` - List with search and pagination
- `create()` - Show create form
- `store()` - Save new record
- `edit()` - Show edit form
- `update()` - Update existing record
- `destroy()` - Delete record (with usage protection)

### 3. Routes
### 3. Routes
Location: `routes/web.php`

All configuration routes are under: `/admin/configuration/`
- `/admin/configuration` - Main configuration dashboard
- `/admin/configuration/divisi` - Manage divisions (full CRUD)
- `/admin/configuration/user-level` - Manage user levels (full CRUD)
- `/admin/configuration/garage` - Manage garages (full CRUD)

Protected by middleware: `auth` and `system_access:user_management`

### 4. Views
Location: `resources/views/admin/configuration/`

All views have been created with professional Tailwind CSS styling:
- **Configuration Dashboard**: `index.blade.php` with three cards
- **Divisi**: `divisi/{index,create,edit}.blade.php`
- **User Level**: `user-level/{index,create,edit}.blade.php`
- **Garage**: `garage/{index,create,edit}.blade.php` (includes lokasi field)

### 5. Integration with User Management
**Updated Files:**
- `app/Http/Controllers/Admin/UserController.php`
  - Import statements updated to use `App\Models\Configuration\*`
  - `create()` method loads master data
  - `edit()` method loads master data

- `resources/views/admin/users/create.blade.php`
  - Dropdowns for divisi, level, garage now dynamic from master data
  - Only shows active master data

- `resources/views/admin/users/edit.blade.php`
  - Dropdowns for divisi, level, garage now dynamic from master data
  - Only shows active master data

## Usage

### Accessing Configuration
1. Login as admin with `user_management` system access
2. Navigate to `/admin/dashboard`
3. Click **Configuration** card
4. Or direct access: `/admin/configuration`

### Adding Master Data
1. Go to respective master data page (divisi/user-level/garage)
2. Click "Tambah" button
3. Fill in the form:
   - **Kode**: Unique code (uppercase for divisi/garage, lowercase for user level)
   - **Nama**: Display name
   - **Deskripsi**: Optional description
   - **Lokasi**: Required for garage only
   - **Is Active**: Check to make it available in user forms
4. Submit

### Editing Master Data
- Click "Edit" on any master data record
- Update the information
- ⚠️ Warning shown if data is being used by users
- Cannot change kode if data is in use (validation)

### Deleting Master Data
- Click "Hapus" to delete
- **Protection**: Data cannot be deleted if it's being used by any user
- Error message shows user count if deletion blocked

### Search & Filter
All index pages include:
- Search bar (searches kode and nama)
- Pagination (10 records per page)
- Status badges (Active/Inactive)
- Usage counter (shows how many users use this data)

## Data Migration & Backward Compatibility

### Initial Migration Includes:
✅ **Master Divisi** - All ENUM values migrated:
- FACTORY, FAT, HCGA, RETAIL, PDCA, PURCHASING, R&D, SALES, WAREHOUSE, WAREHOUSE_SBY

✅ **Master User Level** - All ENUM values migrated:
- admin, manager, spv, staff, headstore, kasir, sales, mekanik, ceo, cfo

✅ **Master Garage** - Extracted from existing users table:
- All unique garage values from users automatically imported

### Backward Compatibility:
✅ **Table `users` NOT MODIFIED**
- Fields `divisi`, `level`, `garage` remain unchanged
- Existing data preserved
- No breaking changes

✅ **Existing Users Continue to Work**
- All authentication logic unchanged
- Middleware and access control unchanged
- System references same field names

✅ **Seamless Integration**
- Old hardcoded dropdowns replaced with dynamic data
- Values stored in `users` table remain identical
- Example: `divisi='FACTORY'` still works exactly the same

## Security & Access Control

### Middleware Protection
```php
Route::middleware(['auth', 'system_access:user_management'])
```

### Features:
- ✅ Only authenticated users can access
- ✅ Requires `user_management` system access
- ✅ Delete protection when data in use
- ✅ Unique constraint on kode field
- ✅ Active/inactive status control

## Admin Dashboard Integration

**Updated File**: `resources/views/admin/dashboard.blade.php`

Added Configuration card with:
- Gear icon (indigo colored)
- Links to `/admin/configuration`
- Protected by `@hasAccess('user_management')`
- Professional hover effects

## Testing Checklist

- [x] Migration executed successfully
- [x] All models created with proper namespace
- [x] All controllers created with proper namespace  
- [x] All routes configured
- [x] All views created and styled
- [x] User forms integrated with master data
- [x] Delete protection working
- [x] Search functionality working
- [x] Pagination working
- [x] Active/inactive filter working
- [x] Configuration card in admin dashboard
- [x] Documentation updated

## File Structure Summary

```
web_otoproject/
├── app/
│   ├── Http/Controllers/Admin/Configuration/
│   │   ├── ConfigurationController.php
│   │   ├── MasterDivisiController.php
│   │   ├── MasterUserLevelController.php
│   │   └── MasterGarageController.php
│   └── Models/Configuration/
│       ├── MasterDivisi.php
│       ├── MasterUserLevel.php
│       └── MasterGarage.php
├── database/migrations/
│   └── 2026_01_29_134155_create_master_data_tables.php
├── resources/views/admin/
│   ├── configuration/
│   │   ├── index.blade.php
│   │   ├── divisi/{index,create,edit}.blade.php
│   │   ├── user-level/{index,create,edit}.blade.php
│   │   └── garage/{index,create,edit}.blade.php
│   └── users/{create,edit}.blade.php (updated)
├── routes/
│   └── web.php (updated with Configuration namespace)
└── docs/configuration/
    └── CONFIGURATION_MASTER_DATA.md (this file)
```

## API Endpoints

All endpoints use standard Laravel Resource Controller convention:

| Method | URI | Action | Description |
|--------|-----|--------|-------------|
| GET | `/admin/configuration` | index | Configuration dashboard |
| GET | `/admin/configuration/divisi` | index | List divisions |
| GET | `/admin/configuration/divisi/create` | create | Show create form |
| POST | `/admin/configuration/divisi` | store | Save new division |
| GET | `/admin/configuration/divisi/{id}/edit` | edit | Show edit form |
| PUT/PATCH | `/admin/configuration/divisi/{id}` | update | Update division |
| DELETE | `/admin/configuration/divisi/{id}` | destroy | Delete division |

Same pattern applies for `user-level` and `garage`.

## Future Enhancements

Potential improvements:
1. Import/Export master data (Excel)
2. Bulk edit operations
3. Audit log for changes
4. API endpoints for mobile apps
5. Advanced filtering and sorting
6. Data validation rules customization

## Support

For issues or questions:
- Check migration status: `php artisan migrate:status`
- Clear cache: `php artisan optimize:clear`
- Check routes: `php artisan route:list --name=configuration`
└── 2026_01_29_134155_create_master_data_tables.php
resources/views/admin/
├── configuration/
│   ├── index.blade.php
│   ├── divisi/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── user-level/ (create similar to divisi)
│   └── garage/ (create similar to divisi)
└── users/
    ├── create.blade.php (updated)
    └── edit.blade.php (updated)
routes/
└── web.php (updated with configuration routes)
```

## Migration Status
✅ Database tables created
✅ Initial data seeded from existing values
✅ Models created
✅ Controllers created
✅ Routes configured
✅ Main views created
✅ User forms integrated

## Tips for Creating Remaining Views

### For User Level Views:
1. Copy `divisi/index.blade.php` → `user-level/index.blade.php`
2. Replace:
   - `$divisis` → `$levels`
   - `divisi` → `userLevel` or `user-level`
   - "Divisi" → "User Level"
   - Table columns if needed

### For Garage Views:
1. Copy `divisi/index.blade.php` → `garage/index.blade.php`
2. Replace:
   - `$divisis` → `$garages`
   - `divisi` → `garage`
   - "Divisi" → "Garage"
3. Add "Lokasi" field in create/edit forms

## Testing Checklist
- [ ] Access configuration page
- [ ] Create new divisi
- [ ] Edit divisi
- [ ] Try to delete used divisi (should fail)
- [ ] Delete unused divisi (should work)
- [ ] Create user with new master data
- [ ] Verify master data shows in user forms
- [ ] Test with inactive master data
