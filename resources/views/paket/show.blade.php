@extends('layout')
@section('isi')
    <div class="bg-white p-6 rounded max-w-2xl">
        <h1 class="text-xl font-bold">{{ $paket->tracking_number }}</h1>
        <p>Status: <b>{{ $paket->status }}</b> | Gagal kirim: {{ $paket->failed_attempts }}x | Kurir:
            {{ $paket->courier?->name ?? '-' }}</p>
        <p>Pengirim: {{ $paket->customer->name }}</p>
        <p>Penerima: {{ $paket->recipient_name }}, {{ $paket->recipient_phone }}</p>
        <p>{{ $paket->recipient_address }}, {{ $paket->destination_city }}</p>
        <p>Zona: {{ $paket->zone?->name ?? 'Tanpa zona' }} {{ $paket->is_zone_manual ? '(zona manual)' : '' }}</p>
        <hr class="my-3">
        <p>Berat: {{ $paket->weight_kg ?? '-' }} kg, ditagih {{ $paket->billable_weight ?? '-' }} kg
            ({{ $paket->service_type }})</p>
        <p>Tarif per kg: Rp {{ number_format($paket->rate_per_kg, 0, ',', '.') }}</p>
        <p>Ongkir: Rp {{ number_format($paket->shipping_cost, 0, ',', '.') }}</p>
        <p>Biaya COD: Rp {{ number_format($paket->cod_fee, 0, ',', '.') }}</p>
        <p class="font-bold">Total bayar di loket: Rp
            {{ number_format($paket->shipping_cost + $paket->cod_fee, 0, ',', '.') }}</p>
        @if ($paket->is_cod)
            <p>Nilai COD dikumpulkan kurir: Rp {{ number_format($paket->cod_amount, 0, ',', '.') }}</p>
        @endif

        <h2 class="font-bold mt-4">Riwayat</h2>
        <table class="w-full text-sm">
            @foreach ($paket->histories as $h)
                <tr class="border-t">
                    <td class="p-1">{{ $h->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $h->from_status ?? '-' }} &rarr; {{ $h->to_status }}</td>
                    <td>{{ $h->actor_role }}</td>
                    <td>{{ $h->notes }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
