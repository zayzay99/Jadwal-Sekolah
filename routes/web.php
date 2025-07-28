<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:siswa')->group(function () {
    Route::get('/dashboard/siswa', [SiswaController::class, 'index'])->name('siswa.dashboard');
});

Route::middleware('auth:guru')->group(function () {
    Route::get('/dashboard/guru', [GuruController::class, 'index'])->name('guru.dashboard');
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
