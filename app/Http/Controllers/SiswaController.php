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

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $siswa = Auth::guard('siswa')->user();

        if ($siswa->profile_picture && Storage::disk('public')->exists($siswa->profile_picture)) {
            Storage::disk('public')->delete($siswa->profile_picture);
        }
        $path = $request->file('profile_picture')->store('profile-pictures/siswas', 'public');
        $siswa->update(['profile_picture' => $path]);
        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
