<?php

use App\Http\Controllers\HistoryBarcodeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManajemenPenggunaController;
use App\Http\Controllers\ManajemenPohonBibitController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

use App\Http\Controllers\FirestoreController;
Route::get('/akun_superadmin', [FirestoreController::class, 'showSuperAdminForm']);
Route::post('/akun_superadmin', [FirestoreController::class, 'storeSuperAdmin']);
Route::get('/register', [FirestoreController::class, 'showForm']);
Route::post('/register', [FirestoreController::class, 'handleForm']);

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [AuthController::class, 'handleLogin']);
Route::get('/dashboard', fn() => view('layouts.dashboard'));
Route::get('/login', function () {
    return view('layouts.login'); // This assumes your Blade view is located in resources/views/login.blade.php
})->name('login');


Route::get('/forgotpassword', function () {
    return view('layouts.forgotpassword'); // This assumes your Blade view is located in resources/views/login.blade.php
})->name('forgotpassword');

Route::get('/resendotp', function () {
    return view('layouts.resendotp'); // This assumes your Blade view is located in resources/views/login.blade.php
});

Route::get('/profile', function () {
    return view('layouts.profile');
})->name('profile');

Route::get('/dashboard', function () {
    return view('layouts.dashboard');
})->name('dashboard');


Route::get('/test-firebase', function () {
    $firebaseCredentials = Config::get('firebase.credentials');

    if (file_exists($firebaseCredentials)) {
        return 'Firebase credentials file found at: ' . $firebaseCredentials;
    }

    return 'Firebase credentials file not found!';
}); // <-- Menambahkan kurung tutup yang hilang di sini

Route::get('/manajemen-kayu-bibit', [ManajemenPohonBibitController::class, 'index'])
    ->name('manajemenkayubibit');

Route::get('/history-scan-barcode', [HistoryBarcodeController::class, 'index'])
    ->name('historyscanbarcode');

Route::get('/history-perawatan-bibit', [HistoryBarcodeController::class, 'index'])
    ->name('historyperawatanbibit');

Route::get('/manajemen-pengguna', [ManajemenPenggunaController::class, 'index'])->name('manajemenpengguna');
