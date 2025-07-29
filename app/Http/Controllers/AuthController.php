<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Login untuk admin (User)
        if (Auth::guard('web')->attempt(['email' => $credentials['username'], 'password' => $credentials['password']])) {
            return redirect()->route('admin.dashboard');
        }

        // Login untuk siswa
        if (Auth::guard('siswa')->attempt(['nama' => $credentials['username'], 'password' => $credentials['password']])) {
            return redirect()->route('siswa.dashboard');
        }

        // Login untuk guru
        if (Auth::guard('guru')->attempt(['nama' => $credentials['username'], 'password' => $credentials['password']])) {
            return redirect()->route('guru.dashboard');
        }

        return back()->withErrors(['login' => 'Username atau password salah.']);
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        Auth::guard('siswa')->logout();
        Auth::guard('guru')->logout();
        return redirect('/');
    }
}
