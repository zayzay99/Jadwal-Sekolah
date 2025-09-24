<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class ManageSiswaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $siswas = Siswa::with('kelas')
            ->when($search, function ($query, $search) {
                return $query->where('nama', 'like', "%{$search}%")
                             ->orWhere('nis', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhereHas('kelas', function ($q) use ($search) {
                                 $q->where('nama_kelas', 'like', "%{$search}%");
                             });
            })
            ->orderBy('nama', 'asc')->paginate(10);

        return view('dashboard.siswa_manage.index', compact('siswas', 'search'));
    }

    public function create()
    {
        $kelas = \App\Models\Kelas::all();
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

    // Simpan ke pivot
    $siswa->kelas()->attach($request->kelas_id);

    return redirect()->route('manage.siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
}

    public function edit($id)
    {
        $siswa = Siswa::with('kelas')->findOrFail($id);
        $kelas = \App\Models\Kelas::all();
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

        // Update relasi pivot
        $siswa->kelas()->sync([$request->kelas_id]);

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
    
        $import = new \App\Imports\SiswaImport;

        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
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
        // Logika untuk mengekspor data ke Excel akan ditambahkan di sini.
        // Contoh: return Excel::download(new SiswasExport, 'siswa.xlsx');
        return redirect()->route('manage.siswa.index')->with('info', 'Fitur export sedang dalam pengembangan.');
    }
}