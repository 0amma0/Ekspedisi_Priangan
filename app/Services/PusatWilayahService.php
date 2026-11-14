<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PusatWilayahService
{
    public function cariZona(string $kota): ?int
    {
        try {
            $res = Http::timeout(3)                                    
                ->withHeaders(['X-API-Key' => config('services.pusat_wilayah.key')])
                ->get(config('services.pusat_wilayah.url'), ['kota' => $kota]);

            if (!$res->ok()) {
                return null;
            }
            $zona = (int) $res->json('zona');
            return in_array($zona, [1, 2, 3], true) ? $zona : null;
        } catch (\Throwable $e) {
            return null;                                               
        }
    }
}
//composer dump-autoload
//"Gunakan header api-key untuk mengirim kunci..." (Berarti di kode tinggal ganti 'X-API-Key' menjadi 'api-key').

//"Kirim nama kota melalui parameter q atau city..." 
//(Berarti di kode tinggal ganti 'kota' menjadi 'q' atau 'city' di dalam fungsi get()).

//"Format JSON balasan dari server adalah { "result": 2 }" 
//(Berarti field zone-nya bernama result, sehingga di kode tinggal ganti $res->json('zona') menjadi $res->json('result')).
