<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Courier;
use App\Support\Telepon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class KurirImport implements OnEachRow, WithHeadingRow, WithChunkReading
{
    private const PASSWORD_SEMENTARA = 'KurirPriangan2026';

    public int $berhasil = 0;
    public int $dilewati = 0;

    public function onRow(Row $row): void
    {
        $data = $row->toArray();

        $kode = trim((string) ($data['kode'] ?? ''));
        $nama = trim((string) ($data['nama'] ?? ''));
        $telepon = Telepon::rapikan(
            trim((string) ($data['telepon'] ?? ''))
        );

        // Validasi data CSV
        if (
            $kode === '' ||
            $nama === '' ||
            !preg_match('/^KUR-[0-9]{2}$/', $kode) ||
            !Telepon::valid($telepon)
        ) {
            $this->dilewati++;
            return;
        }

        // Hindari kode kurir duplikat
        if (Courier::where('code', $kode)->exists()) {
            $this->dilewati++;
            return;
        }

        DB::transaction(function () use ($kode, $nama, $telepon) {

            $user = User::create([
                'name' => $nama,
                'role' => 'kurir',
                'password' => self::PASSWORD_SEMENTARA,
                'must_change_password' => true,
            ]);

            Courier::create([
                'user_id' => $user->id,
                'code' => $kode,
                'name' => $nama,
                'phone' => $telepon,
                'is_active' => true,
            ]);
        });

        $this->berhasil++;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
