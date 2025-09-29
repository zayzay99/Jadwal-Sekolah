<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran; // Import the TahunAjaran model
use Illuminate\Support\Facades\DB; // <--- Import DB facade

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Dapatkan tahun ajaran yang aktif. Jika tidak ada, aktifkan yang terbaru.
        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        if (!$activeTahunAjaran) {
            $activeTahunAjaran = TahunAjaran::latest('created_at')->first();
            if ($activeTahunAjaran) {
                // Nonaktifkan semua yang lain sebelum mengaktifkan yang ini
                TahunAjaran::where('is_active', true)->update(['is_active' => false]);
                $activeTahunAjaran->update(['is_active' => true]);
            }
        }

        // Pastikan session diset dengan tahun ajaran yang aktif
        if ($activeTahunAjaran) {
            session(['tahun_ajaran_id' => $activeTahunAjaran->id]);
        }
        
        $activeTahunAjaranId = session('tahun_ajaran_id');

        // Hitung data berdasarkan tahun ajaran yang aktif
        $guruCount = \App\Models\Guru::count(); // Guru dianggap global, tidak terikat tahun ajaran
        $kelasCount = 0;
        $siswaCount = 0;
        $jadwalCount = 0;

        if ($activeTahunAjaranId) {
            $kelasCount = \App\Models\Kelas::where('tahun_ajaran_id', $activeTahunAjaranId)->count();
            
            // Menghitung siswa unik yang terdaftar di kelas pada tahun ajaran aktif
            $siswaCount = DB::table('kelas_siswa')
                            ->where('tahun_ajaran_id', $activeTahunAjaranId)
                            ->distinct('siswa_id')
                            ->count('siswa_id');
            
            $jadwalCount = \App\Models\Jadwal::where('tahun_ajaran_id', $activeTahunAjaranId)->count();
        }

        // Ambil semua tahun ajaran untuk dropdown
        $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get(); 
        
        // Kirim semua data yang diperlukan ke view
        return view('dashboard.admin', compact(
            'guruCount', 
            'siswaCount', 
            'kelasCount', 
            'jadwalCount', 
            'tahunAjarans',
            'activeTahunAjaran'
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
