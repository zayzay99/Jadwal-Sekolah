<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    private function getSelectedTahunAjaranId()
    {
        $selectedId = session('siswa_tahun_ajaran_id');
        if ($selectedId && TahunAjaran::find($selectedId)) return $selectedId;

        $active = TahunAjaran::where('is_active', true)->first();
        return $active ? $active->id : null;
    }

    public function index()
    {
        $user = Auth::guard('siswa')->user();

        $allTahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $selectedTahunAjaranId = $this->getSelectedTahunAjaranId();

        if (!session()->has('siswa_tahun_ajaran_id')) {
            session(['siswa_tahun_ajaran_id' => $selectedTahunAjaranId]);
        }

        $kelasSiswa = null;
        $jadwals = collect();

        if ($selectedTahunAjaranId) {
            $kelasSiswa = $user->kelas()
                ->where('kelas_siswa.tahun_ajaran_id', $selectedTahunAjaranId)
                ->first();

            if ($kelasSiswa) {
                $jadwals = Jadwal::where('kelas_id', $kelasSiswa->id)
                    ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                    ->with('guru', 'kategori')
                    ->get()
                    ->sortBy(function ($jadwal) {
                        $hariOrder = [
                            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
                            'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7
                        ];
                        return $hariOrder[$jadwal->hari] ?? 99;
                    })
                    ->groupBy('hari');
            }
        }

        $activeGlobal = TahunAjaran::where('is_active', true)->first();
        $isViewingActiveYear = $selectedTahunAjaranId == ($activeGlobal?->id);

        return view('dashboard.siswa', compact(
            'user',
            'jadwals',
            'kelasSiswa',
            'allTahunAjarans',
            'selectedTahunAjaranId',
            'isViewingActiveYear'
        ));
    }

    public function cetakJadwal()
    {
        $user = Auth::guard('siswa')->user();
        $selected = $this->getSelectedTahunAjaranId();

        $kelasSiswa = $user->kelas()->with('tahunAjaran')->where('kelas_siswa.tahun_ajaran_id', $selected)->first();

        $jadwals = collect();
        if ($kelasSiswa) {
            $jadwals = Jadwal::where('kelas_id', $kelasSiswa->id)
                ->where('tahun_ajaran_id', $selected)
                ->with('guru', 'kategori')
                ->get()
                ->sortBy(function ($jadwal) {
                    $hariOrder = [
                        'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
                        'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7
                    ];
                    return $hariOrder[$jadwal->hari] ?? 99;
                })
                ->groupBy('hari');
        }

        $pdf = Pdf::loadView('dashboard.jadwal-siswa', compact('jadwals', 'user', 'kelasSiswa'));
        return $pdf->stream('jadwal-' . $user->nis . '.pdf');
    }

    public function switchTahunAjaran(Request $request)
    {
        $request->validate(['tahun_ajaran_id' => 'required|exists:tahun_ajarans,id']);
        session(['siswa_tahun_ajaran_id' => $request->tahun_ajaran_id]);
        return redirect()->route('siswa.dashboard')->with('success', 'Tahun ajaran berhasil diganti.');
    }

    // Mengupdate foto profil

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $siswa = Auth::guard('siswa')->user();
        $siswaModel = Siswa::find($siswa->id);

        if ($request->hasFile('profile_picture') && $siswaModel) {
            $path = $request->file('profile_picture')->store('profile-pictures/siswa', 'public');
            $siswaModel->profile_picture = $path;
            $siswaModel->save();
        }

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    // Menghapus foto profil
    public function deleteFoto()
    {
        $user = Auth::guard('siswa')->user();

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
            $user->foto = null;
            $user->save();

            return back()->with('foto_message', 'Foto profil berhasil dihapus.');
        }

        return back()->with('foto_message', 'Tidak ada foto untuk dihapus.');
    }
}
