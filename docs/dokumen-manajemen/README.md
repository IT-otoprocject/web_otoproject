# Document Management Module - Laravel

## Overview
Enterprise document management system with role-based access control, folder organization, and comprehensive audit trail capabilities. This module provides a secure and efficient solution for managing organizational documents with granular permission controls.

## Key Features
- **Folder Organization**: Pre-configured folders for SOP, WIP, Form, PICA, SKD, and Internal Memo documents
- **File Management**: Upload, edit, and delete PDF documents with size validation (max 3MB)
- **Access Control**: Role-based permissions for viewing and administrative functions
- **Document Tracking**: Download count and last download timestamp for usage analytics
- **Audit Trail**: Soft delete implementation with complete document lifecycle tracking
- **Responsive Interface**: Mobile-friendly design with dark mode support

## System Architecture

### Directory Structure
```
app/
├── Http/Controllers/DocumentManagement/
│   └── DocumentManagementController.php
├── Models/DocumentManagement/
│   ├── Document.php
│   └── DocumentFolder.php

database/migrations/
├── 2026_01_27_000001_create_document_folders_table.php
├── 2026_01_27_000002_create_documents_table.php
└── 2026_01_27_000003_seed_default_document_folders.php

resources/views/document-management/
├── index.blade.php          # Folder listing page
├── folder.blade.php         # Document listing within folder
├── create.blade.php         # Document upload form
├── edit.blade.php           # Document edit form
└── manage-folders.blade.php # Folder management interface

routes/
└── web.php                  # Route definitions
```

## 🚀 Cara Install

### 1. Run Migration
```bash
php artisan migrate
```

Migration akan membuat:
- Tabel `document_folders` untuk menyimpan folder
- TInstallation Guide

### Prerequisites
- Laravel 11.x installed and configured
- MySQL/PostgreSQL database connection
- PHP 8.2 or higher
- Storage directory with write permissions

### Step 1: Database Migration
Execute the migration files to create required tables and seed default data:
```bash
php artisan migrate
```
Database Schema

### Table: document_folders
Stores folder/category information for document organization.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique folder identifier |
| name | VARCHAR(255) | NOT NULL, UNIQUE | Folder display name |
| slug | VARCHAR(255) | NOT NULL, UNIQUE, INDEXED | URL-safe identifier |
| description | TEXT | NULLABLE | Folder description and purpose |
| icon | VARCHAR(50) | DEFAULT 'folder' | UI icon reference (reserved for future use) |
| order | INTEGER | NOT NULL, DEFAULT 0 | Display sort order |
| is_active | BOOLEAN | NOT NULL, DEFAULT TRUE | Folder visibility status |
| created_at | TIMESTAMP | NOT NULL | Record creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Last modification timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE: `slug`
- INDEX: `is_active`, `order`

### Table: documents
Stores document metadata and file references.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique document identifier |
| folder_id | BIGINT | NOT NULL, FOREIGN KEY | Reference to document_folders.id |
| title | VARCHAR(255) | NOT NULL | Document title |
| description | TEXT | NULLABLE | Document description |
| file_name | VARCHAR(255) | NOT NULL | Original uploaded filename |
| file_path | VARCHAR(500) | NOT NULL | Storage path relative to public disk |
| fUser Guide

### Standard User Operations (dokumen_manajemen)
Users with standard access can view and download documents.

**Viewing Documents:**
1. Navigate to "Dokumen Manajemen" from the dashboard menu
2. Select the desired folder from the grid layout
3. Browse the document list with pagination support
4. Use the "View" button to preview PDF documents in browser
5. Use the "Download" button to download documents to local storage

**Document Search:**
- Documents are organized by upload date (newest first)
- Each folder displays the total number of documents
- Document metadata includes title, description, uploader, and upload date

### Administrator Operations (dokumen_manajemen_admin)
Administrators have full CRUD capabilities for documents and folders.

#### Document Management

**Creating Documents:**
1. Navigate to the target folder
2. Click "Upload Dokumen" button
3. Complete the upload form:
   - **Title** (required): Document name
   - **Description** (optional): Additional context or notes
   - **File** (required): PDF file, maximum 3MB
4. Submit the form to upload

**Editing Documents:**
1. Click the "Edit" button on the document row
2. Modify title, description, or folder assignment
3. Optionally upload a new file to replace the existing one
4. Submit changes to update

**Deleting Documents:**
1. Click the "Delete" button on the document row
2. Confirm the deletion in the modal dialog
3. Document is soft-deleted and file is removed from storage

#### Folder Management

**Accessing Folder Management:**
1. Click "Kelola Folder" button on the main page
2. View all folders with document counts and status

**CAccess Control

### Permission Levels

**dokumen_manajemen** (Standard Access)
- View folder structure
- Browse document listings
- Preview documents in browser
- Download documents
- View document metadata

**dokumen_manajemen_admin** (Administrative Access)
- All standard access permissions
- Upload new documents
- Edit existing documents
- Delete documents
- Create new folders
- Edit folder properties
- Delete empty folders
- View upload statistics

### Role Assignment

**System Administrators:**
- Inherit full access to all modules automatically
- No manual permission assignment required

**Department Managers:**
- Require `dokumen_manajemen` for view/download capabilities
- Require `dokumen_manajemen_admin` for management functions

**Standard Users:**
- MValidation Rules

### File Upload Constraints
- **Format**: PDF documents only (.pdf extension)
- **Size Limit**: Maximum 3 MB (3,072 KB) per file
- **MIME Type**: application/pdf (enforced server-side)
API Routes

All routes are protected by `auth` and `system_access` middleware.

### Document Routes

| Method | Endpoint | Route Name | Controller Method | Required Permission |
|--------|----------|------------|-------------------|---------------------|
| GET | `/document-management` | document-management.index | index() | dokumen_manajemen |
| GET | `/document-management/folder/{slug}` | document-management.folder | showFolder() | dokumen_manajemen |
| GET | `/document-management/folder/{folder}/create` | document-management.create | create() | dokumen_manajemen_admin |
| POST | `/document-management/documents` | document-management.store | store() | dokumen_manajemen_admin |
| GET | `/document-management/documents/{document}/edit` | document-management.edit | edit() | dokumen_manajemen_admin |
| PUT | `/document-management/documents/{document}` | document-management.update | update() | dokumen_manajemen_admin |
| DELETE | `/document-management/documents/{document}` | document-management.destroy | destroy() | dokumen_manajemen_admin |
| GET | `/document-management/documents/{document}/download` | document-management.download | download() | dokumen_manajemen |
| GET | `/document-management/documents/{document}/view` | document-management.view | view() | dokumen_manajemen |

### Folder Management Routes

| Method | Endpoint | Route Name | Controller Method | Required Permission |
|--------|----------|------------|-------------------|---------------------|
| GET | `/document-management/manage-folders` | document-management.manage-folders | manageFolders() | dokumen_manajemen_admin |
| POST | `/document-management/folders` | document-management.folders.store | storeFolder() | dokumen_manajemen_admin |
| PUT | `/document-management/folders/{folder}` | document-management.folders.update | updateFolder() | dokumen_manajemen_admin |
| DELETE | `/document-management/folders/{folder}` | document-management.folders.destroy | destroyFolder() | dokumen_manajemen_admin |

### Route Model Binding
- `{folder}`: Binds to `DocumentFolder` model by ID
- `{document}`: Binds to `Document` model by ID
- `{slug}`: String parameter for URL-friendly folder identification
4. Klik icon mata untuk preview PDF
5. Klik icon download untuk download file

### Untuk Admin (dokumen_manajemen_admin)
#### Upload Dokumen Baru
1. Masuk ke folder
2. Klik tombol "Upload Dokumen"
3. Isi form:
   - Judul Dokumen
   - Deskripsi (opsional)
   - File PDF (max 3MB)
4. Klik "Upload Dokumen"

#### Edit Dokumen
1. Klik icon pensil pada dokumen
2. Edit informasi atau ganti file
3. Klik "Update Dokumen"
User Interface

### Design Principles
- **Responsive Layout**: Mobile-first design with breakpoint optimization
- **Dark Mode Support**: Automatic theme switching based on user preferences
- **Accessibility**: WCAG 2.1 Level AA compliance
- **Performance**: Lazy loading and pagination for large datasets

### Component Library

**Folder Grid View:**
- Card-based layout with 3-column grid (responsive)
- Folder name, description, and document count
- Color-coded badges for active/inactive status
- Hover effects for improved interactivity

**Document Table View:**
- Sortable columns with fixed header
- Pagination (15 items per page)
- File size display in human-readable format
- Upload date and uploader information
- Action button group (View, Download, Edit, Delete)

**Modal Dialogs:**
- Folder creation and editing forms
- Delete confirmation dialogs
- Backdrop overlay for focus management

**Form Components:**
- FTroubleshooting

### Common Issues and Solutions

**Issue: Storage Symbolic Link Not Found**
```bash
# Create symbolic link
php artisan storage:link

# Verify link creation
ls -la public/storage
```

**Issue: File Upload Permission Denied**
```bash
# Set correct permissions for storage directories
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Verify ownership (Unix/Linux)
chown -R www-data:www-data storage
```

**Issue: Menu Not Visible in Dashboard**
1. Verify user has `dokumen_manajemen` permission in system_access
2. Navigate to User Management → Select User → System Access
3. Check permission assignment and save changes
4. Clear browser cache and re-login

**Issue: File Upload Fails**
1. Verify file size is under 3MB limit
2. Confirm file is PDF format (check file extension and MIME type)
3. Check storage directory permissions (775 recommended)
4. Review Laravel logs: `storage/logs/laravel.log`
5. Verify PHP upload limits in `php.ini`:
   ```ini
   upload_max_filesize = 3M
   post_max_size = 3M
   ```

**Issue: 404 Error on Document Routes**
```bash
# Clear route cache
php artisan route:clear
php artisan route:cache

# Verify route registration
php artisan route:list --name=document-management
```

**Issue: Soft Deleted Documents Still Visible**
- Check query scopes in Document model
- Ensure proper use of `withTrashed()` or `onlyTrashed()` methods
- Review controller query filters

## Best Practices

### File Management
- Use descriptive, meaningful document titles
- Include comprehensive descriptions for context and searchability
- Organize documents logically within appropriate folders
- Regular cleanup of obsolete documents

### Storage Optimization
- Files are stored in `storage/app/public/documents/{folder-slug}/`
- Soft delete preserves files for audit purposes
- Implement periodic cleanup of soft-deleted documents
- Monitor storage usage and set up alerts

### Performance Considerations
- Document listing uses pagination (15 items per page)
- Eager loading of relationships to prevent N+1 queries
- Index optimization on frequently queried columns
- Consider implementing document search functionality for large datasets

## Security Features

### Authentication & Authorization
- All routes protected by `auth` middleware
- Role-based access control via `system_access` middleware
- Authorization checks in every controller method
- Session-based authentication with CSRF protection

### File Security
- Server-side validation of file type and size
- MIME type verification (not just extension)
- Files stored outside public web root
- Controlled access through application routes (no direct file access)

### Audit Trail
- Soft delete implementation for document lifecycle tracking
- Upload user tracking via foreign key relationship
- Download count and timestamp logging
- Comprehensive activity logging in Laravel logs

### Data Integrity
- Foreign key constraints with cascade delete
- Database-level validation and constraints
- Transaction support for complex operations
- Regular database backups recommended

## Technical Support

For technical assistance, bug reports, or feature requests:

**Internal Development Team:**
- Email: dev-team@company.com
- Documentation: `/docs/dokumen-manajemen/`
- Issue Tracker: Internal project management system

**Emergency Contacts:**
- System Administrator: admin@company.com
- Database Administrator: dba@company.com

**Resources:**
- Laravel Documentation: https://laravel.com/docs
- Application Logs: `storage/logs/laravel.log`
- System Logs: Check server error logs for infrastructure issues
| Method | URI | Name | Access |
|--------|-----|------|--------|
| GET | /document-management | document-management.index | dokumen_manajemen |
| GET | /document-management/folder/{slug} | document-management.folder | dokumen_manajemen |
| GET | /document-management/folder/{folder}/create | document-management.create | dokumen_manajemen_admin |
| POST | /document-management/documents | document-management.store | dokumen_manajemen_admin |
| GET | /document-management/documents/{document}/edit | document-management.edit | dokumen_manajemen_admin |
| PUT | /document-management/documents/{document} | document-management.update | dokumen_manajemen_admin |
| DELETE | /document-management/documents/{document} | document-management.destroy | dokumen_manajemen_admin |
| GET | /document-management/documents/{document}/download | document-management.download | dokumen_manajemen |
| GET | /document-management/documents/{document}/view | document-management.view | dokumen_manajemen |
| GET | /document-management/manage-folders | document-management.manage-folders | dokumen_manajemen_admin |
| POST | /document-management/folders | document-management.folders.store | dokumen_manajemen_admin |
| PUT | /document-management/folders/{folder} | document-management.folders.update | dokumen_manajemen_admin |
| DELETE | /document-management/folders/{folder} | document-management.folders.destroy | dokumen_manajemen_admin |

## 🎨 UI Components
- Responsive grid layout untuk folder
- Table view untuk list dokumen
- Modal untuk manage folder
- Icons: Folder (yellow), PDF (red)
- Action buttons: View, Download, Edit, Delete

## 🔧 Troubleshooting

### Error: Storage link not found
```bash
php artisan storage:link
```

### Error: Permission denied saat upload
Pastikan folder `storage/app/public` memiliki permission yang benar:
```bash
chmod -R 775 storage
```

### Menu tidak muncul di dashboard
1. Pastikan user memiliki akses `dokumen_manajemen` di system_access
2. Cek di User Management → Show User → System Access

### Tidak bisa upload file
1. Cek ukuran file (max 3MB)
2. Pastikan file berformat PDF
3. Cek permission folder storage

## 💡 Tips
- Gunakan nama file yang deskriptif untuk memudahkan pencarian
- Isi deskripsi dokumen untuk konteks tambahan
- Folder default sudah otomatis dibuat saat migration
- File disimpan di `storage/app/public/documents/{folder-slug}/`
- Soft delete digunakan, file tetap ada di storage meski dokumen dihapus

## 🔐 Security
- Semua route dilindungi middleware `auth` dan `system_access`
- Validasi file type dan size di server-side
- Authorization check di setiap method controller
- Soft delete untuk audit trail

## 📞 Support
Jika ada pertanyaan atau issue, silakan hubungi tim development.
