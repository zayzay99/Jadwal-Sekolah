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

        // Optimized availability and schedule fetching
        $availabilities = GuruAvailability::all()->keyBy(function($item) {
            return $item->guru_id . '-' . $item->hari . '-' . $item->jam;
        });

        $allSchedules = Jadwal::all()->keyBy(function($item) {
            return $item->hari . '-' . $item->jam;
        });

        $availableGurus = [];
        foreach ($days as $day) {
            foreach ($timeSlots as $timeSlot) {
                $jam = $timeSlot->jam_mulai . ' - ' . $timeSlot->jam_selesai;
                $availableGurus[$day][$jam] = [];
                foreach ($gurus as $guru) {
                    $availabilityKey = $guru->id . '-' . $day . '-' . $jam;
                    $scheduleKey = $day . '-' . $jam;

                    $isAvailable = $availabilities->has($availabilityKey);
                    $bookedSchedule = $allSchedules->get($scheduleKey);

                    $isBookedInThisClass = $bookedSchedule && $bookedSchedule->guru_id == $guru->id && $bookedSchedule->kelas_id == $kelas_id;

                    if ($isAvailable) {
                        if (!$bookedSchedule || $isBookedInThisClass) {
                            $availableGurus[$day][$jam][] = $guru;
                        }
                    }
                }
            }
        }

        return view('jadwal.create', compact('kelas', 'gurus', 'days', 'timeSlots', 'scheduleGrid', 'kategoris', 'availableGurus'));
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

        DB::beginTransaction();
        try {
            // --- Validasi Jadwal ---
            $availabilities = GuruAvailability::all()->keyBy(fn($item) => $item->guru_id . '-' . $item->hari . '-' . $item->jam);
            $otherSchedules = Jadwal::where('kelas_id', '!=', $kelasId)->get()->keyBy(fn($item) => $item->hari . '-' . $item->jam . '-' . $item->guru_id);

            foreach ($schedules as $scheduleData) {
                if ($scheduleData['guru_id']) {
                    $guru = Guru::find($scheduleData['guru_id']);
                    $availabilityKey = $scheduleData['guru_id'] . '-' . $scheduleData['hari'] . '-' . $scheduleData['jam'];
                    $scheduleKey = $scheduleData['hari'] . '-' . $scheduleData['jam'] . '-' . $scheduleData['guru_id'];

                    if (!$availabilities->has($availabilityKey)) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => "Guru {$guru->nama} tidak tersedia pada {$scheduleData['hari']} jam {$scheduleData['jam']}."], 400);
                    }

                    if ($otherSchedules->has($scheduleKey)) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => "Guru {$guru->nama} sudah dijadwalkan di kelas lain pada waktu yang sama."], 400);
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
                    return response()->json(['success' => false, 'message' => 'Guru ' . $guru->nama . ' akan melebihi total jam mengajar.'], 400);
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
}