<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class ManageSiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::with('kelas')->orderBy('nama', 'asc')->get();
        return view('dashboard.siswa_manage.index', compact('siswas'));
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
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ]);

    // Buat user baru untuk siswa
    $user = \App\Models\User::create([
        'name' => $request->nama,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'murid', // pastikan field 'role' ada di tabel users
    ]);

    // Buat data siswa dan relasikan dengan user
    $siswa = Siswa::create([
        'user_id' => $user->id, // pastikan field user_id ada di tabel siswas
        'nama' => $request->nama,
        'nis' => $request->nis,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

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
        ]);
        $siswa->update([
            'nama' => $request->nama,
            'nis' => $request->nis,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $siswa->password,
        ]);
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
}