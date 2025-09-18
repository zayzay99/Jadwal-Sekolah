<?php
namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasKategoriController extends Controller
{
    // Halaman kategori utama ( VII, VIII, IX, X, XI, XII)
    public function index()
    {
        $kategoriList = ['VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $kategoriData = [];

        foreach ($kategoriList as $kategori) {
            $kelasCount = Kelas::where(function($query) use ($kategori) {
                $query->where('nama_kelas', 'like', $kategori . '\-%')
                      ->orWhere('nama_kelas', 'like', $kategori . ' %');
            })->count();

            $kategoriData[] = (object)[
                'nama' => $kategori,
                'kelas_count' => $kelasCount,
            ];
        }

        return view('dashboard.kelas_kategori.index', ['kategori' => $kategoriData]);
    }

    // Daftar subkelas (X-1, X-2, dst)
    public function show(Request $request, $kategori)
    {
        $search = $request->input('search');

        $subkelasQuery = Kelas::where(function($query) use ($kategori) {
            $query->where('nama_kelas', 'like', $kategori . '\-%')
                  ->orWhere('nama_kelas', 'like', $kategori . ' %');
        })
        ->with(['guru']) // Eager load guru
        ->withCount('siswas');

        if ($search) {
            $subkelasQuery->where(function($query) use ($search) {
                $query->where('nama_kelas', 'like', '%' . $search . '%')
                      ->orWhereHas('guru', function ($q) use ($search) { // Search by teacher name
                          $q->where('nama', 'like', '%' . $search . '%');
                      })
                      ->orWhereRaw('CAST(siswas_count AS CHAR) LIKE ?', ["%{$search}%"]);
            });
        }

        $subkelas = $subkelasQuery->get();

        return view('dashboard.kelas_kategori.show', compact('kategori', 'subkelas'));
    }

    // Daftar siswa di subkelas
    public function detail(Request $request, $kategori, $kelas)
    {
        $search = $request->input('search');
        $kelasObj = Kelas::where('nama_kelas', $kelas)->firstOrFail();
        
        $siswasQuery = $kelasObj->siswasOrdered(); // Get the query builder

        if ($search) {
            $siswasQuery->where(function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('nis', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $siswas = $siswasQuery->get();

        return view('dashboard.kelas_kategori.detail', compact('kelasObj', 'siswas', 'kategori'));
    }
}
