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
    $jadwals = Jadwal::where('kelas_id', $kelas_id)->with('guru')->get();

    // Definisikan hari dan slot waktu
    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $timeSlots = [
        '07:00-07:45', '07:45-08:30', '08:30-09:15', '09:15-10:00',
        '10:15-11:00', '11:00-11:45', '11:45-12:30',
        '13:00-13:45', '13:45-14:30'
    ];

    // Ubah jadwal yang ada menjadi format grid untuk kemudahan akses di view
    $scheduleGrid = [];
    foreach ($jadwals as $jadwal) {
        $scheduleGrid[$jadwal->hari][$jadwal->jam] = $jadwal;
    }

    return view('jadwal.create', compact('kelas', 'gurus', 'days', 'timeSlots', 'scheduleGrid'));
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

public function storeAjax(Request $request)
{
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'mapel' => 'required',
        'kelas_id' => 'required|exists:kelas,id',
        'guru_id' => [
            'required',
            'exists:gurus,id',
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

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    try {
        $jadwal = Jadwal::create($request->all());
        // Load relasi guru agar bisa dikirim kembali ke frontend
        $jadwal->load('guru'); 
        return response()->json(['success' => true, 'message' => 'Jadwal berhasil disimpan!', 'jadwal' => $jadwal]);
    } catch (\Exception $e) {
        // Log the error if needed
        return response()->json(['success' => false, 'message' => 'Gagal menyimpan jadwal. Terjadi kesalahan server.'], 500);
    }
}

public function destroyAjax($id)
{
    try {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();
        return response()->json(['success' => true, 'message' => 'Jadwal berhasil dihapus.']);
    } catch (\Exception $e) {
        // Log the error if needed
        \Log::error('Gagal menghapus jadwal: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal menghapus jadwal.'], 500);
    }
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
