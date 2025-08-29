<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $jadwals = [];

        $kelas = $siswa->kelas->first();

        if ($kelas) {
            $jadwals = Jadwal::where('kelas_id', $kelas->id)->with('guru')->get();
        }

        return view('dashboard.siswa', compact('siswa', 'jadwals'));
    }

    public function jadwal()
    {
        $siswa = Auth::guard('siswa')->user();
        $jadwals = [];

        $kelas = $siswa->kelas->first();

        if ($kelas) {
            $jadwals = Jadwal::where('kelas_id', $kelas->id)->with('guru')->get();
        }
        // Anda perlu membuat view 'dashboard.siswa_jadwal' jika belum ada
        return view('dashboard.siswa', compact('siswa', 'jadwals'));
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
}
