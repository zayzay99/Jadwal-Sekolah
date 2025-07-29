<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class ManageSiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::all();
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
            'email' => 'required|email|unique:siswas',
            'password' => 'required|min:6',
        ]);
        Siswa::create([
            'nama' => $request->nama,
            'nis' => $request->nis,
            'kelas_id' => $request->kelas_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return redirect()->route('manage.siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        return view('dashboard.siswa_manage.edit', compact('siswa'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $request->validate([
            'nama' => 'required',
            'nis' => 'required|unique:siswas,nis,'.$id,
            'kelas' => 'required',
            'email' => 'required|email|unique:siswas,email,'.$id,
        ]);
        $siswa->update([
            'nama' => $request->nama,
            'nis' => $request->nis,
            'kelas' => $request->kelas,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $siswa->password,
        ]);
        return redirect()->route('manage.siswa.index')->with('success', 'Siswa berhasil diupdate!');
    }

    public function destroy($id)
    {
        Siswa::destroy($id);
        return redirect()->route('manage.siswa.index')->with('success', 'Siswa berhasil dihapus!');
    }
}
