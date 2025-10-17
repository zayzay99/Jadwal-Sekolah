<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageKelasController extends Controller
{
    public function index(Request $request)
    {
        $kategoriList = ['VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $selectedKategori = $request->input('kategori');

        $activeTahunAjaranId = session('tahun_ajaran_id');

        $query = Kelas::with('guru');

        if ($activeTahunAjaranId) {
            // Jika ada tahun ajaran aktif, tampilkan kelas untuk tahun ajaran itu
            // dan eager load siswa khusus untuk tahun ajaran itu.
            $query->where('tahun_ajaran_id', $activeTahunAjaranId)
                  ->with(['siswas' => function ($q) use ($activeTahunAjaranId) {
                      $q->where('kelas_siswa.tahun_ajaran_id', $activeTahunAjaranId);
                  }]);
        }
        
        if ($selectedKategori && in_array($selectedKategori, $kategoriList)) {
            $query->where('nama_kelas', 'like', $selectedKategori . '\-%');
        }

        $kelas = $query->get();

        return view('dashboard.kelas_manage.index', compact('kelas', 'kategoriList', 'selectedKategori'));
    }

    public function create()
    {
        $gurus = Guru::all();
        // Get students who are not yet in a class for the current school year
        $activeTahunAjaranId = session('tahun_ajaran_id');
        $siswas = Siswa::whereDoesntHave('kelas', function ($query) use ($activeTahunAjaranId) {
            $query->where('kelas_siswa.tahun_ajaran_id', $activeTahunAjaranId);
        })->get();
    
        return view('dashboard.kelas_manage.create', compact('gurus', 'siswas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkat_kelas' => 'required|string|in:VII,VIII,IX,X,XI,XII',
            'sub_kelas' => 'required|string|max:10',
            'guru_id' => 'nullable|exists:gurus,id',
            'siswa_ids' => 'array',
            'siswa_ids.*' => 'exists:siswas,id',
        ]);

        $activeTahunAjaranId = session('tahun_ajaran_id');
        if (!$activeTahunAjaranId) {
            return back()->withErrors(['tahun_ajaran' => 'Sesi tahun ajaran tidak ditemukan. Silakan pilih tahun ajaran terlebih dahulu.'])->withInput();
        }

        $nama_kelas = $request->tingkat_kelas . '-' . $request->sub_kelas;
        if (Kelas::where('nama_kelas', $nama_kelas)->where('tahun_ajaran_id', $activeTahunAjaranId)->exists()) {
            return back()
                ->withErrors(['sub_kelas' => 'Nama kelas "' . $nama_kelas . '" sudah ada.'])
                ->withInput();
        }

        $kelas = Kelas::create([
            'nama_kelas' => $nama_kelas,
            'guru_id' => $request->guru_id,
            'tahun_ajaran_id' => $activeTahunAjaranId,
        ]);

        if ($request->has('siswa_ids')) {
            $insertData = [];
            foreach ($request->siswa_ids as $siswaId) {
                $insertData[] = [
                    'kelas_id' => $kelas->id,
                    'siswa_id' => $siswaId,
                    'tahun_ajaran_id' => $activeTahunAjaranId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($insertData)) {
                DB::table('kelas_siswa')->insert($insertData);
            }
        }

        return redirect()->route('manage.kelas.index')->with('success', 'Kelas "' . $nama_kelas . '" berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $activeTahunAjaranId = session('tahun_ajaran_id');

        // Load the class with students specifically for its school year
        $kelas = Kelas::with(['siswas' => function ($query) use ($activeTahunAjaranId) {
            $query->where('kelas_siswa.tahun_ajaran_id', $activeTahunAjaranId);
        }])->findOrFail($id);

        $gurus = Guru::all();

        // Get students who are not yet in a class for the current school year, plus the ones already in this class
        $siswasInThisClass = $kelas->siswas->pluck('id');
        $siswas = Siswa::whereDoesntHave('kelas', function ($query) use ($activeTahunAjaranId) {
            $query->where('kelas_siswa.tahun_ajaran_id', $activeTahunAjaranId);
        })->orWhereIn('id', $siswasInThisClass)->get();


        $nama_kelas_parts = explode('-', $kelas->nama_kelas, 2);
        $tingkat_kelas_val = $nama_kelas_parts[0] ?? '';
        $sub_kelas_val = $nama_kelas_parts[1] ?? '';
        
        $kategori = ['VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        if (!in_array($tingkat_kelas_val, $kategori) || count($nama_kelas_parts) < 2) {
            $tingkat_kelas = '';
            $sub_kelas = $kelas->nama_kelas;
        } else {
            $tingkat_kelas = $tingkat_kelas_val;
            $sub_kelas = $sub_kelas_val;
        }

        return view('dashboard.kelas_manage.edit', compact('kelas', 'gurus', 'siswas', 'tingkat_kelas', 'sub_kelas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tingkat_kelas' => 'required|string|in:VII,VIII,IX,X,XI,XII',
            'sub_kelas' => 'required|string|max:10',
            'guru_id' => 'nullable|exists:gurus,id',
            'siswa_ids' => 'array',
            'siswa_ids.*' => 'exists:siswas,id',
        ]);
        $kelas = Kelas::findOrFail($id);
        $activeTahunAjaranId = session('tahun_ajaran_id'); // Use the active school year from session

        $nama_kelas = $request->tingkat_kelas . '-' . $request->sub_kelas;
        if (Kelas::where('nama_kelas', $nama_kelas)
                   ->where('id', '!=', $id)
                   ->where('tahun_ajaran_id', $activeTahunAjaranId)
                   ->exists()) {
            return back()
                ->withErrors(['sub_kelas' => 'Nama kelas "' . $nama_kelas . '" sudah ada.'])
                ->withInput();
        }

        // === VALIDASI BARU: CEK SISWA DI KELAS LAIN ===
        // Cek apakah ada siswa yang dipilih sudah terdaftar di kelas lain pada tahun ajaran ini.
        if ($request->has('siswa_ids')) {
            $conflictingStudents = DB::table('kelas_siswa')
                ->join('siswas', 'kelas_siswa.siswa_id', '=', 'siswas.id')
                ->join('kelas', 'kelas_siswa.kelas_id', '=', 'kelas.id')
                ->whereIn('kelas_siswa.siswa_id', $request->siswa_ids)
                ->where('kelas_siswa.tahun_ajaran_id', $activeTahunAjaranId)
                ->where('kelas_siswa.kelas_id', '!=', $id) // Cek di kelas lain
                ->select('siswas.nama as nama_siswa', 'kelas.nama_kelas')
                ->get();

            if ($conflictingStudents->isNotEmpty()) {
                $errorMessages = $conflictingStudents->map(function ($conflict) {
                    return "Gagal: Siswa '{$conflict->nama_siswa}' sudah terdaftar di kelas '{$conflict->nama_kelas}'.";
                })->all();
                return back()->withErrors(['siswa_ids' => $errorMessages])->withInput();
            }
        }

        $kelas->update([
            'nama_kelas' => $nama_kelas,
            'guru_id' => $request->guru_id,
        ]);

        // Detach all students for the current year and re-attach the new set.
        DB::table('kelas_siswa')->where('kelas_id', $id)->where('tahun_ajaran_id', $activeTahunAjaranId)->delete();

        $insertData = [];
        if ($request->has('siswa_ids')) {
            foreach ($request->siswa_ids as $siswaId) {
                $insertData[] = [
                    'kelas_id' => $id,
                    'siswa_id' => $siswaId,
                    'tahun_ajaran_id' => $activeTahunAjaranId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if (!empty($insertData)) {
            DB::table('kelas_siswa')->insert($insertData);
        }


        return redirect()->route('manage.kelas.index')->with('success', 'Kelas "' . $nama_kelas . '" berhasil diupdate.');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $activeTahunAjaranId = session('tahun_ajaran_id');

        // Validasi: Cek apakah kelas masih memiliki siswa atau jadwal di tahun ajaran aktif
        $hasSiswa = $kelas->siswas()->wherePivot('tahun_ajaran_id', $activeTahunAjaranId)->exists();
        $hasJadwal = $kelas->jadwals()->where('tahun_ajaran_id', $activeTahunAjaranId)->exists();

        if ($hasSiswa || $hasJadwal) {
            return redirect()->route('manage.kelas.index')->with('error', 'Kelas "' . $kelas->nama_kelas . '" tidak dapat dihapus karena masih memiliki siswa atau jadwal pelajaran.');
        }

        $kelas->delete();
        return redirect()->route('manage.kelas.index')->with('success', 'Kelas berhasil dihapus');
    }
}
