<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $jadwals = collect();
        $activeTahunAjaranId = session('tahun_ajaran_id');
        $tahunAjarans = \App\Models\TahunAjaran::all();

        // Ambil kelas siswa untuk tahun ajaran yang aktif
        $kelas = $siswa->kelas()->where('tahun_ajaran_id', $activeTahunAjaranId)->first();

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

    public function jadwal(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        $tahunAjarans = \App\Models\TahunAjaran::all();
        $selectedTahunAjaranId = $request->input('tahun_ajaran_id', session('tahun_ajaran_id'));

        $jadwals = collect();

        // Ambil kelas siswa untuk tahun ajaran yang dipilih
        $kelas = $siswa->kelas()->where('tahun_ajaran_id', $selectedTahunAjaranId)->first();

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

    public function cetakJadwal()
    {
        $siswa = Auth::guard('siswa')->user();
        $jadwals = collect();
        $activeTahunAjaranId = session('tahun_ajaran_id');

        // Ambil kelas siswa untuk tahun ajaran yang aktif
        $kelas = $siswa->kelas()->where('tahun_ajaran_id', $activeTahunAjaranId)->first();

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

        $all_kelas = $siswa->kelas;
        Log::info('All kelas for siswa: ' . $all_kelas);

        $kelas = $siswa->kelas->where('pivot.tahun_ajaran_id', $tahun_ajaran_id)->first();
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