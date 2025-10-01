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
        $activeTahunAjaranId = session('tahun_ajaran_id');
        
        // Hitung data berdasarkan tahun ajaran yang aktif                       
        $guruCount = \App\Models\Guru::count(); // Guru dianggap global, tidak terikat tahun ajaran
        $kelasCount = 0;
        $siswaCount = 0;
        $jadwalCount = 0;
        if ($activeTahunAjaranId) {
            $kelasCount = \App\Models\Kelas::where('tahun_ajaran_id', $activeTahunAjaranId)->count();
            
            // Menghitung siswa unik yang terdaftar di kelas pada tahun ajaran aktif menggunakan relasi Eloquent.
            $siswaCount = \App\Models\Siswa::whereHas('kelas', function ($query) use ($activeTahunAjaranId) {
                $query->where('kelas.tahun_ajaran_id', $activeTahunAjaranId);
            })->count();
            
            $jadwalCount = \App\Models\Jadwal::where('tahun_ajaran_id', $activeTahunAjaranId)->count();
        }

        // Kirim semua data yang diperlukan ke view
        return view('dashboard.admin', compact(
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
