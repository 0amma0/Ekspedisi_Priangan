<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

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

    public function gantiPassword(Request $r)
    {
        $r->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed',
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password minimal 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($r->password_lama, $user->password)) {
            return back()->withErrors([
                'password_lama' => 'Password lama tidak sesuai.'
            ]);
        }

        $user->password = Hash::make($r->password_baru);
        $user->save();

        return redirect()->route('akun.password')
            ->with('success', 'Password berhasil diperbarui!');
    }
}
