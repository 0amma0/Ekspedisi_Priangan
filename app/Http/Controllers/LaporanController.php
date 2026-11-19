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
        $dari = $r->input(
            'dari',
            now()->startOfMonth()->toDateString()
        );

        $sampai = $r->input(
            'sampai',
            now()->toDateString()
        );

        if ($dari > $sampai) {
            return back()->with(
                'error',
                'Tanggal awal tidak boleh setelah tanggal akhir.'
            );
        }

        // REKAP JUMLAH PAKET PER STATUS
        $perStatus = Package::whereBetween(
            'received_date',
            [$dari, $sampai]
        )
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        // REKAP PENDAPATAN PER ZONA
        $pendapatan = Package::whereBetween(
            'packages.received_date',
            [$dari, $sampai]
        )
            ->leftJoin('zones', 'zones.id', '=', 'packages.zone_id')
            ->select(
                'zones.name as zona',
                DB::raw('SUM(packages.shipping_cost) as ongkir'),
                DB::raw('SUM(packages.cod_fee) as biaya_cod')
            )
            ->groupBy('zones.name')
            ->get();

        // LAPORAN PERFORMA KURIR
        $performa = Package::query()
            ->whereBetween('received_date', [$dari, $sampai])
            ->whereNotNull('courier_id')
            ->selectRaw('courier_id,
                SUM(CASE WHEN status = "Terkirim" THEN 1 ELSE 0 END) AS terkirim,
                SUM(CASE WHEN status = "Gagal Kirim" THEN 1 ELSE 0 END) AS gagal')
            ->groupBy('courier_id')
            ->with('courier')
            ->get();

        $performa->transform(function ($item) {
            $terkirim = (int) $item->terkirim;
            $gagal = (int) $item->gagal;

            $total = $terkirim + $gagal;

            $item->persen_terkirim = $total
                ? round(($terkirim / $total) * 100, 2)
                : 0;

            $item->persen_gagal = $total
                ? round(($gagal / $total) * 100, 2)
                : 0;

            return $item;
        });

        // REKAP SETORAN COD PER KURIR
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
            ->groupBy(
                'couriers.id',
                'couriers.code',
                'couriers.name'
            )
            ->get();

        $totalBelum = $rekap->sum('belum');
        $totalSudah = $rekap->sum('sudah');
        $totalCod = $totalBelum + $totalSudah;

        return view('laporan', compact(
            'dari',
            'sampai',
            'perStatus',
            'pendapatan',
            'performa',
            'rekap',
            'totalBelum',
            'totalSudah',
            'totalCod'
        ));
    }
}
