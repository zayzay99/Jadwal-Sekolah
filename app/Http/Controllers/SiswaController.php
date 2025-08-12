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
        // $siswa->kelas()->sync($request->kelas_ids ?? []);
        return view('dashboard.siswa', compact('siswa'));
    }
}
