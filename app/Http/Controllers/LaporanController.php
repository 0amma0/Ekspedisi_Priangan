<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $r)
    {
        $dari = $r->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $r->input('sampai', now()->toDateString());
        if ($dari > $sampai) {
            return back()->with('error', 'Tanggal awal tidak boleh setelah tanggal akhir.');
        }

        // 1. jumlah paket per status (menurut tanggal masuk)
        $perStatus = Package::whereBetween('received_date', [$dari, $sampai])
            ->select('status', DB::raw('count(*) as jumlah'))->groupBy('status')->pluck('jumlah', 'status');

        // 2. pendapatan per zona = ongkir + biaya COD, semua status
        $pendapatan = Package::whereBetween('packages.received_date', [$dari, $sampai])
            ->leftJoin('zones', 'zones.id', '=', 'packages.zone_id')
            ->select('zones.name as zona', DB::raw('sum(packages.shipping_cost) as ongkir'), DB::raw('sum(packages.cod_fee) as
biaya_cod'))
            ->groupBy('zones.name')->get();

        // 3. rekap COD per kurir: SEMUA paket Terkirim sampai hari ini (tanpa periode)
        $rekap = Courier::leftJoin('packages', function ($j) {
            $j->on('packages.courier_id', '=', 'couriers.id')
                ->where('packages.is_cod', true)->where('packages.status', 'Terkirim');
        })
            ->select(
                'couriers.code',
                'couriers.name',
                DB::raw('coalesce(sum(case when packages.cod_settled_at is null then packages.cod_amount else 0 end), 0) as belum'),
                DB::raw('coalesce(sum(case when packages.cod_settled_at is not null then packages.cod_amount else 0 end), 0) as sudah')
            )
            ->groupBy('couriers.id', 'couriers.code', 'couriers.name')->get();

        return view('laporan', compact('dari', 'sampai', 'perStatus', 'pendapatan', 'rekap'));
    }
}
