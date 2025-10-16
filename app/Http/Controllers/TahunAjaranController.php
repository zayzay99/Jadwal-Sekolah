<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        return view('manage.tahun-ajaran.index', compact('tahunAjarans'));
    }

    public function create()
    {
        $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        return view('manage.tahun-ajaran.create', compact('tahunAjarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                \Illuminate\Validation\Rule::unique('tahun_ajarans')->where(fn ($query) => $query->where('semester', $request->semester)),
            ],
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'nullable|boolean',
            'source_tahun_ajaran_id' => 'nullable|exists:tahun_ajarans,id',
            'skip_kelas_assignments' => 'nullable|boolean',
            'skip_jadwal' => 'nullable|boolean',
        ]);

        $newTahunAjaran = null;

        DB::transaction(function () use ($request, &$newTahunAjaran) {
            $data = $request->only('tahun_ajaran', 'semester', 'is_active');
            $data['is_active'] = $request->has('is_active');

            if ($data['is_active']) {
                TahunAjaran::where('is_active', true)->update(['is_active' => false]);
            }

            $newTahunAjaran = TahunAjaran::create($data);

            $sourceYearId = $request->input('source_tahun_ajaran_id');

            // Jika tidak memilih "Salin Data", buat tahun ajaran kosong total
            if (empty($sourceYearId)) {
                return;
            }

            // Jika memilih "Salin Data", lanjutkan proses kloning
            $newYearId = $newTahunAjaran->id;

            // 1. Clone Guru dengan tahun_ajaran_id baru
            $sourceGurus = \App\Models\Guru::where('tahun_ajaran_id', $sourceYearId)->get();
            $oldToNewGuruIdMap = [];
            
            foreach ($sourceGurus as $sourceGuru) {
                $newGuru = $sourceGuru->replicate();
                $newGuru->tahun_ajaran_id = $newYearId;
                $newGuru->save();
                $oldToNewGuruIdMap[$sourceGuru->id] = $newGuru->id;
            }

            // 2. Clone Kelas dan buat peta ID
            $oldToNewKelasIdMap = [];
            $sourceClasses = Kelas::where('tahun_ajaran_id', $sourceYearId)->get();
            
            foreach ($sourceClasses as $sourceClass) {
                $existingClass = Kelas::where('nama_kelas', $sourceClass->nama_kelas)
                                    ->where('tahun_ajaran_id', $newYearId)
                                    ->first();

                if (!$existingClass) {
                    $newClass = $sourceClass->replicate();
                    $newClass->tahun_ajaran_id = $newYearId;
                    // Update guru_id dengan ID guru yang baru jika ada
                    if (isset($oldToNewGuruIdMap[$sourceClass->guru_id])) {
                        $newClass->guru_id = $oldToNewGuruIdMap[$sourceClass->guru_id];
                    }
                    $newClass->save();
                    $oldToNewKelasIdMap[$sourceClass->id] = $newClass->id;
                } else {
                    $oldToNewKelasIdMap[$sourceClass->id] = $existingClass->id;
                }
            }

            // 3. Clone data siswa ke kelas_siswa
            $sourceKelasSiswa = DB::table('kelas_siswa')
                ->where('tahun_ajaran_id', $sourceYearId)
                ->get();

            if ($request->boolean('skip_kelas_assignments')) {
                // Jika "Kosongkan Penempatan Siswa" dicentang:
                // 1. Buat atau cari kelas "Belum Ditempatkan" untuk tahun ajaran baru ini.
                $unassignedKelas = Kelas::firstOrCreate(
                    [
                        'nama_kelas' => '[Belum Ditempatkan]',
                        'tahun_ajaran_id' => $newYearId,
                    ],
                    [
                        'guru_id' => null, // Tidak ada wali kelas
                    ]
                );

                // 2. Ambil semua ID siswa unik dari tahun ajaran sumber.
                $siswaIds = $sourceKelasSiswa->pluck('siswa_id')->unique();
                $newKelasSiswaData = [];

                // 3. Daftarkan siswa ke tahun ajaran baru dengan menempatkan mereka di kelas "Belum Ditempatkan".
                foreach ($siswaIds as $siswaId) {
                    $newKelasSiswaData[] = [
                        'siswa_id' => $siswaId,
                        'kelas_id' => $unassignedKelas->id,
                        'tahun_ajaran_id' => $newYearId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($newKelasSiswaData)) {
                    DB::table('kelas_siswa')->insert($newKelasSiswaData);
                }
            } else {
                // Jika checkbox TIDAK dicentang: Salin siswa dengan kelas seperti biasa
                $newKelasSiswaData = [];

                foreach ($sourceKelasSiswa as $pivot) {
                    if (isset($oldToNewKelasIdMap[$pivot->kelas_id])) {
                        $newKelasSiswaData[] = [
                            'siswa_id' => $pivot->siswa_id,
                            'kelas_id' => $oldToNewKelasIdMap[$pivot->kelas_id],
                            'tahun_ajaran_id' => $newYearId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (!empty($newKelasSiswaData)) {
                    DB::table('kelas_siswa')->insert($newKelasSiswaData);
                }
            }
            
            // 4. Clone Jadwal jika checkbox TIDAK dicentang
            if (!$request->boolean('skip_jadwal')) {
                $sourceJadwals = \App\Models\Jadwal::where('tahun_ajaran_id', $sourceYearId)->get();
                $newJadwalsData = [];

                foreach ($sourceJadwals as $jadwal) {
                    if (isset($oldToNewKelasIdMap[$jadwal->kelas_id])) {
                        $newJadwalsData[] = [
                            'hari' => $jadwal->hari,
                            'jam' => $jadwal->jam,
                            'tabelj_id' => $jadwal->tabelj_id,
                            'guru_id' => isset($oldToNewGuruIdMap[$jadwal->guru_id]) ? $oldToNewGuruIdMap[$jadwal->guru_id] : $jadwal->guru_id,
                            'mapel' => $jadwal->mapel,
                            'kelas_id' => $oldToNewKelasIdMap[$jadwal->kelas_id],
                            'jadwal_kategori_id' => $jadwal->jadwal_kategori_id,
                            'tahun_ajaran_id' => $newYearId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (!empty($newJadwalsData)) {
                    DB::table('jadwals')->insert($newJadwalsData);
                }
            }
        });

        // Force activation of the new school year to avoid user confusion.
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        $newTahunAjaran->update(['is_active' => true]);
        session(['tahun_ajaran_id' => $newTahunAjaran->id]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Tahun Ajaran berhasil dibuat dan langsung diaktifkan.');
    }

    public function validationMessages()
    {
        return ['tahun_ajaran.unique' => 'Kombinasi Tahun Ajaran dan Semester ini sudah ada.'];
    }

    public function show(TahunAjaran $tahunAjaran)
    {
        //
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('manage.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'tahun_ajaran' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                \Illuminate\Validation\Rule::unique('tahun_ajarans')->where(fn ($query) => $query->where('semester', $request->semester))->ignore($tahunAjaran->id),
            ],
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only('tahun_ajaran', 'semester', 'is_active');
        $data['is_active'] = $request->has('is_active');

        if ($data['is_active']) {
            TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['is_active' => false]);
        }

        $tahunAjaran->update($data);
        
        if ($data['is_active']) {
            session(['tahun_ajaran_id' => $tahunAjaran->id]);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        try {
            $tahunAjaran->delete();
            return response()->json(['message' => 'Tahun Ajaran berhasil dihapus.'], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json(['message' => 'Gagal menghapus: Tahun Ajaran ini masih digunakan oleh data lain (misalnya Jadwal atau Kelas). Hapus atau ubah data terkait terlebih dahulu.'], 409);
            }
            return response()->json(['message' => 'Gagal menghapus data karena masalah database.'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function setActive(Request $request, TahunAjaran $tahunAjaran)
    {
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        $tahunAjaran->update(['is_active' => true]);

        session(['tahun_ajaran_id' => $tahunAjaran->id]);

        return back()->with('success', "Tahun ajaran '{$tahunAjaran->tahun_ajaran}' telah diaktifkan.");
    }

    public function switchActive(TahunAjaran $tahunAjaran)
    {
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        $tahunAjaran->update(['is_active' => true]);
        session(['tahun_ajaran_id' => $tahunAjaran->id]);

        return redirect()->route('admin.dashboard')->with('success', 'Tahun Ajaran berhasil diganti ke ' . $tahunAjaran->tahun_ajaran . ' (' . $tahunAjaran->semester . ')');
    }
}