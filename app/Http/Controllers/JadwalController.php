<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Guru;

class JadwalController extends Controller
{
    public function index()
    {
        // Alihkan ke halaman "pilih kelas" sebagai tindakan default untuk index.
        return redirect()->route('jadwal.pilihKelasLihat');
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
        'guru_id' => [
            'required',
            'exists:gurus,id',
            function ($attribute, $value, $fail) use ($request) {
                $jumlahJam = Jadwal::where('guru_id', $value)
                                       ->where('hari', $request->input('hari'))
                                       ->count();
                if ($jumlahJam >= 8) {
                    $fail('Guru ini sudah mencapai batas maksimal 8 jam mengajar pada hari yang dipilih.');
                }
            },
        ],
        'hari' => 'required',
        'jam' => 'required',
    ]);
    Jadwal::create($request->all());
    return redirect()->route('jadwal.perKelas', ['kelas' => $request->kelas_id])->with('success', 'Jadwal berhasil ditambahkan');
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

public function destroy($id)
{
    $jadwal = Jadwal::findOrFail($id);
    $jadwal->delete();

    return back()->with('success', 'Jadwal berhasil dihapus.');
}
}
