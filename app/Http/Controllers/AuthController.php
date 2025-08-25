<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
// public function login(Request $request)
// {
//     $credentials = $request->only('username', 'password');

//     // Login untuk user (admin/guru/murid)
//     if (Auth::guard('web')->attempt(['email' => $credentials['username'], 'password' => $credentials['password']])) {
//         $user = Auth::guard('web')->user();
//         if ($user->role === 'admin') {
//             return redirect()->route('admin.dashboard');
//         } elseif ($user->role === 'guru') {
//             return redirect()->route('guru.dashboard');
//         } elseif ($user->role === 'murid') {
//             return redirect()->route('siswa.dashboard');
//         }
//         Auth::guard('web')->logout();
//         return back()->withErrors(['login' => 'Role tidak dikenali.']);
//     }

//     // Login untuk siswa (jika pakai guard khusus siswa)
//     if (Auth::guard('siswa')->attempt(['nama' => $credentials['username'], 'password' => $credentials['password']])) {
//         return redirect()->route('siswa.dashboard');
//     }

//     // Login untuk guru (jika pakai guard khusus guru)
//     if (Auth::guard('guru')->attempt(['nama' => $credentials['username'], 'password' => $credentials['password']])) {
//         return redirect()->route('guru.dashboard');
//     }

//     return back()->withErrors(['login' => 'Username atau password salah.']);
// }
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    // Login untuk user (admin/guru/murid)
    if (Auth::guard('web')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
        $user = Auth::guard('web')->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'guru') {
            return redirect()->route('guru.dashboard');
        } elseif ($user->role === 'siswa') {
            return redirect()->route('siswa.dashboard');
        }
        Auth::guard('web')->logout();
        return back()->withErrors(['login' => 'Role tidak dikenali.']);
    }

    // Login untuk siswa (pakai email)
    if (Auth::guard('siswa')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
        return redirect()->route('siswa.dashboard');
    }

    // Login untuk guru (pakai email)
    if (Auth::guard('guru')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
        return redirect()->route('guru.dashboard');
    }

    return back()->withErrors(['login' => 'Email atau password salah.']);
}
// public function login(Request $request)
//     {
//         $credentials = $request->only('username', 'password');

//         // Login untuk admin (User)
//         if (Auth::guard('web')->attempt(['email' => $credentials['username'], 'password' => $credentials['password']])) {
//             return redirect()->route('admin.dashboard');
//         }

//         // Login untuk siswa
//         if (Auth::guard('siswa')->attempt(['nama' => $credentials['username'], 'password' => $credentials['password']])) {
//             return redirect()->route('siswa.dashboard');
//         }

//         // Login untuk guru
//         if (Auth::guard('guru')->attempt(['nama' => $credentials['username'], 'password' => $credentials['password']])) {
//             return redirect()->route('guru.dashboard');
//         }

//         return back()->withErrors(['login' => 'Username atau password salah.']);
//     }

    public function logout()
    {
        Auth::guard('web')->logout();
        Auth::guard('siswa')->logout();
        Auth::guard('guru')->logout();
        return redirect('/');
    }
}
