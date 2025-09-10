<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Barryvdh\DomPDF\Facade\Pdf;

class GuruController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:guru');
    }

    public function index()
    {
        $guru = Auth::guard('guru')->user();
        $jadwals = Jadwal::where('guru_id', $guru->id)->with('kelas')->get();
        
        return view('dashboard.guru', compact('guru', 'jadwals'));
    }

    public function jadwal()
    {
        $guru = Auth::guard('guru')->user();
        $jadwals = Jadwal::where('guru_id', $guru->id)->with('kelas')->get();
        return view('dashboard.guru_jadwal', compact('guru', 'jadwals'));
    }

    public function cetakJadwal()
    {
        $guru = Auth::guard('guru')->user();
        // Ambil jadwal, urutkan, dan kelompokkan berdasarkan hari
        $jadwals = Jadwal::where('guru_id', $guru->id)
                         ->with('kelas')
                         ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
                         ->orderBy('jam')
                         ->get()
                         ->groupBy('hari');
 
        $pdf = Pdf::loadView('jadwal.pdf', compact('jadwals', 'guru'));
        // Gunakan stream() agar PDF terbuka di tab baru, bukan langsung download
        return $pdf->stream('jadwal-mengajar-'.$guru->nama.'.pdf');
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $authGuru = Auth::guard('guru')->user();
        $guru = \App\Models\Guru::find($authGuru->id);

        if ($guru->profile_picture && Storage::disk('public')->exists($guru->profile_picture)) {
            Storage::disk('public')->delete($guru->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profile-pictures/gurus', 'public');
        $guru->profile_picture = $path;
        $guru->save();

       return redirect()->route('guru.dashboard')->with('success', 'Foto profil berhasil diperbarui.');
    }
}