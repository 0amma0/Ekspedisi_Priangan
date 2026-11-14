<?php

namespace App\Support;

class Telepon
{
    public static function rapikan(string $nomor): string
    {
        $nomor = preg_replace('/[\s\-\.\(\)]/', '', $nomor);
        if (str_starts_with($nomor, '+62')) {
            $nomor = '0' . substr($nomor, 3);
        } elseif (str_starts_with($nomor, '62')) {
            $nomor = '0' . substr($nomor, 2);
        }
        return $nomor;
    }

    public static function valid(string $nomor): bool
    {
        return preg_match('/^08[0-9]{8,12}$/', self::rapikan($nomor)) === 1;
    }

    public static function samarkan(string $nomor): string
    {
        $nomor = self::rapikan($nomor);
        return substr($nomor, 0, 7) . str_repeat('*', max(0, strlen($nomor) - 7));
    }

    public static function aturan(): \Closure
    {
        return function ($atribut, $nilai, $gagal) {
            if (!self::valid((string) $nilai)) {
                $gagal('Nomor telepon tidak valid. Gunakan 10-14 digit dan hanya angka.');
            }
        };
    }
}
