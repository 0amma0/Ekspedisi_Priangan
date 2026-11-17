<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\KurirImport;
use App\Imports\PelangganImport;
use App\Imports\PaketImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kurir' => 'nullable|file|mimes:csv,txt|max:10240',
            'pelanggan' => 'nullable|file|mimes:csv,txt|max:10240',
            'paket' => 'nullable|file|mimes:csv,txt|max:10240',
        ]);

        $files = $request->only(['kurir', 'pelanggan', 'paket']);
        $files = array_filter($files);

        if (empty($files)) {
            return back()->withErrors([
                'import' => 'Pilih minimal satu file CSV.',
            ]);
        }

        try {
            $laporan = [];

            if (isset($files['kurir'])) {
                $importKurir = new KurirImport();

                Excel::import($importKurir, $files['kurir']);

                $laporan[] = 'Import kurir: '
                    . $importKurir->berhasil . ' berhasil, '
                    . $importKurir->dilewati . ' dilewati.';
            }

            if (isset($files['pelanggan'])) {
                Excel::import(new PelangganImport, $files['pelanggan']);
                $laporan[] = 'Import pelanggan selesai.';
            }

            if (isset($files['paket'])) {
                $importPaket = new PaketImport();
                Excel::import($importPaket, $files['paket']);

                $laporan[] = 'Import paket selesai.';
                $laporan[] = 'Paket gagal: ' . count($importPaket->gagal);
            }

            return back()
                ->with('success', 'Proses import selesai.')
                ->with('import_report', $laporan);
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors([
                'import' => 'Import gagal. Periksa laravel.log untuk detail error.',
            ]);
        }
    }
}
