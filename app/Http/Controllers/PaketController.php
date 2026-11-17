<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Zone;
use App\Services\PaketDitolak;
use App\Services\PaketService;
use App\Services\ZonaService;
use App\Support\Telepon;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    public function __construct(
        protected ZonaService $zonaService
    ) {}

    public function index(Request $r)
    {
        $pakets = Package::with(['customer', 'courier'])
            ->when($r->q, fn($q, $v) => $q->where('tracking_number', 'like', '%' . $v . '%'))
            ->latest('id')->paginate(15)->withQueryString();
        $kurirs = Courier::where('is_active', true)->orderBy('code')->get();
        return view('paket.index', compact('pakets', 'kurirs'));
    }

    public function create()
    {
        return view('paket.create', ['pelanggans' => Customer::orderBy('name')->get(), 'zonas' => Zone::all()]);
    }

    public function store(Request $r, PaketService $svc)
    {
        $data = $r->validate([
            'customer_id' => 'required|exists:customers,id',
            'recipient_name' => 'required',
            'recipient_phone' => ['required', Telepon::aturan()],
            'recipient_address' => 'required',
            'destination_city' => 'required',
            'weight_kg' => 'required|numeric|gt:0|max:1000',
            'service_type' => 'required|in:Reguler,Express',
            'cod_amount' => 'required_if:is_cod,1|nullable|integer|min:1',
            'zona_manual' => 'nullable|exists:zones,id',
        ], [
            'customer_id.required' => 'Pilih pelanggan pengirim.',
            'weight_kg.gt' => 'Berat harus lebih dari 0.',
            'cod_amount.required_if' => 'Nilai COD wajib diisi bila paket COD.',
        ]);
        $data['is_cod'] = $r->boolean('is_cod');

        try {
            $paket = $svc->buat($data, $r->user());
        } catch (PaketDitolak $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
        return redirect('/paket/' . $paket->id)->with('ok', 'Paket tersimpan.');
    }

    public function show(Package $paket)
    {
        $paket->load(['customer', 'courier', 'zone', 'histories']);
        return view('paket.show', compact('paket'));
    }
    public function transit(Package $paket, PaketService $svc, Request $r)
    {
        return $this->jalankan(fn() => $svc->keTransit($paket, $r->user()), 'Paket dipindah ke Transit.');
    }

    public function tugaskan(Package $paket, PaketService $svc, Request $r)
    {
        $r->validate(['courier_id' => 'required|exists:couriers,id']);
        $kurir = Courier::find($r->courier_id);
        return $this->jalankan(fn() => $svc->tugaskan($paket, $kurir, $r->user()), 'Paket ditugaskan ke kurir.');
    }

    public function retur(Package $paket, PaketService $svc, Request $r)
    {
        return $this->jalankan(fn() => $svc->retur($paket, $r->user()), 'Paket diubah menjadi Retur.');
    }

    private function jalankan(\Closure $aksi, string $pesanOk)
    {
        try {
            $aksi();
        } catch (PaketDitolak $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('ok', $pesanOk);
    }
    public function cod()
    {
        $pakets = Package::with('courier')->where('is_cod', true)->where('status', 'Terkirim')->latest('id')->get();
        return view('cod', compact('pakets'));
    }

    public function setoran(Package $paket, PaketService $svc, Request $r)
    {
        return $this->jalankan(fn() => $svc->catatSetoran($paket, $r->user()), 'Setoran COD dicatat.');
    }
    public function cekZona(Request $request)
    {
        $request->validate([
            'kota' => 'required|string|max:100',
        ]);

        $hasil = $this->zonaService->cariZona(
            $request->query('kota')
        );

        return response()->json($hasil);
    }
    public function setoranCod(Package $paket)
    {
        DB::transaction(function () use ($paket) {

            $baris = Package::whereKey($paket->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($baris->is_cod, 422, 'Paket bukan COD.');

            abort_unless(
                $baris->status === 'Terkirim',
                422,
                'Setoran hanya untuk paket yang sudah terkirim.'
            );

            abort_if(
                $baris->cod_settled_at !== null,
                422,
                'Setoran paket ini sudah dicatat.'
            );

            $baris->update([
                'cod_settled_at' => now(),
                'cod_settled_by_user_id' => request()->user()->getAuthIdentifier(),
            ]);
        });

        return back()->with('ok', 'Setoran COD berhasil dicatat.');
    }
}
