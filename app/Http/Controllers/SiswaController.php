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

        $kelas = $user->kelas()->where('kelas_siswa.tahun_ajaran_id', $selected)->first();

        $jadwals = collect();
        if ($kelas) {
            $jadwals = Jadwal::where('kelas_id', $kelas->id)
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

        $pdf = Pdf::loadView('print.jadwal-siswa', compact('jadwals', 'user', 'kelas'));
        return $pdf->stream('jadwal-' . $user->nis . '.pdf');
    }

    public function switchTahunAjaran(Request $request)
    {
        $request->validate(['tahun_ajaran_id' => 'required|exists:tahun_ajarans,id']);
        session(['siswa_tahun_ajaran_id' => $request->tahun_ajaran_id]);
        return redirect()->route('siswa.dashboard')->with('success', 'Tahun ajaran berhasil diganti.');
    }

    public function updateFoto(Request $request)
    {
        $user = Auth::guard('siswa')->user();

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto_siswa', 'public');

            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $user->foto = $path;
            $user->save();

            return back()->with('success', 'Foto profil berhasil diperbarui!');
        }

        return back()->with('error', 'Tidak ada file yang diunggah.');
    }

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
