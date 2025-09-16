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
use App\Models\Tabelj;

class JadwalController extends Controller
{
    public function index()
    {
        return redirect()->route('jadwal.pilihKelasLihat');
    }

    public function create($kelas_id)
    {
        $kelas = Kelas::findOrFail($kelas_id);
        $gurus = Guru::with(['availabilities' => function ($query) {
            $query->orderBy('hari')->orderBy('jam_mulai');
        }])->orderBy('nama')->get();
        
        $kategoris = JadwalKategori::all();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $timeSlots = Tabelj::with('jadwalKategori')->orderBy('jam_mulai')->get()->map(function ($slot) {
            $slot->jam_mulai = \Carbon\Carbon::parse($slot->jam_mulai)->format('H:i');
            $slot->jam_selesai = \Carbon\Carbon::parse($slot->jam_selesai)->format('H:i');
            return $slot;
        });

        $jadwals = Jadwal::where('kelas_id', $kelas_id)
            ->with('guru', 'kategori')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam')
            ->get();

        // Mengambil semua jadwal yang ada untuk validasi di frontend
        $allSchedules = Jadwal::with(['guru:id,nama', 'kelas:id,nama_kelas'])->get();

        $scheduleGrid = [];
        foreach ($timeSlots as $slot) {
            foreach ($days as $day) {
                $scheduleGrid[$slot->jam][$day] = null;
            }
        }

        foreach ($jadwals as $jadwal) {
            if (isset($scheduleGrid[$jadwal->jam][$jadwal->hari])) {
                $scheduleGrid[$jadwal->jam][$jadwal->hari] = $jadwal;
            }
        }

        $guruAvailabilities = $gurus->mapWithKeys(function ($guru) {
            return [$guru->id => $guru->availabilities->map(function ($avail) {
                $jam_mulai_formatted = \Carbon\Carbon::parse($avail->jam_mulai)->format('H:i');
                $jam_selesai_formatted = \Carbon\Carbon::parse($avail->jam_selesai)->format('H:i');
                return [
                    'id' => $avail->id,
                    'hari' => $avail->hari,
                    'jam' => $jam_mulai_formatted . ' - ' . $jam_selesai_formatted,
                ];
            })];
        });

        // Data untuk dropdown guru yang tersedia per slot waktu
        $availabilities = GuruAvailability::with('guru:id,nama,pengampu')->get();
        $availableGurus = [];

        // 1. Inisialisasi struktur data untuk view, menggunakan kolom 'jam' asli sebagai kunci
        foreach ($days as $day) {
            foreach ($timeSlots as $slot) {
                $availableGurus[$day][$slot->jam] = [];
            }
        }

        // 2. Buat peta lookup dari jam_mulai/selesai yang dinormalisasi ke kolom 'jam' asli dari tabelj
        $lookupMap = [];
        foreach ($timeSlots as $slot) {
            $key = preg_replace('/\s+/', '', trim($slot->jam_mulai) . '-' . trim($slot->jam_selesai));
            $lookupMap[$key] = $slot->jam;
        }

        // 3. Cocokkan dan isi guru yang tersedia
        foreach ($availabilities as $availability) {
            $key = preg_replace('/\s+/', '', trim($availability->jam_mulai) . '-' . trim($availability->jam_selesai));
            if (isset($lookupMap[$key])) {
                $originalJamKey = $lookupMap[$key];
                $availableGurus[$availability->hari][$originalJamKey][] = $availability->guru;
            }
        }

        return view('jadwal.create', compact(
            'kelas',
            'gurus',
            'kategoris',
            'jadwals',
            'guruAvailabilities',
            'days',
            'timeSlots',
            'scheduleGrid',
            'allSchedules',
            'availableGurus'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:gurus,id',
            'availability_id' => 'required|exists:guru_availabilities,id',
        ]);

        $availability = GuruAvailability::findOrFail($validated['availability_id']);
        $guru = Guru::findOrFail($validated['guru_id']);
        $kelasId = $validated['kelas_id'];
        $hari = $availability->hari;
        $jam = $availability->jam_mulai . ' - ' . $availability->jam_selesai;

        // Validasi 1: Cek apakah sudah ada jadwal di kelas dan waktu yang sama
        $slotTaken = Jadwal::where('kelas_id', $kelasId)
            ->where('hari', $hari)
            ->where('jam', $jam)
            ->exists();

        if ($slotTaken) {
            return back()->with('error', 'Jadwal bentrok! Sudah ada jadwal lain di kelas ini pada waktu tersebut.');
        }

        // Validasi 2: Cek apakah guru sudah mengajar di kelas lain pada waktu yang sama
        $teacherClash = Jadwal::where('guru_id', $guru->id)
            ->where('hari', $hari)
            ->where('jam', $jam)
            ->where('kelas_id', '!=', $kelasId)
            ->first();

        if ($teacherClash) {
            $kelasBentrok = Kelas::find($teacherClash->kelas_id);
            return back()->with('error', "Jadwal bentrok! Guru {$guru->nama} sudah mengajar di kelas {$kelasBentrok->nama_kelas} pada waktu tersebut.");
        }

        Jadwal::create([
            'kelas_id' => $kelasId,
            'guru_id' => $guru->id,
            'mapel' => $guru->pengampu,
            'hari' => $hari,
            'jam' => $jam,
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function storeKategori(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'kategori_id' => 'required|exists:jadwal_kategoris,id',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam' => 'required|string',
        ]);
        
        // Basic validation for jam format
        if (!preg_match('/^\d{2}:\d{2} - \d{2}:\d{2}$/', $validated['jam'])) {
            return back()->with('error', 'Format jam tidak valid. Harusnya HH:MM - HH:MM');
        }

        Jadwal::create([
            'kelas_id' => $validated['kelas_id'],
            'jadwal_kategori_id' => $validated['kategori_id'],
            'hari' => $validated['hari'],
            'jam' => $validated['jam'],
        ]);

        return back()->with('success', 'Kategori jadwal berhasil ditambahkan.');
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

            // 1. Create a lookup map of all possible time slots from Tabelj
            $timeSlotMap = Tabelj::all()->keyBy('jam')->map(function ($slot) {
                return preg_replace('/\s+/', '', trim($slot->jam_mulai) . '-' . trim($slot->jam_selesai));
            });

            // 2. Create a set of available slots for each teacher
            $availabilitySet = GuruAvailability::all()->mapWithKeys(function ($avail) {
                $key = $avail->guru_id . '-' . $avail->hari . '-' . preg_replace('/\s+/', '', trim($avail->jam_mulai) . '-' . trim($avail->jam_selesai));
                return [$key => true];
            });

            // 3. Get other schedules for clash validation
            $otherSchedules = Jadwal::where('kelas_id', '!=', $kelasId)->get()
                ->keyBy(fn($item) => $item->hari . '-' . $item->jam . '-' . $item->guru_id);


            // 4. Loop and validate
            foreach ($schedules as $scheduleData) {
                if ($scheduleData['guru_id']) {
                    $guru = Guru::find($scheduleData['guru_id']);
                    $submittedJam = $scheduleData['jam'];

                    // --- Availability Check ---
                    if (!isset($timeSlotMap[$submittedJam])) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => "Jam tidak valid: {$submittedJam}."], 400);
                    }
                    $normalizedJam = $timeSlotMap[$submittedJam];
                    $availabilityKey = $scheduleData['guru_id'] . '-' . $scheduleData['hari'] . '-' . $normalizedJam;

                    if (!isset($availabilitySet[$availabilityKey])) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => "Guru {$guru->nama} tidak tersedia pada {$scheduleData['hari']} jam {$scheduleData['jam']}."], 400);
                    }

                    // --- Clash Check ---
                    $scheduleKey = $scheduleData['hari'] . '-' . $scheduleData['jam'] . '-' . $scheduleData['guru_id'];
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
