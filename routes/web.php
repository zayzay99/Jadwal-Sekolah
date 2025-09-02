<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\ManageGuruController;
use App\Http\Controllers\ManageSiswaController;
use App\Http\Controllers\ManageKelasController;
use App\Http\Controllers\KelasKategoriController;
use App\Http\Controllers\TabeljController;

Route::get('/', function () {
    return view('welcome');
});


// Redirect GET /login ke halaman utama (/) agar tidak error method
Route::get('/login', function() { return redirect('/'); });
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:siswa')->group(function () {
    Route::get('/dashboard/siswa', [SiswaController::class, 'index'])->name('siswa.dashboard');
    Route::get('/dashboard/siswa/jadwal', [SiswaController::class, 'jadwal'])->name('siswa.jadwal');
    Route::get('/dashboard/siswa/jadwal/cetak', [SiswaController::class, 'cetakJadwal'])->name('siswa.jadwal.cetak');
    Route::post('/dashboard/siswa/profile/update', [App\Http\Controllers\SiswaController::class, 'updateProfile'])
    ->name('siswa.profile.update');
});

Route::middleware('auth:guru')->group(function () {
    Route::get('/dashboard/guru', [GuruController::class, 'index'])->name('guru.dashboard');
    Route::get('/dashboard/guru/jadwal', [GuruController::class, 'jadwal'])->name('guru.jadwal');
    Route::get('/dashboard/guru/jadwal/cetak', [GuruController::class, 'cetakJadwal'])->name('guru.jadwal.cetak');
    Route::post('/dashboard/guru/profile/update', [GuruController::class, 'updateProfilePicture'])->name('guru.profile.update');
});


Route::middleware('auth:web')->group(function () {
    Route::get('/dashboard/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/jadwal/pilih-kelas', [JadwalController::class, 'pilihKelas'])->name('jadwal.pilihKelas');
    Route::get('/jadwal/create/{kelas}', [JadwalController::class, 'create'])->name('jadwal.create');
    Route::get('/jadwal/kelas', [JadwalController::class, 'pilihKelasLihat'])->name('jadwal.pilihKelasLihat');
    Route::get('/jadwal/kelas/{kelas}', [JadwalController::class, 'jadwalPerKelas'])->name('jadwal.perKelas');
    Route::post('/jadwal/bulk-store', [JadwalController::class, 'bulkStore'])->name('jadwal.bulkStore');
    Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    Route::get('/tabelj', [TabeljController::class, 'index'])->name('tabelj.index');
    Route::post('/tabelj', [TabeljController::class, 'store'])->name('tabelj.store');
    Route::delete('/tabelj/{tabelj}', [TabeljController::class, 'destroy'])->name('tabelj.destroy');
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
    Route::resource('manage/kelas', \App\Http\Controllers\ManageKelasController::class, ['names' => [
        'index' => 'manage.kelas.index',
        'create' => 'manage.kelas.create',
        'store' => 'manage.kelas.store',
        'edit' => 'manage.kelas.edit',
        'update' => 'manage.kelas.update',
        'destroy' => 'manage.kelas.destroy',
        'show' => 'manage.kelas.show',
    ]]);

    Route::get('/kelas', [KelasKategoriController::class, 'index'])->name('kelas.kategori');
    Route::get('/kelas/{kategori}', [KelasKategoriController::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{kategori}/{kelas}', [KelasKategoriController::class, 'detail'])->name('kelas.detail');
});


Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
