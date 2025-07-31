<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});


// Redirect GET /login ke halaman utama (/) agar tidak error method
Route::get('/login', function() { return redirect('/'); });
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard/admin', [AdminController::class, 'index'])->name('dashboard.admin.index');
});

Route::middleware('auth:guru')->group(function () {
    Route::get('/dashboard/guru', [GuruController::class, 'index'])->name('dashboard.guru.index');
});

Route::middleware('auth:siswa')->group(function () {
    Route::get('/dashboard/siswa', [SiswaController::class, 'index'])->name('dashboard.siswa.index');
});


Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
