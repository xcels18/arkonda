<?php

use App\Http\Controllers\Admin\ContractController as AdminContractController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\OpdController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractController;
use Illuminate\Support\Facades\Route;

// ==================== Guest Routes ====================
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ==================== Auth Routes (all authenticated users) ====================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/verification-pending', [AuthController::class, 'verificationPending'])->name('verification.pending');
});

// ==================== Verified User Routes ====================
Route::middleware(['auth', 'verified.user'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Contracts for regular users (view + create for their own OPD)
    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
    Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
    Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
    Route::get('/contracts/{contract}/download', [ContractController::class, 'download'])->name('contracts.download');
    Route::get('/contracts/{contract}/preview', [ContractController::class, 'preview'])->name('contracts.preview');
    
    // API untuk validasi nomor kontrak
    Route::post('/api/validate-contract-number', [ContractController::class, 'validateContractNumber'])->name('api.validate.contract');
});

// ==================== Admin Routes ====================
Route::middleware(['auth', 'verified.user', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/verify', [UserController::class, 'verify'])->name('users.verify');
    Route::post('/users/{user}/unverify', [UserController::class, 'unverify'])->name('users.unverify');
    Route::patch('/users/{user}/role', [UserController::class, 'changeRole'])->name('users.changeRole');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // OPD Management
    Route::resource('opds', OpdController::class)->except(['show']);

    // Contract Management (CRUD)
    Route::resource('contracts', AdminContractController::class);
    Route::post('/contracts/validate-number', [AdminContractController::class, 'validateContractNumber'])->name('contracts.validate');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Downloads
    Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
    Route::post('/downloads/zip', [DownloadController::class, 'download'])->name('downloads.zip');
});
