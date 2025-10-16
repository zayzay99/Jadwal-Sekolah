<?php
namespace App\Http\Controllers;

use App\Models\Siswa;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class ManageSiswaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $activeTahunAjaranId = session('tahun_ajaran_id');

        // Base query: ONLY get students who are registered in the active school year via the pivot table.
        $baseQuery = Siswa::whereHas('kelas', function ($query) use ($activeTahunAjaranId) {
            $query->where('kelas_siswa.tahun_ajaran_id', $activeTahunAjaranId);
        });

        // Clone the base query for stats to avoid search filters affecting totals.
        $statsQuery = clone $baseQuery;

        // Apply search if exists
        if ($search) {
            $baseQuery->where(function($query) use ($search, $activeTahunAjaranId) {
                $query->where('nama', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      // Search by class name within the already filtered students for the active year
                      ->orWhereHas('kelas', function ($q) use ($search, $activeTahunAjaranId) {
                          $q->where('nama_kelas', 'like', "%{$search}%")
                            ->where('kelas_siswa.tahun_ajaran_id', $activeTahunAjaranId);
                      });
            });
        }

        // Eager load the relationship and paginate
        $siswas = $baseQuery->with(['kelas' => function ($query) use ($activeTahunAjaranId) {
                $query->where('kelas_siswa.tahun_ajaran_id', $activeTahunAjaranId);
            }])
            ->orderBy('nama', 'asc')
            ->paginate(10);

        // Calculate stats based on the students IN THIS YEAR
        $totalSiswa = $statsQuery->count();
        // Based on the query, all students shown are in a class for this year.
        $siswaSudahDikelas = $totalSiswa;
        $siswaBelumDikelas = 0;

        return view('dashboard.siswa_manage.index', compact(
            'siswas',
            'search',
            'totalSiswa',
            'siswaSudahDikelas',
            'siswaBelumDikelas'
        ));
    }

    public function create()
    {
        // FIX: Hanya ambil kelas dari tahun ajaran yang aktif
        $activeTahunAjaranId = session('tahun_ajaran_id');
        $kelas = \App\Models\Kelas::where('tahun_ajaran_id', $activeTahunAjaranId)->orderBy('nama_kelas')->get();
        return view('dashboard.siswa_manage.create', compact('kelas'));
    }

public function store(Request $request)
{
    $request->validate([
        'nama' => 'required',
        'nis' => 'required|unique:siswas',
        'kelas_id' => 'required|exists:kelas,id',
        'email' => 'required|email|unique:siswas,email',
        'password' => 'required|min:6',
        'profile_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $data = $request->only(['nama', 'nis', 'email']);
    $data['password'] = Hash::make($request->password);

    if ($request->hasFile('profile_picture')) {
        $image = $request->file('profile_picture');
        $name = 'profile-pictures/' . time().'.'.$image->getClientOriginalExtension();
        $image->storeAs('public', $name);
        $data['profile_picture'] = $name;
    }

    $siswa = Siswa::create($data);

    $activeTahunAjaranId = session('tahun_ajaran_id');
    // Simpan ke pivot
    if ($activeTahunAjaranId) {
        $siswa->kelas()->attach([$request->kelas_id => ['tahun_ajaran_id' => $activeTahunAjaranId]]);
    }

    return redirect()->route('manage.siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
}

    public function edit($id)
    {
        $activeTahunAjaranId = session('tahun_ajaran_id');
        $siswa = Siswa::with(['kelas' => fn($q) => $q->where('kelas_siswa.tahun_ajaran_id', $activeTahunAjaranId)])->findOrFail($id);
        // FIX: Hanya ambil kelas dari tahun ajaran yang aktif
        $kelas = \App\Models\Kelas::where('tahun_ajaran_id', $activeTahunAjaranId)->orderBy('nama_kelas')->get();
        return view('dashboard.siswa_manage.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $request->validate([
            'nama' => 'required',
            'nis' => 'required|unique:siswas,nis,'.$id,
            'kelas_id' => 'required|exists:kelas,id',
            'email' => 'required|email|unique:siswas,email,'.$id,
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->only(['nama', 'nis', 'email']);

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $name = 'profile-pictures/' . time().'.'.$image->getClientOriginalExtension();
            $image->storeAs('public', $name);
            $data['profile_picture'] = $name;
        }

        $siswa->update($data);

        $activeTahunAjaranId = session('tahun_ajaran_id');
        // Update relasi pivot
        // Hanya sync untuk tahun ajaran yang aktif
        if ($activeTahunAjaranId) {
            $siswa->kelas()->wherePivot('tahun_ajaran_id', $activeTahunAjaranId)->detach();
            $siswa->kelas()->attach([$request->kelas_id => ['tahun_ajaran_id' => $activeTahunAjaranId]]);
        }

        return redirect()->route('manage.siswa.index')->with('success', 'Siswa berhasil diupdate!');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->kelas()->detach();
        $siswa->delete();
        return redirect()->route('manage.siswa.index')->with('success', 'Siswa berhasil dihapus!');
    }

    public function showImportForm()
    {
        return view('dashboard.siswa_manage.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
    
        try {
            // Pindahkan pembuatan objek ke dalam blok try
            $import = new SiswaImport();
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            if ($importedCount > 0) {
                $successMessage = 'Data siswa berhasil diimpor. ' . $importedCount . ' baris ditambahkan.';
                return redirect()->route('manage.siswa.index')->with('success', $successMessage);
            } else {
                return back()->with('import_errors', ['Tidak ada baris data yang berhasil diimpor. Pastikan file tidak kosong, format benar, dan ada Tahun Ajaran yang aktif.']);
            }

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return back()->with('import_errors', $errorMessages)->withInput();
        } catch (\Exception $e) {
            // Tangkap semua jenis exception lain, termasuk dari constructor
            return back()->with('import_errors', [$e->getMessage()])->withInput();
        }
    }

    public function export()
    {
        // return Excel::download(new SiswaExport, 'daftar-siswa.xlsx');
    }
}