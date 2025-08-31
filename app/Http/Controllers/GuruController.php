<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller; // Tambahkan ini kalau belum
use App\Models\Jadwal;
use PDF;

class GuruController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:guru');
    }

    public function index()
    {
        $guru = Auth::guard('guru')->user();
        return view('dashboard.guru', compact('guru'));
    }

    public function jadwal()
    {
        $guru = Auth::guard('guru')->user();
        $jadwals = Jadwal::where('guru_id', $guru->id)->with('kelas')->get();
        return view('dashboard.guru_jadwal', compact('jadwals'));
    }

    public function cetakJadwal()
    {
        $guru = Auth::guard('guru')->user();
        $jadwals = Jadwal::where('guru_id', $guru->id)->with('kelas')->get();

        $pdf = PDF::loadView('jadwal.pdf', compact('jadwals', 'guru'));
        return $pdf->download('jadwal-guru.pdf');
    }

    public function tambah(){
        
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $guru = Auth::guard('guru')->user();

        if ($guru->profile_picture && Storage::disk('public')->exists($guru->profile_picture)) {
            Storage::disk('public')->delete($guru->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profile-pictures/gurus', 'public');
        $guru->update(['profile_picture' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
