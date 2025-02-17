<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeDashboardController;

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



require __DIR__.'/auth.php';
