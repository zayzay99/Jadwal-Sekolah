<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nis'      => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->input('nis');
        $password   = $request->input('password');

        // === 1) ADMIN ===
        if (Auth::guard('web')->attempt(['nip' => $identifier, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard')->with('login_success', 'Berhasil masuk sebagai Admin!');
        }

        // === 2) GURU ===
        if (Auth::guard('guru')->attempt(['nip' => $identifier, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->route('guru.dashboard')->with('login_success', 'Berhasil masuk sebagai Guru!');
        }

        // === 3) SISWA ===
        if (Auth::guard('siswa')->attempt(['nis' => $identifier, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->route('siswa.dashboard')->with('login_success', 'Berhasil masuk sebagai Siswa!');
        }

        // === Jika gagal ===
        return back()->withErrors(['login' => 'NIS/NIP atau password yang kamu masukkan salah.']);

    }

    public function logout(Request $request)
    {
        foreach (['web', 'guru', 'siswa'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('logout_success', 'Berhasil logout!');
    }
}
