<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\KerjaMekanikController;
use App\Http\Controllers\ReportSpkController; // Tambahkan controller untuk report SPK
use App\Http\Controllers\SpkItemMekanikController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
});

// Route utama welcome
Route::get('/', function () {
    return view('welcome');
});

// Route dashboard utama (menu kotak SPK & PR)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route untuk menu SPK (daily_spk) - lebih profesional via controller
Route::get('/spk/daily', [SpkController::class, 'daily'])->middleware(['auth'])->name('spk.daily');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin', function () {
    return view('admin.dashboard');
})->middleware('level:admin');

Route::get('/kasir', function () {
    return view('kasir.dashboard');
})->middleware('level:kasir');

Route::get('/mekanik', function () {
    return view('mekanik.dashboard');
})->middleware('level:mekanik');


// Rute untuk dashboard admin
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

// Admin routes with middleware
Route::middleware(['auth', 'system_access:user_management'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
});

// Test route for checking system access
Route::middleware(['auth'])->get('/test-access', function () {
    return view('test-access');
})->name('test.access');

// rute untuk SPK  
// Route::get('/spk/create', function () {
//     return view('spk.create');
// })->middleware('level:kasir');

// Route::post('/spk', function () {
//     return view('spk.store');
// })->middleware('level:kasir');

// rute untuk SPK
Route::get('/spk/create', [SpkController::class, 'create'])->name('spk.create');
Route::post('/spk', [SpkController::class, 'store'])->name('spk.store');
Route::get('/spk', [SpkController::class, 'index'])->name('spk.index');

// Rute untuk mekanik.spk.show (detail SPK)
Route::get('/mekanik/spk/{id}', [SpkController::class, 'show'])->name('mekanik.spk.show');
// rute untuk tombol cancel (detail SPK)
Route::put('/spk/{spk_id}/cancel', [SpkController::class, 'cancel'])->name('spk.cancel');

// Route untuk halaman edit
Route::get('/spk/edit/{spk_id}', [SpkController::class, 'edit'])->name('spk.edit');

// Route untuk pembaruan data
Route::put('/spk/update/{spk_id}', [SpkController::class, 'update'])->name('spk.update');

Route::get('/spk/{spk_id}/edit-barang', [SpkController::class, 'editBarang'])->name('spk.editBarang');
Route::put('/spk/{spk_id}/update-barang', [SpkController::class, 'updateBarang'])->name('spk.updateBarang');

Route::delete('/spk/barang/{id}', [SpkController::class, 'destroyBarang'])->name('spk.destroyBarang');

// Rute untuk kerja mekanik
Route::post('mekanik/spk/kerja-mekanik/{spk_id}', [KerjaMekanikController::class, 'waktu_mulai_kerja'])->name('spk.waktuMulaiKerja');
Route::get('mekanik/spk/kerja-mekanik/{spk_id}', [KerjaMekanikController::class, 'show'])->name('kerja.mekanik');
Route::post('mekanik/spk/kerja-selesai/{spk_id}', [KerjaMekanikController::class, 'selesai'])->name('kerja.selesai');

// Route untuk halaman report SPK dan export ke Excel
Route::get('/report/spk', function() {
    return view('report.spk.index');
})->name('report.spk.index')->middleware(['auth']);
Route::get('/report/spk/report_spk', function() {
    return view('report.spk.report_spk');
})->name('report.spk.report_spk')->middleware(['auth']);
Route::get('/report/spk/export', [ReportSpkController::class, 'export'])->name('report.spk.export');
// Route untuk export rata-rata waktu pengerjaan barang ke Excel
Route::get('/report/spk/export-avg-barang', [ReportSpkController::class, 'exportAvgBarang'])->name('report.spk.export_avg_barang');

// Report SPK Barang (Export Rata-rata Barang per SKU)
Route::get('/report/spk/barang', function() {
    return view('report.spk.report_spk_barang');
})->name('report.spk.barang')->middleware(['auth']);

// Report SPK Mekanik per Produk (Export Rata-rata Mekanik per Produk)
Route::get('/report/spk/mekanik-product', function() {
    return view('report.spk.report_spk_mekanik_product');
})->name('report.spk.mekanik_product')->middleware(['auth']);

Route::get('/report/spk/export-avg-mekanik-product', [\App\Http\Controllers\ReportSpkController::class, 'exportAvgMekanikProduct'])->name('report.spk.export_avg_mekanik_product')->middleware(['auth']);

// Report SPK Mekanik (Export Rata-rata Waktu Kerja Mekanik)
Route::get('/report/spk/mekanik', function() {
    return view('report.spk.report_spk_mekanik');
})->name('report.spk.mekanik')->middleware(['auth']);

Route::get('/report/spk/export-avg-mekanik', [\App\Http\Controllers\ReportSpkController::class, 'exportAvgMekanik'])->name('report.spk.export_avg_mekanik')->middleware(['auth']);

// Pilih mekanik untuk item SPK
Route::get('/spk/{spk}/pilih-mekanik', [SpkItemMekanikController::class, 'form'])->name('spk.items.pilihMekanik');
Route::post('/spk/{spk}/assign-mekanik', [SpkItemMekanikController::class, 'assign'])->name('spk.items.assignMekanik');

// Route untuk AJAX
Route::post('/spk/item/waktu-pengerjaan', [KerjaMekanikController::class, 'setWaktuPengerjaanBarang'])->name('spk.item.waktu_pengerjaan');

// Route Odoo Product
Route::get('/odoo/products', [\App\Http\Controllers\ProductOdooController::class, 'index'])->name('odoo.products');
Route::get('/odoo/products/{db}/{id}', [\App\Http\Controllers\ProductOdooController::class, 'show'])->name('odoo.products.show');

// Tambahkan route ini
Route::get('/api/search-products', [App\Http\Controllers\Api\ProductSearchController::class, 'search']);

// Route untuk data barang (JSON) untuk kebutuhan AJAX reload produk di kerja_mekanik
Route::get('/spk/{spk_id}/items/json', [SpkController::class, 'itemsJson'])->name('spk.items.json');

// Routes untuk Purchase Request
Route::middleware(['auth', 'system_access:pr'])->group(function () {
    Route::resource('purchase-request', App\Http\Controllers\Access_PR\Purchase_Request\PurchaseRequestController::class);
    Route::post('purchase-request/{purchaseRequest}/approve', [App\Http\Controllers\Access_PR\Purchase_Request\PurchaseRequestController::class, 'approve'])->name('purchase-request.approve');
    Route::post('purchase-request/{purchaseRequest}/reject', [App\Http\Controllers\Access_PR\Purchase_Request\PurchaseRequestController::class, 'reject'])->name('purchase-request.reject');
    Route::post('purchase-request/{purchaseRequest}/update-status', [App\Http\Controllers\Access_PR\Purchase_Request\PurchaseRequestController::class, 'updateStatus'])->name('purchase-request.update-status');
    Route::post('purchase-request/{purchaseRequest}/bulk-update-item-status', [App\Http\Controllers\Access_PR\Purchase_Request\PurchaseRequestController::class, 'bulkUpdateItemStatus'])->name('purchase-request.bulk-update-item-status');
    Route::post('purchase-request/{purchaseRequest}/ga-approve-with-items', [App\Http\Controllers\Access_PR\Purchase_Request\PurchaseRequestController::class, 'gaApproveWithItemSelection'])->name('purchase-request.ga-approve-with-items');
    Route::post('purchase-request/{purchaseRequest}/purchasing-partial-approval', [App\Http\Controllers\Access_PR\Purchase_Request\PurchaseRequestController::class, 'purchasingPartialApproval'])->name('purchase-request.purchasing-partial-approval');
    Route::post('purchase-request/{purchaseRequest}/add-attachment', [App\Http\Controllers\Access_PR\Purchase_Request\PurchaseRequestController::class, 'addAttachment'])->name('purchase-request.add-attachment');
    Route::delete('purchase-request/{purchaseRequest}/delete-attachment', [App\Http\Controllers\Access_PR\Purchase_Request\PurchaseRequestController::class, 'deleteAttachment'])->name('purchase-request.delete-attachment');
    Route::post('purchase-request/{purchaseRequest}/update-asset-number', [App\Http\Controllers\Access_PR\Purchase_Request\PurchaseRequestController::class, 'updateAssetNumber'])->name('purchase-request.update-asset-number');
    Route::post('purchase-request/{purchaseRequest}/assign-asset-numbers', [App\Http\Controllers\Access_PR\Purchase_Request\PurchaseRequestController::class, 'assignAssetNumbers'])->name('purchase-request.assign-asset-numbers');
    
    // Routes untuk PR Categories (hanya FAT manager dan SPV)
    Route::resource('pr-categories', App\Http\Controllers\Access_PR\PrCategoryController::class);
    Route::post('pr-categories/{prCategory}/toggle-status', [App\Http\Controllers\Access_PR\PrCategoryController::class, 'toggleStatus'])->name('pr-categories.toggle-status');
    
    // Routes untuk Master Location dalam PR Module
    Route::resource('master-locations', App\Http\Controllers\Access_PR\MasterLocationController::class);
    Route::post('master-locations/{masterLocation}/toggle-status', [App\Http\Controllers\Access_PR\MasterLocationController::class, 'toggleStatus'])->name('master-locations.toggle-status');
    Route::get('api/master-locations', [App\Http\Controllers\Access_PR\MasterLocationController::class, 'getLocations'])->name('api.master-locations');
});

require __DIR__ . '/auth.php';
