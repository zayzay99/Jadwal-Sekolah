<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        return view('manage.tahun-ajaran.index', compact('tahunAjarans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        return view('manage.tahun-ajaran.create', compact('tahunAjarans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                // Validasi unik berdasarkan kombinasi tahun_ajaran dan semester
                \Illuminate\Validation\Rule::unique('tahun_ajarans')->where(fn ($query) => $query->where('semester', $request->semester)),
            ],
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'nullable|boolean',
            'source_tahun_ajaran_id' => 'nullable|exists:tahun_ajarans,id',
            'skip_kelas_assignments' => 'nullable|boolean',
            'skip_jadwal' => 'nullable|boolean', // Validated but not used for now
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

            if ($sourceYearId) {
                $newYearId = $newTahunAjaran->id;

                // 1. Clone Kelas and create an ID map
                $oldToNewKelasIdMap = [];
                $sourceClasses = Kelas::where('tahun_ajaran_id', $sourceYearId)->get();


                foreach ($sourceClasses as $sourceClass) {
                   // Check if a class with the same name already exists in the new academic year
                    $existingClass = Kelas::where('nama_kelas', $sourceClass->nama_kelas)->where('tahun_ajaran_id', $newYearId)->first();
                    if (!$existingClass) {
                        $newClass = $sourceClass->replicate();
                        $newClass->tahun_ajaran_id = $newYearId;
                        $newClass->save();
                        $oldToNewKelasIdMap[$sourceClass->id] = $newClass->id;}
                }


                // 2. Clone kelas_siswa pivot data if checkbox is not checked
                if (!$request->boolean('skip_kelas_assignments')) {
                    // FIX: Filter pivot data by source year ID as well
                    $sourceKelasSiswa = DB::table('kelas_siswa')
                        ->where('tahun_ajaran_id', $sourceYearId)
                        ->get();
                    $newKelasSiswaData = [];

                    foreach ($sourceKelasSiswa as $pivot) {
                        if (isset($oldToNewKelasIdMap[$pivot->kelas_id])) {
                            $newKelasSiswaData[] = [
                                'siswa_id' => $pivot->siswa_id,
                                'kelas_id' => $oldToNewKelasIdMap[$pivot->kelas_id],
                                'tahun_ajaran_id' => $newYearId, // Ini yang kurang
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (!empty($newKelasSiswaData)) {
                        DB::table('kelas_siswa')->insert($newKelasSiswaData);
                    }
                }
                
                // 3. Clone Jadwal data if checkbox is not checked
                if (!$request->boolean('skip_jadwal')) {
                    // We need to find schedules associated with the original classes
                    $sourceJadwals = \App\Models\Jadwal::where('tahun_ajaran_id', $sourceYearId)->whereIn('kelas_id', array_keys($oldToNewKelasIdMap))->get();
                    $newJadwalsData = [];

                    foreach ($sourceJadwals as $jadwal) {
                        // Ensure the old class ID exists in our map
                        if (isset($oldToNewKelasIdMap[$jadwal->kelas_id])) {
                            $newJadwalsData[] = [
                                'hari' => $jadwal->hari,
                                'jam' => $jadwal->jam,
                                'tabelj_id' => $jadwal->tabelj_id,
                                'guru_id' => $jadwal->guru_id,
                                'mapel' => $jadwal->mapel,
                                'kelas_id' => $oldToNewKelasIdMap[$jadwal->kelas_id], // Use the new class ID
                                'jadwal_kategori_id' => $jadwal->jadwal_kategori_id,
                                'tahun_ajaran_id' => $newYearId, // Use the new academic year ID
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (!empty($newJadwalsData)) {
                        DB::table('jadwals')->insert($newJadwalsData);
                    }
                }
            }
        });

        return redirect()->route('admin.dashboard')
            ->with('success', 'Tahun Ajaran berhasil dibuat.');
    }

    public function validationMessages()
    {
        return ['tahun_ajaran.unique' => 'Kombinasi Tahun Ajaran dan Semester ini sudah ada.'];
    }

    /**
     * Display the specified resource.
     */
    public function show(TahunAjaran $tahunAjaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('manage.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'tahun_ajaran' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                // FIX: Validate uniqueness based on the combination of tahun_ajaran and semester, ignoring the current record
                \Illuminate\Validation\Rule::unique('tahun_ajarans')->where(fn ($query) => $query->where('semester', $request->semester))->ignore($tahunAjaran->id),
            ],
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only('tahun_ajaran', 'semester', 'is_active');
        $data['is_active'] = $request->has('is_active');

        if ($data['is_active']) {
            // Deactivate all other academic years
            TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['is_active' => false]);
        }

        $tahunAjaran->update($data);
        
        // Set the active academic year in the session
        if ($data['is_active']) {
            session(['tahun_ajaran_id' => $tahunAjaran->id]);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAjaran $tahunAjaran)
    {
        try {
            $tahunAjaran->delete();
            // On success, return a JSON response
            return response()->json(['message' => 'Tahun Ajaran berhasil dihapus.'], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for foreign key violation error (SQLSTATE 23000)
            if ($e->getCode() === '23000') {
                return response()->json(['message' => 'Gagal menghapus: Tahun Ajaran ini masih digunakan oleh data lain (misalnya Jadwal atau Kelas). Hapus atau ubah data terkait terlebih dahulu.'], 409); // 409 Conflict
            }
            // For other database-related errors
            return response()->json(['message' => 'Gagal menghapus data karena masalah database.'], 500);
        } catch (\Exception $e) {
            // For any other generic errors
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
        // Deactivate all other academic years
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);

        // Activate the selected one
        $tahunAjaran->update(['is_active' => true]);

        // Set the active academic year in the session
        session(['tahun_ajaran_id' => $tahunAjaran->id]);

        // Redirect back to the admin dashboard
        return redirect()->route('admin.dashboard')->with('success', 'Tahun Ajaran berhasil diganti ke ' . $tahunAjaran->tahun_ajaran . ' (' . $tahunAjaran->semester . ')');
    }
}