<?php
namespace App\Services;
 
use App\Models\Courier;
use App\Models\Package;
use App\Models\PackageHistory;
use App\Models\User;
use App\Models\ZoneRate;
use App\Support\Telepon;
use Illuminate\Support\Facades\DB;
 
class PaketService
{
    
    public function nomorResi(): string
    {
        $hariIni = now()->toDateString();
        DB::table('daily_sequences')->insertOrIgnore(['sequence_date' => $hariIni, 'last_number' => 0]);
        $baris = DB::table('daily_sequences')->where('sequence_date', $hariIni)->lockForUpdate()->first();
        $nomor = $baris->last_number + 1;
        DB::table('daily_sequences')->where('sequence_date', $hariIni)->update(['last_number' => $nomor]);
 
        return 'PKE-' . now()->format('ymd') . '-' . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }
 
    public function hitungBeratTertagih(float $berat): int
    {
        return max(1, (int) ceil(round($berat, 2)));
    }
 
    public function hitungOngkir(int $beratTertagih, int $tarif, string $layanan): int
    {
        $ongkir = $beratTertagih * $tarif;
        if ($layanan === 'Express') {
            $ongkir = $ongkir * 1.5;                 
        }
        return (int) round($ongkir);
    }
 
    public function hitungBiayaCod(int $nilaiCod): int
    {
        return $nilaiCod > 0 ? max(2000, (int) round($nilaiCod * 0.02)) : 0;
    }
 
    public function buat(array $data, User $admin): Package
    {
        return DB::transaction(function () use ($data, $admin) {
            $manual = !empty($data['zona_manual']);
            $zonaId = $manual
                ? (int) $data['zona_manual']
                : app(PusatWilayahService::class)->cariZona($data['destination_city']);
            if (!$zonaId) {
                throw new PaketDitolak('Zona tidak dapat ditentukan. Pilih zona secara manual.');
            }
 
            $tarif = ZoneRate::where('zone_id', $zonaId)->value('price_per_kg');
            $berat = (float) $data['weight_kg'];
            $tertagih = $this->hitungBeratTertagih($berat);
            $nilaiCod = $data['is_cod'] ? (int) $data['cod_amount'] : 0;
 
            $paket = Package::create([
                'tracking_number' => $this->nomorResi(),
                'received_date' => now()->toDateString(),
                'customer_id' => $data['customer_id'],
                'recipient_name' => $data['recipient_name'],
                'recipient_phone' => Telepon::rapikan($data['recipient_phone']),
                'recipient_address' => $data['recipient_address'],
                'destination_city' => $data['destination_city'],
                'zone_id' => $zonaId,
                'is_zone_manual' => $manual,
                'weight_kg' => $berat,
                'billable_weight' => $tertagih,
                'service_type' => $data['service_type'],
                // snapshot: tarif dan ongkir tersimpan di paket
                'rate_per_kg' => $tarif,
                'shipping_cost' => $this->hitungOngkir($tertagih, $tarif, $data['service_type']),
                'is_cod' => $data['is_cod'],
                'cod_amount' => $nilaiCod,
                'cod_fee' => $this->hitungBiayaCod($nilaiCod),
                'status' => 'Diterima',
                'created_by_user_id' => $admin->id,
            ]);
 
            $this->catat($paket, null, 'Diterima', $admin->id, 'Admin', null, 'Paket diterima di loket');
            return $paket;
        });
    }
 
    private function catat(Package $paket, ?string $dari, string $ke, ?int $userId, string $peran, ?int $kurirId, ?string $catatan): void
    {
        PackageHistory::create([
            'package_id' => $paket->id, 'from_status' => $dari, 'to_status' => $ke,
            'actor_user_id' => $userId, 'actor_role' => $peran,
            'courier_id' => $kurirId, 'notes' => $catatan,
        ]);
    }
 
}