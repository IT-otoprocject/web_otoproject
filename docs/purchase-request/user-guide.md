# 👤 Purchase Request - User Guide

## 🚀 Getting Started

### Login ke Sistem
1. Buka browser dan akses aplikasi web
2. Masukkan email dan password
3. Klik "Login"
4. Dashboard akan menampilkan menu utama

### 📊 Dashboard Overview
Setelah login, Anda akan melihat:
- **Notifications** - PR yang memerlukan action dari Anda
- **Quick Stats** - Ringkasan PR Anda
- **Recent Activity** - Aktivitas terbaru
- **Navigation Menu** - Akses ke fitur utama

## 📝 Membuat Purchase Request Baru

### Step 1: Akses Form PR
1. Klik menu **"Purchase Request"**
2. Pilih **"Buat PR Baru"**
3. Form pembuatan PR akan terbuka

### Step 2: Isi Informasi Dasar
```
┌─────────────────────────────────────┐
│ Request Date: [Tanggal Pengajuan]   │
│ Due Date: [Tanggal Dibutuhkan]      │
│ Description: [Deskripsi PR]         │
│ Location: [Lokasi Penggunaan]       │
└─────────────────────────────────────┘
```

**Tips Pengisian:**
- **Request Date**: Otomatis terisi tanggal hari ini
- **Due Date**: Pilih tanggal kapan barang dibutuhkan
- **Description**: Jelaskan tujuan/keperluan secara singkat
- **Location**: Lokasi/department yang akan menggunakan

### Step 3: Tambah Items
1. Klik tombol **"Tambah Item"**
2. Isi detail item:

```
┌─────────────────────────────────────┐
│ Nama Item: [Nama barang/jasa]       │
│ Quantity: [Jumlah] Unit: [Satuan]   │
│ Estimated Price: [Perkiraan harga]  │
│ Description: [Detail spesifikasi]   │
│ Notes: [Catatan tambahan]           │
└─────────────────────────────────────┘
```

**Contoh Pengisian:**
- Nama Item: "Laptop Dell Latitude 5520"
- Quantity: 2, Unit: "Unit"
- Estimated Price: 15000000
- Description: "Laptop untuk staff IT baru"

### Step 4: Review & Submit
1. Periksa kembali semua data
2. Pastikan items sudah lengkap dan benar
3. Klik **"Submit PR"**
4. PR akan berubah status menjadi "SUBMITTED"

## 📋 Mengelola PR Anda

### Melihat Daftar PR
1. Menu **"Purchase Request"** → **"Daftar PR"**
2. Akan muncul tabel berisi semua PR Anda:

```
┌────────────┬─────────────┬──────────┬─────────────┬─────────────┐
│ PR Number  │ Description │ Status   │ Created     │ Action      │
├────────────┼─────────────┼──────────┼─────────────┼─────────────┤
│ PR-2025001 │ Office...   │ APPROVED │ 01/09/2025  │ [View]      │
│ PR-2025002 │ IT Equip... │ PENDING  │ 03/09/2025  │ [View][Edit]│
└────────────┴─────────────┴──────────┴─────────────┴─────────────┘
```

### Melihat Detail PR
1. Klik tombol **"View"** pada PR yang ingin dilihat
2. Halaman detail akan menampilkan:
   - **Informasi Dasar** PR
   - **Daftar Items** yang diminta
   - **Status Approval** saat ini
   - **History Approval** dengan timestamps
   - **Status Updates** dari purchasing (jika sudah approved)

### Edit PR (Hanya Status DRAFT)
⚠️ **Penting:** PR hanya dapat diedit jika statusnya masih DRAFT

1. Klik tombol **"Edit"** pada PR
2. Ubah data yang diperlukan
3. Klik **"Update PR"**

## 🔔 Notifikasi & Status

### Memahami Status PR

#### 📝 DRAFT
- PR masih dalam tahap pembuatan
- Dapat diedit dan dihapus
- Belum masuk proses approval

#### 📤 SUBMITTED  
- PR telah disubmit untuk approval
- Tidak dapat diedit
- Menunggu approval Department Head

#### ✅ APPROVED
- Semua level telah menyetujui
- Siap diproses oleh purchasing
- Anda akan mendapat notifikasi

#### ❌ REJECTED
- PR ditolak di salah satu level
- Lihat reason penolakan di detail PR
- Dapat membuat PR baru jika diperlukan

#### 🎯 COMPLETED
- Purchasing process telah selesai
- Barang telah diterima
- PR officially closed

### Notifikasi yang Anda Terima

#### Sebagai Pembuat PR:
- ✅ PR approved di setiap level
- ❌ PR rejected dengan alasan
- 📦 Status update dari purchasing
- ✔️ PR completed

#### Sebagai Approver:
- 🔔 Ada PR baru yang perlu Anda approve
- ⏰ Reminder PR yang belum di-approve

## 🎯 Approval Process (Untuk Approver)

### Jika Anda adalah Approver
Anda akan melihat notifikasi khusus di dashboard:

```
┌─────────────────────────────────────────────────────────┐
│ 🔔 3 Purchase Request Menunggu Approval Anda!          │
│                                                         │
│ Sebagai Department Head, Anda dapat menyetujui PR      │
│ dari divisi HCGA                                        │
│                                                         │
│ PR yang memerlukan action Anda akan ditandai dengan    │
│ badge [Perlu Action] di kolom Approval Level.          │
└─────────────────────────────────────────────────────────┘
```

### Cara Approve/Reject PR:
1. **View PR Detail** - Klik PR yang perlu di-approve
2. **Review Informasi** - Periksa semua detail dan items
3. **Buat Keputusan:**
   - **Approve**: Klik "Approve" + isi notes (optional)
   - **Reject**: Klik "Reject" + wajib isi reason

### Best Practices untuk Approver:
- ✅ Review budget availability
- ✅ Check business necessity  
- ✅ Verify specifications
- ✅ Consider timing requirements
- ✅ Provide clear notes/feedback

## 🛒 Purchasing Process (Untuk User Purchasing)

### Jika Anda dari Divisi Purchasing
Anda akan melihat notifikasi khusus untuk PR yang approved:

```
┌─────────────────────────────────────────────────────────┐
│ 📦 2 Purchase Request Siap untuk Diproses!             │
│                                                         │
│ PR dengan status APPROVED perlu diproses hingga        │
│ status COMPLETED                                        │
│                                                         │
│ Langkah-langkah Purchasing:                            │
│ 1. Buka detail PR dengan status APPROVED               │
│ 2. Review item-item yang dibutuhkan                    │
│ 3. Lakukan purchasing/procurement                      │
│ 4. Update status PR melalui tombol "Update Status"     │
│ 5. Ubah status menjadi COMPLETED setelah selesai       │
└─────────────────────────────────────────────────────────┘
```

### Update Status Purchasing:
1. **Buka PR Detail** yang status APPROVED
2. **Klik "Tambah Update"** di bagian Status Updates
3. **Pilih Update Type:**
   - VENDOR_SEARCH - Sedang mencari vendor
   - PRICE_COMPARISON - Membandingkan harga
   - PO_CREATED - Purchase Order dibuat
   - GOODS_RECEIVED - Barang diterima
   - GOODS_RETURNED - Barang dikembalikan
   - CLOSED - Proses selesai
4. **Isi Description** dengan detail update
5. **Submit Update**

## 🆘 Troubleshooting

### Masalah Umum & Solusi

#### 1. Tidak Bisa Submit PR
**Kemungkinan Penyebab:**
- Form belum lengkap diisi
- Belum ada items yang ditambahkan
- Koneksi internet bermasalah

**Solusi:**
- Periksa semua field wajib (*)
- Pastikan minimal 1 item sudah ditambahkan
- Refresh halaman dan coba lagi

#### 2. PR Tidak Muncul di Daftar
**Solusi:**
- Refresh halaman
- Periksa filter status
- Hubungi admin jika masih bermasalah

#### 3. Tidak Dapat Edit PR
**Penyebab:** PR sudah status SUBMITTED/APPROVED
**Solusi:** PR yang sudah disubmit tidak dapat diedit. Buat PR baru jika perlu perubahan.

#### 4. Tidak Menerima Notifikasi
**Solusi:**
- Periksa pengaturan notifikasi
- Pastikan email terdaftar dengan benar
- Hubungi admin IT

## 📞 Bantuan & Support

### Kontak Support
- **IT Helpdesk**: ext. 123
- **Admin Sistem**: admin@company.com
- **Emergency**: 08123456789

### FAQ
**Q: Berapa lama proses approval?**
A: Biasanya 5-10 hari kerja tergantung kompleksitas PR

**Q: Bisa cancel PR yang sudah disubmit?**
A: Tidak. Hubungi approver untuk reject jika perlu dibatalkan

**Q: Maksimal berapa item dalam 1 PR?**
A: Tidak ada batas, tapi disarankan max 20 items per PR

---

**Next:** [Admin Guide](admin-guide.md)
