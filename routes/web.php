<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\RapatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\IuranAdminController;
use App\Http\Controllers\Admin\RapatAdminController;
use App\Http\Controllers\Admin\KaderisasiAdminController;

// =============================
// AUTH ROUTES
// =============================

Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// =============================
// HOME PER ROLE
// =============================

Route::middleware(['auth', 'role:admin'])->get('/admin/home', [DashboardAdminController::class, 'index'])->name('admin.home');Route::middleware(['auth', 'role:ketua'])->get('/ketua/home', fn() => view('ketua.welcome'))->name('ketua.home');
Route::middleware(['auth', 'role:user'])->get('/user/home', [DashboardController::class, 'index'])->name('user.home');

// =============================
// DASHBOARD
// =============================

Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// =============================
// IURAN (USER)
// =============================

Route::middleware(['auth', 'role:user'])->prefix('iuran')->name('iuran.user.')->group(function () {
    Route::get('/', [IuranController::class, 'index'])->name('index');
    Route::get('/create', [IuranController::class, 'create'])->name('create');
    Route::post('/', [IuranController::class, 'store'])->name('store');
    Route::get('/{id}', [IuranController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [IuranController::class, 'edit'])->name('edit');
    Route::put('/{id}', [IuranController::class, 'update'])->name('update');
    Route::delete('/{id}', [IuranController::class, 'destroy'])->name('destroy');
});

// =============================
// IURAN (ADMIN)
// =============================

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/iuran', [IuranAdminController::class, 'index'])->name('iuran.index');
    Route::get('/iuran/{id}', [IuranAdminController::class, 'show'])->name('iuran.show');  // Tambahkan ini
    Route::patch('/iuran/{id}/status', [IuranAdminController::class, 'updateStatus'])->name('iuran.updateStatus');
});


// =============================
// RAPAT (USER)
// =============================

Route::middleware(['auth', 'role:user'])->prefix('rapat')->name('rapat.user.')->group(function () {
    Route::get('/', [RapatController::class, 'index'])->name('index');
    Route::get('/create', [RapatController::class, 'create'])->name('create');
    Route::post('/', [RapatController::class, 'store'])->name('store');
    Route::get('/{id}', [RapatController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [RapatController::class, 'edit'])->name('edit');
    Route::put('/{id}', [RapatController::class, 'update'])->name('update');
    Route::delete('/{id}', [RapatController::class, 'destroy'])->name('destroy');
    Route::get('/menunggu', [RapatController::class, 'menunggu'])->name('menunggu');
});

// =============================
// RAPAT (ADMIN)
// =============================

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/rapat', [RapatAdminController::class, 'index'])->name('rapat.index');
    Route::get('/rapat/{id}', [RapatAdminController::class, 'show'])->name('rapat.show');
    Route::post('/rapat/{id}/terima', [RapatAdminController::class, 'terima'])->name('rapat.terima');
    Route::post('/rapat/{id}/tolak', [RapatAdminController::class, 'tolak'])->name('rapat.tolak');
    Route::patch('/rapat/{id}/status', [RapatAdminController::class, 'updateStatus'])->name('rapat.updateStatus');
});
// =============================
// User Kaderisasi
// =============================
Route::middleware(['auth', 'role:user'])->prefix('kaderisasi')->name('kaderisasi.user.')->group(function () {
    Route::get('/', [App\Http\Controllers\KaderisasiController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\KaderisasiController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\KaderisasiController::class, 'store'])->name('store');
    Route::get('/{kaderisasi}', [App\Http\Controllers\KaderisasiController::class, 'show'])->name('show');
    Route::get('/{kaderisasi}/edit', [App\Http\Controllers\KaderisasiController::class, 'edit'])->name('edit');
    Route::put('/{kaderisasi}', [App\Http\Controllers\KaderisasiController::class, 'update'])->name('update');
    Route::delete('/{kaderisasi}', [App\Http\Controllers\KaderisasiController::class, 'destroy'])->name('destroy');
});

// =============================
// Admin Kaderisasi
// =============================
Route::middleware(['auth', 'role:admin'])->prefix('admin/kaderisasi')->name('kaderisasi.admin.')->group(function () {
    Route::get('/', [KaderisasiAdminController::class, 'index'])->name('index');
    Route::get('/pdf', [KaderisasiAdminController::class, 'exportPdf'])->name('pdf'); // INI PENTING
    Route::get('/{id}', [KaderisasiAdminController::class, 'show'])->name('show');
    Route::put('/{id}/status', [KaderisasiAdminController::class, 'updateStatus'])->name('status.update');
    Route::delete('/{id}', [KaderisasiAdminController::class, 'destroy'])->name('destroy');
});


// =============================
// MANAJEMEN USER (ADMIN)
// =============================

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});

// =============================
// STATIC VIEWS (UNTUK KETUA / USER)
// =============================

Route::middleware('auth')->group(function () {
    Route::view('/inputiuran', 'iuran.inputiuran')->name('inputiuran');
    Route::view('/viewiuran', 'iuran.viewiuran')->name('viewiuran');
    Route::view('/viewtransaksi', 'iuran.viewtransaksi')->name('viewtransaksi');
    Route::view('/inputkaderisasi', 'kaderisasi.inputkaderisasi')->name('inputkaderisasi');
    Route::view('/viewkaderisasi', 'kaderisasi.viewkaderisasi')->name('viewkaderisasi');
});
