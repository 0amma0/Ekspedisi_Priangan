<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Package;
use App\Services\PaketDitolak;
use App\Services\PaketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class KurirApiController extends Controller
{
    public function login(Request $r)
    {
        $r->validate(['akun' => 'required', 'password' => 'required']);

        // akun = kode kurir (KUR-01), huruf besar atau kecil sama saja
        $kurir = Courier::whereRaw('LOWER(code) = ?', [strtolower($r->akun)])->first();
        if (!$kurir || !Hash::check($r->password, $kurir->user->password)) {
            return response()->json(['message' => 'Akun atau password salah.'], 401);
        }
        return [
            'token' => $kurir->user->createToken('apk')->plainTextToken,
            'kode' => $kurir->code,
            'nama' => $kurir->name,
            'must_change_password' => (bool) $kurir->user->must_change_password,
        ];
    }

    public function logout(Request $r)
    {
        $r->user()->currentAccessToken()->delete();
        return ['message' => 'Berhasil keluar.'];
    }

    public function password(Request $r)
    {
        $r->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed',
        ], [
            'password_baru.min' => 'Password minimal 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak sama.',
        ]);
        $user = $r->user();
        if (!Hash::check($r->password_lama, $user->password)) {
            return response()->json(['message' => 'Password lama salah.'], 422);
        }
        $user->update(['password' => $r->password_baru, 'must_change_password' => false]);
        return ['message' => 'Password berhasil diganti.'];
    }

    public function paket(Request $r)
    {
        $kurir = $r->user()->courier;   // id kurir dari TOKEN, bukan dari parameter
        return Package::where('courier_id', $kurir->id)->where('status', 'Dikirim')
            ->get()->map(fn($p) => $this->ringkas($p));
    }

    public function detail(Package $paket, Request $r)
    {
        if ($paket->courier_id != $r->user()->courier->id) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke fitur ini.'], 403);
        }
        return $this->ringkas($paket);
    }

    public function hasil(Package $paket, Request $r, PaketService $svc)
    {
        $kurir = $r->user()->courier;
        if ($paket->courier_id != $kurir->id) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke fitur ini.'], 403);
        }
        $r->validate([
            'hasil' => 'required|in:Terkirim,Gagal Kirim',
            'catatan' => 'required_if:hasil,Gagal Kirim|nullable|string',
        ], [
            'hasil.in' => 'Hasil harus Terkirim atau Gagal Kirim.',
            'catatan.required_if' => 'Catatan wajib diisi untuk Gagal Kirim.',
        ]);
        try {
            $svc->laporkanHasil($paket, $kurir, $r->hasil, $r->catatan);
        } catch (PaketDitolak $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
        return ['message' => 'Status paket menjadi ' . $r->hasil . '.'];
    }

    public function cod(Request $r)
    {
        $kurir = $r->user()->courier;
        $daftar = Package::where('courier_id', $kurir->id)->where('is_cod', true)->where('status', 'Terkirim')->get();
        return [
            'total_belum_disetor' => (int) $daftar->whereNull('cod_settled_at')->sum('cod_amount'),
            'paket' => $daftar->map(fn($p) => [
                'id' => $p->id,
                'resi' => $p->tracking_number,
                'nilai_cod' => $p->cod_amount,
                'disetor' => $p->cod_settled_at !== null,
            ]),
        ];
    }

    private function ringkas(Package $p): array
    {
        return [
            'id' => $p->id,
            'resi' => $p->tracking_number,
            'penerima' => $p->recipient_name,
            'telepon' => $p->recipient_phone,
            'alamat' => $p->recipient_address,
            'kota' => $p->destination_city,
            'layanan' => $p->service_type,
            'cod' => $p->is_cod,
            'nilai_cod' => $p->cod_amount,
            'status' => $p->status,
        ];
    }
    public function akun(Request $r)
    {
        return [
            'email' => $r->user()->email,
        ];
    }

    public function email(Request $r)
    {
        $r->validate([
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($r->user()->id),
            ],
        ], [
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan akun lain.',
        ]);

        $r->user()->update([
            'email' => $r->filled('email') ? $r->email : null,
        ]);

        return [
            'message' => 'Email berhasil diperbarui.',
            'email' => $r->user()->fresh()->email,
        ];
    }

    public function forgotPassword(Request $r)
    {
        $r->validate([
            'akun' => 'required|string',
            'email' => 'required|email',
        ]);

        // Cari akun berdasarkan kode kurir.
        $kurir = Courier::whereRaw(
            'LOWER(code) = ?',
            [strtolower(trim($r->akun))]
        )->first();

        // Email harus cocok dengan email akun kurir.
        if (
            !$kurir ||
            !$kurir->user ||
            !$kurir->user->email ||
            strtolower($kurir->user->email) !== strtolower(trim($r->email))
        ) {
            // Pesan dibuat umum agar tidak membocorkan akun yang terdaftar.
            return response()->json([
                'message' => 'Jika kode akun dan email terdaftar cocok, tautan reset akan dikirim ke email tersebut.'
            ]);
        }

        // Kirim email reset menggunakan password broker Laravel.
        $status = Password::sendResetLink([
            'email' => $kurir->user->email,
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Email reset belum dapat dikirim. Silakan coba lagi nanti.'
            ], 422);
        }

        return response()->json([
            'message' => 'Jika kode akun dan email terdaftar cocok, tautan reset akan dikirim ke email tersebut.'
        ]);
    }


    public function resetPassword(Request $r)
    {
        $r->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        // Pastikan email tersebut milik akun kurir.
        $user = User::where('email', $r->email)
            ->where('role', 'kurir')
            ->first();

        if (!$user || !Courier::where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'Tautan reset tidak valid atau sudah kedaluwarsa.'
            ], 422);
        }

        // Validasi token dan ubah password melalui Laravel Password Broker.
        $status = Password::reset(
            [
                'email' => $r->email,
                'token' => $r->token,
                'password' => $r->password,
                'password_confirmation' => $r->password_confirmation,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                    'must_change_password' => false,
                ])->save();

                // Putuskan sesi/token APK yang lama.
                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Tautan reset tidak valid atau sudah kedaluwarsa.'
            ], 422);
        }

        return response()->json([
            'message' => 'Password berhasil direset. Silakan login kembali.'
        ]);
    }
}
