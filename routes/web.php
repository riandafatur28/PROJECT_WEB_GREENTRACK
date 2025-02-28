<?php

<<<<<<< HEAD
use App\Http\Controllers\LoginController;
=======
>>>>>>> 850b15a7fa3f55b7db7e78908d138f1ac6af5019
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
<<<<<<< HEAD

Route::get('/dashboard', function () {
    return view('layouts.dashboard');
});

Route::get('/login', function () {
    return view('layouts.login'); // This assumes your Blade view is located in resources/views/login.blade.php
});

Route::post('/login', [LoginController::class, 'login']);
=======
>>>>>>> 850b15a7fa3f55b7db7e78908d138f1ac6af5019
