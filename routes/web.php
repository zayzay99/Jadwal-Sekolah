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
use App\Http\Controllers\JadwalKategoriController;
use App\Http\Controllers\TahunAjaranController;

// ============================
// HALAMAN UTAMA / LOGIN
// ============================
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Redirect GET /login agar tidak error
Route::get('/login', fn() => redirect('/'));
Route::post('/login', [AuthController::class, 'login'])->name('login');

// ============================
// SISWA
// ============================
Route::middleware('auth:siswa')->group(function () {
    Route::get('/dashboard/siswa', [SiswaController::class, 'index'])->name('siswa.dashboard');
    Route::get('/dashboard/siswa/jadwal', [SiswaController::class, 'jadwal'])->name('siswa.jadwal');
    Route::get('/dashboard/siswa/jadwal/cetak', [SiswaController::class, 'cetakJadwal'])->name('siswa.jadwal.cetak');
    Route::post('/dashboard/siswa/profile/update', [GuruController::class, 'updateProfilePicture'])->name('siswa.profile.update');
    Route::post('/dashboard/siswa/profile/update', [SiswaController::class, 'updateProfilePicture'])->name('siswa.profile.update');
    Route::post('/dashboard/siswa/switch-tahun-ajaran', [SiswaController::class, 'switchTahunAjaran'])->name('siswa.switch-tahun-ajaran');
    Route::get('/dashboard/siswa/jadwal/arsip/{tahun_ajaran_id}', [SiswaController::class, 'getArsipJadwal'])->name('siswa.jadwal.arsip');
});

// ============================
// GURU
// ============================
Route::middleware('auth:guru')->group(function () {
    Route::get('/dashboard/guru', [GuruController::class, 'index'])->name('guru.dashboard');
    Route::get('/dashboard/guru/jadwal', [GuruController::class, 'jadwal'])->name('guru.jadwal');
    Route::get('/dashboard/guru/jadwal/cetak', [GuruController::class, 'cetakJadwal'])->name('guru.jadwal.cetak');
    Route::post('/dashboard/guru/profile/update', [GuruController::class, 'updateProfilePicture'])->name('guru.profile.update');
    Route::post('/dashboard/guru/switch-tahun-ajaran', [GuruController::class, 'switchTahunAjaran'])->name('guru.switch-tahun-ajaran');
    Route::get('/dashboard/guru/jadwal/arsip/{tahun_ajaran_id}', [GuruController::class, 'getArsipJadwal'])->name('guru.jadwal.arsip');
});

// ============================
// ADMIN
// ============================
Route::middleware('auth:web')->group(function () {
    // Dashboard
    Route::get('/dashboard/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // Jadwal CRUD
    Route::get('/jadwal/pilih-kelas', [JadwalController::class, 'pilihKelas'])->name('jadwal.pilihKelas');
    Route::get('/jadwal/pilih-subkelas/{kategori}', [JadwalController::class, 'pilihSubKelas'])->name('jadwal.pilihSubKelas');
    Route::get('/jadwal/create/{kelas}', [JadwalController::class, 'create'])->name('jadwal.create');
    Route::post('/jadwal/store', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::post('/jadwal/store-kategori', [JadwalController::class, 'storeKategori'])->name('jadwal.storeKategori');
    Route::get('/jadwal/kelas', [JadwalController::class, 'pilihKelasLihat'])->name('jadwal.pilihKelasLihat');
    Route::get('/jadwal/kelas/{kelas}', [JadwalController::class, 'jadwalPerKelas'])->name('jadwal.perKelas');
    Route::get('/jadwal/kelas/{kelas}/cetak', [JadwalController::class, 'cetakJadwal'])->name('admin.jadwal.cetak');
    Route::get('/jadwal/cetak-bulk', [JadwalController::class, 'cetakJadwalBulk'])->name('admin.jadwal.cetak.bulk');
    Route::post('/jadwal/bulk-store', [JadwalController::class, 'bulkStore'])->name('jadwal.bulkStore');
    Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    Route::delete('/jadwal/destroy-all/{kelas_id}', [JadwalController::class, 'destroyAll'])->name('jadwal.destroyAll');

    // Manajemen tabel jadwal
    Route::delete('/manage/tabelj/destroy-all', [TabeljController::class, 'destroyAll'])->name('manage.tabelj.destroyAll');
    Route::resource('manage/tabelj', TabeljController::class)->except(['show'])->names([
        'index' => 'manage.tabelj.index',
        'create' => 'manage.tabelj.create',
        'store' => 'manage.tabelj.store',
        'edit' => 'manage.tabelj.edit',
        'update' => 'manage.tabelj.update',
        'destroy' => 'manage.tabelj.destroy',
    ]);
    Route::get('/manage/tabelj/assign-category', [TabeljController::class, 'assignCategory'])->name('manage.tabelj.assignCategory');
    Route::post('/manage/tabelj/assign-category', [TabeljController::class, 'storeAssignedCategory'])->name('manage.tabelj.storeAssignedCategory');
    Route::post('/manage/tabelj/{tabelj}/add-break', [TabeljController::class, 'addBreak'])->name('manage.tabelj.addBreak');

    // Manajemen Guru
    // Rute spesifik harus di atas resource controller
    Route::get('manage/guru/import', [ManageGuruController::class, 'showImportForm'])->name('manage.guru.import.show');
    Route::post('manage/guru/import', [ManageGuruController::class, 'import'])->name('manage.guru.import.store');
    Route::get('manage/guru/{guru}/availability', [ManageGuruController::class, 'editAvailability'])->name('manage.guru.availability.edit');
    Route::post('manage/guru/{guru}/availability', [ManageGuruController::class, 'updateAvailability'])->name('manage.guru.availability.update');
    Route::resource('manage/guru', ManageGuruController::class, ['except' => ['show'], 'names' => [
        'index' => 'manage.guru.index',
        'create' => 'manage.guru.create',
        'store' => 'manage.guru.store',
        'edit' => 'manage.guru.edit',
        'update' => 'manage.guru.update',
        'destroy' => 'manage.guru.destroy',
    ]]);

    // FIX: Pindahkan rute spesifik ke ATAS resource controller

    Route::get('manage/siswa/export', [ManageSiswaController::class, 'export'])->name('manage.siswa.export');
    Route::get('manage/siswa/import', [ManageSiswaController::class, 'showImportForm'])->name('manage.siswa.import.form');
    Route::post('manage/siswa/import', [ManageSiswaController::class, 'import'])->name('manage.siswa.import'); // Ubah nama rute agar konsisten
    Route::resource('manage/siswa', ManageSiswaController::class)->except(['show'])->names([
        'index' => 'manage.siswa.index',
        'create' => 'manage.siswa.create',
        'store' => 'manage.siswa.store',
        'edit' => 'manage.siswa.edit',
        'update' => 'manage.siswa.update',
        'destroy' => 'manage.siswa.destroy',
    ]);

    // Manajemen Kelas
    Route::resource('manage/kelas', ManageKelasController::class)->names([
        'index' => 'manage.kelas.index',
        'create' => 'manage.kelas.create',
        'store' => 'manage.kelas.store',
        'edit' => 'manage.kelas.edit',
        'update' => 'manage.kelas.update',
        'destroy' => 'manage.kelas.destroy',
        'show' => 'manage.kelas.show',
    ]);

    // Jadwal Kategori & Tahun Ajaran
    Route::resource('jadwal-kategori', JadwalKategoriController::class);
    Route::resource('manage/tahun-ajaran', TahunAjaranController::class)->names([
        'index' => 'manage.tahun-ajaran.index',
        'create' => 'manage.tahun-ajaran.create',
        'store' => 'manage.tahun-ajaran.store',
        'show' => 'manage.tahun-ajaran.show',
        'edit' => 'manage.tahun-ajaran.edit',
        'update' => 'manage.tahun-ajaran.update',
        'destroy' => 'manage.tahun-ajaran.destroy',
    ]);
    Route::post('manage/tahun-ajaran/{tahun_ajaran}/set-active', [TahunAjaranController::class, 'setActive'])->name('manage.tahun-ajaran.setActive');
    Route::get('manage/tahun-ajaran/{tahun_ajaran}/switch-active', [TahunAjaranController::class, 'switchActive'])->name('manage.tahun-ajaran.switch');

    // Kelas Kategori
    Route::get('/kelas', [KelasKategoriController::class, 'index'])->name('kelas.kategori');
    Route::get('/kelas/{kategori}', [KelasKategoriController::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{kategori}/{kelas}', [KelasKategoriController::class, 'detail'])->name('kelas.detail');
});

// ============================
// LOGOUT (GET agar tidak error 405)
// ============================
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');