@extends('layout')

@section('judul', 'Tarif per Zona - Priangan Kilat Express')

@section('isi')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#245C45]">
            Tarif Pengiriman
        </h1>
        <p class="mt-1 text-sm text-[#718477]">
            Daftar tarif pengiriman berdasarkan zona tujuan per kilogram.
        </p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-[#DCE7DD] bg-white shadow-sm">

        {{-- HEADER CARD --}}
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#E5ECE3] bg-[#F5F7F2] px-5 py-4">
            <div>
                <h2 class="font-bold text-[#34443B]">
                    Tarif per Zona
                </h2>
                <p class="mt-1 text-xs text-[#819487]">
                    Harga pengiriman untuk setiap zona
                </p>
            </div>

            <span class="rounded-lg bg-[#245C45] px-3 py-2 text-xs font-semibold text-white">
                Harga / KG
            </span>
        </div>

        {{-- TABEL --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">

                <thead class="bg-[#245C45] text-white">
                    <tr>
                        <th class="px-5 py-4 font-semibold">
                            Zona Tujuan
                        </th>
                        <th class="px-5 py-4 font-semibold">
                            Tarif Pengiriman
                        </th>
                        <th class="px-5 py-4 text-center font-semibold">
                            Satuan
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#E5ECE3]">

                    @foreach ($zonas as $z)
                        <tr class="transition hover:bg-[#F5F7F2]">

                            {{-- NAMA ZONA --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#E8F0E7] text-[#245C45]">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M12 21s7-5.5 7-12a7 7 0 1 0-14 0c0 6.5 7 12 7 12Z" />
                                            <circle cx="12" cy="9" r="2.5" stroke-width="1.8" />
                                        </svg>
                                    </div>

                                    <span class="font-semibold text-[#34443B]">
                                        {{ $z->name }}
                                    </span>
                                </div>
                            </td>

                            {{-- TARIF --}}
                            <td class="px-5 py-4">
                                @if (auth()->user()->role === 'manajer')
                                    <form method="POST" action="{{ url('/tarif/' . $z->id) }}"
                                        class="flex flex-wrap items-center gap-2">

                                        @csrf

                                        <div class="relative">
                                            <span
                                                class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-[#819487]">
                                                Rp
                                            </span>

                                            <input type="number" name="price_per_kg" min="0" step="1"
                                                value="{{ $tarif[$z->id] ?? 0 }}"
                                                class="w-36 rounded-lg border border-[#DCE7DD] bg-[#FAFCF8] py-2 pl-9 pr-3 text-sm font-semibold text-[#34443B] outline-none transition focus:border-[#245C45] focus:ring-2 focus:ring-[#245C45]/10"
                                                required>
                                        </div>

                                        <button type="submit"
                                            class="rounded-lg bg-[#E88732] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#D87524]">
                                            Simpan
                                        </button>

                                    </form>
                                @else
                                    <span class="font-bold text-[#245C45]">
                                        Rp {{ number_format($tarif[$z->id] ?? 0, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>

                            {{-- SATUAN --}}
                            <td class="px-5 py-4 text-center">
                                <span class="rounded-lg bg-[#FFF1E3] px-3 py-1.5 text-xs font-semibold text-[#B96520]">
                                    Per kg
                                </span>
                            </td>

                        </tr>
                    @endforeach

                    @if ($zonas->isEmpty())
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-sm text-[#819487]">
                                Belum ada data zona tarif.
                            </td>
                        </tr>
                    @endif

                </tbody>
            </table>
        </div>

        {{-- FOOTER CARD --}}
        <div class="border-t border-[#E5ECE3] bg-[#FAFCF8] px-5 py-3">
            <p class="text-xs text-[#819487]">
                Tarif pengiriman ditampilkan berdasarkan zona tujuan dan dihitung per kilogram.
            </p>
        </div>

    </div>

@endsection
