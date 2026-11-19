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
    private const TRANSISI = [
        'Diterima'    => ['Transit'],
        'Transit'     => ['Dikirim'],
        'Dikirim'     => ['Terkirim', 'Gagal Kirim'],
        'Gagal Kirim' => ['Dikirim', 'Retur'],
        'Terkirim'    => [],
        'Retur'       => [],
    ];

    public function nomorResi(): string
    {
        $hariIni = now()->toDateString();
        DB::table('daily_sequences')->insertOrIgnore(['sequence_date' => $hariIni, 'last_number' => 0]);
        $baris = DB::table('daily_sequences')->where('sequence_date', $hariIni)->lockForUpdate()->first();
        $nomor = $baris->last_number + 1;
        DB::table('daily_sequences')->where('sequence_date', $hariIni)->update(['last_number' => $nomor]);

        return 'PKE-' . now()->format('ymd') . '-' . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }

    // dibulatkan ke atas, minimal 1 kg (round dulu supaya 2.0000001 tidak jadi 3)
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

    // 2% dari nilai COD, minimal Rp2.000 (0 bila bukan COD)
    public function hitungBiayaCod(int $nilaiCod): int
    {
        return $nilaiCod > 0 ? max(2000, (int) round($nilaiCod * 0.02)) : 0;
    }

    public function buat(array $data, User $admin): Package
    {
        $aktual = (float) $data['weight_kg'];
        $lengkap = filled($data['length_cm'] ?? null)
            && filled($data['width_cm'] ?? null)
            && filled($data['height_cm'] ?? null);

        $volumetrik = null;
        $beratDasar = $aktual;
        if ($lengkap) {
            $volumetrik = (
                (float) $data['length_cm'] *
                (float) $data['width_cm'] *
                (float) $data['height_cm']
            ) / 6000;
            $beratDasar = max($aktual, $volumetrik);
        }
        $beratTertagih = max(1, ceil(round($beratDasar, 2)));
        return DB::transaction(function () use ($data, $admin, $beratDasar, $volumetrik) {

            $manual = filled($data['zona_manual'] ?? null);

            if ($manual) {
                $zonaId = (int) $data['zona_manual'];
                $isManual = true;
            } else {
                $hasil = app(ZonaService::class)
                    ->cariZona($data['destination_city']);

                if (($hasil['status'] ?? null) !== 'found') {
                    throw new PaketDitolak(
                        'Zona belum ditemukan. Silakan pilih zona manual.'
                    );
                }

                $zonaId = (int) $hasil['zone_id'];
                $isManual = false;
            }

            // Pastikan tarif zona tersedia
            $tarif = ZoneRate::where('zone_id', $zonaId)
                ->value('price_per_kg');

            if ($tarif === null) {
                throw new PaketDitolak(
                    'Tarif untuk zona tersebut belum tersedia.'
                );
            }

            $berat = (float) $data['weight_kg'];

            $tertagih = $this->hitungBeratTertagih($beratDasar);

            $nilaiCod = $data['is_cod']
                ? (int) $data['cod_amount']
                : 0;
            // Perhitungan asuransi 0,5% dari nilai barang
            $isInsured = (bool) ($data['is_insured'] ?? false);

            $declaredValue = $isInsured
                ? (float) $data['declared_value']
                : null;

            $insuranceCost = $isInsured
                ? (int) round($declaredValue * 0.005)
                : 0;

            $paket = Package::create([
                'tracking_number' => $this->nomorResi(),
                'received_date' => now()->toDateString(),
                'customer_id' => $data['customer_id'],
                'recipient_name' => $data['recipient_name'],
                'recipient_phone' => Telepon::rapikan(
                    $data['recipient_phone']
                ),
                'recipient_address' => $data['recipient_address'],
                'destination_city' => $data['destination_city'],

                'zone_id' => $zonaId,
                'is_zone_manual' => $isManual,

                'weight_kg' => $berat,
                'length_cm' => $data['length_cm'] ?? null,
                'width_cm' => $data['width_cm'] ?? null,
                'height_cm' => $data['height_cm'] ?? null,
                'volumetric_weight_kg' => $volumetrik,
                'base_weight_kg' => $beratDasar,
                'billable_weight' => $tertagih,

                'service_type' => $data['service_type'],

                'rate_per_kg' => $tarif,
                'shipping_cost' => $this->hitungOngkir(
                    $tertagih,
                    (int) $tarif,
                    $data['service_type']
                ),

                'is_cod' => $data['is_cod'],
                'cod_amount' => $nilaiCod,
                'cod_fee' => $this->hitungBiayaCod($nilaiCod),

                // Data asuransi
                'is_insured' => $isInsured,
                'declared_value' => $declaredValue,
                'insurance_cost' => $insuranceCost,

                'status' => 'Diterima',
                'created_by_user_id' => $admin->id,
            ]);

            $this->catat(
                $paket,
                null,
                'Diterima',
                $admin->id,
                'Admin',
                null,
                'Paket diterima di loket'
            );

            return $paket;
        });
    }

    private function catat(Package $paket, ?string $dari, string $ke, ?int $userId, string $peran, ?int $kurirId, ?string $catatan): void
    {
        PackageHistory::create([
            'package_id' => $paket->id,
            'from_status' => $dari,
            'to_status' => $ke,
            'actor_user_id' => $userId,
            'actor_role' => $peran,
            'courier_id' => $kurirId,
            'notes' => $catatan,
        ]);
    }

    public function ubahStatus(Package $paket, string $baru, ?int $userId, string $peran, ?string $catatan = null, ?int $kurirId = null): void
    {
        DB::transaction(function () use ($paket, $baru, $userId, $peran, $catatan, $kurirId) {
            $p = Package::lockForUpdate()->find($paket->id);
            $lama = $p->status;

            if (in_array($lama, ['Terkirim', 'Retur'])) {
                throw new PaketDitolak("Paket berstatus $lama sudah final dan tidak dapat diubah.");
            }
            if (!in_array($baru, self::TRANSISI[$lama])) {
                throw new PaketDitolak('Status tidak boleh loncat. Urutan: Diterima, Transit, Dikirim, Terkirim.');
            }
            if ($lama === 'Gagal Kirim' && $baru === 'Dikirim' && $p->failed_attempts >= 2) {
                throw new PaketDitolak('Paket sudah gagal 2 kali. Kirim ulang ditolak, ubah status menjadi Retur.');
            }
            if ($lama === 'Gagal Kirim' && $baru === 'Retur' && $p->failed_attempts < 2) {
                throw new PaketDitolak('Retur hanya boleh setelah 2 kali gagal kirim.');
            }
            if ($baru === 'Gagal Kirim') {
                if (!$catatan) {
                    throw new PaketDitolak('Catatan wajib diisi untuk Gagal Kirim.');
                }
                $p->failed_attempts = $p->failed_attempts + 1;
            }

            $p->status = $baru;
            if ($kurirId) {
                $p->courier_id = $kurirId;
            }
            $p->save();
            $this->catat($p, $lama, $baru, $userId, $peran, $p->courier_id, $catatan);
        });
    }

    public function keTransit(Package $paket, User $admin): void
    {
        $this->ubahStatus($paket, 'Transit', $admin->id, 'Admin');
    }

    // penugasan (juga untuk kirim ulang dari Gagal Kirim)
    public function tugaskan(Package $paket, Courier $kurir, User $admin): void
    {
        DB::transaction(function () use ($paket, $kurir, $admin) {
            // kunci baris kurir supaya dua penugasan bersamaan tidak sama-sama lolos
            $k = Courier::lockForUpdate()->find($kurir->id);
            if (!$k->is_active) {
                throw new PaketDitolak('Kurir nonaktif tidak dapat ditugaskan paket.');
            }
            $dibawa = Package::where('courier_id', $k->id)->where('status', 'Dikirim')->count();
            if ($dibawa >= $k->max_packages) {
                throw new PaketDitolak('Kurir sudah membawa ' . $k->max_packages . ' paket berstatus Dikirim.');
            }
            // catatan sengaja kosong: catatan riwayat tampil ke publik
            $this->ubahStatus($paket, 'Dikirim', $admin->id, 'Admin', null, $k->id);
        });
    }

    public function retur(Package $paket, User $admin, ?string $catatan = null): void
    {
        $this->ubahStatus($paket, 'Retur', $admin->id, 'Admin', $catatan);
    }

    public function laporkanHasil(Package $paket, Courier $kurir, string $hasil, ?string $catatan): void
    {
        if (!in_array($hasil, ['Terkirim', 'Gagal Kirim'])) {
            throw new PaketDitolak('Hasil harus Terkirim atau Gagal Kirim.');
        }
        $this->ubahStatus($paket, $hasil, $kurir->user_id, 'Kurir', $catatan, $kurir->id);
    }

    public function catatSetoran(Package $paket, User $admin): void
    {
        DB::transaction(function () use ($paket, $admin) {
            $p = Package::lockForUpdate()->find($paket->id);

            // Pastikan hanya paket COD status Terkirim yang bisa disetor
            if (!$p->is_cod || $p->status !== 'Terkirim') {
                throw new PaketDitolak('Hanya paket COD dengan status Terkirim yang dapat disetor.');
            }
            $this->catat($p, $p->status, $p->status, $admin->id, 'Admin', $p->courier_id, 'Setoran COD dicatat');
        });
    }
}
