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
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    public function __construct(
        protected ZonaService $zonaService
    ) {}

    public function index(Request $r)
    {
        $filter = $r->input('filter', 'semua');
        $batasWaktu = now()->subHours(72);

        // Mengambil waktu Transit yang paling baru dari history.
        $waktuTransit = DB::table('package_histories')
            ->select('created_at')
            ->whereColumn('package_id', 'packages.id')
            ->where('to_status', 'Transit')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(1);

        $query = Package::with(['customer', 'courier'])
            ->select('packages.*')
            ->selectSub($waktuTransit, 'transit_mulai')
            ->when($r->filled('q'), function ($q) use ($r) {
                $q->where(
                    'tracking_number',
                    'like',
                    '%' . $r->input('q') . '%'
                );
            });

        // Filter paket yang sudah Transit lebih dari 72 jam.
        if ($filter === 'tertahan') {
            $query->where('packages.status', 'Transit')
                ->whereRaw(
                    '(SELECT created_at FROM package_histories
                WHERE package_id = packages.id
                AND to_status = ?
                ORDER BY created_at DESC, id DESC
                LIMIT 1) < ?',
                    ['Transit', $batasWaktu]
                );
        }

        $pakets = $query->latest('packages.id')
            ->paginate(15)
            ->withQueryString();

        // Menghitung durasi Transit untuk ditampilkan.
        foreach ($pakets as $p) {
            $mulai = $p->transit_mulai
                ? \Carbon\Carbon::parse($p->transit_mulai)
                : null;

            $p->transit_tertahan = (
                $p->status === 'Transit'
                && $mulai
                && $mulai->lt($batasWaktu)
            );

            $p->transit_durasi = $mulai
                ? now()->diff($mulai)->format('%a hari %h jam')
                : '-';

            $p->transit_tanggal = $mulai
                ? $mulai->format('d-m-Y H:i')
                : '-';
        }

        $kurirs = Courier::where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('paket.index', compact('pakets', 'kurirs', 'filter'));
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
            'length_cm' => 'nullable|numeric|gt:0|max:10000|required_with:width_cm,height_cm',
            'width_cm'  => 'nullable|numeric|gt:0|max:10000|required_with:length_cm,height_cm',
            'height_cm' => 'nullable|numeric|gt:0|max:10000|required_with:length_cm,width_cm',
            'is_insured' => 'nullable|boolean',
            'declared_value' => 'required_if:is_insured,1|nullable|numeric|min:1',
        ], [
            'customer_id.required' => 'Pilih pelanggan pengirim.',
            'weight_kg.gt' => 'Berat harus lebih dari 0.',
            'cod_amount.required_if' => 'Nilai COD wajib diisi bila paket COD.',
        ]);
        $data['is_cod'] = $r->boolean('is_cod');
        $data['is_insured'] = $r->boolean('is_insured');

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
