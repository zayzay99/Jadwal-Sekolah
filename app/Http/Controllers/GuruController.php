<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\TahunAjaran;
use App\Models\Guru;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class GuruController extends Controller
{
    /**
     * Mendapatkan ID tahun ajaran yang sedang dilihat oleh guru.
     * Prioritas: Sesi guru -> Tahun ajaran aktif global.
     */
    private function getSelectedTahunAjaranId()
    {
        // Coba dapatkan dari sesi guru terlebih dahulu
        $selectedId = session('guru_tahun_ajaran_id');

        if ($selectedId && TahunAjaran::find($selectedId)) {
            return $selectedId;
        }

        // Jika tidak ada di sesi, gunakan tahun ajaran aktif global
        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        return $activeTahunAjaran ? $activeTahunAjaran->id : null;
    }

    public function index()
    {
        $guru = Auth::guard('guru')->user();
        $allTahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'desc')->get();
        
        $selectedTahunAjaranId = $this->getSelectedTahunAjaranId();
        
        // Simpan ID terpilih ke sesi jika belum ada
        if (!session()->has('guru_tahun_ajaran_id')) {
            session(['guru_tahun_ajaran_id' => $selectedTahunAjaranId]);
        }

        $jadwals = collect();

        if ($selectedTahunAjaranId) {
            // Ambil jadwal mengajar guru berdasarkan tahun ajaran yang dipilih
            $jadwals = Jadwal::where('guru_id', $guru->id)
                ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                ->with('kelas', 'kategori')
                ->get()
                ->sortBy(function ($jadwal) {
                    $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
                    return ($hariOrder[$jadwal->hari] ?? 99) . '-' . $jadwal->jam_mulai;
                });
        }
        
        $activeGlobalTahunAjaran = TahunAjaran::where('is_active', true)->first();
        $isViewingActiveYear = $selectedTahunAjaranId == ($activeGlobalTahunAjaran?->id);

        // Untuk modal arsip, tampilkan tahun ajaran selain yang aktif
        $inactiveTahunAjarans = $allTahunAjarans->where('id', '!=', $selectedTahunAjaranId);

        return view('dashboard.guru', compact(
            'guru',
            'jadwals',
            'allTahunAjarans',
            'inactiveTahunAjarans',
            'selectedTahunAjaranId',
            'isViewingActiveYear'
        ));
    }

    public function jadwal()
    {
        // Logika ini sekarang ditangani oleh index(), jadi kita bisa redirect ke sana.
        return redirect()->route('guru.dashboard');
    }

    /**
     * Mengganti tahun ajaran di sesi guru.
     */
    public function switchTahunAjaran(Request $request)
    {
        $request->validate(['tahun_ajaran_id' => 'required|exists:tahun_ajarans,id']);
        session(['guru_tahun_ajaran_id' => $request->tahun_ajaran_id]);
        return redirect()->route('guru.dashboard')->with('success', 'Tampilan tahun ajaran berhasil diganti.');
    }

    public function cetakJadwal()
    {
        $guru = Auth::guard('guru')->user();
        $selectedTahunAjaranId = $this->getSelectedTahunAjaranId();
        $selectedTahunAjaran = TahunAjaran::find($selectedTahunAjaranId);

        $jadwals = Jadwal::where('guru_id', $guru->id)
            ->where('tahun_ajaran_id', $selectedTahunAjaranId)
            ->with('kelas', 'kategori')
            ->get()
            ->sortBy(function ($jadwal) {
                $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
                return ($hariOrder[$jadwal->hari] ?? 99) . '-' . $jadwal->jam_mulai;
            })
            ->groupBy('hari');

        // Pastikan view 'prints.jadwal-guru' ada
        $pdf = Pdf::loadView('prints.jadwal-guru', compact('jadwals', 'guru', 'selectedTahunAjaran'));
        return $pdf->stream('jadwal-mengajar-'.$guru->nip.'.pdf');
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $guru = Auth::guard('guru')->user();
        $guruModel = Guru::find($guru->id);

        if ($request->hasFile('profile_picture') && $guruModel) {
            $path = $request->file('profile_picture')->store('profile-pictures/gurus', 'public');
            $guruModel->profile_picture = $path;
            $guruModel->save();
        }

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function getArsipJadwal($tahun_ajaran_id)
    {
        $guru = Auth::guard('guru')->user();
        
        $jadwals = Jadwal::where('guru_id', $guru->id)
            ->where('tahun_ajaran_id', $tahun_ajaran_id)
            ->with('kelas')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam')
            ->get();

        return response()->json($jadwals);
    }
}