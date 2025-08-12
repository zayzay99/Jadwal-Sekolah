<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;

class ManageKelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with(['guru', 'siswas'])->get();
        return view('dashboard.kelas_manage.index', compact('kelas'));
    }

    public function create()
    {
        $gurus = Guru::all();
        $siswas = Siswa::all();
    
        return view('dashboard.kelas_manage.create', compact('gurus', 'siswas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'guru_id' => 'required|exists:gurus,id',
            'siswa_ids' => 'array',
            'siswa_ids.*' => 'exists:siswas,id',
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'guru_id' => $request->guru_id,
        ]);
        $kelas->siswas()->sync($request->siswa_ids ?? []);
        return redirect()->route('manage.kelas.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kelas = Kelas::with('siswas')->findOrFail($id);
        $gurus = Guru::all();
        $siswas = Siswa::all();
        $kelas->siswas()->sync($request->siswa_ids ?? []);
        return view('dashboard.kelas_manage.edit', compact('kelas', 'gurus', 'siswas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'guru_id' => 'required|exists:gurus,id',
            'siswa_ids' => 'array',
            'siswa_ids.*' => 'exists:siswas,id',
        ]);
        $kelas = Kelas::findOrFail($id);
        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'guru_id' => $request->guru_id,
        ]);
        $kelas->siswas()->sync($request->siswa_ids ?? []);
        return redirect()->route('manage.kelas.index')->with('success', 'Kelas berhasil diupdate');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        return redirect()->route('manage.kelas.index')->with('success', 'Kelas berhasil dihapus');
    }
}
