<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

// Import Controller
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManajemenPenggunaController;
use App\Http\Controllers\ManajemenPohonBibitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoryPerawatanController;
use App\Http\Controllers\FirestoreController;
use App\Http\Controllers\HistoryBarcodeController;
use App\Http\Controllers\DashboardController;

// Halaman Welcome (beranda)
Route::get('/', fn() => view('welcome'))->name('welcome');

// Login & Auth
Route::get('/login', fn() => view('layouts.login'))->name('login');
Route::post('/login', [AuthController::class, 'handleLogin']);
Route::get('/forgotpassword', fn() => view('layouts.forgotpassword'))->name('forgotpassword');
Route::get('/resendotp', fn() => view('layouts.resendotp'))->name('resendotp');

// Dashboard & Profile
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/profile', fn() => view('layouts.profile'))->name('profile');

// Manajemen Pengguna & Bibit
Route::get('/manajemen-pengguna', [ManajemenPenggunaController::class, 'index'])->name('manajemenpengguna');
Route::post('/update-admin', [ManajemenPenggunaController::class, 'updateAdmin']);
Route::post('/update-status', [ManajemenPenggunaController::class, 'updateStatus']);
Route::get('/manajemen-pengguna', [ManajemenPenggunaController::class, 'index'])->name('manajemenpengguna.index');

Route::get('/manajemen-kayu-bibit', [ManajemenPohonBibitController::class, 'index'])->name('manajemenkayubibit');

// Riwayat / History
Route::get('/history-perawatan', [HistoryPerawatanController::class, 'index'])->name('historyperawatan');
Route::get('/history', [HistoryBarcodeController::class, 'index'])->name('history.index');
Route::get('/history-scan-barcode', [HistoryBarcodeController::class, 'index'])->name('historyscanbarcode');

// Akun Superadmin
Route::get('/akun_superadmin', [FirestoreController::class, 'showSuperAdminForm'])->name('akun_superadmin.form');
Route::post('/akun_superadmin', [FirestoreController::class, 'storeSuperAdmin'])->name('akun_superadmin.store');

// Register User
Route::get('/register', [FirestoreController::class, 'showForm'])->name('register.form');
Route::post('/register', [FirestoreController::class, 'handleForm'])->name('register.store');

// Test Firebase Config (opsional debug)
Route::get('/test-firebase', function () {
    $firebaseCredentials = Config::get('firebase.credentials');

    if (file_exists($firebaseCredentials)) {
        return 'Firebase credentials file found at: ' . $firebaseCredentials;
    }

    return 'Firebase credentials file not found!';
});

Route::post('/add-admin', [ManajemenPenggunaController::class, 'store'])->name('admin.store');
