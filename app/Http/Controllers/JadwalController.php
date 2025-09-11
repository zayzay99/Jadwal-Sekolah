<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\JadwalKategori;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalController extends Controller
{
    public function index()
    {
        return redirect()->route('jadwal.pilihKelasLihat');
    }

    public function create($kelas_id)
    {
        $kelas = \App\Models\Kelas::findOrFail($kelas_id);
        // Ambil guru beserta batas mengajarnya
        $gurus = \App\Models\Guru::orderBy('nama')->get(['id', 'nama', 'pengampu', 'max_jp_per_hari']);

        $allSchedules = Jadwal::with('kelas:id,nama_kelas')->get(['guru_id', 'kelas_id', 'hari', 'jam']);
        $jadwals = Jadwal::where('kelas_id', $kelas_id)->with('guru')->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $timeSlots = \App\Models\Tabelj::orderBy('jam_mulai')->get();

        $scheduleGrid = [];
        foreach ($jadwals as $jadwal) {
            $scheduleGrid[$jadwal->hari][$jadwal->jam] = $jadwal;
        }

        $kategoris = JadwalKategori::all();

        return view('jadwal.create', compact('kelas', 'gurus', 'days', 'timeSlots', 'scheduleGrid', 'kategoris', 'allSchedules'));
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

        // --- VALIDASI BACKEND ---
        $teacherSchedules = []; // [guru_id][hari] = total_jp
        $teacherClashes = []; // [guru_id][hari][jam] = kelas_id

        // 1. Ambil semua jadwal yang ada KECUALI untuk kelas yang sedang diedit
        $existingSchedules = Jadwal::where('kelas_id', '!=', $kelasId)->whereNotNull('guru_id')->get();
        foreach ($existingSchedules as $jadwal) {
            $teacherClashes[$jadwal->guru_id][$jadwal->hari][$jadwal->jam] = $jadwal->kelas_id;
        }

        // 2. Validasi jadwal baru yang di-submit
        foreach ($schedules as $scheduleData) {
            if (empty($scheduleData['guru_id'])) continue;

            $guruId = $scheduleData['guru_id'];
            $hari = $scheduleData['hari'];
            $jam = $scheduleData['jam'];

            // Cek bentrok
            if (isset($teacherClashes[$guruId][$hari][$jam])) {
                $guru = Guru::find($guruId);
                $kelas = Kelas::find($teacherClashes[$guruId][$hari][$jam]);
                return response()->json(['success' => false, 'message' => "Jadwal bentrok! Guru {$guru->nama} sudah mengajar di kelas {$kelas->nama_kelas} pada hari {$hari}, jam {$jam}."], 422);
            }
            // Tandai jadwal ini untuk pengecekan selanjutnya
            $teacherClashes[$guruId][$hari][$jam] = $kelasId;

            // Hitung JP harian
            try {
                $jamParts = explode(' - ', $jam);
                $jamMulai = new \DateTime($jamParts[0]);
                $jamSelesai = new \DateTime($jamParts[1]);
                $durasiMenit = ($jamSelesai->getTimestamp() - $jamMulai->getTimestamp()) / 60;
                $jp = floor($durasiMenit / 35); // 1 JP = 35 menit

                if (!isset($teacherSchedules[$guruId][$hari])) {
                    $teacherSchedules[$guruId][$hari] = 0;
                }
                $teacherSchedules[$guruId][$hari] += $jp;
            } catch (\Exception $e) {
                // Abaikan jika format jam salah, akan divalidasi di frontend
            }
        }
        // --- AKHIR VALIDASI BACKEND ---

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

    public function jadwalPerKelas(Request $request, $kelas_id)
    {
        $kelas = \App\Models\Kelas::findOrFail($kelas_id);
        $jadwals = \App\Models\Jadwal::where('kelas_id', $kelas_id)->with('guru', 'kategori')->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")->orderBy('jam')->get()->groupBy('hari');
        
        $is_management = $request->query('management', 'true') !== 'false';

        return view('jadwal.jadwal_per_kelas', compact('kelas', 'jadwals', 'is_management'));
    }

    public function cetakJadwal($kelas_id)
    {
        $kelas = Kelas::findOrFail($kelas_id);
        $jadwals = Jadwal::where('kelas_id', $kelas_id)->with('guru', 'kategori')->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")->orderBy('jam')->get()->groupBy('hari');

        $pdf = Pdf::loadView('jadwal.pdf', compact('jadwals', 'kelas'));
        return $pdf->download('jadwal-' . $kelas->nama_kelas . '.pdf');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}