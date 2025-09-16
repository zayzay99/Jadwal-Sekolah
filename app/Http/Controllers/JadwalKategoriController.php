<?php

namespace App\Http\Controllers;

use App\Models\JadwalKategori;
use Illuminate\Http\Request;

class JadwalKategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoris = JadwalKategori::all();
        return view('jadwal-kategori.index', compact('kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jadwal-kategori.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:jadwal_kategoris',
        ]);

        JadwalKategori::create($request->all());

        return redirect()->route('jadwal-kategori.index')
                         ->with('success', 'Kategori jadwal berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JadwalKategori $jadwal_kategori)
    {
        return view('jadwal-kategori.edit', ['kategori' => $jadwal_kategori]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JadwalKategori $jadwal_kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:jadwal_kategoris,nama_kategori,' . $jadwal_kategori->id,
        ]);

        $jadwal_kategori->update($request->all());

        return redirect()->route('jadwal-kategori.index')
                         ->with('success', 'Kategori jadwal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JadwalKategori $jadwal_kategori)
    {
        $jadwal_kategori->delete();

        return redirect()->route('jadwal-kategori.index')
                         ->with('success', 'Kategori jadwal berhasil dihapus.');
    }
}
