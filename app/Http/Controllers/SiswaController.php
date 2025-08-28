<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $kelas = $siswa->kelas->first();
        $jadwals = [];

        if ($kelas) {
            $jadwals = Jadwal::where('kelas_id', $kelas->id)->with('guru')->get();
        }

        return view('dashboard.siswa', compact('siswa', 'jadwals'));
    }
}
