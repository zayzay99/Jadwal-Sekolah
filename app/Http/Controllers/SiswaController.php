<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Jadwal;

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
}
