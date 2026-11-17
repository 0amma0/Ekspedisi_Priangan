@extends('layout')
@section('isi')
    <h1 class="text-xl font-bold mb-3">Laporan</h1>
    <form class="mb-4 flex gap-2 items-center">
        Dari <input type="date" name="dari" value="{{ $dari }}" class="border p-1">
        sampai <input type="date" name="sampai" value="{{ $sampai }}" class="border p-1">
        <button class="bg-amber-800 text-white px-3 py-1">Tampilkan</button>
    </form>

    <h2 class="font-bold mt-4">Pengiriman per status</h2>
    <table class="bg-white text-sm mb-4">
        @foreach (['Diterima', 'Transit', 'Dikirim', 'Terkirim', 'Gagal Kirim', 'Retur'] as $s)
            <tr class="border-t">
                <td class="p-2">{{ $s }}</td>
                <td class="p-2">{{ $perStatus[$s] ?? 0 }}</td>
            </tr>
        @endforeach
        <tr class="border-t font-bold">
            <td class="p-2">Total</td>
            <td class="p-2">{{ $perStatus->sum() }}</td>
        </tr>
    </table>

    <h2 class="font-bold mt-4">Pendapatan per zona</h2>
    <table class="bg-white text-sm mb-4">
        <tr class="bg-gray-200">
            <th class="p-2">Zona</th>
            <th class="p-2">Ongkir</th>
            <th class="p-2">Biaya COD</th>
            <th class="p-2">Total</th>
        </tr>
        @foreach ($pendapatan as $z)
            <tr class="border-t">
                <td class="p-2">{{ $z->zona ?? 'Tanpa zona' }}</td>
                <td class="p-2">Rp {{ number_format($z->ongkir, 0, ',', '.') }}</td>
                <td class="p-2">Rp {{ number_format($z->biaya_cod, 0, ',', '.') }}</td>
                <td class="p-2">Rp {{ number_format($z->ongkir + $z->biaya_cod, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr class="border-t font-bold">
            <td class="p-2">Total</td>
            <td class="p-2" colspan="3">Rp
                {{ number_format($pendapatan->sum('ongkir') + $pendapatan->sum('biaya_cod'), 0, ',', '.') }}</td>
        </tr>
    </table>

    <h2 class="font-bold mt-4">
        Rekap COD per kurir (periode {{ $dari }} sampai {{ $sampai }})
    </h2>

    <table class="bg-white text-sm">
        <tr class="bg-gray-200">
            <th class="p-2">Kurir</th>
            <th class="p-2">Belum disetor</th>
            <th class="p-2">Sudah disetor</th>
            <th class="p-2">Total COD</th>
        </tr>

        @foreach ($rekap as $k)
            <tr class="border-t">
                <td class="p-2">
                    {{ $k->code }} {{ $k->name }}
                </td>

                <td class="p-2">
                    Rp {{ number_format($k->belum, 0, ',', '.') }}
                </td>

                <td class="p-2">
                    Rp {{ number_format($k->sudah, 0, ',', '.') }}
                </td>

                <td class="p-2">
                    Rp {{ number_format($k->belum + $k->sudah, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach

        <tr class="border-t font-bold bg-gray-100">
            <td class="p-2">Total</td>

            <td class="p-2">
                Rp {{ number_format($totalBelum, 0, ',', '.') }}
            </td>

            <td class="p-2">
                Rp {{ number_format($totalSudah, 0, ',', '.') }}
            </td>

            <td class="p-2">
                Rp {{ number_format($totalCod, 0, ',', '.') }}
            </td>
        </tr>
    </table>
@endsection
