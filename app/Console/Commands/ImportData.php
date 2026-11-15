<?php

namespace App\Console\Commands;

use App\Models\Courier;
use App\Models\Customer;
use App\Models\Package;
use App\Models\PackageHistory;
use App\Models\User;
use App\Models\ZoneRate;
use App\Services\PaketService;
use App\Services\PusatWilayahService;
use App\Support\Telepon;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportData extends Command
{
    protected $signature = 'import:data {--path=database/data}';
    protected $description = 'Impor kurir, pelanggan, dan paket historis dari CSV';

    public function handle(): int
    {
        $dir = base_path($this->option('path'));
        $hitung = ['kurir' => 0, 'pelanggan' => 0, 'paket' => 0, 'duplikat' => 0, 'perlu verifikasi' => 0, 'dilewati' => 0];

        DB::beginTransaction();
        try {
            $this->impKurir($dir, $hitung);
            $this->impPelanggan($dir, $hitung);
            $this->impPaket($dir, $hitung);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Gagal: ' . $e->getMessage());
            return 1;
        }
        foreach ($hitung as $nama => $jumlah) {
            $this->line(str_pad($nama, 18) . $jumlah);
        }
        return 0;
    }

    private function impKurir(string $dir, array &$hitung): void
    {
        foreach ($this->baca($dir . '/kurir.csv') as $r) {
            $kode = strtoupper($this->ambil($r, ['kode', 'kode_kurir', 'code']));
            if ($kode === '') {
                $hitung['dilewati']++;
                continue;
            }
            if (Courier::where('code', $kode)->exists()) {
                continue;
            }

            $nama = $this->nama($this->ambil($r, ['nama', 'name', 'nama_kurir']));
            $user = User::create(['name' => $nama, 'role' => 'kurir', 'password' => 'Kurir12345', 'must_change_password' => true]);
            Courier::create([
                'user_id' => $user->id,
                'code' => $kode,
                'name' => $nama,
                'phone' => Telepon::rapikan($this->ambil($r, ['telepon', 'no_telp', 'phone', 'hp'])),
            ]);
            $hitung['kurir']++;
        }
    }

    private function impPelanggan(string $dir, array &$hitung): void
    {
        foreach ($this->baca($dir . '/pelanggan.csv') as $r) {
            $telp = Telepon::rapikan($this->ambil($r, ['telepon', 'no_telp', 'phone', 'hp']));
            if ($telp === '') {
                $hitung['dilewati']++;
                continue;
            }
            $ada = Customer::where('phone', $telp)->exists();
            Customer::updateOrCreate(['phone' => $telp], [
                'name' => $this->nama($this->ambil($r, ['nama', 'name', 'nama_pelanggan'])),
                'address' => $this->ambil($r, ['alamat', 'address']),
            ]);
            if (!$ada) {
                $hitung['pelanggan']++;
            }
        }
    }

    private function impPaket(string $dir, array &$hitung): void
    {
        $svc = app(PaketService::class);
        $wilayah = app(PusatWilayahService::class);
        $admin = User::where('role', 'admin')->first();
        $zonaKota = [];

        foreach ($this->baca($dir . '/paket_historis.csv') as $r) {
            $resi = strtoupper($this->ambil($r, ['no_resi', 'resi', 'tracking_number']));
            if ($resi === '') {
                $hitung['dilewati']++;
                continue;
            }
            if (Package::where('tracking_number', $resi)->exists()) {
                $hitung['duplikat']++;
                continue;
            }

            $kota = $this->ambil($r, ['kota_tujuan', 'kota', 'destination_city']);
            $zona = (int) $this->ambil($r, ['zona', 'zone']);
            if (!in_array($zona, [1, 2, 3])) {
                if (!array_key_exists($kota, $zonaKota)) {
                    $zonaKota[$kota] = $kota !== '' ? $wilayah->cariZona($kota) : null;
                }
                $zona = $zonaKota[$kota];
            }

            // berat: kosong berarti perlu verifikasi
            $teks = str_replace(',', '.', $this->ambil($r, ['berat_kg', 'berat', 'weight_kg']));
            $berat = (is_numeric($teks) && (float) $teks > 0) ? (float) $teks : null;
            $layanan = stripos($this->ambil($r, ['layanan', 'service_type']), 'express') !== false ? 'Express' : 'Reguler';
            $tarif = $zona ? (ZoneRate::where('zone_id', $zona)->value('price_per_kg') ?? 0) : 0;
            $tertagih = $berat ? $svc->hitungBeratTertagih($berat) : null;
            $ongkir = $berat ? $svc->hitungOngkir($tertagih, $tarif, $layanan) : 0;

            $status = ucwords(strtolower($this->ambil($r, ['status', 'status_saat_ini'])));
            if (!in_array($status, ['Diterima', 'Transit', 'Dikirim', 'Terkirim', 'Gagal Kirim', 'Retur'])) {
                $status = 'Terkirim';
            }
            $cod = in_array(strtolower($this->ambil($r, ['cod', 'is_cod'])), ['1', 'ya', 'yes', 'true', 'cod']);
            $nilaiCod = $cod ? (int) preg_replace('/\D/', '', $this->ambil($r, ['nilai_cod', 'cod_amount'])) : 0;

            $pengirim = $this->nama($this->ambil($r, ['nama_pengirim', 'pengirim', 'pelanggan']));
            $pelanggan = Customer::firstOrCreate(
                ['name' => $pengirim !== '' ? $pengirim : 'Pelanggan Data Awal'],
                ['phone' => '', 'address' => '']
            );
            $kode = strtoupper($this->ambil($r, ['kurir', 'kode_kurir']));
            $kurir = $kode !== '' ? Courier::where('code', $kode)->first() : null;

            try {
                $tanggal = Carbon::parse($this->ambil($r, ['tanggal_terima', 'tanggal_masuk', 'tanggal']))->toDateString();
            } catch (\Throwable $e) {
                $tanggal = now()->toDateString();
            }

            $paket = Package::create([
                'tracking_number' => $resi,
                'received_date' => $tanggal,
                'customer_id' => $pelanggan->id,
                'recipient_name' => $this->nama($this->ambil($r, ['nama_penerima', 'penerima', 'recipient_name'])),
                'recipient_phone' => Telepon::rapikan($this->ambil($r, ['telepon_penerima', 'no_telp_penerima', 'recipient_phone'])),
                'recipient_address' => $this->ambil($r, ['alamat_penerima', 'alamat', 'recipient_address']),
                'destination_city' => $kota,
                'zone_id' => $zona,
                'is_zone_manual' => $zona === null,
                'weight_kg' => $berat,
                'billable_weight' => $tertagih,
                'needs_verification' => $berat === null,
                'service_type' => $layanan,
                'rate_per_kg' => $tarif,
                'shipping_cost' => $ongkir,
                'is_cod' => $cod,
                'cod_amount' => $nilaiCod,
                'cod_fee' => $svc->hitungBiayaCod($nilaiCod),
                'status' => $status,
                'failed_attempts' => $status === 'Retur' ? 2 : ($status === 'Gagal Kirim' ? 1 : 0),
                'courier_id' => $kurir?->id,
                'created_by_user_id' => $admin->id,
                'cod_settled_at' => ($cod && $status === 'Terkirim') ? now() : null,
            ]);
            PackageHistory::create([
                'package_id' => $paket->id,
                'to_status' => $status,
                'actor_role' => 'Admin',
                'actor_user_id' => $admin->id,
                'courier_id' => $kurir?->id,
                'notes' => 'Data awal diimpor',
            ]);
            $hitung['paket']++;
            if ($berat === null) {
                $hitung['perlu verifikasi']++;
            }
        }
    }

    private function baca(string $file): array
    {
        if (!file_exists($file)) {
            return [];
        }
        $isi = preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($file));
        $baris = preg_split('/\r\n|\n|\r/', trim($isi));
        $pemisah = substr_count($baris[0], ';') > substr_count($baris[0], ',') ? ';' : ',';
        $header = array_map(fn($h) => strtolower(trim($h)), str_getcsv(array_shift($baris), $pemisah));

        $hasil = [];
        foreach ($baris as $b) {
            if (trim($b) === '') {
                continue;
            }
            $sel = array_map('trim', str_getcsv($b, $pemisah));
            $sel = array_slice(array_pad($sel, count($header), ''), 0, count($header));
            $hasil[] = array_combine($header, $sel);
        }
        return $hasil;
    }

    private function ambil(array $baris, array $kandidat): string
    {
        foreach ($kandidat as $k) {
            if (isset($baris[$k]) && $baris[$k] !== '') {
                return $baris[$k];
            }
        }
        return '';
    }

    private function nama(string $nama): string
    {
        return mb_convert_case(mb_strtolower(trim($nama)), MB_CASE_TITLE, 'UTF-8');
    }
}
