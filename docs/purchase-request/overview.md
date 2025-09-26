# 📋 Purchase Request System - Overview

## 🎯 Tujuan
Sistem Purchase Request (PR) dirancang untuk mengotomatisasi proses pengajuan pembelian barang/jasa dalam perusahaan dengan workflow approval yang terstruktur.

## 🏗️ Arsitektur Sistem

### 📊 Komponen Utama
- **Purchase Request Model** - Model utama untuk data PR
- **Purchase Request Items** - Detail item yang diminta
- **Approval Workflow** - Sistem persetujuan bertingkat
- **Status Updates** - Tracking status purchasing
- **Notification System** - Notifikasi real-time

### 🗂️ Struktur Database
```
purchase_requests
├── id (Primary Key)
├── pr_number (Unique PR Number)
├── user_id (Pembuat PR)
├── request_date
├── due_date
├── description
├── location
├── status (DRAFT, SUBMITTED, APPROVED, REJECTED, COMPLETED)
├── approval_flow (JSON - Alur approval)
├── approvals (JSON - Data approval)
└── notes

purchase_request_items
├── id
├── purchase_request_id
├── item_name
├── quantity
├── unit
├── estimated_price
├── description
└── notes

purchase_request_status_updates
├── id
├── purchase_request_id
├── update_type
├── description
├── data (JSON)
├── updated_by
└── created_at
```

## 🔄 Status PR

### Status Utama
1. **DRAFT** - PR masih dalam tahap pembuatan
2. **SUBMITTED** - PR telah disubmit dan menunggu approval
3. **APPROVED** - PR telah disetujui semua level
4. **REJECTED** - PR ditolak di salah satu level
5. **COMPLETED** - PR telah selesai diproses purchasing

### Approval Levels
1. **Department Head** - Manager dari divisi yang sama
2. **GA (General Affairs)** - Staff/SPV/Manager HCGA
3. **Finance Department** - SPV/Manager FAT
4. **CEO Approval** - Chief Executive Officer
5. **CFO Approval** - Chief Financial Officer

## 🎭 Roles & Permissions

### User Levels
- **Admin** - Akses penuh, dapat approve semua level
- **CEO** - Dapat approve level CEO
- **CFO** - Dapat approve level CFO
- **Manager** - Dapat approve sebagai Department Head
- **SPV/Staff HCGA** - Dapat approve sebagai GA
- **SPV/Manager FAT** - Dapat approve sebagai Finance Dept
- **Staff PURCHASING** - Dapat update status purchasing

### Divisi yang Terlibat
- **Semua Divisi** - Dapat membuat PR
- **HCGA** - Berperan sebagai GA approval
- **FAT** - Berperan sebagai Finance approval
- **PURCHASING** - Memproses PR yang sudah approved

## 🚦 Business Rules

### Pembuatan PR
- User dapat membuat PR untuk divisinya
- PR minimal harus memiliki 1 item
- Estimated price harus diisi untuk setiap item

### Approval Process
- PR harus melalui semua level approval secara berurutan
- Jika ditolak di satu level, proses approval berhenti
- Approval harus dilakukan oleh user yang berwenang sesuai levelnya

### Purchasing Process
- Hanya PR dengan status APPROVED yang dapat diproses purchasing
- User divisi PURCHASING dapat menambah status update
- Status dapat diubah menjadi COMPLETED setelah proses selesai

## 📱 Fitur Utama

### 1. Dashboard & Notifications
- Notifikasi real-time untuk pending approvals
- Notifikasi khusus untuk purchasing department
- Dashboard statistics dan overview

### 2. PR Management
- Create, Read, Update, Delete PR
- Bulk operations untuk admin
- Export ke Excel/PDF

### 3. Approval Workflow
- Sequential approval process
- Email notifications (optional)
- Approval history tracking

### 4. Purchasing Management
- Status updates dengan timestamps
- Progress tracking
- Integration dengan vendor management

### 5. Reporting
- PR reports berdasarkan periode
- Approval statistics
- Purchasing performance metrics

## 🔗 Integrasi

### Internal Systems
- User Management System
- Notification System
- Reporting Module

### External APIs
- Email Service (untuk notifikasi)
- Export Services (Excel/PDF)

## 📈 Metrics & KPI

### Performance Indicators
- Average approval time per level
- Rejection rate by department
- Purchasing completion time
- Monthly PR volume

### Reporting Features
- Real-time dashboards
- Customizable reports
- Export capabilities
- Historical data analysis

---

**Next:** [Workflow Documentation](workflow.md)
