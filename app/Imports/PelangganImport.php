<?php

namespace App\Imports;

use App\Models\Customer;
use App\Support\Telepon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PelangganImport implements OnEachRow, WithHeadingRow, WithChunkReading
{
    public function onRow(Row $row): void
    {
        $data = $row->toArray();

        $nama = trim($data['nama'] ?? '');
        $telepon = Telepon::rapikan(trim($data['telepon'] ?? ''));
        $alamat = trim($data['alamat'] ?? '');
        $kota = trim($data['kota'] ?? '');

        // Validasi data wajib
        if (
            $nama === '' ||
            $alamat === '' ||
            !Telepon::valid($telepon)
        ) {
            return;
        }

        // Cek pelanggan duplikat berdasarkan nama dan nomor telepon
        $duplikat = Customer::whereRaw(
            'LOWER(name) = ?',
            [mb_strtolower($nama)]
        )->where('phone', $telepon)->exists();

        if ($duplikat) {
            return;
        }

        // Database customers belum memiliki kolom kota
        if ($kota !== '') {
            $alamat .= ' (Kota: ' . $kota . ')';
        }

        DB::transaction(function () use ($nama, $telepon, $alamat) {
            Customer::create([
                'name' => $nama,
                'phone' => $telepon,
                'address' => $alamat,
            ]);
        });
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
