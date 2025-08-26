<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Proses login menggunakan NIS/NIP + password.
     * - Guru/admin login pakai NIP (nip)
     * - Siswa login pakai NIS (nis)
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'nis'      => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->input('nis');
        $password   = $request->input('password');

        // === 1) Login admin (guard: web, kolom: nip) ===
        if (Auth::guard('web')->attempt(['nip' => $identifier, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        // === 2) Login guru (guard: guru, kolom: nip) ===
        if (Auth::guard('guru')->attempt(['nip' => $identifier, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->route('guru.dashboard');
        }

        // === 3) Login siswa (guard: siswa, kolom: nis) ===
        if (Auth::guard('siswa')->attempt(['nis' => $identifier, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->route('siswa.dashboard');
        }

        // Jika semua gagal
        return back()
            ->withErrors(['login' => 'NIS/NIP atau password salah.'])
            ->withInput(['nis' => $identifier]);
    }

    /**
     * Logout semua guard
     */
    public function logout(Request $request)
    {
        foreach (['web', 'guru', 'siswa'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }

        // Reset session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
