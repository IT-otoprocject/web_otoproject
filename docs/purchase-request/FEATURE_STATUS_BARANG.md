# Purchase Request - Status Barang dan Keterangan Purchasing

## Fitur yang Ditambahkan

### 1. Status Barang untuk Item
- Setiap item dalam Purchase Request kini memiliki field `item_status` dengan pilihan:
  - PENDING (default)
  - VENDOR_SEARCH (Pencarian Vendor)
  - PRICE_COMPARISON (Perbandingan Harga)  
  - PO_CREATED (PO ke Vendor)
  - GOODS_RECEIVED (Barang Diterima)
  - GOODS_RETURNED (Barang Dikembalikan)
  - TERSEDIA_DI_GA (Tersedia di GA)
  - CLOSED (Closed)

### 2. Keterangan dari Purchasing
- Field `purchasing_notes` untuk catatan dari tim purchasing per item

### 3. Update Status Barang (Bulk)
- Tim Purchasing dapat memilih beberapa item sekaligus (bulk selection)
- Update status dan keterangan untuk item yang dipilih
- Item dengan status "TERSEDIA_DI_GA" tidak dapat diupdate lagi
- Hanya bisa dilakukan setelah PR fully approved

### 4. Enhanced GA Approval
- Saat GA approval, ada opsi untuk memilih barang yang sudah tersedia di GA
- Jika beberapa barang dipilih sebagai "Tersedia di GA":
  - Status item berubah menjadi "TERSEDIA_DI_GA"
  - Approval tetap dilanjutkan ke level berikutnya
- Jika SEMUA barang tersedia di GA:
  - PR status langsung menjadi "COMPLETED"
  - Approval level menjadi "Tersedia di GA"

### 5. Database Changes
```sql
-- Menambah kolom ke purchase_request_items
ALTER TABLE purchase_request_items ADD COLUMN item_status ENUM('PENDING','VENDOR_SEARCH','PRICE_COMPARISON','PO_CREATED','GOODS_RECEIVED','GOODS_RETURNED','TERSEDIA_DI_GA','CLOSED') DEFAULT 'PENDING';
ALTER TABLE purchase_request_items ADD COLUMN purchasing_notes TEXT NULL;

-- Update enum untuk purchase_request_status_updates
ALTER TABLE purchase_request_status_updates MODIFY COLUMN update_type ENUM('VENDOR_SEARCH','PRICE_COMPARISON','PO_CREATED','GOODS_RECEIVED','GOODS_RETURNED','TERSEDIA_DI_GA','CLOSED');
```

### 6. Route yang Ditambahkan
```php
Route::post('purchase-request/{purchaseRequest}/bulk-update-item-status', 'bulkUpdateItemStatus')->name('purchase-request.bulk-update-item-status');
Route::post('purchase-request/{purchaseRequest}/ga-approve-with-items', 'gaApproveWithItemSelection')->name('purchase-request.ga-approve-with-items');
```

### 7. Controller Methods Baru
- `bulkUpdateItemStatus()` - Update status item secara bulk
- `gaApproveWithItemSelection()` - GA approval dengan seleksi item

### 8. View Enhancements
- Tabel item menampilkan status barang dan catatan purchasing
- Checkbox untuk bulk selection (hanya untuk purchasing team)
- Form bulk update status barang
- Enhanced approve modal untuk GA dengan item selection
- Mobile view yang sudah disesuaikan

## Cara Penggunaan

### Untuk Tim Purchasing:
1. Buka Purchase Request yang sudah fully approved
2. Pilih item yang ingin diupdate (centang checkbox)
3. Pilih status barang dari dropdown
4. Tambahkan keterangan (opsional)
5. Klik "Update Status Item Terpilih"

### Untuk Tim GA:
1. Saat approve PR di level GA
2. Jika ada barang yang tersedia, centang item tersebut
3. Tambahkan catatan approval
4. Klik "Ya, Setujui"
5. Sistem akan otomatis mengatur status berdasarkan kondisi:
   - Jika semua barang tersedia → PR COMPLETED
   - Jika sebagian → lanjut ke approval berikutnya

## Technical Implementation

### Model Updates:
- `PurchaseRequestItem`: Tambah fields `item_status`, `purchasing_notes`
- `PurchaseRequestStatusUpdate`: Update enum values
- `PurchaseRequest`: Tambah approval level "tersedia_di_ga"

### Security:
- Hanya purchasing team yang bisa bulk update item status  
- Item dengan status "TERSEDIA_DI_GA" tidak bisa diupdate
- Validasi role dan permission sesuai existing system

### UI/UX:
- Color-coded status badges
- Intuitive bulk selection with "Select All" option
- Responsive design (desktop + mobile)
- Clear visual feedback untuk selected items
