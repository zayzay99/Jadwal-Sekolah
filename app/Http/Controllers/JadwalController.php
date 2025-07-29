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

    public function create()
    {
        $gurus = Guru::all();
        $kelas = \App\Models\Kelas::all();
        return view('dashboard.jadwal.create', compact('gurus', 'kelas'));
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
        Jadwal::create([
            'mapel' => $request->mapel,
            'kelas_id' => $request->kelas_id,
            'guru_id' => $request->guru_id,
            'hari' => $request->hari,
            'jam' => $request->jam,
        ]);
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }
}
