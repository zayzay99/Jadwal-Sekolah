<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
    $siswas = \App\Models\Siswa::with('kelas')->get();
    return view('dashboard.siswa_manage.index', compact('siswas'));
    }
}
