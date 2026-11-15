<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Support\Telepon;
use Illuminate\Http\Request;

class CekResiController extends Controller
{
    public function index(Request $r)
    {
        $resi = trim($r->query('resi', ''));
        $hasil = null;

        if ($resi !== '') {
            $paket = Package::where('tracking_number', $resi)->first();
            if ($paket) {
                $hasil = [
                    'resi' => $paket->tracking_number,
                    'status' => $paket->status,
                    'telepon' => Telepon::samarkan($paket->recipient_phone),
                    'riwayat' => $paket->histories()->orderBy('id')->get()->map(fn($h) => [
                        'waktu' => $h->created_at->format('d-m-Y H:i'),
                        'status' => $h->to_status,
                        'catatan' => $h->notes,
                    ])->all(),
                ];
            } else {
                $hasil = false;
            }
        }
        return view('cek-resi', compact('resi', 'hasil'));
    }
}
