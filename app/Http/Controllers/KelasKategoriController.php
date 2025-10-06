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

        $activeTahunAjaranId = session('tahun_ajaran_id');

        foreach ($kategoriList as $kategori) {
            $query = Kelas::where(function($query) use ($kategori) {
                $query->where('nama_kelas', 'like', $kategori . '\-%')
                      ->orWhere('nama_kelas', 'like', $kategori . ' %');
            });

            $kelasCount = $query->where('tahun_ajaran_id', $activeTahunAjaranId)->count();

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
        $activeTahunAjaranId = session('tahun_ajaran_id');

        $subkelasQuery = Kelas::where(function($query) use ($kategori) {
            $query->where('nama_kelas', 'like', $kategori . '\-%')
                  ->orWhere('nama_kelas', 'like', $kategori . ' %');
        })
        ->with(['guru']) // Eager load guru
        ->withCount('siswas')
        ->when($activeTahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $activeTahunAjaranId));
        
        if ($search) {
            // Group all search conditions, including the one for the aliased 'siswas_count'
            $subkelasQuery->where(function ($query) use ($search) {
                $query->where('nama_kelas', 'like', '%' . $search . '%')
                      ->orWhereHas('guru', function ($q) use ($search) {
                          $q->where('nama', 'like', '%' . $search . '%');
                      })
                      ->orHaving('siswas_count', 'LIKE', '%' . $search . '%');
            });
        }

        $subkelas = $subkelasQuery->get();

        return view('dashboard.kelas_kategori.show', compact('kategori', 'subkelas'));
    }

    // Daftar siswa di subkelas
    public function detail(Request $request, $kategori, $kelas)
    {
        $search = $request->input('search');
        $activeTahunAjaranId = session('tahun_ajaran_id');

        // Find the class by name. The school year context is applied when fetching students.
        $kelasObj = Kelas::where('nama_kelas', $kelas)->firstOrFail();
        
        // Get the query builder for students and filter by the active school year
        $siswasQuery = $kelasObj->siswasOrdered()->wherePivot('tahun_ajaran_id', $activeTahunAjaranId);

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
