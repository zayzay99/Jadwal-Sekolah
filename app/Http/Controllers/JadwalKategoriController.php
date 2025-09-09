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
            'nama_kategori' => 'required|string|max:255',
        ]);

        JadwalKategori::create($request->all());

        return redirect()->route('jadwal-kategori.index')
                         ->with('success', 'Kategori jadwal berhasil ditambahkan.');
    }
}