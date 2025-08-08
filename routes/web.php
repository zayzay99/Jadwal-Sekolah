<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\ManageGuruController;
use App\Http\Controllers\ManageSiswaController;

Route::get('/', function () {
    return view('welcome');
});


// Redirect GET /login ke halaman utama (/) agar tidak error method
Route::get('/login', function() { return redirect('/'); });
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:siswa')->group(function () {
    Route::get('/dashboard/siswa', [SiswaController::class, 'index'])->name('siswa.dashboard');
    Route::get('/dashboard/siswa/jadwal', [SiswaController::class, 'jadwal'])->name('siswa.jadwal');
});

Route::middleware('auth:guru')->group(function () {
    Route::get('/dashboard/guru', [GuruController::class, 'index'])->name('guru.dashboard');
    Route::get('/dashboard/guru/jadwal', [GuruController::class, 'jadwal'])->name('guru.jadwal');
});


Route::middleware('auth:web')->group(function () {
    Route::get('/dashboard/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('jadwal', JadwalController::class)->except(['show', 'edit', 'update', 'destroy']);
        Route::get('/dashboard/admin', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/jadwal/kelas', [JadwalController::class, 'pilihKelasLihat'])->name('jadwal.pilihKelasLihat');
Route::get('/jadwal/kelas/{kelas}', [JadwalController::class, 'jadwalPerKelas'])->name('jadwal.perKelas');
    Route::get('/jadwal/pilih-kelas', [JadwalController::class, 'pilihKelas'])->name('jadwal.pilihKelas');
    Route::get('/jadwal/create/{kelas}', [JadwalController::class, 'create'])->name('jadwal.create');
    Route::resource('jadwal', JadwalController::class)->except(['show', 'edit', 'update', 'destroy', 'create']);
       Route::resource('manage/guru', ManageGuruController::class, ['names' => [
        'index' => 'manage.guru.index',
        'create' => 'manage.guru.create',
        'store' => 'manage.guru.store',
        'edit' => 'manage.guru.edit',
        'update' => 'manage.guru.update',
        'destroy' => 'manage.guru.destroy',
        'show' => 'manage.guru.show',
    ]]);
    Route::resource('manage/siswa', ManageSiswaController::class, ['names' => [
        'index' => 'manage.siswa.index',
        'create' => 'manage.siswa.create',
        'store' => 'manage.siswa.store',
        'edit' => 'manage.siswa.edit',
        'update' => 'manage.siswa.update',
        'destroy' => 'manage.siswa.destroy',
        'show' => 'manage.siswa.show',
    ]]);
});
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');



