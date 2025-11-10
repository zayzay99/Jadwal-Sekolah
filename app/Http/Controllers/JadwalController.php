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
        $activeTahunAjaranId = session('tahun_ajaran_id');
        if (!$activeTahunAjaranId) {
            abort(400, 'Tahun ajaran tidak aktif. Silakan pilih tahun ajaran terlebih dahulu.');
        }

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
            ->where('tahun_ajaran_id', $activeTahunAjaranId)
            ->with('guru', 'kategori')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam')
            ->get();

        // Mengambil semua jadwal yang ada untuk validasi di frontend, hanya untuk tahun ajaran ini
        $allSchedules = Jadwal::where('tahun_ajaran_id', $activeTahunAjaranId)
            ->with(['guru:id,nama', 'kelas:id,nama_kelas'])->get();

        $scheduleGrid = [];
        foreach ($timeSlots as $slot) {
            $jam = $slot->jam_mulai . ' - ' . $slot->jam_selesai;
            foreach ($days as $day) {
                $scheduleGrid[$jam][$day] = null;
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

        // Initialize with formatted jam string
        foreach ($days as $day) {
            foreach ($timeSlots as $slot) {
                $jam = $slot->jam_mulai . ' - ' . $slot->jam_selesai;
                $availableGurus[$day][$jam] = [];
            }
        }

        // Populate available gurus
        foreach ($availabilities as $availability) {
            $jamMulai = \Carbon\Carbon::parse($availability->jam_mulai)->format('H:i');
            $jamSelesai = \Carbon\Carbon::parse($availability->jam_selesai)->format('H:i');
            $jam = $jamMulai . ' - ' . $jamSelesai;

            if (isset($availableGurus[$availability->hari][$jam])) {
                $availableGurus[$availability->hari][$jam][] = $availability->guru;
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

        $activeTahunAjaranId = session('tahun_ajaran_id');
        if (!$activeTahunAjaranId) {
            return back()->with('error', 'Tahun ajaran tidak aktif. Silakan pilih tahun ajaran terlebih dahulu.');
        }

        $availability = GuruAvailability::findOrFail($validated['availability_id']);
        $guru = Guru::findOrFail($validated['guru_id']);
        $kelasId = $validated['kelas_id'];
        $hari = $availability->hari;
        $jam = $availability->jam_mulai . ' - ' . $availability->jam_selesai;

        // Validasi 1: Cek apakah sudah ada jadwal di kelas dan waktu yang sama untuk tahun ajaran ini
        $slotTaken = Jadwal::where('kelas_id', $kelasId)
            ->where('hari', $hari)
            ->where('jam', $jam)
            ->where('tahun_ajaran_id', $activeTahunAjaranId)
            ->exists();

        if ($slotTaken) {
            return back()->with('error', 'Jadwal bentrok! Sudah ada jadwal lain di kelas ini pada waktu tersebut.');
        }

        // Validasi 2: Cek apakah guru sudah mengajar di kelas lain pada waktu yang sama untuk tahun ajaran ini
        $teacherClash = Jadwal::where('guru_id', $guru->id)
            ->where('hari', $hari)
            ->where('jam', $jam)
            ->where('kelas_id', '!=', $kelasId)
            ->where('tahun_ajaran_id', $activeTahunAjaranId)
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
            'tahun_ajaran_id' => $activeTahunAjaranId,
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
            'schedules.*.tabelj_id' => 'nullable|exists:tabeljs,id',
            'schedules.*.jam' => 'required|string',
        ]);

        $kelasId = $validated['kelas_id'];
        $schedules = $validated['schedules'];
        $activeTahunAjaranId = session('tahun_ajaran_id');

        if (!$activeTahunAjaranId) {
            return response()->json(['success' => false, 'message' => 'Tahun ajaran tidak aktif.'], 400);
        }

        // --- Backend Validation (Clash check) ---
        $teacherClashes = [];
        $existingSchedules = Jadwal::where('kelas_id', '!=', $kelasId)
            ->where('tahun_ajaran_id', $activeTahunAjaranId)
            ->whereNotNull('guru_id')->get();

        foreach ($existingSchedules as $jadwal) {
            $teacherClashes[$jadwal->guru_id][$jadwal->hari][$jadwal->jam] = $jadwal->kelas_id;
        }

        foreach ($schedules as $scheduleData) {
            if (empty($scheduleData['guru_id'])) continue;
            $guruId = $scheduleData['guru_id'];
            $hari = $scheduleData['hari'];
            $jam = $scheduleData['jam'];

            if (isset($teacherClashes[$guruId][$hari][$jam])) {
                $guru = Guru::find($guruId);
                $kelas = Kelas::find($teacherClashes[$guruId][$hari][$jam]);
                return response()->json([
                    'success' => false,
                    'message' => "Jadwal bentrok! Guru {$guru->nama} sudah mengajar di kelas {$kelas->nama_kelas} pada hari {$hari}, jam {$jam}."
                ], 422);
            }
            $teacherClashes[$guruId][$hari][$jam] = $kelasId;
        }

        DB::beginTransaction();
        try {
            // --- Teacher Hour Calculation ---
            $calculateDuration = function($jam) {
                try {
                    $parts = explode(' - ', $jam);
                    if (count($parts) !== 2) return 0;
                    $start = \Carbon\Carbon::parse($parts[0]);
                    $end = \Carbon\Carbon::parse($parts[1]);
                    return $end->diffInMinutes($start);
                } catch (\Exception $e) { return 0; }
            };

            $oldSchedules = Jadwal::where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $activeTahunAjaranId)->get();

            $guruMinutesChange = [];

            // Subtract old minutes
            foreach ($oldSchedules as $schedule) {
                if ($schedule->guru_id) {
                    $duration = $calculateDuration($schedule->jam);
                    $guruMinutesChange[$schedule->guru_id] = ($guruMinutesChange[$schedule->guru_id] ?? 0) - $duration;
                }
            }

            // Add new minutes
            foreach ($schedules as $scheduleData) {
                if (!empty($scheduleData['guru_id'])) {
                    $duration = $calculateDuration($scheduleData['jam']);
                    $guruMinutesChange[$scheduleData['guru_id']] = ($guruMinutesChange[$scheduleData['guru_id']] ?? 0) + $duration;
                }
            }

            $involvedGuruIds = array_keys($guruMinutesChange);
            $gurus = !empty($involvedGuruIds) ? Guru::whereIn('id', $involvedGuruIds)->get() : collect();
            $gurusById = $gurus->keyBy('id');

            if (!empty($involvedGuruIds)) {
                foreach ($gurusById as $guruId => $guru) {
                    $change = $guruMinutesChange[$guruId];
                    if (($guru->sisa_jam_mengajar - $change) < 0) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => 'Guru ' . $guru->nama . ' akan melebihi total jam mengajar.'], 422);
                    }
                }
            }

            // --- Save Schedule ---
            Jadwal::where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $activeTahunAjaranId)
                ->delete();

            $dataToInsert = [];
            $now = now();
            
            foreach ($schedules as $scheduleData) {
                if (!empty($scheduleData['guru_id']) || !empty($scheduleData['jadwal_kategori_id'])) {
                    $mapel = null;
                    if (!empty($scheduleData['guru_id']) && isset($gurusById[$scheduleData['guru_id']])) {
                        $mapel = $gurusById[$scheduleData['guru_id']]->pengampu;
                    }

                    $dataToInsert[] = [
                        'kelas_id' => $kelasId,
                        'guru_id' => $scheduleData['guru_id'] ?: null,
                        'mapel' => $mapel,
                        'jadwal_kategori_id' => $scheduleData['jadwal_kategori_id'] ?: null,
                        'hari' => $scheduleData['hari'],
                        'tabelj_id' => $scheduleData['tabelj_id'] ?: null,
                        'jam' => $scheduleData['jam'],
                        'tahun_ajaran_id' => $activeTahunAjaranId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($dataToInsert)) {
                Jadwal::insert($dataToInsert);
            }

            // --- Update Teacher Hours ---
            // Update sisa jam mengajar untuk semua guru yang terlibat
            foreach ($guruMinutesChange as $guruId => $menitPerubahan) {
                // Gunakan query update untuk menghindari masalah race condition
                // dan lebih efisien. `sisa_jam_mengajar = sisa_jam_mengajar - (perubahan)`
                // Jika menitPerubahan positif (jam bertambah), sisa jam berkurang.
                // Jika menitPerubahan negatif (jam berkurang), sisa jam bertambah.
                Guru::where('id', $guruId)
                    ->decrement('sisa_jam_mengajar', $menitPerubahan);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Jadwal telah berhasil disimpan.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk store error: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan jadwal. Terjadi kesalahan server.'], 500);
        }
    }

    public function pilihKelas()
    {
        $kategoriList = ['VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $kategoriData = [];

        $activeTahunAjaran = \App\Models\TahunAjaran::find(session('tahun_ajaran_id'));
        $activeTahunAjaranId = session('tahun_ajaran_id');

        foreach ($kategoriList as $kategori) {
            $query = Kelas::where('nama_kelas', 'like', $kategori . '-%');

            if ($activeTahunAjaranId) {
                $query->where('tahun_ajaran_id', $activeTahunAjaranId);
            }
            $kelasCount = $query->count();

            $kategoriData[] = (object)[
                'nama' => $kategori,
                'kelas_count' => $kelasCount,
            ];
        }
        return view('jadwal.pilih_kelas', ['kategori' => $kategoriData, 'activeTahunAjaran' => $activeTahunAjaran]);
    }

    public function pilihSubKelas($kategori)
    {
        $activeTahunAjaranId = session('tahun_ajaran_id');
        $subkelas = Kelas::where('nama_kelas', 'like', $kategori . '-%')
                         ->where('tahun_ajaran_id', $activeTahunAjaranId) // FIX: Filter by active year
                         ->orderBy('nama_kelas')
                         ->get();
        return view('jadwal.pilih_subkelas', compact('kategori', 'subkelas'));
    }

    public function pilihKelasLihat()
    {
        $activeTahunAjaranId = session('tahun_ajaran_id');
        $query = Kelas::query();
        if ($activeTahunAjaranId) {
            $query->where('tahun_ajaran_id', $activeTahunAjaranId);
        }
        $kelas = $query->orderBy('nama_kelas')->get();
        return view('jadwal.pilih_kelas_lihat', compact('kelas'));
    }

    public function jadwalPerKelas(Request $request, $kelas_id)
    {
        $kelas = Kelas::findOrFail($kelas_id);
        $activeTahunAjaranId = session('tahun_ajaran_id');
        $jadwals = Jadwal::where('kelas_id', $kelas_id)
            ->where('tahun_ajaran_id', $activeTahunAjaranId) // Filter by active academic year
            ->with('guru', 'kategori')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam')
            ->get()
            ->groupBy('hari');

        $is_management = $request->query('management', 'true') !== 'false';

        return view('jadwal.jadwal_per_kelas', compact('kelas', 'jadwals', 'is_management'));
    }

    public function updateInline(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:guru,kategori',
            'guru_id' => 'nullable|required_if:type,guru|exists:gurus,id',
            'jadwal_kategori_id' => 'nullable|required_if:type,kategori|exists:jadwal_kategoris,id',
        ]);

        $jadwal = Jadwal::findOrFail($id);
        $activeTahunAjaranId = session('tahun_ajaran_id');

        DB::beginTransaction();
        try {
            $oldGuruId = $jadwal->guru_id;
            $newGuruId = null;
            $newMapel = null;

            // --- Validasi Bentrok ---
            if ($validated['type'] === 'guru') {
                $newGuruId = $validated['guru_id'];
                $teacherClash = Jadwal::where('guru_id', $newGuruId)
                    ->where('hari', $jadwal->hari)
                    ->where('jam', $jadwal->jam)
                    ->where('tahun_ajaran_id', $activeTahunAjaranId)
                    ->where('id', '!=', $id) // Exclude current schedule
                    ->first();

                if ($teacherClash) {
                    $kelasBentrok = Kelas::find($teacherClash->kelas_id);
                    return response()->json(['success' => false, 'message' => "Jadwal bentrok! Guru tersebut sudah mengajar di kelas {$kelasBentrok->nama_kelas} pada waktu yang sama."], 422);
                }
            }

            // --- Kalkulasi Ulang Jam Guru ---
            $duration = $this->calculateDuration($jadwal->jam);
            
            // Kembalikan jam ke guru lama jika ada
            if ($oldGuruId) {
                Guru::where('id', $oldGuruId)->increment('sisa_jam_mengajar', $duration);
            }

            // Kurangi jam dari guru baru jika ada
            if ($newGuruId) {
                $guruBaru = Guru::find($newGuruId);
                if ($guruBaru->sisa_jam_mengajar < $duration) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Guru ' . $guruBaru->nama . ' akan melebihi total jam mengajar.'], 422);
                }
                $guruBaru->decrement('sisa_jam_mengajar', $duration);
                $newMapel = $guruBaru->pengampu;
            }

            // --- Update Jadwal ---
            if ($validated['type'] === 'guru') {
                $jadwal->guru_id = $newGuruId;
                $jadwal->mapel = $newMapel;
                $jadwal->jadwal_kategori_id = null;
            } else { // type 'kategori'
                $jadwal->guru_id = null;
                $jadwal->mapel = null;
                $jadwal->jadwal_kategori_id = $validated['jadwal_kategori_id'];
            }
            $jadwal->save();

            DB::commit();

            // Ambil data terbaru untuk dikirim kembali ke frontend
            $updatedJadwal = Jadwal::with('guru', 'kategori')->find($id);

            return response()->json(['success' => true, 'message' => 'Jadwal berhasil diperbarui.', 'data' => $updatedJadwal]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Inline update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui jadwal. Terjadi kesalahan server.'], 500);
        }
    }

    public function cetakJadwal($kelas_id)
    {
        $kelas = Kelas::findOrFail($kelas_id);
        $activeTahunAjaranId = session('tahun_ajaran_id');
        $jadwals = Jadwal::where('kelas_id', $kelas_id)
            ->with('guru', 'kategori')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam')
            ->get()
            ->groupBy('hari');

        $pdf = Pdf::loadView('jadwal.pdf', compact('jadwals', 'kelas'));
        return $pdf->download('jadwal-' . $kelas->nama_kelas . '.pdf');
    }

    public function cetakJadwalBulk(Request $request)
    {
        $request->validate([
            'kelas_ids' => 'required|string', // Comma-separated string of IDs
        ]);

        $kelasIds = explode(',', $request->kelas_ids);
        $activeTahunAjaranId = session('tahun_ajaran_id');

        $allKelasData = [];
        foreach ($kelasIds as $kelas_id) {
            $kelas = Kelas::findOrFail($kelas_id);
            $jadwals = Jadwal::where('kelas_id', $kelas_id)
                ->where('tahun_ajaran_id', $activeTahunAjaranId)
                ->with('guru', 'kategori')
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('jam')
                ->get()
                ->groupBy('hari');
            
            $allKelasData[] = [
                'kelas' => $kelas,
                'jadwals' => $jadwals,
            ];
        }
        $firstKelasNameParts = explode('-', $allKelasData[0]['kelas']->nama_kelas);
        $angkatanName = $firstKelasNameParts[0] ?? 'Unknown';
        $pdf = Pdf::loadView('jadwal.pdf_bulk', compact('allKelasData'));
        return $pdf->download('jadwal-angkatan-' . $angkatanName . '.pdf');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($jadwal->guru_id) {
                $guru = Guru::find($jadwal->guru_id);
                if ($guru) {
                    $durasiMenit = 0;
                    try {
                        $jamParts = explode(' - ', $jadwal->jam);
                        if (count($jamParts) == 2) {
                            $jamMulai = \Carbon\Carbon::parse($jamParts[0]);
                            $jamSelesai = \Carbon\Carbon::parse($jamParts[1]);
                            $durasiMenit = $jamSelesai->diffInMinutes($jamMulai);
                        }
                    } catch (\Exception $e) {
                        // Abaikan jika format jam salah, durasi akan tetap 0
                    }
                    $guru->sisa_jam_mengajar += $durasiMenit;
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

    public function destroyAll(Request $request, $kelas_id)
    {
        $activeTahunAjaranId = session('tahun_ajaran_id');
        if (!$activeTahunAjaranId) {
            return back()->with('error', 'Tahun ajaran tidak aktif. Silakan pilih tahun ajaran terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            $jadwals = Jadwal::where('kelas_id', $kelas_id)
                ->where('tahun_ajaran_id', $activeTahunAjaranId)->get();

            if ($jadwals->isEmpty()) {
                // Jika memang sudah kosong, anggap berhasil dan redirect kembali.
                return redirect()->route('jadwal.perKelas', ['kelas' => $kelas_id])->with('success', 'Jadwal sudah kosong.');
            }

            $guruHoursToRestore = [];
            foreach ($jadwals as $jadwal) {
                if ($jadwal->guru_id) {
                    $duration = $this->calculateDuration($jadwal->jam);
                    $guruHoursToRestore[$jadwal->guru_id] = ($guruHoursToRestore[$jadwal->guru_id] ?? 0) + $duration;
                }
            }

            // Kembalikan jam mengajar ke setiap guru
            foreach ($guruHoursToRestore as $guruId => $menit) {
                Guru::where('id', $guruId)->increment('sisa_jam_mengajar', $menit);
            }

            // Hapus semua jadwal untuk kelas ini pada tahun ajaran aktif
            Jadwal::where('kelas_id', $kelas_id)
                ->where('tahun_ajaran_id', $activeTahunAjaranId)
                ->delete();
            
            DB::commit();
            return redirect()->route('jadwal.perKelas', ['kelas' => $kelas_id])->with('success', 'Semua jadwal telah berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting all schedules: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus semua jadwal.');
        }
    }

    private function calculateDuration($jamString){
                   $parts = explode(' - ', $jamString);
            if (count($parts) !== 2) return 0;
            $start = \Carbon\Carbon::parse($parts[0]);
            $end = \Carbon\Carbon::parse($parts[1]);
    } 
}
        