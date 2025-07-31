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
        return view('dashboard.guru.index', compact('guru'));
    }

    public function jadwal()
    {
        // ...
    }

    
}