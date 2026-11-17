<?php

namespace App\Services;

use App\Models\Zone;
use App\Models\ZoneRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ZonaService
{
    
public function cariZona(string $kota): array
{
    $kota = trim($kota);

    $cacheKey = 'zona_import_' . md5(strtolower($kota));

    return Cache::remember($cacheKey, now()->addHours(6), function () use ($kota) {
        try {
            $url = rtrim(
                config('services.pusat_wilayah.url', ''),
                '/'
            );

            $apiKey = config('services.pusat_wilayah.key');

            if (empty($url) || empty($apiKey)) {
                return [
                    'status' => 'unavailable',
                    'zone_id' => null,
                    'zone_name' => null,
                    'tariff' => null,
                    'message' => 'Konfigurasi API Pusat Wilayah belum tersedia.',
                ];
            }

            $response = Http::acceptJson()
                ->withHeaders(['X-API-Key' => $apiKey])
                ->connectTimeout(5)
                ->timeout(10)
                ->get($url . '/wilayah', ['kota' => $kota]);

            if (!$response->successful()) {
                Log::warning('API Pusat Wilayah gagal', [
                    'kota' => $kota,
                    'status' => $response->status(),
                ]);

                return [
                    'status' => 'unavailable',
                    'zone_id' => null,
                    'zone_name' => null,
                    'tariff' => null,
                    'message' => 'Layanan Pusat Wilayah sedang tidak tersedia.',
                ];
            }

            $hasil = $response->json();
            $zonaId = $hasil['zona'] ?? null;

            if (!$zonaId) {
                return [
                    'status' => 'not_found',
                    'zone_id' => null,
                    'zone_name' => null,
                    'tariff' => null,
                    'message' => 'Kota tidak ditemukan dalam Pusat Wilayah.',
                ];
            }

            $zona = Zone::find($zonaId);

            if (!$zona) {
                return [
                    'status' => 'unavailable',
                    'zone_id' => null,
                    'zone_name' => null,
                    'tariff' => null,
                    'message' => 'Data zona belum tersedia di database lokal.',
                ];
            }

            $tarif = ZoneRate::where('zone_id', $zonaId)
                ->value('price_per_kg');

            return [
                'status' => 'found',
                'zone_id' => (int) $zonaId,
                'zone_name' => $zona->name,
                'tariff' => $tarif,
                'message' => 'Zona berhasil ditemukan.',
            ];

        } catch (\Throwable $e) {
            Log::error('Gagal menghubungi Pusat Wilayah', [
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'unavailable',
                'zone_id' => null,
                'zone_name' => null,
                'tariff' => null,
                'message' => 'Layanan Pusat Wilayah sedang tidak tersedia.',
            ];
        }
    });
}

    }