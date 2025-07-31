<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $kelas = $siswa->kelas;
        return view('dashboard.siswa.index', compact('siswa', 'kelas'));
    }

   
    }

