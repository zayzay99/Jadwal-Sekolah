<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use Illuminate\Support\Facades\Hash;

class ManageGuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::all();
        return view('dashboard.guru_manage.index', compact('gurus'));
    }

    public function create()
    {
        $kelas = \App\Models\Kelas::all();
        return view('dashboard.guru_manage.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:gurus',
            'pengampu' => 'required',
            'email' => 'required|email|unique:gurus',
            'password' => 'required|min:6',
        ]);
        Guru::create([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'pengampu' => $request->pengampu,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return redirect()->route('manage.guru.index')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        $kelas = \App\Models\Kelas::all();
        return view('dashboard.guru_manage.edit', compact('guru', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:gurus,nip,'.$id,
            'pengampu' => 'required',
            'email' => 'required|email|unique:gurus,email,'.$id,
            
        ]);
        $guru->update([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'pengampu' => $request->pengampu,
            'email' => $request->email,
            
            'password' => $request->password ? Hash::make($request->password) : $guru->password,
        ]);
        return redirect()->route('manage.guru.index')->with('success', 'Guru berhasil diupdate!');
    }

    public function destroy($id)
    {
        Guru::destroy($id);
        return redirect()->route('manage.guru.index')->with('success', 'Guru berhasil dihapus!');
    }
}
