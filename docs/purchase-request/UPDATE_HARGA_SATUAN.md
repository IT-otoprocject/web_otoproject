# Purchase Request - Perubahan Harga Estimasi ke Harga Satuan

## Summary Perubahan

Berhasil mengubah sistem Purchase Request dari **"Estimasi Harga Total"** menjadi **"Estimasi Harga Satuan"** dengan kalkulasi otomatis berdasarkan **Quantity × Harga Satuan**.

## 📋 Changes Made

### 1. **View Updates - Create Purchase Request**
**File**: `resources/views/Access_PR/Purchase_Request/create.blade.php`

- ✅ Label "Estimasi Harga" → "Estimasi Harga Satuan" (semua template)
- ✅ Tambah `onchange="calculateTotal()"` pada input quantity
- ✅ Update JavaScript function `calculateTotal()`:
  ```javascript
  // OLD: Total dari sum semua estimated_price
  total += value;
  
  // NEW: Total dari (quantity × unit price) per item
  const quantity = parseFloat(quantityInput.value) || 0;
  const unitPrice = parseFloat(priceInput.value) || 0;
  const itemTotal = quantity * unitPrice;
  total += itemTotal;
  ```

### 2. **View Updates - Show Purchase Request**
**File**: `resources/views/Access_PR/Purchase_Request/show.blade.php`

#### Desktop Table:
- ✅ Header "Harga Est." → "Harga Est. Satuan"
- ✅ Tambah kolom baru "Total Harga Barang"
- ✅ Update colspan untuk total row (account for new column)
- ✅ Kalkulasi total: `$itemTotal = $item->quantity * $item->estimated_price`

#### Mobile View:
- ✅ Label "Harga Est." → "Harga Est. Satuan"
- ✅ Tambah section "Total Harga Barang" dengan styling khusus (blue font)
- ✅ Update total calculation dengan formula yang sama

#### Total Calculation:
```php
// NEW: Calculate based on quantity × unit price
$calculatedTotal = $purchaseRequest->items->sum(function($item) {
    return $item->quantity * ($item->estimated_price ?? 0);
});
$total = $purchaseRequest->total_estimated_price ?? $calculatedTotal;
```

### 3. **View Updates - Edit Purchase Request**
**File**: `resources/views/Access_PR/Purchase_Request/edit.blade.php`

- ✅ Update semua label "Estimasi Harga" → "Estimasi Harga Satuan"
- ✅ Update pada existing items template
- ✅ Update pada JavaScript template untuk new items

### 4. **Controller Updates**
**File**: `app/Http/Controllers/Access_PR/Purchase_Request/PurchaseRequestController.php`

#### Store Method:
```php
// OLD: Sum all estimated_price
$totalEstimatedPrice = collect($request->items)->sum(function($item) {
    return (float)($item['estimated_price'] ?? 0);
});

// NEW: Sum (quantity × unit price) for each item
$totalEstimatedPrice = collect($request->items)->sum(function($item) {
    $quantity = (float)($item['quantity'] ?? 0);
    $unitPrice = (float)($item['estimated_price'] ?? 0);
    return $quantity * $unitPrice;
});
```

#### Update Method:
- ✅ Tambah kalkulasi total yang sama
- ✅ Update `total_estimated_price` field saat update PR

## 🎯 Expected Behavior Sekarang

### 1. **Create/Edit PR**:
- Input "Estimasi Harga Satuan" per item
- Total otomatis kalkulasi: Qty × Harga Satuan untuk setiap item
- Real-time update saat user mengubah quantity atau harga satuan

### 2. **Show PR**:
- **Desktop**: Tabel dengan kolom terpisah untuk "Harga Est. Satuan" dan "Total Harga Barang"
- **Mobile**: Display yang terstruktur dengan total harga barang di highlight
- **Total Estimasi**: Sum dari semua (Qty × Harga Satuan) per item

### 3. **Database**:
- Field `estimated_price` di table `purchase_request_items` sekarang represents unit price
- Field `total_estimated_price` di table `purchase_requests` calculated dari sum(qty × unit_price)

## 📊 Example Calculation

### Sebelum:
- Item 1: Qty 5, Estimasi Harga: Rp 50.000 (total untuk 5 pcs)
- Item 2: Qty 10, Estimasi Harga: Rp 100.000 (total untuk 10 pcs)
- **Total**: Rp 150.000

### Sesudah:
- Item 1: Qty 5, Harga Satuan: Rp 10.000 → **Total Barang**: Rp 50.000
- Item 2: Qty 10, Harga Satuan: Rp 10.000 → **Total Barang**: Rp 100.000
- **Total Estimasi**: Rp 150.000

## ✅ All Changes Applied & Ready for Testing

- Views: Create, Show (Desktop/Mobile), Edit ✅
- Controllers: Store & Update methods ✅
- JavaScript: Real-time calculation ✅
- Cache cleared ✅

Sistem sekarang menggunakan harga satuan dengan kalkulasi otomatis quantity × unit price!
