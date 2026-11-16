@extends('layout')
@section('isi')
    <h1 class="text-xl font-bold mb-3">Setoran COD</h1>
    <table class="w-full bg-white text-sm">
        <tr class="bg-gray-200 text-left">
            <th class="p-2">Resi</th>
            <th>Kurir</th>
            <th>Nilai COD</th>
            <th>Setoran</th>
        </tr>
        @forelse ($pakets as $p)
            <tr class="border-t">
                <td class="p-2">{{ $p->tracking_number }}</td>
                <td>{{ $p->courier?->name ?? '-' }}</td>
                <td>Rp {{ number_format($p->cod_amount, 0, ',', '.') }}</td>
                <td>
                    @if ($p->cod_settled_at)
                        Sudah disetor {{ $p->cod_settled_at->format('d-m-Y H:i') }}
                    @else
                        <form method="POST" action="{{ url('/paket/' . $p->id . '/setoran') }}"
                            onsubmit="return confirm('Catat setoran?')">@csrf
                            <button class="text-blue-700">Catat Setoran</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="p-3">Belum ada paket COD terkirim.</td>
            </tr>
        @endforelse
    </table>
@endsection