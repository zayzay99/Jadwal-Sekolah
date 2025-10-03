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
        
        // Cari tahun ajaran yang aktif
        $activeTahunAjaran = $tahunAjarans->firstWhere('is_active', true);
        $activeTahunAjaranId = session('tahun_ajaran_id');

        // Jika ada tahun ajaran aktif, pastikan sesi sinkron
        if ($activeTahunAjaran && $activeTahunAjaran->id !== $activeTahunAjaranId) {
            session(['tahun_ajaran_id' => $activeTahunAjaran->id]);
            $activeTahunAjaranId = $activeTahunAjaran->id;
        }

        // Hitung data berdasarkan tahun ajaran yang aktif
        $guruCount = \App\Models\Guru::count(); // Guru dianggap global, tidak terikat tahun ajaran
        
        // Jika tidak ada tahun ajaran aktif, semua count terkait adalah 0
        $kelasCount = $activeTahunAjaranId ? \App\Models\Kelas::where('tahun_ajaran_id', $activeTahunAjaranId)->count() : 0;
        $siswaCount = $activeTahunAjaranId ? \App\Models\Siswa::whereHas('kelas', function ($query) use ($activeTahunAjaranId) {
            $query->where('kelas_siswa.tahun_ajaran_id', $activeTahunAjaranId);
        })->count() : 0;
        $jadwalCount = $activeTahunAjaranId ? \App\Models\Jadwal::where('tahun_ajaran_id', $activeTahunAjaranId)->count() : 0;

        // Kirim semua data yang diperlukan ke view
        return view('dashboard.admin', compact(
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
