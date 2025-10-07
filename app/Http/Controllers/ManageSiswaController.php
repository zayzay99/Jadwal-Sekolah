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

        $siswas = Siswa::with(['kelas' => function ($query) use ($activeTahunAjaranId) {
                // Hanya load kelas yang sesuai dengan tahun ajaran aktif
                $query->where('kelas_siswa.tahun_ajaran_id', $activeTahunAjaranId);
            }])
            ->when($search, function ($query, $search) {
                return $query->where('nama', 'like', "%{$search}%")
                             ->orWhere('nis', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhereHas('kelas', function ($q) use ($search) {
                                 $q->where('nama_kelas', 'like', "%{$search}%"); // Pencarian ini mungkin perlu disesuaikan jika ingin mencari di tahun ajaran aktif saja
                             });
            })
            ->orderBy('nama', 'asc')->paginate(10);

        return view('dashboard.siswa_manage.index', compact('siswas', 'search'));
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
    } else {
        $data['profile_picture'] = 'Default-Profile.png';
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
    
        $import = new SiswaImport();

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return back()->with('import_errors', $errorMessages);
        }
    
        $successMessage = 'Data siswa berhasil diimpor. ' . $import->getImportedCount() . ' baris ditambahkan.';
    
        return redirect()->route('manage.siswa.index')->with('success', $successMessage);
    }

    public function export()
    {
        return Excel::download(new SiswaExport, 'daftar-siswa.xlsx');
    }
}