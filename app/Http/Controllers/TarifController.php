<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\ZoneRate;
use Illuminate\Http\Request;
 
class TarifController extends Controller
{
    public function index()
    {
        return view('tarif', ['zonas' => Zone::all(), 'tarif' => ZoneRate::pluck('price_per_kg', 'zone_id')]);
    }
 
    public function update(Request $r, Zone $zone)
    {
        $r->validate(['price_per_kg' => 'required|integer|min:1']);
        ZoneRate::where('zone_id', $zone->id)->update([
            'price_per_kg' => $r->price_per_kg,
            'updated_by_user_id' => $r->user()->id,
        ]);
        return back()->with('ok', 'Tarif diperbarui. Paket lama tidak berubah.');
    }
}
