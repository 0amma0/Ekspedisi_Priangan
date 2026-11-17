<?php

namespace App\Services;

use App\Models\Courier;
use App\Models\Customer;
use App\Models\Package;
use App\Models\PackageHistory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Throwable;
use App\Support\Telepon;

class CsvImportService
{
    public function importSemua(array $files, User $admin): array
    {
        $laporan = [];

        // Import pelanggan dan kurir sebelum paket.
        foreach (['pelanggan', 'kurir', 'paket'] as $jenis) {
            if (isset($files[$jenis])) {
                $laporan[$jenis] = $this->proses(
                    $jenis,
                    $files[$jenis],
                    $admin
                );
            }
        }

        return $laporan;
    }

    private function bacaCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            throw new \RuntimeException('File CSV tidak dapat dibaca.');
        }

        $header = fgetcsv($handle, 0, ',');

        if (!$header) {
            fclose($handle);
            throw new \RuntimeException('File CSV kosong.');
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        $header = array_map(fn ($item) => strtolower(trim($item)), $header);

        $hasil = [];

        while (($baris = fgetcsv($handle, 0, ',')) !== false) {
            if (count($baris) !== count($header)) {
                continue;
            }

            $hasil[] = array_combine($header, $baris);
        }

        fclose($handle);

        return $hasil;
    }

    private function proses(string $jenis, UploadedFile $file, User $admin): array
    {
        $data = $this->bacaCsv($file);

        $laporan = [
            'berhasil' => 0,
            'duplikat' => 0,
            'gagal' => 0,
            'detail_gagal' => [],
        ];

        foreach ($data as $nomor => $baris) {
            try {
                DB::transaction(function () use (
                    $jenis,
                    $baris,
                    $admin,
                    &$laporan
                ) {
                    if ($jenis === 'kurir') {
                        $this->importKurir($baris);
                    } elseif ($jenis === 'pelanggan') {
                        $this->importPelanggan($baris);
                    } else {
                        $this->importPaket($baris, $admin);
                    }

                    $laporan['berhasil']++;
                });
            } catch (\DomainException $e) {
                $laporan['duplikat']++;
                $laporan['detail_gagal'][] = 'Baris ' . ($nomor + 2) . ': ' . $e->getMessage();
            } catch (Throwable $e) {
                $laporan['gagal']++;
                $laporan['detail_gagal'][] = 'Baris ' . ($nomor + 2) . ': ' . $e->getMessage();
            }
        }

        return $laporan;
    }

    private function importKurir(array $row): void
    {
        $kode = trim($row['kode'] ?? '');
        $nama = trim($row['nama'] ?? '');
        $telepon = Telepon::rapikan(trim($row['telepon'] ?? ''));

        if (!$kode || !$nama || !Telepon::valid($telepon)) {
            throw new \RuntimeException('Kode, nama, atau nomor telepon kurir tidak valid.');
        }

        if (Courier::where('code', $kode)->exists()) {
            throw new \DomainException("Kurir {$kode} sudah ada.");
        }

        $passwordSementara = env('IMPORT_KURIR_PASSWORD');

        if (!$passwordSementara || strlen($passwordSementara) < 8) {
            throw new \RuntimeException(
                'Atur IMPORT_KURIR_PASSWORD minimal 8 karakter di file .env.'
            );
        }

        $user = User::create([
            'name' => $nama,
            'role' => 'kurir',
            'password' => $passwordSementara,
            'must_change_password' => true,
        ]);

        Courier::create([
            'user_id' => $user->id,
            'code' => $kode,
            'name' => $nama,
            'phone' => $telepon,
        ]);
    }

    private function importPelanggan(array $row): void
    {
        $nama = trim($row['nama'] ?? '');
        $telepon = Telepon::rapikan(trim($row['telepon'] ?? ''));
        $alamat = trim($row['alamat'] ?? '');
        $kota = trim($row['kota'] ?? '');

        if (!$nama || !$alamat || !Telepon::valid($telepon)) {
            throw new \RuntimeException('Nama, alamat, atau nomor telepon pelanggan tidak valid.');
        }

        $sudahAda = Customer::whereRaw('LOWER(name) = ?', [mb_strtolower($nama)])
            ->where('phone', $telepon)
            ->exists();

        if ($sudahAda) {
            throw new \DomainException("Pelanggan {$nama} sudah ada.");
        }

        // Tabel customers belum memiliki kolom kota.
        if ($kota) {
            $alamat .= ' (Kota: ' . $kota . ')';
        }

        Customer::create([
            'name' => $nama,
            'phone' => $telepon,
            'address' => $alamat,
        ]);
    }

    private function importPaket(array $row, User $admin): void
    {
        $resi = trim($row['no_resi'] ?? '');

        if (!$resi) {
            throw new \RuntimeException('Nomor resi kosong.');
        }

        if (Package::where('tracking_number', $resi)->exists()) {
            throw new \DomainException("Resi {$resi} sudah ada.");
        }

        $namaPengirim = trim($row['pengirim'] ?? '');

        $pelanggan = Customer::whereRaw(
            'LOWER(name) = ?',
            [mb_strtolower($namaPengirim)]
        )->get();

        if ($pelanggan->count() !== 1) {
            throw new \RuntimeException(
                "Pengirim {$namaPengirim} tidak ditemukan atau namanya tidak unik."
            );
        }

        $kota = trim($row['kota_tujuan'] ?? '');
        $layanan = trim($row['layanan'] ?? '');
        $status = trim($row['status'] ?? '');

        if (!in_array($layanan, ['Reguler', 'Express'], true)) {
            throw new \RuntimeException('Jenis layanan tidak valid.');
        }

        $statusValid = [
            'Diterima', 'Transit', 'Dikirim',
            'Terkirim', 'Gagal Kirim', 'Retur',
        ];

        if (!in_array($status, $statusValid, true)) {
            throw new \RuntimeException('Status paket tidak valid.');
        }

        $tanggal = Carbon::parse($row['tanggal_terima'] ?? '');

        $beratInput = trim($row['berat_kg'] ?? '');
        $berat = $beratInput === ''
            ? null
            : (float) str_replace(',', '.', $beratInput);

        if ($berat !== null && $berat <= 0) {
            throw new \RuntimeException('Berat paket harus lebih dari 0.');
        }

        $nomorPenerima = Telepon::rapikan(
            trim($row['telepon_penerima'] ?? '')
        );

        if (!Telepon::valid($nomorPenerima)) {
            throw new \RuntimeException('Nomor telepon penerima tidak valid.');
        }

        // Cari zona dan tarif dari layanan wilayah.
        $hasilZona = app(ZonaService::class)->cariZona($kota);

        if (($hasilZona['status'] ?? '') !== 'found') {
            throw new \RuntimeException(
                "Zona untuk {$kota} tidak ditemukan. Paket belum diimpor."
            );
        }

        $zonaId = $hasilZona['zone_id'];
        $tarif = (int) ($hasilZona['tariff'] ?? 0);

        if ($tarif <= 0) {
            throw new \RuntimeException("Tarif untuk kota {$kota} belum tersedia.");
        }

        $beratTagih = $berat === null
            ? null
            : max(1, (int) ceil(round($berat, 2)));

        $ongkir = 0;

        if ($beratTagih !== null) {
            $ongkir = $beratTagih * $tarif;

            if ($layanan === 'Express') {
                $ongkir = (int) round($ongkir * 1.5);
            }
        }

        $nilaiCod = (int) ($row['nilai_cod'] ?? 0);

        if ($nilaiCod < 0) {
            throw new \RuntimeException('Nilai COD tidak boleh negatif.');
        }

        $isCod = $nilaiCod > 0;
        $biayaCod = $isCod
            ? max(2000, (int) round($nilaiCod * 0.02))
            : 0;

        $kodeKurir = trim($row['kode_kurir'] ?? '');
        $kurir = $kodeKurir
            ? Courier::where('code', $kodeKurir)->first()
            : null;

        if ($kodeKurir && !$kurir) {
            throw new \RuntimeException("Kurir {$kodeKurir} tidak ditemukan.");
        }

        $package = Package::create([
            'tracking_number' => $resi,
            'received_date' => $tanggal->toDateString(),
            'customer_id' => $pelanggan->first()->id,
            'recipient_name' => trim($row['penerima'] ?? ''),
            'recipient_phone' => $nomorPenerima,
            'recipient_address' => 'Alamat penerima belum tersedia pada data historis.',
            'destination_city' => $kota,
            'zone_id' => $zonaId,
            'is_zone_manual' => false,
            'weight_kg' => $berat,
            'billable_weight' => $beratTagih,
            'needs_verification' => $berat === null,
            'service_type' => $layanan,
            'rate_per_kg' => $tarif,
            'shipping_cost' => $ongkir,
            'is_cod' => $isCod,
            'cod_amount' => $nilaiCod,
            'cod_fee' => $biayaCod,
            'status' => $status,
            'courier_id' => $kurir?->id,
            'created_by_user_id' => $admin->id,
            'cod_settled_at' => ($isCod && $status === 'Terkirim')
                ? now()
                : null,
            'cod_settled_by_user_id' => ($isCod && $status === 'Terkirim')
                ? $admin->id
                : null,
        ]);

        PackageHistory::create([
            'package_id' => $package->id,
            'from_status' => null,
            'to_status' => $status,
            'actor_user_id' => $admin->id,
            'actor_role' => 'Admin',
            'courier_id' => $kurir?->id,
            'notes' => 'Paket historis diimpor dari CSV.',
            'created_at' => $tanggal,
        ]);
    }
}
