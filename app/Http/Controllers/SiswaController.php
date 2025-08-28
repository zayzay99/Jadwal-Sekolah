<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;

class SiswaController extends Controller
{
    /**
     * Menampilkan dashboard utama siswa, termasuk ringkasan jadwal.
     */
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $kelas = $siswa->kelas->first();
        $jadwals = [];

        if ($kelas) {
            $jadwals = Jadwal::where('kelas_id', $kelas->id)->with('guru')->get();
        }

        return view('dashboard.siswa', compact('jadwals'));
    }

    /**
     * Menampilkan halaman khusus jadwal pelajaran siswa.
     */
    public function jadwal()
    {
        $siswa = Auth::guard('siswa')->user();
        $kelas = $siswa->kelas->first();
        $jadwals = [];

        if ($kelas) {
            $jadwals = Jadwal::where('kelas_id', $kelas->id)->with('guru')->get();
        }

        return view('dashboard.siswa_jadwal', compact('jadwals', 'kelas'));
    }
}
