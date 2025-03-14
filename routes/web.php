<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManajemenPenggunaController;
use App\Http\Controllers\ManajemenPohonBibitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('layouts.dashboard');
})->name('dashboard');

Route::get('/history-scan-barcode', function () {
    return view('layouts.historyscan');
})->name('history scan barcode');

Route::get('/login', function () {
    return view('layouts.login'); // This assumes your Blade view is located in resources/views/login.blade.php
});

Route::get('/forgotpassword', function () {
    return view('layouts.forgotpassword'); // This assumes your Blade view is located in resources/views/login.blade.php
});

Route::get('/resendotp', function () {
    return view('layouts.resendotp'); // This assumes your Blade view is located in resources/views/login.blade.php
});


Route::get('/manajemen-kayu-bibit', [ManajemenPohonBibitController::class, 'index'])
    ->name('manajemenkayubibit');


Route::get('/manajemen-pengguna', [ManajemenPenggunaController::class, 'index'])->name('manajemenpengguna');

Route::post('/login', [LoginController::class, 'login']);

