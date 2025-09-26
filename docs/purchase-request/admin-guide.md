# ⚙️ Purchase Request - Admin Guide

## 🔧 System Administration

### Overview
Sebagai admin, Anda bertanggung jawab untuk:
- **User Management** - Mengelola user dan roles
- **System Configuration** - Mengatur settings
- **Data Maintenance** - Backup dan monitoring
- **Troubleshooting** - Mengatasi masalah sistem

## 👥 User Management

### Roles & Permissions

#### Role Hierarchy
```
┌─────────────────┐
│      CEO        │ ← Level tertinggi
├─────────────────┤
│      CFO        │ ← Finance oversight
├─────────────────┤
│   FINANCE_DEPT  │ ← Finance department
├─────────────────┤
│       GA        │ ← General Affairs
├─────────────────┤
│   DEPT_HEAD     │ ← Department heads
├─────────────────┤
│   PURCHASING    │ ← Purchasing department
├─────────────────┤
│      USER       │ ← Regular users
└─────────────────┘
```

#### Permission Matrix
| Action | USER | PURCHASING | DEPT_HEAD | GA | FINANCE_DEPT | CFO | CEO |
|--------|------|------------|-----------|----|--------------|----|-----|
| Create PR | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| View Own PR | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| View All PR | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Approve as Dept Head | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Approve as GA | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ |
| Approve as Finance | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| Approve as CFO | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| Approve as CEO | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| Process Purchasing | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| User Admin | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |

### Managing Users

#### Membuat User Baru
1. **Akses User Management**
   - Menu Admin → User Management
   - Klik "Tambah User Baru"

2. **Isi Form User**
```
┌─────────────────────────────────────┐
│ Name: [Nama lengkap]                │
│ Email: [email@company.com]          │
│ Password: [Generate/Manual]         │
│ Level: [Pilih level sesuai jabatan] │
│ Divisi: [Divisi/Department]         │
│ Status: [Active/Inactive]           │
└─────────────────────────────────────┘
```

3. **Panduan Level Assignment**
   - **USER**: Staff biasa, pembuat PR
   - **DEPT_HEAD**: Kepala divisi, approver pertama
   - **GA**: General Affairs, approver kedua
   - **FINANCE_DEPT**: Finance staff, approver ketiga
   - **CFO**: Chief Financial Officer, approver keempat
   - **CEO**: Chief Executive Officer, final approver
   - **PURCHASING**: Tim purchasing, processor PR

#### Edit User Existing
1. **Cari User** di tabel user management
2. **Klik "Edit"** pada user yang dipilih
3. **Update informasi** yang diperlukan
4. **Save Changes**

⚠️ **Perhatian Level Changes:**
- Mengubah level user akan langsung mempengaruhi approval rights
- Backup data sebelum mengubah level kritikal (CEO, CFO)
- Test approval flow setelah perubahan level

#### Reset Password User
1. **Pilih User** yang perlu reset password
2. **Klik "Reset Password"**
3. **Generate New Password** atau set manual
4. **Kirim credentials** ke user via email secure
5. **Wajibkan user** untuk change password pada login pertama

#### Deactivate/Reactivate User
**Deactivate:**
- User tidak dapat login
- PR history tetap tersimpan
- Approval yang pending akan ter-stuck

**Reactivate:**
- User dapat login kembali
- Semua access rights dikembalikan

### User Level Configuration

#### CEO & CFO Levels
**Special Properties:**
- **Divisi Field**: Optional untuk CEO/CFO
- **Global Access**: Dapat melihat semua PR
- **Final Authority**: CEO adalah final approver
- **Financial Oversight**: CFO focus pada finance approval

**Setup Guidelines:**
```
CEO Setup:
├── Level: CEO
├── Divisi: [Kosong/Optional]
├── Email: ceo@company.com
└── Global Notifications: ON

CFO Setup:
├── Level: CFO  
├── Divisi: [Kosong/Optional]
├── Email: cfo@company.com
└── Finance Notifications: ON
```

## 🔧 System Configuration

### Database Management

#### Backup Schedule
**Daily Backup:**
```powershell
# Automated backup script (Run as scheduled task)
mysqldump -u [username] -p[password] [database_name] > backup_$(Get-Date -Format "yyyyMMdd").sql
```

**Weekly Full Backup:**
```powershell
# Full system backup including files
Compress-Archive -Path "C:\path\to\application" -DestinationPath "backup_full_$(Get-Date -Format "yyyyMMdd").zip"
```

#### Database Maintenance
**Monthly Tasks:**
1. **Analyze Tables**
   ```sql
   ANALYZE TABLE users, spks, spk_items, cache, jobs;
   ```

2. **Cleanup Old Data**
   ```sql
   -- Cleanup cache older than 30 days
   DELETE FROM cache WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
   
   -- Cleanup failed jobs older than 7 days  
   DELETE FROM failed_jobs WHERE failed_at < DATE_SUB(NOW(), INTERVAL 7 DAY);
   ```

3. **Index Optimization**
   ```sql
   OPTIMIZE TABLE spks, spk_items;
   ```

### Application Monitoring

#### Performance Metrics
**Key Indicators:**
- Response time < 2 seconds
- Database query time < 500ms
- Memory usage < 80%
- Disk space > 20% free

#### Log Monitoring
**Important Log Files:**
```
├── storage/logs/laravel.log     # Application errors
├── storage/logs/query.log       # Database queries  
├── stderr.log                   # System errors
└── public/error_log            # Web server errors
```

**Daily Checks:**
```powershell
# Check for errors in logs
Select-String -Path "storage/logs/laravel.log" -Pattern "ERROR" | Select-Object -Last 10

# Check disk space
Get-WmiObject -Class Win32_LogicalDisk | Select-Object DeviceID, @{n="Free(GB)";e={[math]::Round($_.FreeSpace/1GB,2)}}
```

### Notification Settings

#### Email Configuration
**SMTP Settings** (config/mail.php):
```php
'mailers' => [
    'smtp' => [
        'host' => env('MAIL_HOST', 'smtp.company.com'),
        'port' => env('MAIL_PORT', 587),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
    ],
],
```

**Notification Rules:**
- PR submission → Notify relevant approver
- PR approval → Notify next level approver
- PR completion → Notify requestor
- Daily summary → Notify managers

## 📊 Reporting & Analytics

### Standard Reports

#### Daily PR Summary
```sql
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_prs,
    COUNT(CASE WHEN status = 'SUBMITTED' THEN 1 END) as pending,
    COUNT(CASE WHEN status = 'APPROVED' THEN 1 END) as approved,
    COUNT(CASE WHEN status = 'REJECTED' THEN 1 END) as rejected
FROM spks 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

#### Approval Time Analysis
```sql
SELECT 
    s.id,
    s.pr_number,
    s.created_at,
    s.approved_at,
    DATEDIFF(s.approved_at, s.created_at) as approval_days
FROM spks s 
WHERE s.status = 'APPROVED'
AND s.approved_at IS NOT NULL
ORDER BY approval_days DESC;
```

#### User Activity Report
```sql
SELECT 
    u.name,
    u.level,
    COUNT(s.id) as total_prs,
    AVG(si.estimated_price * si.quantity) as avg_pr_value
FROM users u
LEFT JOIN spks s ON u.id = s.user_id
LEFT JOIN spk_items si ON s.id = si.spk_id
WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
GROUP BY u.id, u.name, u.level
ORDER BY total_prs DESC;
```

### Custom Reports

#### Create Report Views
```sql
-- Create view for management dashboard
CREATE VIEW pr_management_dashboard AS
SELECT 
    s.status,
    COUNT(*) as count,
    SUM(CASE WHEN si.estimated_price IS NOT NULL 
        THEN si.estimated_price * si.quantity 
        ELSE 0 END) as total_value
FROM spks s
LEFT JOIN spk_items si ON s.id = si.spk_id
WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY s.status;
```

## 🚨 Troubleshooting

### Common Issues

#### 1. PR Stuck in Approval
**Symptoms:**
- PR tidak bergerak dari satu level ke level berikutnya
- Approver tidak menerima notifikasi

**Diagnosis:**
```sql
-- Check approval flow
SELECT 
    id, pr_number, status, dept_head_approval, 
    ga_approval, finance_dept_approval, cfo_approval, ceo_approval
FROM spks 
WHERE status = 'SUBMITTED';
```

**Solutions:**
- Verify approver user levels
- Check email settings
- Manually trigger notification
- Reset approval if necessary

#### 2. Database Connection Issues
**Error Signs:**
- "Connection refused" errors
- Slow page loads
- Query timeouts

**Check Connection:**
```powershell
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

**Solutions:**
- Restart database service
- Check connection limits
- Verify credentials in .env
- Monitor server resources

#### 3. File Permission Issues
**Common on Windows:**
- storage/ directory not writable
- cache/ directory not accessible

**Fix Permissions:**
```powershell
# Fix storage permissions
icacls "storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "bootstrap/cache" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

#### 4. Session/Cache Issues
**Symptoms:**
- Users logged out frequently
- Old data still showing
- Cache not updating

**Clear Cache:**
```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Emergency Procedures

#### System Recovery
**If system is down:**
1. **Check server status**
2. **Verify database connectivity**
3. **Check application logs**
4. **Restart services if needed**
5. **Verify backup availability**

#### Data Recovery
**If data corruption occurs:**
1. **Stop application immediately**
2. **Assess damage scope**
3. **Restore from latest backup**
4. **Verify data integrity**
5. **Inform users of downtime**

#### Rollback Procedures
**If update fails:**
```powershell
# Rollback to previous version
git checkout [previous-commit-hash]
php artisan migrate:rollback
php artisan cache:clear
```

## 📋 Maintenance Checklist

### Daily Tasks
- [ ] Check system logs for errors
- [ ] Monitor server resources
- [ ] Verify backup completion
- [ ] Check PR approval queues

### Weekly Tasks  
- [ ] Review user activity reports
- [ ] Check database performance
- [ ] Update security patches
- [ ] Clean up temporary files

### Monthly Tasks
- [ ] Full system backup
- [ ] Database optimization
- [ ] User access review
- [ ] Performance analysis

### Quarterly Tasks
- [ ] Security audit
- [ ] Disaster recovery test
- [ ] User training updates
- [ ] System capacity planning

---

**Next:** [API Reference](api-reference.md)
