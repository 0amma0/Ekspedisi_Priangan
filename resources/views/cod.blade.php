@extends('layout')

@section('isi')
    <h1 class="text-xl font-bold mb-3">Setoran COD</h1>

    @if (session('ok'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-3">
            {{ session('ok') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full bg-white text-sm">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2">Resi</th>
                    <th class="p-2">Kurir</th>
                    <th class="p-2">Nilai COD</th>
                    <th class="p-2">Setoran</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($pakets as $p)
                    <tr class="border-t">
                        <td class="p-2">
                            {{ $p->tracking_number }}
                        </td>

                        <td class="p-2">
                            {{ $p->courier?->name ?? '-' }}
                        </td>

                        <td class="p-2">
                            Rp {{ number_format($p->cod_amount, 0, ',', '.') }}
                        </td>

                        <td class="p-2">
                            @if ($p->cod_settled_at)
                                <span class="text-green-700 font-semibold">
                                    Sudah disetor
                                </span>

                                <div class="text-xs text-gray-500">
                                    {{ $p->cod_settled_at->format('d-m-Y H:i') }}
                                </div>
                            @else
                                <form method="POST" action="{{ url('/paket/' . $p->id . '/setoran-cod') }}"
                                    onsubmit="return confirm('Catat setoran COD paket ini?')">
                                    @csrf

                                    <button type="submit"
                                        class="bg-green-700 hover:bg-green-800 text-white px-3 py-2 rounded">
                                        Catat Setoran
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-3 text-center text-gray-500">
                            Belum ada paket COD terkirim.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
