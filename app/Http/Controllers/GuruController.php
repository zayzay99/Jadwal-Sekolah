<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function tambah(){
        // ...
    }

    public function jadwal()
    {
        $guru = Auth::guard('guru')->user();
        $jadwals = \App\Models\Jadwal::where('guru_id', $guru->id)->get();
        return view('dashboard.guru_jadwal', compact('jadwals'));
    }
}
