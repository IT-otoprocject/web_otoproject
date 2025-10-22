<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController as AdminUserController;

// Test routes to bypass middleware temporarily
Route::prefix('test-admin')->group(function () {
    Route::get('users/template', [AdminUserController::class, 'downloadTemplate'])->name('test.users.template');
    Route::get('users/import', [AdminUserController::class, 'importPage'])->name('test.users.import');
    Route::get('users/bulk-edit', [AdminUserController::class, 'bulkEdit'])->name('test.users.bulk-edit');
});
