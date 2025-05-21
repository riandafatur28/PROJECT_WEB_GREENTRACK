<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

// Import Controllers
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManajemenPenggunaController;
use App\Http\Controllers\ManajemenPohonBibitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoryPerawatanController;
use App\Http\Controllers\FirestoreController;
use App\Http\Controllers\HistoryBarcodeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// Login & Auth
Route::get('/login', fn() => view('layouts.login'))->name('login');
Route::post('/login', [AuthController::class, 'handleLogin']);
Route::get('/forgotpassword', fn() => view('layouts.forgotpassword'))->name('forgotpassword');
Route::get('/resendotp', fn() => view('layouts.resendotp'))->name('resendotp');

// Dashboard & Profile
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/profile', fn() => view('layouts.profile'))->name('profile');


// Manajemen Pengguna
Route::get('/manajemen-pengguna', [ManajemenPenggunaController::class, 'index'])->name('manajemenpengguna.index');
Route::post('/update-admin', [ManajemenPenggunaController::class, 'updateAdmin'])->name('manajemenpengguna.updateadmin');
Route::post('/update-status', [ManajemenPenggunaController::class, 'updateStatus'])->name('manajemenpengguna.updatestatus');
Route::post('/add-admin', [ManajemenPenggunaController::class, 'store'])->name('admin.store');

// Manajemen Kayu & Bibit
Route::get('/manajemen-kayu-bibit', [ManajemenPohonBibitController::class, 'index'])->name('manajemenkayubibit');
Route::post('/bibit/update-status', [ManajemenPohonBibitController::class, 'updateBibitStatus'])->name('bibit.update.status');
Route::post('/kayu/update-status', [ManajemenPohonBibitController::class, 'updateKayuStatus'])->name('kayu.update.status');
Route::post('/edit-bibit', [ManajemenPohonBibitController::class, 'editBibit']);
Route::post('/edit-kayu', [ManajemenPohonBibitController::class, 'editKayu']);
Route::post('/delete-bibit', [ManajemenPohonBibitController::class, 'deleteBibit']);
Route::post('/delete-kayu', [ManajemenPohonBibitController::class, 'deleteKayu']);


// Routes untuk Kayu
Route::prefix('kayu')->name('kayu.')->group(function () {
    Route::get('/', [ManajemenPohonBibitController::class, 'getKayu'])->name('index');
    Route::post('/update-status', [ManajemenPohonBibitController::class, 'updateKayuStatus'])->name('update.status');
    Route::post('/store', [ManajemenPohonBibitController::class, 'storeKayu'])->name('store');
    Route::put('/update/{id}', [ManajemenPohonBibitController::class, 'updateKayu'])->name('update');
    Route::delete('/delete/{id}', [ManajemenPohonBibitController::class, 'deleteKayu'])->name('delete');
});

// Routes untuk Bibit
Route::prefix('bibit')->name('bibit.')->group(function () {
    Route::get('/manajemen-kayu-bibit', [ManajemenPohonBibitController::class, 'index'])->name('manajemenkayubibit');
    Route::get('/', [ManajemenPohonBibitController::class, 'getBibit'])->name('index');
    Route::post('/update-status', [ManajemenPohonBibitController::class, 'updateBibitStatus'])->name('update.status');
    Route::post('/store', [ManajemenPohonBibitController::class, 'storeBibit'])->name('store');
    Route::post('/update/{id}', [ManajemenPohonBibitController::class, 'updateBibit'])->name('update');
    Route::delete('/delete/{id}', [ManajemenPohonBibitController::class, 'deleteBibit'])->name('delete');
});

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

// Admin Registration Route
Route::post('/register-admin', [FirestoreController::class, 'registerAdmin'])->name('register.admin');

// Test Firebase Config (Opsional Debug)
Route::get('/test-firebase', function () {
    $firebaseCredentials = Config::get('firebase.credentials');

    if (file_exists($firebaseCredentials)) {
        return 'Firebase credentials file found at: ' . $firebaseCredentials;
    }

    return 'Firebase credentials file not found!';
});

// Pemberitahuan Pengiriman Tautan Reset Kata Sandi
Route::get('/password-link-sent', fn() => view('layouts.password-link-sent'))->name('password-link-sent');

// Halaman Lupa Sandi (Menampilkan Formulir Email)
Route::get('/forgotpassword', fn() => view('layouts.forgotpassword'))->name('forgotpassword');

// Resend OTP (Untuk Pengguna yang Tidak Menerima Email)
Route::get('/resendotp', fn() => view('layouts.resendotp'))->name('resendotp');

// Halaman Kata Sandi Berhasil Diperbarui
Route::get('/password-reset-success', fn() => view('layouts.passwordupdate'))->name('password-reset-success');

// Setelah berhasil reset kata sandi
Route::post('/reset-password', [AuthController::class, 'handlePasswordReset'])->name('reset-password');

// Routes untuk Manajemen Pengguna
Route::get('/manajemen-pengguna', [ManajemenPenggunaController::class, 'index'])->name('manajemenpengguna.index');
Route::get('/manajemen-pengguna', [ManajemenPenggunaController::class, 'index'])->name('manajemenpengguna.index');
Route::post('/update-admin', [ManajemenPenggunaController::class, 'updateAdmin'])->name('manajemenpengguna.updateadmin');
Route::post('/update-status', [ManajemenPenggunaController::class, 'updateStatus'])->name('manajemenpengguna.updatestatus');
Route::post('/add-admin', [ManajemenPenggunaController::class, 'store'])->name('admin.store');
Route::post('/delete-admin', [ManajemenPenggunaController::class, 'delete'])->name('admin.delete');

Route::get('/', function () {
    return view('landingpage');
});
Route::view('/about', 'about');
Route::view('/services', 'services');

Route::view('/contact', 'contact');
Route::view('/landingpage', 'landingpage');
