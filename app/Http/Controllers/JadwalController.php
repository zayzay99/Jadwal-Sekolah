<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Guru;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function index()
    {
        // Alihkan ke halaman "pilih kelas" sebagai tindakan default untuk index.
        return redirect()->route('jadwal.pilihKelasLihat');
    }

   public function create($kelas_id)
{
    $kelas = \App\Models\Kelas::findOrFail($kelas_id);
    $gurus = \App\Models\Guru::orderBy('nama')->get();

    // Ambil jumlah jadwal per guru per hari
    $hariKerja = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $scheduleCounts = Jadwal::select('guru_id', 'hari', DB::raw('count(*) as total_jam'))
        ->whereIn('hari', $hariKerja)
        ->groupBy('guru_id', 'hari')
        ->get()
        ->groupBy('guru_id');

    $guruSchedules = [];
    foreach ($gurus as $guru) {
        $guru_id = $guru->id;
        $dailyCounts = [];
        $weeklyTotal = 0;

        // Inisialisasi semua hari kerja dengan 0 jam
        foreach ($hariKerja as $hari) {
            $dailyCounts[$hari] = 0;
        }

        // Jika guru memiliki jadwal, hitung totalnya
        if (isset($scheduleCounts[$guru_id])) {
            foreach ($scheduleCounts[$guru_id] as $schedule) {
                $dailyCounts[$schedule->hari] = $schedule->total_jam;
                $weeklyTotal += $schedule->total_jam;
            }
        }

        $guruSchedules[$guru_id] = [
            'weekly_total' => $weeklyTotal,
            'daily_counts' => $dailyCounts,
        ];
    }

    return view('jadwal.create', compact('kelas', 'gurus', 'guruSchedules'));
}

public function store(Request $request)
{
    $request->validate([
        'mapel' => 'required',
        'kelas_id' => 'required|exists:kelas,id',
        'guru_id' => [
            'required',
            'exists:gurus,id',
            // Validasi kustom untuk jam mengajar guru
            function ($attribute, $value, $fail) use ($request) {
                $guru_id = $value;
                $hari = $request->input('hari');

                // 1. Validasi batas jam harian (maks 8 jam)
                $jumlahJamHarian = Jadwal::where('guru_id', $guru_id)
                                       ->where('hari', $hari)
                                       ->count();
                if ($jumlahJamHarian >= 8) {
                    $fail("Guru ini sudah mencapai batas maksimal 8 jam mengajar pada hari {$hari}.");
                }

                // 2. Validasi batas jam mingguan (maks 48 jam untuk Senin-Sabtu)
                $hariKerja = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $jumlahJamMingguan = Jadwal::where('guru_id', $guru_id)
                                             ->whereIn('hari', $hariKerja)
                                             ->count();
                if ($jumlahJamMingguan >= 48) {
                    $fail('Guru ini sudah mencapai batas maksimal 48 jam mengajar dalam satu minggu (Senin-Sabtu).');
                }
            },
        ],
        'hari' => 'required',
        'jam' => 'required',
    ]);
    Jadwal::create($request->all());
    return redirect()->route('jadwal.perKelas', ['kelas' => $request->kelas_id])->with('success_create', 'Jadwal berhasil ditambahkan');
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
