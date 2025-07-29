<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $jadwals = \App\Models\Jadwal::where('kelas', $siswa->kelas)->with('guru')->get();
        $kelas = $siswa->kelas;
        return view('dashboard.siswa', compact('siswa', 'jadwals', 'kelas'));
    }

    public function jadwal()
    {
        $siswa = Auth::guard('siswa')->user();
        $jadwals = \App\Models\Jadwal::where('kelas', $siswa->kelas)->with('guru')->get();
        $kelas = $siswa->kelas;
        return view('dashboard.siswa_jadwal', compact('jadwals', 'kelas'));
    }
}
