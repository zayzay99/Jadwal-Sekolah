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
    /**
     * Dashboard utama siswa
     */
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $jadwals = collect();
        $activeTahunAjaranId = session('tahun_ajaran_id');
        $tahunAjarans = TahunAjaran::all();

        // Ambil kelas siswa untuk tahun ajaran aktif
        $kelas = $siswa->kelas()->where('kelas.tahun_ajaran_id', $activeTahunAjaranId)->first();

        if ($kelas) {
            $jadwals = Jadwal::where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $activeTahunAjaranId)
                ->with('guru', 'kategori')
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('jam')
                ->get()
                ->groupBy('hari');
        }

        return view('dashboard.siswa', compact('siswa', 'jadwals', 'tahunAjarans'));
    }

    /**
     * Menampilkan halaman jadwal siswa
     */
    public function jadwal(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        $tahunAjarans = TahunAjaran::all();
        $selectedTahunAjaranId = $request->input('tahun_ajaran_id', session('tahun_ajaran_id'));

        $jadwals = collect();

        $kelas = $siswa->kelas()->where('kelas.tahun_ajaran_id', $selectedTahunAjaranId)->first();

        if ($kelas) {
            $jadwals = Jadwal::where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                ->with('guru', 'kategori')
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('jam')
                ->get()
                ->groupBy('hari');
        }

        return view('dashboard.siswa_jadwal', compact('siswa', 'jadwals', 'tahunAjarans', 'selectedTahunAjaranId'));
    }

    /**
     * Cetak jadwal dalam format PDF
     */
    public function cetakJadwal()
    {
        $siswa = Auth::guard('siswa')->user();
        $jadwals = collect();
        $activeTahunAjaranId = session('tahun_ajaran_id');

        $kelas = $siswa->kelas()->where('kelas.tahun_ajaran_id', $activeTahunAjaranId)->first();

        if ($kelas) {
            $jadwals = Jadwal::where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $activeTahunAjaranId)
                ->with('guru', 'kategori')
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('jam')
                ->get()
                ->groupBy('hari');
        }

        $pdf = Pdf::loadView('jadwal.pdf', compact('jadwals', 'siswa'));
        return $pdf->download('jadwal-siswa.pdf');
    }

    /**
     * Update atau upload foto profil siswa
     */
    public function updateFoto(Request $request)
    {
        $user = Auth::guard('siswa')->user();

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto_siswa', 'public');

            // Hapus foto lama kalau ada
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $user->foto = $path;
            $user->save();

            return back()->with('success', 'Foto profil berhasil diperbarui!');
        }

        return back()->with('error', 'Tidak ada file yang diunggah.');
    }

    /**
     * Hapus foto profil siswa
     */
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

    /**
     * Mengambil arsip jadwal siswa berdasarkan tahun ajaran
     */
    public function getArsipJadwal($tahun_ajaran_id)
    {
        Log::info('Fetching arsip jadwal for tahun ajaran: ' . $tahun_ajaran_id);
        $siswa = Auth::guard('siswa')->user();
        $jadwals = collect();

        $kelas = $siswa->kelas()->where('kelas.tahun_ajaran_id', $tahun_ajaran_id)->first();

        if ($kelas) {
            $jadwals = Jadwal::where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $tahun_ajaran_id)
                ->with('guru', 'kategori')
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('jam')
                ->get();
        }

        return response()->json($jadwals);
    }
}
