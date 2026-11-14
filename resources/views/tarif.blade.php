@extends('layout')

@section('isi')
    <h1 class="text-xl font-bold mb-3">Tarif per Zona (per kg)</h1>
    <table class="bg-white text-sm w-full border">
        @foreach ($zonas as $z)
            <tr class="border-t">
                <td class="p-2">{{ $z->name }}</td>
                <td class="p-2">
                    @if (auth()->user()->role === 'manajer')
                        <form method="POST" action="{{ url('/tarif/' . $z->id) }}">
                            @csrf
                            <input name="price_per_kg" value="{{ $tarif[$z->id] ?? 0 }}" class="border p-1 w-28">
                            <button class="text-blue-700 ml-2">Simpan</button>
                        </form>
                    @else
                        Rp {{ number_format($tarif[$z->id] ?? 0, 0, ',', '.') }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endsection
