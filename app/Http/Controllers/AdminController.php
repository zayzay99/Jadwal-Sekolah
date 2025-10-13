<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran; // Import the TahunAjaran model
use Illuminate\Support\Facades\DB; // <--- Import DB facade
use Illuminate\Support\Facades\Artisan; // Import the Artisan facade

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua tahun ajaran untuk dropdown
        $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        
        // 1. Cari tahun ajaran yang aktif dari database sebagai sumber kebenaran utama.
        $activeTahunAjaran = $tahunAjarans->firstWhere('is_active', true);
        $activeTahunAjaranId = $activeTahunAjaran ? $activeTahunAjaran->id : null;

        // 2. Pastikan sesi selalu sinkron dengan data dari database.
        if ($activeTahunAjaran && session('tahun_ajaran_id') !== $activeTahunAjaran->id) {
            session(['tahun_ajaran_id' => $activeTahunAjaran->id]);
        }

        // 3. Hitung data berdasarkan ID tahun ajaran aktif yang sudah kita dapatkan.
        $guruCount = \App\Models\Guru::count(); // Guru dianggap global, tidak terikat tahun ajaran
        
        // Jika tidak ada tahun ajaran aktif, semua count terkait adalah 0
        $siswaCount = \App\Models\Siswa::count(); // Hitung semua siswa terlepas dari tahun ajaran
        $kelasCount = $activeTahunAjaranId ? \App\Models\Kelas::where('tahun_ajaran_id', $activeTahunAjaranId)->count() : 0;
        $jadwalCount = $activeTahunAjaranId ? \App\Models\Jadwal::where('tahun_ajaran_id', $activeTahunAjaranId)->count() : 0;

        // Kirim semua data yang diperlukan ke view
        return view('dashboard.home', compact(
            'tahunAjarans',
            'activeTahunAjaran',
            'guruCount', 
            'siswaCount', 
            'kelasCount', 
            'jadwalCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
