@extends('layout')

@section('isi')
    <div class="max-w-5xl mx-auto space-y-5">

        {{-- Navigasi --}}
        <div>
            <a href="{{ url('/paket') }}"
                class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-amber-800 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar Paket
            </a>
        </div>

        {{-- Header Resi --}}
        <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Nomor Resi
                    </p>

                    <h1 class="text-2xl font-bold text-amber-800 mt-1">
                        {{ $paket->tracking_number }}
                    </h1>

                    <p class="text-sm text-slate-500 mt-2">
                        Detail informasi pengiriman paket.
                    </p>
                </div>

                <div class="sm:text-right">
                    @php
                        $statusStyle = match ($paket->status) {
                            'Diterima' => 'bg-blue-100 text-blue-800',
                            'Transit' => 'bg-amber-100 text-amber-800',
                            'Dalam Pengiriman' => 'bg-indigo-100 text-indigo-800',
                            'Terkirim' => 'bg-green-100 text-green-800',
                            'Gagal Kirim' => 'bg-red-100 text-red-800',
                            'Retur' => 'bg-slate-200 text-slate-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp

                    <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusStyle }}">
                        {{ $paket->status }}
                    </span>

                    <p class="text-xs text-slate-500 mt-2">
                        Gagal kirim: {{ $paket->failed_attempts }} kali
                    </p>
                </div>
            </div>
        </div>

        {{-- Informasi Pengiriman --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Pengirim dan Penerima --}}
            <div class="bg-white border border-slate-200 rounded-xl p-5 space-y-5">
                <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-amber-800"></i>
                    Informasi Pengiriman
                </h2>

                <div>
                    <p class="text-xs text-slate-500 mb-1">Pengirim</p>
                    <p class="font-medium text-slate-800">
                        {{ $paket->customer->name }}
                    </p>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <p class="text-xs text-slate-500 mb-1">Penerima</p>

                    <p class="font-medium text-slate-800">
                        {{ $paket->recipient_name }}
                    </p>

                    <p class="text-sm text-slate-600 mt-1">
                        {{ $paket->recipient_phone }}
                    </p>

                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                        {{ $paket->recipient_address }}
                    </p>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <p class="text-xs text-slate-500 mb-1">Kota Tujuan</p>

                    <p class="font-medium text-slate-800">
                        {{ $paket->destination_city }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-1">Zona Pengiriman</p>

                    <p class="font-medium text-slate-800">
                        {{ $paket->zone?->name ?? 'Tanpa zona' }}

                        @if ($paket->is_zone_manual)
                            <span class="text-xs text-amber-700">(Zona manual)</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Detail Paket --}}
            <div class="bg-white border border-slate-200 rounded-xl p-5 space-y-5">

                <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                    <i data-lucide="package" class="w-5 h-5 text-amber-800"></i>
                    Detail Paket
                </h2>

                {{-- Berat Aktual --}}
                <div class="bg-slate-50 rounded-lg p-4">
                    <p class="text-xs text-slate-500">Berat Aktual</p>

                    <p class="text-xl font-semibold text-slate-800 mt-1">
                        {{ $paket->weight_kg ?? '-' }} kg
                    </p>
                </div>

                {{-- Dimensi --}}
                <div>
                    <p class="text-sm font-semibold text-slate-800 mb-3">
                        Dimensi Paket
                    </p>

                    @if ($paket->length_cm !== null && $paket->width_cm !== null && $paket->height_cm !== null)
                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-slate-50 rounded-lg p-3 text-center">
                                <p class="text-xs text-slate-500">Panjang</p>
                                <p class="font-semibold text-slate-800 mt-1">
                                    {{ $paket->length_cm }} cm
                                </p>
                            </div>

                            <div class="bg-slate-50 rounded-lg p-3 text-center">
                                <p class="text-xs text-slate-500">Lebar</p>
                                <p class="font-semibold text-slate-800 mt-1">
                                    {{ $paket->width_cm }} cm
                                </p>
                            </div>

                            <div class="bg-slate-50 rounded-lg p-3 text-center">
                                <p class="text-xs text-slate-500">Tinggi</p>
                                <p class="font-semibold text-slate-800 mt-1">
                                    {{ $paket->height_cm }} cm
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="bg-slate-50 rounded-lg p-4">
                            <p class="text-sm text-slate-500">
                                Dimensi tidak dimasukkan.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Perhitungan Berat --}}
                <div class="border border-amber-200 bg-amber-50/50 rounded-xl p-4 space-y-4">

                    <h3 class="text-sm font-semibold text-amber-900 flex items-center gap-2">
                        <i data-lucide="calculator" class="w-4 h-4"></i>
                        Perhitungan Berat
                    </h3>

                    <div class="flex justify-between items-center gap-3">
                        <span class="text-sm text-slate-600">
                            Berat Volumetrik
                        </span>

                        <span class="font-semibold text-slate-800">
                            {{ $paket->volumetric_weight_kg !== null
                                ? number_format($paket->volumetric_weight_kg, 2, ',', '.') . ' kg'
                                : '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center gap-3">
                        <span class="text-sm text-slate-600">
                            Berat Dasar
                        </span>

                        <span class="font-semibold text-slate-800">
                            {{ $paket->base_weight_kg !== null
                                ? number_format($paket->base_weight_kg, 2, ',', '.') . ' kg'
                                : number_format($paket->weight_kg, 2, ',', '.') . ' kg' }}
                        </span>
                    </div>

                    <div class="border-t border-amber-200 pt-3">
                        <div class="flex justify-between items-center gap-3">
                            <span class="text-sm font-semibold text-amber-900">
                                Berat Ditagih
                            </span>

                            <span class="text-xl font-bold text-amber-800">
                                {{ $paket->billable_weight ?? '-' }} kg
                            </span>
                        </div>
                    </div>

                </div>

                {{-- Jenis Layanan --}}
                <div>
                    <p class="text-xs text-slate-500 mb-1">Jenis Layanan</p>

                    <p class="font-medium text-slate-800">
                        {{ $paket->service_type }}
                    </p>
                </div>

                {{-- Kurir --}}
                <div>
                    <p class="text-xs text-slate-500 mb-1">Kurir</p>

                    <p class="font-medium text-slate-800">
                        {{ $paket->courier?->name ?? 'Belum ditugaskan' }}
                    </p>
                </div>

                {{-- Rincian Biaya --}}
                <div class="border-t border-slate-100 pt-4 space-y-3">

                    <h3 class="text-sm font-semibold text-slate-800">
                        Rincian Biaya
                    </h3>

                    <div class="flex justify-between text-sm gap-3">
                        <span class="text-slate-500">Tarif per kg</span>

                        <span class="font-medium text-slate-700">
                            Rp {{ number_format($paket->rate_per_kg, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between text-sm gap-3">
                        <span class="text-slate-500">Ongkos Kirim</span>

                        <span class="font-medium text-slate-700">
                            Rp {{ number_format($paket->shipping_cost, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between text-sm gap-3">
                        <span class="text-slate-500">Biaya COD</span>

                        <span class="font-medium text-slate-700">
                            Rp {{ number_format($paket->cod_fee, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="border-t border-slate-200 pt-3 flex justify-between items-center gap-3">
                        <span class="font-semibold text-slate-800">
                            Total Bayar di Loket
                        </span>

                        <span class="text-lg font-bold text-amber-800">
                            Rp {{ number_format($paket->shipping_cost + $paket->cod_fee, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Informasi COD --}}
                    @if ($paket->is_cod)
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-xs text-amber-800">
                                Nilai COD yang dikumpulkan kurir
                            </p>

                            <p class="font-bold text-amber-900 mt-1">
                                Rp {{ number_format($paket->cod_amount, 0, ',', '.') }}
                            </p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- Riwayat Pengiriman --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

            <div class="p-5 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5 text-amber-800"></i>
                    Riwayat Pengiriman
                </h2>

                <p class="text-xs text-slate-500 mt-1">
                    Catatan perubahan status paket.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="bg-amber-50 text-amber-900">
                        <tr class="text-left">
                            <th class="px-4 py-3 font-semibold">Waktu</th>
                            <th class="px-4 py-3 font-semibold">Perubahan Status</th>
                            <th class="px-4 py-3 font-semibold">Aktor</th>
                            <th class="px-4 py-3 font-semibold">Catatan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($paket->histories as $h)
                            <tr class="hover:bg-amber-50/40">

                                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                    {{ $h->created_at->format('d-m-Y H:i') }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="text-slate-500">
                                        {{ $h->from_status ?? '-' }}
                                    </span>

                                    <i data-lucide="arrow-right" class="inline w-3 h-3 mx-1 text-amber-700"></i>

                                    <span class="font-medium text-slate-800">
                                        {{ $h->to_status }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-slate-600">
                                    {{ $h->actor_role }}
                                </td>

                                <td class="px-4 py-3 text-slate-600">
                                    {{ $h->notes ?: '-' }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                    Belum ada riwayat pengiriman.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
