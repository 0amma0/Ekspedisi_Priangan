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

        $perStatus = Package::whereBetween('received_date', [$dari, $sampai])
            ->select('status', DB::raw('count(*) as jumlah'))->groupBy('status')->pluck('jumlah', 'status');

        $pendapatan = Package::whereBetween('packages.received_date', [$dari, $sampai])
            ->leftJoin('zones', 'zones.id', '=', 'packages.zone_id')
            ->select('zones.name as zona', DB::raw('sum(packages.shipping_cost) as ongkir'), DB::raw('sum(packages.cod_fee) as
biaya_cod'))
            ->groupBy('zones.name')->get();

        $rekap = Courier::leftJoin('packages', function ($j) use ($dari, $sampai) {
            $j->on('packages.courier_id', '=', 'couriers.id')
                ->where('packages.is_cod', true)
                ->where('packages.status', 'Terkirim')
                ->whereBetween('packages.received_date', [$dari, $sampai]);
        })
            ->select(
                'couriers.code',
                'couriers.name',
                DB::raw('COALESCE(SUM(CASE WHEN packages.cod_settled_at IS NULL THEN packages.cod_amount ELSE 0 END), 0) as belum'),
                DB::raw('COALESCE(SUM(CASE WHEN packages.cod_settled_at IS NOT NULL THEN packages.cod_amount ELSE 0 END), 0) as sudah')
            )
            ->groupBy('couriers.id', 'couriers.code', 'couriers.name')
            ->get();

        $totalBelum = $rekap->sum('belum');
        $totalSudah = $rekap->sum('sudah');
        $totalCod = $totalBelum + $totalSudah;

        return view('laporan', compact(
            'dari',
            'sampai',
            'perStatus',
            'pendapatan',
            'rekap',
            'totalBelum',
            'totalSudah',
            'totalCod'
        ));
    }
}
