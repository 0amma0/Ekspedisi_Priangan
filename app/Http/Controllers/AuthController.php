<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function form()
    {
        return view('login');
    }

    public function login(Request $r)
    {
        $r->validate(['email' => 'required', 'password' => 'required']);

        if (!Auth::attempt($r->only('email', 'password'))) {
            return back()->withInput()->with('error', 'Akun atau password salah.');
        }
        // kurir hanya boleh memakai APK
        if (Auth::user()->role === 'kurir') {
            Auth::logout();
            return back()->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }
        $r->session()->regenerate();
        return redirect(Auth::user()->role === 'admin' ? '/paket' : '/laporan');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/login');
    }
}
