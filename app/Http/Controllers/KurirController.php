<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Package;
use App\Models\User;
use App\Support\Telepon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KurirController extends Controller
{
    public function index()
    {
        return view('kurir.index', ['kurirs' => Courier::orderBy('code')->get()]);
    }

    public function create()
    {
        return view('kurir.form');
    }

    public function store(Request $r)
    {
        $v = $r->validate([
            'name' => 'required',
            'phone' => ['required', Telepon::aturan()],
            'password' => 'required|min:8',
        ], ['password.min' => 'Password minimal 8 karakter.']);

        DB::transaction(function () use ($v) {
            // kode berikutnya: angka terbesar + 1 (KUR-01, KUR-02, ...)
            $maks = Courier::pluck('code')->map(fn($k) => (int) substr($k, 4))->max() ?? 0;
            $user = User::create([
                'name' => $v['name'],
                'role' => 'kurir',
                'password' => $v['password'],
                'must_change_password' => true,
            ]);
            Courier::create([
                'user_id' => $user->id,
                'code' => 'KUR-' . str_pad($maks + 1, 2, '0', STR_PAD_LEFT),
                'name' => $v['name'],
                'phone' => Telepon::rapikan($v['phone']),
            ]);
        });
        return redirect('/kurir')->with('ok', 'Kurir dan akun tersimpan.');
    }

    public function nonaktif(Courier $kurir)
    {
        // kurir yang masih membawa paket Dikirim tidak boleh dinonaktifkan
        if (Package::where('courier_id', $kurir->id)->where('status', 'Dikirim')->exists()) {
            return back()->with('error', 'Kurir masih membawa paket berstatus Dikirim.');
        }
        $kurir->update(['is_active' => false]);
        return back()->with('ok', 'Kurir dinonaktifkan.');
    }

    public function aktif(Courier $kurir)
    {
        $kurir->update(['is_active' => true]);
        return back()->with('ok', 'Kurir diaktifkan.');
    }

    public function reset(Request $r, Courier $kurir)
    {
        $r->validate(['password' => 'required|min:8'], ['password.min' => 'Password minimal 8 karakter.']);
        $kurir->user->update(['password' => $r->password, 'must_change_password' => true]);
        return back()->with('ok', 'Password direset. Kurir wajib menggantinya saat login.');
    }
    public function edit(int $id)
    {
        $kurir = Courier::findOrFail($id);
        return view('kurir.form', compact('kurir'));
    }
}
