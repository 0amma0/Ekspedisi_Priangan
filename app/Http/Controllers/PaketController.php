<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Zone;
use App\Services\PaketDitolak;
use App\Services\PaketService;
use App\Support\Telepon;
use Illuminate\Http\Request;

class PaketController extends Controller
{
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
            'zona_manual' => 'nullable|in:1,2,3',
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
}
