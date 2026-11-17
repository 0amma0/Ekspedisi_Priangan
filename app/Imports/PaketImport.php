<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Courier;
use App\Models\Package;
use App\Models\PackageHistory;
use App\Models\ZoneRate;
use App\Services\ZonaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Support\Telepon;

class PaketImport implements OnEachRow, WithHeadingRow, WithChunkReading
{
    private array $zonaCache = [];

    public array $gagal = [];

    public function onRow(Row $row): void
    {
        $data = $row->toArray();

        $resi = trim($data['no_resi'] ?? '');

        if ($resi === '') {
            $this->gagal[] = 'Nomor resi kosong.';
            return;
        }

        if (Package::where('tracking_number', $resi)->exists()) {
            $this->gagal[] = "Resi {$resi} sudah terdaftar.";
            return;
        }

        $pengirim = trim($data['pengirim'] ?? '');
        $namaPenerima = trim($data['penerima'] ?? '');
        $teleponPenerima = Telepon::rapikan(
            trim((string) ($data['telepon_penerima'] ?? ''))
        );
        $kota = trim($data['kota_tujuan'] ?? '');
        $layanan = trim($data['layanan'] ?? '');
        $status = trim($data['status'] ?? '');
        $kodeKurir = trim($data['kode_kurir'] ?? '');

        $customer = Customer::whereRaw(
            'LOWER(name) = ?',
            [mb_strtolower($pengirim)]
        )->first();

        if (!$customer) {
            $this->gagal[] = "Pengirim {$pengirim} tidak ditemukan. Resi: {$resi}";
            return;
        }

        $courier = null;

        if ($kodeKurir !== '') {
            $courier = Courier::where('code', $kodeKurir)->first();

            if (!$courier) {
                $this->gagal[] = "Kurir {$kodeKurir} tidak ditemukan. Resi: {$resi}";
                return;
            }
        }

        if (!in_array($layanan, ['Reguler', 'Express'], true)) {
            $this->gagal[] = "Layanan tidak valid. Resi: {$resi}";
            return;
        }

        $statusValid = [
            'Diterima',
            'Transit',
            'Dikirim',
            'Terkirim',
            'Gagal Kirim',
            'Retur',
        ];

        if (!in_array($status, $statusValid, true)) {
            $this->gagal[] = "Status tidak valid. Resi: {$resi}";
            return;
        }

        if (
            $namaPenerima === '' ||
            $kota === '' ||
            !Telepon::valid($teleponPenerima)
        ) {
            $this->gagal[] = "Data penerima, telepon, atau kota tidak valid. Resi: {$resi}";
            return;
        }

        $kotaKey = mb_strtolower($kota);

        if (!array_key_exists($kotaKey, $this->zonaCache)) {
            $this->zonaCache[$kotaKey] =
                app(ZonaService::class)->cariZona($kota);
        }

        $zona = $this->zonaCache[$kotaKey];

        if (($zona['status'] ?? '') !== 'found') {
            $this->gagal[] = "Zona kota {$kota} tidak ditemukan. Resi: {$resi}";
            return;
        }

        $zoneId = $zona['zone_id'];

        $tarif = ZoneRate::where('zone_id', $zoneId)
            ->value('price_per_kg');

        if (!$tarif) {
            $this->gagal[] = "Tarif kota {$kota} belum tersedia. Resi: {$resi}";
            return;
        }

        $beratKosong = !isset($data['berat_kg'])
            || trim((string) $data['berat_kg']) === '';

        $berat = $beratKosong
            ? null
            : (float) str_replace(',', '.', $data['berat_kg']);

        if ($berat !== null && $berat <= 0) {
            $this->gagal[] = "Berat tidak valid. Resi: {$resi}";
            return;
        }

        $beratTagihan = $berat === null
            ? null
            : max(1, (int) ceil(round($berat, 2)));

        $ongkir = 0;

        if ($beratTagihan !== null) {
            $ongkir = $beratTagihan * (int) $tarif;

            if ($layanan === 'Express') {
                $ongkir = (int) round($ongkir * 1.5);
            }
        }

        $nilaiCod = (int) ($data['nilai_cod'] ?? 0);
        $nilaiCod = max(0, $nilaiCod);

        $biayaCod = $nilaiCod > 0
            ? max(2000, (int) round($nilaiCod * 0.02))
            : 0;

        try {
            $tanggal = Carbon::parse($data['tanggal_terima'])->toDateString();
        } catch (\Throwable $e) {
            $this->gagal[] = "Tanggal tidak valid. Resi: {$resi}";
            return;
        }

        $adminId = request()->user()?->getAuthIdentifier();

        if (!$adminId) {
            $this->gagal[] = "Admin belum login. Resi: {$resi}";
            return;
        }

        DB::transaction(function () use (
            $resi,
            $tanggal,
            $customer,
            $namaPenerima,
            $teleponPenerima,
            $kota,
            $zoneId,
            $berat,
            $beratTagihan,
            $beratKosong,
            $layanan,
            $status,
            $tarif,
            $ongkir,
            $nilaiCod,
            $biayaCod,
            $courier,
            $adminId
        ) {
            $paket = Package::create([
                'tracking_number' => $resi,
                'received_date' => $tanggal,
                'customer_id' => $customer->id,
                'recipient_name' => $namaPenerima,
                'recipient_phone' => $teleponPenerima,
                'recipient_address' => 'Tidak tersedia dari data CSV',
                'destination_city' => $kota,
                'zone_id' => $zoneId,
                'is_zone_manual' => false,
                'weight_kg' => $berat,
                'billable_weight' => $beratTagihan,
                'needs_verification' => $beratKosong,
                'service_type' => $layanan,
                'rate_per_kg' => $tarif,
                'shipping_cost' => $ongkir,
                'is_cod' => $nilaiCod > 0,
                'cod_amount' => $nilaiCod,
                'cod_fee' => $biayaCod,
                'status' => $status,
                'courier_id' => $courier?->id,
                'created_by_user_id' => $adminId,
                'cod_settled_at' => (
                    $status === 'Terkirim' && $nilaiCod > 0
                ) ? now() : null,

                'cod_settled_by_user_id' => (
                    $status === 'Terkirim' && $nilaiCod > 0
                ) ? $adminId : null,
            ]);

            PackageHistory::create([
                'package_id' => $paket->id,
                'from_status' => null,
                'to_status' => $status,
                'actor_user_id' => $adminId,
                'actor_role' => 'Admin',
                'courier_id' => $courier?->id,
                'notes' => 'Import data historis CSV',
            ]);
        });
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
