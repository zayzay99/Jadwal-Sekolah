<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // The AppServiceProvider already handles fetching tahunAjarans and the active one.
        // We just need the active ID for our queries here.
        $activeTahunAjaranId = session('tahun_ajaran_id');

        if (!$activeTahunAjaranId) {
            // If no year is active in session, default counts to 0.
            // The AppServiceProvider should handle activating a default year and refreshing.
            $guruCount = 0;
            $siswaCount = 0;
            $kelasCount = 0;
            $jadwalCount = 0;
        } else {
            // GURU: Hitung guru yang terikat dengan tahun ajaran aktif
            $guruCount = \App\Models\Guru::where('tahun_ajaran_id', $activeTahunAjaranId)->count();
            
            // SISWA: Hitung siswa yang terdaftar di kelas_siswa untuk tahun ajaran aktif
            $siswaCount = DB::table('kelas_siswa')
                ->where('tahun_ajaran_id', $activeTahunAjaranId)
                ->distinct('siswa_id')
                ->count('siswa_id');
            
            // KELAS: Hitung kelas di tahun ajaran aktif
            $kelasCount = \App\Models\Kelas::where('tahun_ajaran_id', $activeTahunAjaranId)->count();
            
            // JADWAL: Hitung jadwal di tahun ajaran aktif
            $jadwalCount = \App\Models\Jadwal::where('tahun_ajaran_id', $activeTahunAjaranId)->count();
        }

        // The 'tahunAjarans' and 'activeTahunAjaran' variables are passed to the view
        // by the AppServiceProvider. We only need to pass the counts.
        return view('dashboard.home', compact(
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