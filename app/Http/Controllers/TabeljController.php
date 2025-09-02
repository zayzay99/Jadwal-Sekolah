<?php

namespace App\Http\Controllers;

// app/Http/Controllers/PelajaranController.php

use App\Models\Tabelj;
use App\Models\Guru;
use Illuminate\Http\Request;

class TabeljController extends Controller
{
    public function index()
    {
        $tabeljs = Tabelj::with('guru')->get();
        return view('jadwal.tabel_jadwal', compact('tabeljs'));
    }

    public function create()
    {
        // $gurus = Guru::all();
        // return view('jadwal.form_pelajaran', compact('gurus'));
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'kode_pelajaran' => 'required|unique:pelajarans',
        //     'nama_pelajaran' => 'required',
        //     'guru_id' => 'nullable|exists:gurus,id'
        // ]);

        // Tabelj::create($request->all());
        // return redirect()->route('tabelj.index')->with('success', 'Pelajaran berhasil ditambahkan.');
    }

    public function edit(Tabelj $tabelj)
    {
        // $gurus = Guru::all();
        // return view('jadwal.form_Tabelj', compact('Tabelj', 'gurus'));
    }

    public function update(Request $request, Tabelj $tabelj)
    {
        // $request->validate([
        //     'kode_Tabelj' => 'required|unique:Tabeljs,kode_Tabelj,'.$tabelj->id,
        //     'nama_Tabelj' => 'required',
        //     'guru_id' => 'nullable|exists:gurus,id'
        // ]);

        // $tabelj->update($request->all());
        // return redirect()->route('Tabelj.index')->with('success', 'Tabelj berhasil diperbarui.');
    }

    public function destroy(Tabelj $tabelj)
    {
        // $tabelj->delete();
        // return redirect()->route('Tabelj.index')->with('success', 'Tabelj berhasil dihapus.');
    }
}