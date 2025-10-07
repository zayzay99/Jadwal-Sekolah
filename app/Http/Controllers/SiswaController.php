<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    /**
     * Mendapatkan ID tahun ajaran yang sedang dilihat oleh siswa.
     * Prioritas: Sesi siswa -> Tahun ajaran aktif global.
     */
    private function getSelectedTahunAjaranId()
    {
        // Coba dapatkan dari sesi siswa terlebih dahulu
        $selectedId = session('siswa_tahun_ajaran_id');

        if ($selectedId && TahunAjaran::find($selectedId)) {
            return $selectedId;
        }

        // Jika tidak ada di sesi, gunakan tahun ajaran aktif global
        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        return $activeTahunAjaran ? $activeTahunAjaran->id : null;
    }

    public function index()
    {
        $user = Auth::guard('siswa')->user();
        $allTahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'desc')->get();
        
        $selectedTahunAjaranId = $this->getSelectedTahunAjaranId();
        
        // Simpan ID terpilih ke sesi jika belum ada
        if (!session()->has('siswa_tahun_ajaran_id')) {
            session(['siswa_tahun_ajaran_id' => $selectedTahunAjaranId]);
        }

        $kelasSiswa = null;
        $jadwals = collect();

        if ($selectedTahunAjaranId) {
            // Ambil kelas siswa untuk tahun ajaran yang dipilih
            $kelasSiswa = $user->kelas()
                               ->where('kelas_siswa.tahun_ajaran_id', $selectedTahunAjaranId)
                               ->first();

            if ($kelasSiswa) {
                // Ambil jadwal berdasarkan kelas dan tahun ajaran yang dipilih
                $jadwals = Jadwal::where('kelas_id', $kelasSiswa->id)
                                ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                                ->with('guru', 'kategori')
                                ->get()
                                ->sortBy(function ($jadwal) {
                                    $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
                                    return $hariOrder[$jadwal->hari] ?? 99;
                                })
                                ->groupBy('hari');
            }
        }
        
        $activeGlobalTahunAjaran = TahunAjaran::where('is_active', true)->first();
        $isViewingActiveYear = $selectedTahunAjaranId == ($activeGlobalTahunAjaran?->id);

        // Untuk modal arsip
        $tahunAjarans = $allTahunAjarans;

        return view('dashboard.siswa', compact(
            'jadwals', 
            'kelasSiswa',
            'tahunAjarans', // Untuk modal arsip
            'allTahunAjarans', // Untuk dropdown utama
            'selectedTahunAjaranId',
            'isViewingActiveYear'
        ));
    }

    public function jadwal(Request $request)
    {
        // Logika ini sekarang ditangani oleh index(), jadi kita bisa redirect ke sana.
        return redirect()->route('siswa.dashboard');
    }

    public function cetakJadwal()
    {
        $user = Auth::guard('siswa')->user();
        $selectedTahunAjaranId = $this->getSelectedTahunAjaranId();
        $kelasSiswa = null;

        $jadwals = collect();

        // Ambil kelas siswa untuk tahun ajaran yang dipilih
        $kelasSiswa = $user->kelas()->where('kelas_siswa.tahun_ajaran_id', $selectedTahunAjaranId)->first();

        if ($kelasSiswa) {
            $jadwals = Jadwal::where('kelas_id', $kelasSiswa->id)
                                ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                                ->with('guru', 'kategori')
                                ->get()
                                ->sortBy(function ($jadwal) {
                                    $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
                                    return ($hariOrder[$jadwal->hari] ?? 99) . '-' . $jadwal->jam_mulai;
                                })
                                ->groupBy('hari');
        }

        // Menggunakan view yang benar untuk cetak PDF siswa, pastikan view 'prints.jadwal-siswa' ada.
        $pdf = Pdf::loadView('dashboard.jadwal-siswa', compact('jadwals', 'user', 'kelasSiswa'));
        return $pdf->stream('jadwal-'.$user->nis.'.pdf');
    }

    /**
     * Mengganti tahun ajaran di sesi siswa.
     */
    public function switchTahunAjaran(Request $request)
    {
        $request->validate(['tahun_ajaran_id' => 'required|exists:tahun_ajarans,id']);
        session(['siswa_tahun_ajaran_id' => $request->tahun_ajaran_id]);
        return redirect()->route('siswa.dashboard')->with('success', 'Tampilan tahun ajaran berhasil diganti.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('siswa')->user();

        // Retrieve the Eloquent model instance for the siswa
        $siswaModel = \App\Models\Siswa::find($user->id);

        if ($request->hasFile('profile_picture') && $siswaModel) {
            // simpan ke storage/app/public/profile-pictures/siswas
            $path = $request->file('profile_picture')->store('profile-pictures/siswas', 'public');

            // update ke DB
            $siswaModel->profile_picture = $path;
            $siswaModel->save();
        }

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function getArsipJadwal($tahun_ajaran_id)
    {
        Log::info('Fetching arsip jadwal for tahun ajaran: ' . $tahun_ajaran_id);
        $siswa = Auth::guard('siswa')->user();
        $jadwals = collect();

        $kelas = $siswa->kelas()->wherePivot('tahun_ajaran_id', $tahun_ajaran_id)->first();
        Log::info('Siswa: ' . $siswa->nama . ' - Kelas: ' . ($kelas ? $kelas->nama_kelas : 'Tidak ada kelas'));

        if ($kelas) {
            $jadwals = Jadwal::where('kelas_id', $kelas->id)
                                ->where('tahun_ajaran_id', $tahun_ajaran_id)
                                ->with('guru', 'kategori')
                                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                                ->orderBy('jam')
                                ->get();
            Log::info('Found ' . $jadwals->count() . ' jadwals');
        }

        return response()->json($jadwals);
    }
}