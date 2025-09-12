<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\JadwalKategori;
use App\Models\GuruAvailability;
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
        $kelas = Kelas::findOrFail($kelas_id);
        $gurus = Guru::orderBy('nama')->get();
        $jadwals = Jadwal::where('kelas_id', $kelas_id)->with('guru')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $timeSlots = \App\Models\Tabelj::orderBy('jam_mulai')->get();
        $kategoris = JadwalKategori::all();

        // Susun grid jadwal untuk tampilan awal
        $scheduleGrid = [];
        foreach ($jadwals as $jadwal) {
            $scheduleGrid[$jadwal->hari][$jadwal->jam] = $jadwal;
        }

        // Mengambil semua ketersediaan guru
        $availabilities = GuruAvailability::all()->keyBy(function ($item) {
            return $item->guru_id . '-' . $item->hari . '-' . $item->jam;
        });

        // Ambil semua jadwal untuk validasi bentrok di frontend
        $allSchedules = Jadwal::with('kelas:id,nama_kelas')
            ->get(['guru_id', 'kelas_id', 'hari', 'jam']);

        // Siapkan daftar guru yang tersedia untuk setiap slot waktu
        $availableGurus = [];
        foreach ($days as $day) {
            foreach ($timeSlots as $timeSlot) {
                $jam = $timeSlot->jam_mulai . ' - ' . $timeSlot->jam_selesai;
                $availableGurus[$day][$jam] = [];

                foreach ($gurus as $guru) {
                    $availabilityKey = $guru->id . '-' . $day . '-' . $jam;
                    if ($availabilities->has($availabilityKey)) {
                        $availableGurus[$day][$jam][] = $guru;
                    }
                }
            }
        }

        return view('jadwal.create', compact(
            'kelas',
            'gurus',
            'days',
            'timeSlots',
            'scheduleGrid',
            'kategoris',
            'allSchedules',
            'availableGurus'
        ));
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
        $jamPelajaranMenit = 35;

        // --- VALIDASI BACKEND ---
        $teacherSchedules = []; // [guru_id][hari] = total_jp
        $teacherClashes = [];   // [guru_id][hari][jam] = kelas_id

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
                return response()->json([
                    'success' => false,
                    'message' => "Jadwal bentrok! Guru {$guru->nama} sudah mengajar di kelas {$kelas->nama_kelas} pada hari {$hari}, jam {$jam}."
                ], 422);
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
                // Abaikan jika format jam salah
            }
        }

        // --- AKHIR VALIDASI BACKEND ---

        DB::beginTransaction();
        try {
            // --- Validasi Jadwal ---
            $availabilities = GuruAvailability::all()->keyBy(fn($item) => $item->guru_id . '-' . $item->hari . '-' . $item->jam);
            $otherSchedules = Jadwal::where('kelas_id', '!=', $kelasId)->get()
                ->keyBy(fn($item) => $item->hari . '-' . $item->jam . '-' . $item->guru_id);

            foreach ($schedules as $scheduleData) {
                if ($scheduleData['guru_id']) {
                    $guru = Guru::find($scheduleData['guru_id']);
                    $availabilityKey = $scheduleData['guru_id'] . '-' . $scheduleData['hari'] . '-' . $scheduleData['jam'];
                    $scheduleKey = $scheduleData['hari'] . '-' . $scheduleData['jam'] . '-' . $scheduleData['guru_id'];

                    if (!$availabilities->has($availabilityKey)) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Guru {$guru->nama} tidak tersedia pada {$scheduleData['hari']} jam {$scheduleData['jam']}."
                        ], 400);
                    }

                    if ($otherSchedules->has($scheduleKey)) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Guru {$guru->nama} sudah dijadwalkan di kelas lain pada waktu yang sama."
                        ], 400);
                    }
                }
            }

            // --- Kalkulasi Jam Mengajar ---
            $oldSchedules = Jadwal::where('kelas_id', $kelasId)->get();
            $oldGuruHours = [];
            foreach ($oldSchedules as $schedule) {
                if ($schedule->guru_id) {
                    $oldGuruHours[$schedule->guru_id] = ($oldGuruHours[$schedule->guru_id] ?? 0) + $jamPelajaranMenit;
                }
            }

            $newGuruHours = [];
            foreach ($schedules as $scheduleData) {
                if ($scheduleData['guru_id']) {
                    $newGuruHours[$scheduleData['guru_id']] = ($newGuruHours[$scheduleData['guru_id']] ?? 0) + $jamPelajaranMenit;
                }
            }

            $allGuruIds = array_unique(array_merge(array_keys($oldGuruHours), array_keys($newGuruHours)));
            $gurus = Guru::whereIn('id', $allGuruIds)->get()->keyBy('id');

            foreach ($gurus as $guruId => $guru) {
                $oldHours = $oldGuruHours[$guruId] ?? 0;
                $newHours = $newGuruHours[$guruId] ?? 0;
                $change = $newHours - $oldHours;

                if (($guru->sisa_jam_mengajar - $change) < 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Guru ' . $guru->nama . ' akan melebihi total jam mengajar.'
                    ], 400);
                }
            }

            // --- Simpan Jadwal ---
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

            // --- Update Sisa Jam Mengajar ---
            foreach ($gurus as $guruId => $guru) {
                $oldHours = $oldGuruHours[$guruId] ?? 0;
                $newHours = $newGuruHours[$guruId] ?? 0;
                $change = $newHours - $oldHours;
                $guru->sisa_jam_mengajar -= $change;
                $guru->save();
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
        $kelas = Kelas::all();
        return view('jadwal.pilih_kelas', compact('kelas'));
    }

    public function pilihKelasLihat()
    {
        $kelas = Kelas::all();
        return view('jadwal.pilih_kelas_lihat', compact('kelas'));
    }

    public function jadwalPerKelas(Request $request, $kelas_id)
    {
        $kelas = Kelas::findOrFail($kelas_id);
        $jadwals = Jadwal::where('kelas_id', $kelas_id)
            ->with('guru', 'kategori')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam')
            ->get()
            ->groupBy('hari');

        $is_management = $request->query('management', 'true') !== 'false';

        return view('jadwal.jadwal_per_kelas', compact('kelas', 'jadwals', 'is_management'));
    }

    public function cetakJadwal($kelas_id)
    {
        $kelas = Kelas::findOrFail($kelas_id);
        $jadwals = Jadwal::where('kelas_id', $kelas_id)
            ->with('guru', 'kategori')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam')
            ->get()
            ->groupBy('hari');

        $pdf = Pdf::loadView('jadwal.pdf', compact('jadwals', 'kelas'));
        return $pdf->download('jadwal-' . $kelas->nama_kelas . '.pdf');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jamPelajaranMenit = 35;

        DB::beginTransaction();
        try {
            if ($jadwal->guru_id) {
                $guru = Guru::find($jadwal->guru_id);
                if ($guru) {
                    $guru->sisa_jam_mengajar += $jamPelajaranMenit;
                    $guru->save();
                }
            }

            $jadwal->delete();
            DB::commit();

            return back()->with('success', 'Jadwal berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting schedule: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus jadwal.');
        }
    }

    public function destroyAll($kelas_id)
    {
        DB::beginTransaction();
        try {
            $jadwals = Jadwal::where('kelas_id', $kelas_id)->get();

            // Kelompokkan jadwal berdasarkan guru untuk efisiensi
            $guruHoursToRestore = [];
            foreach ($jadwals as $jadwal) {
                if ($jadwal->guru_id) {
                    // Hitung durasi dari string 'jam' (contoh: '07:00 - 07:35')
                    $jamParts = explode(' - ', $jadwal->jam);
                    if (count($jamParts) == 2) {
                        try {
                            $jamMulai = new \DateTime($jamParts[0]);
                            $jamSelesai = new \DateTime($jamParts[1]);
                            $durasiMenit = ($jamSelesai->getTimestamp() - $jamMulai->getTimestamp()) / 60;

                            if (!isset($guruHoursToRestore[$jadwal->guru_id])) {
                                $guruHoursToRestore[$jadwal->guru_id] = 0;
                            }
                            $guruHoursToRestore[$jadwal->guru_id] += $durasiMenit;
                        } catch (\Exception $e) {
                            // Abaikan jika format jam salah
                        }
                    }
                }
            }

            // Kembalikan jam mengajar ke setiap guru
            foreach ($guruHoursToRestore as $guruId => $menit) {
                Guru::where('id', $guruId)->increment('sisa_jam_mengajar', $menit);
            }

            // Hapus semua jadwal untuk kelas ini
            Jadwal::where('kelas_id', $kelas_id)->delete();

            DB::commit();
            return redirect()->route('jadwal.pilihKelas')->with('success', 'Semua jadwal untuk kelas ini telah berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting all schedules: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus semua jadwal.');
        }
    }
}
