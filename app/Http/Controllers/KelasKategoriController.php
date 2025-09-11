<?php
namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Support\Facades\DB;

class KelasKategoriController extends Controller
{
    // Halaman kategori utama ( VII, VIII, IX, X, XI, XII)
    public function index()
    {
        $kategoriList = ['VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $kategoriData = [];

        foreach ($kategoriList as $kategori) {
            $kelasCount = Kelas::where('nama_kelas', 'like', $kategori . ' - %')->count();

            $kategoriData[] = (object)[
                'nama' => $kategori,
                'kelas_count' => $kelasCount,
            ];
        }

        return view('dashboard.kelas_kategori.index', ['kategori' => $kategoriData]);
    }

    // Daftar subkelas (X-1, X-2, dst)
    public function show($kategori)
    {
        $subkelas = Kelas::where('nama_kelas', 'like', $kategori . ' - %')
                         ->withCount('siswas')
                         ->get();
        return view('dashboard.kelas_kategori.show', compact('kategori', 'subkelas'));
    }

    // Daftar siswa di subkelas
    public function detail($kategori, $kelas)
    {
        $kelasObj = Kelas::where('nama_kelas', $kelas)->firstOrFail();
        // Ambil siswa dengan scope ordered
    $siswas = $kelasObj->siswasOrdered()->get(); // pastikan relasi di model Kelas: siswas()
        return view('dashboard.kelas_kategori.detail', compact('kelasObj', 'siswas', 'kategori'));
    }
}