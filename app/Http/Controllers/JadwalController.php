<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Guru;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwals = Jadwal::with('guru')->get();
        return view('dashboard.jadwal.index', compact('jadwals'));
    }

   public function create($kelas_id)
{
    $kelas = \App\Models\Kelas::findOrFail($kelas_id); // satu objek, bukan all()
    $guru = \App\Models\Guru::all();
    return view('jadwal.create', compact('kelas', 'guru'));
}

public function store(Request $request)
{
    $request->validate([
        'mapel' => 'required',
        'kelas_id' => 'required|exists:kelas,id',
        'guru_id' => 'required|exists:gurus,id',
        'hari' => 'required',
        'jam' => 'required',
    ]);
    \App\Models\Jadwal::create($request->all());
    return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan');
    
// JadwalController.php
return redirect()->route('jadwal.index')->with('success', 'Penambahan jadwal telah berhasil');
}

public function pilihKelas()
{
    $kelas = \App\Models\Kelas::all();
    return view('jadwal.pilih_kelas', compact('kelas'));
}

public function pilihKelasLihat()
{
    $kelas = \App\Models\Kelas::all();
    return view('jadwal.pilih_kelas_lihat', compact('kelas'));
}

public function jadwalPerKelas($kelas_id)
{
    $kelas = \App\Models\Kelas::findOrFail($kelas_id);
    $jadwals = \App\Models\Jadwal::where('kelas_id', $kelas_id)->with('guru')->get();
    return view('jadwal.jadwal_per_kelas', compact('kelas', 'jadwals'));
}


}
