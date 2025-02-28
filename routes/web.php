<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('layouts.dashboard');
});

Route::get('/login', function () {
    return view('layouts.login'); // This assumes your Blade view is located in resources/views/login.blade.php
});

Route::post('/login', [LoginController::class, 'login']);

