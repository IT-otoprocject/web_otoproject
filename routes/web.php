<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SpkController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
});


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

// Route::middleware(['auth', 'level:kasir'])->group(function () {
//     Route::get('/spk/create', [SpkController::class, 'create'])->name('spk.create');
//     Route::post('/spk', [SpkController::class, 'store'])->name('spk.store');
// });

// Route::middleware(['auth', 'level:mekanik'])->group(function () {
//     Route::get('/mekanik/spk', [SpkController::class, 'index'])->name('mekanik.spk.index');
//     Route::get('/mekanik/spk/{id}', [SpkController::class, 'show'])->name('mekanik.spk.show');
// });



require __DIR__.'/auth.php';