<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManajemenPohonBibitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


Route::get('/login', function () {
    return view('layouts.login'); // This assumes your Blade view is located in resources/views/login.blade.php
});

Route::get('/forgotpassword', function () {
    return view('layouts.forgotpassword'); // This assumes your Blade view is located in resources/views/login.blade.php
});

Route::get('/resendotp', function () {
    return view('layouts.resendotp'); // This assumes your Blade view is located in resources/views/login.blade.php
});


Route::get('/manajemen-pohon-bibit', [ManajemenPohonBibitController::class, 'index'])
    ->name('manajemenkayubibit');


Route::post('/login', [LoginController::class, 'login']);

