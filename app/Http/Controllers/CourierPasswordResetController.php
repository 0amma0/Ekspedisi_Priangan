<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class CourierPasswordResetController extends Controller
{
    /**
     * Menampilkan halaman form reset password.
     */
    public function form(Request $request, string $token)
    {
        return view('kurir.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Memproses password baru dari tautan email.
     */
    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        // Pastikan akun merupakan akun kurir.
        $user = User::where('email', $request->email)
            ->where('role', 'kurir')
            ->first();

        if (!$user || !Courier::where('user_id', $user->id)->exists()) {
            return back()->withErrors([
                'email' => 'Akun kurir tidak ditemukan.',
            ])->withInput($request->only('email'));
        }

        // Validasi token dan reset password menggunakan Laravel.
        $status = Password::reset(
            [
                'email' => $request->email,
                'token' => $request->token,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'must_change_password' => false,
                ])->save();

                // Logout semua perangkat yang menggunakan token lama.
                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors([
                'email' => 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.',
            ])->withInput($request->only('email'));
        }

        return view('kurir.reset-password-selesai');
    }
}
