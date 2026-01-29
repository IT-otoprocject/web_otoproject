-- =====================================================
-- Document Management Module - Database Schema
-- =====================================================
-- Version: 1.0
-- Date: January 27, 2026
-- Description: Complete database schema for Document Management module
-- =====================================================

-- Table: document_folders
-- Purpose: Stores folder/category structure for document organization
CREATE TABLE `document_folders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Folder display name',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL-safe identifier',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Folder purpose and description',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'folder' COMMENT 'UI icon reference (reserved)',
  `order` int(11) NOT NULL DEFAULT 0 COMMENT 'Display sort order',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Folder visibility status',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_folders_name_unique` (`name`),
  UNIQUE KEY `document_folders_slug_unique` (`slug`),
  KEY `document_folders_is_active_index` (`is_active`),
  KEY `document_folders_order_index` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Document folder/category definitions';

-- Default folder data
INSERT INTO `document_folders` (`name`, `slug`, `description`, `icon`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
('SOP', 'sop', 'Standard Operating Procedure documents', 'folder', 1, 1, NOW(), NOW()),
('WIP', 'wip', 'Work Instruction Procedure documents', 'folder', 2, 1, NOW(), NOW()),
('Form', 'form', 'Company forms and templates', 'folder', 3, 1, NOW(), NOW()),
('PICA', 'pica', 'Problem Identification and Corrective Action records', 'folder', 4, 1, NOW(), NOW()),
('SKD', 'skd', 'Surat Keputusan Direksi (Director Decision Letters)', 'folder', 5, 1, NOW(), NOW()),
('Internal Memo', 'internal-memo', 'Internal Memorandum and communications', 'folder', 6, 1, NOW(), NOW());

-- Table: documents
-- Purpose: Stores document metadata and file references
CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `folder_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Reference to document_folders',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Document title',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Document description and notes',
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Original uploaded filename',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Storage path relative to public disk',
  `file_size` int(11) NOT NULL COMMENT 'File size in bytes',
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'application/pdf' COMMENT 'File MIME type',
  `uploaded_by` bigint(20) UNSIGNED NOT NULL COMMENT 'User ID of uploader',
  `download_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Total download counter',
  `last_downloaded_at` timestamp NULL DEFAULT NULL COMMENT 'Last download timestamp',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  KEY `documents_folder_id_foreign` (`folder_id`),
  KEY `documents_uploaded_by_foreign` (`uploaded_by`),
  KEY `documents_folder_id_index` (`folder_id`),
  KEY `documents_created_at_index` (`created_at`),
  KEY `documents_deleted_at_index` (`deleted_at`),
  CONSTRAINT `documents_folder_id_foreign` FOREIGN KEY (`folder_id`) REFERENCES `document_folders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Document metadata and file references';

-- =====================================================
-- User System Access Configuration
-- =====================================================

-- Grant document management view access to specific user
-- UPDATE users 
-- SET system_access = JSON_ARRAY_APPEND(system_access, '$', 'dokumen_manajemen') 
-- WHERE id = 1;

-- Grant document management admin access to specific user
-- UPDATE users 
-- SET system_access = JSON_ARRAY_APPEND(system_access, '$', 'dokumen_manajemen_admin') 
-- WHERE id = 1;

-- =====================================================
-- Analytical Queries
-- =====================================================

-- Query: Folder statistics with document counts and sizes
SELECT 
    df.id,
    df.name AS folder_name,
    df.slug,
    df.description,
    df.is_active AS active_status,
    COUNT(d.id) AS total_documents,
    COALESCE(SUM(d.file_size), 0) AS total_size_bytes,
    ROUND(COALESCE(SUM(d.file_size), 0) / 1024 / 1024, 2) AS total_size_mb,
    SUM(d.download_count) AS total_downloads
FROM document_folders df
LEFT JOIN documents d ON df.id = d.folder_id AND d.deleted_at IS NULL
GROUP BY df.id, df.name, df.slug, df.description, df.is_active
ORDER BY df.order ASC;

-- Query: Most downloaded documents
SELECT 
    d.id,
    d.title,
    df.name AS folder_name,
    u.name AS uploaded_by,
    d.download_count,
    d.last_downloaded_at,
    d.created_at AS upload_date
FROM documents d
INNER JOIN document_folders df ON d.folder_id = df.id
INNER JOIN users u ON d.uploaded_by = u.id
WHERE d.deleted_at IS NULL
ORDER BY d.download_count DESC
LIMIT 10;

-- Query: Recently uploaded documents
SELECT 
    d.id,
    d.title,
    df.name AS folder_name,
    u.name AS uploaded_by,
    d.file_size,
    ROUND(d.file_size / 1024 / 1024, 2) AS file_size_mb,
    d.created_at AS upload_date
FROM documents d
INNER JOIN document_folders df ON d.folder_id = df.id
INNER JOIN users u ON d.uploaded_by = u.id
WHERE d.deleted_at IS NULL
ORDER BY d.created_at DESC
LIMIT 10;

-- Query: Storage usage by folder
SELECT 
    df.name AS folder_name,
    COUNT(d.id) AS total_files,
    SUM(d.file_size) AS total_bytes,
    ROUND(SUM(d.file_size) / 1024 / 1024, 2) AS total_mb,
    ROUND(AVG(d.file_size) / 1024 / 1024, 2) AS avg_file_size_mb
FROM document_folders df
LEFT JOIN documents d ON df.id = d.folder_id AND d.deleted_at IS NULL
GROUP BY df.id, df.name
ORDER BY total_bytes DESC;

-- Query: User upload activity
SELECT 
    u.id,
    u.name AS uploader_name,
    u.email,
    COUNT(d.id) AS documents_uploaded,
    SUM(d.file_size) AS total_bytes_uploaded,
    ROUND(SUM(d.file_size) / 1024 / 1024, 2) AS total_mb_uploaded,
    MAX(d.created_at) AS last_upload_date
FROM users u
INNER JOIN documents d ON u.id = d.uploaded_by
WHERE d.deleted_at IS NULL
GROUP BY u.id, u.name, u.email
ORDER BY documents_uploaded DESC
LIMIT 10;

-- Query: Documents pending cleanup (soft deleted > 365 days)
SELECT 
    d.id,
    d.title,
    d.file_path,
    df.name AS folder_name,
    d.deleted_at,
    DATEDIFF(NOW(), d.deleted_at) AS days_since_deletion
FROM documents d
INNER JOIN document_folders df ON d.folder_id = df.id
WHERE d.deleted_at IS NOT NULL
  AND d.deleted_at < DATE_SUB(NOW(), INTERVAL 365 DAY)
ORDER BY d.deleted_at ASC;
