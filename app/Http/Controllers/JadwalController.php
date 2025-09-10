<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\JadwalKategori;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    public function index()
    {
        return redirect()->route('jadwal.pilihKelasLihat');
    }

    public function create($kelas_id)
    {
        $kelas = \App\Models\Kelas::findOrFail($kelas_id);
        $gurus = \App\Models\Guru::orderBy('nama')->get();
        $jadwals = Jadwal::where('kelas_id', $kelas_id)->with('guru')->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $timeSlots = \App\Models\Tabelj::orderBy('jam_mulai')->get();

        $scheduleGrid = [];
        foreach ($jadwals as $jadwal) {
            $scheduleGrid[$jadwal->hari][$jadwal->jam] = $jadwal;
        }

        $kategoris = JadwalKategori::all();

        return view('jadwal.create', compact('kelas', 'gurus', 'days', 'timeSlots', 'scheduleGrid', 'kategoris'));
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'schedules' => 'present|array',
            'schedules.*.guru_id' => 'nullable|exists:gurus,id',
            'schedules.*.mapel' => 'nullable|string',
            'schedules.*.jadwal_kategori_id' => 'nullable|exists:jadwal_kategoris,id',
            'schedules.*.hari' => 'required|string',
            'schedules.*.jam' => 'required|string',
        ]);

        $kelasId = $validated['kelas_id'];
        $schedules = $validated['schedules'];

        DB::beginTransaction();
        try {
            Jadwal::where('kelas_id', $kelasId)->delete();

            foreach ($schedules as $scheduleData) {
                Jadwal::create([
                    'kelas_id' => $kelasId,
                    'guru_id' => $scheduleData['guru_id'],
                    'mapel' => $scheduleData['mapel'],
                    'jadwal_kategori_id' => $scheduleData['jadwal_kategori_id'],
                    'hari' => $scheduleData['hari'],
                    'jam' => $scheduleData['jam'],
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Jadwal telah berhasil disimpan.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan jadwal. Terjadi kesalahan server.'], 500);
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
        $jadwals = \App\Models\Jadwal::where('kelas_id', $kelas_id)->with('guru', 'kategori')->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")->orderBy('jam')->get()->groupBy('hari');
        return view('jadwal.jadwal_per_kelas', compact('kelas', 'jadwals'));
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}