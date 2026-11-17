@extends('layout-public')

@section('judul', 'Cek Resi - Priangan Kilat Express')

@section('isi')

    <div class="mx-auto max-w-4xl">

        {{-- HERO --}}
        <div class="mb-8 rounded-2xl bg-amber-900 px-6 py-10 text-center text-white shadow-sm sm:px-10">
            <span
                class="mb-4 inline-block rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold tracking-wider text-amber-100">
                PRIANGAN KILAT EXPRESS
            </span>

            <h1 class="mb-3 text-3xl font-bold tracking-tight sm:text-4xl">
                Lacak Kiriman Anda
            </h1>

            <p class="mx-auto max-w-xl text-sm leading-relaxed text-amber-100 sm:text-base">
                Masukkan nomor resi untuk mengetahui status dan perjalanan paket Anda.
            </p>

            {{-- SEARCH FORM --}}
            <form method="GET" action="{{ url('/cek-resi') }}"
                class="mx-auto mt-7 flex max-w-2xl flex-col gap-3 rounded-xl bg-white p-2 sm:flex-row">

                <input type="text" name="resi" value="{{ $resi }}" placeholder="Contoh: PKE-261116-0001"
                    class="min-w-0 flex-1 rounded-lg border-0 px-4 py-3 text-sm text-slate-800 outline-none ring-0 placeholder:text-slate-400 focus:ring-2 focus:ring-amber-500">

                <button type="submit"
                    class="rounded-lg bg-amber-800 px-7 py-3 text-sm font-bold text-white transition hover:bg-amber-950">
                    Cek Resi
                </button>
            </form>
        </div>

        {{-- NOT FOUND --}}
        @if ($hasil === false)

            <div class="rounded-2xl border border-red-200 bg-white p-8 text-center shadow-sm">
                <div
                    class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-2xl font-bold text-red-500">
                    !
                </div>

                <h2 class="text-lg font-bold text-slate-800">
                    Paket Tidak Ditemukan
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Nomor resi yang kamu masukkan tidak ditemukan.
                    Silakan periksa kembali nomor resi.
                </p>
            </div>

            {{-- FOUND --}}
        @elseif ($hasil)
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                {{-- PACKAGE HEADER --}}
                <div class="border-b border-slate-100 p-6">

                    <div class="flex flex-wrap items-start justify-between gap-4">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Nomor Resi
                            </p>

                            <h2 class="mt-1 text-xl font-bold text-slate-900">
                                {{ $hasil['resi'] }}
                            </h2>
                        </div>

                        <span class="rounded-full bg-amber-50 px-4 py-2 text-xs font-bold text-amber-800">
                            {{ $hasil['status'] }}
                        </span>

                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs text-slate-500">Telepon Penerima</p>
                            <p class="mt-1 font-semibold text-slate-800">
                                {{ $hasil['telepon'] }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs text-slate-500">Status Pengiriman</p>
                            <p class="mt-1 font-semibold text-slate-800">
                                {{ $hasil['status'] }}
                            </p>
                        </div>

                    </div>
                </div>

                {{-- TRACKING HISTORY --}}
                <div class="p-6">

                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-900">
                            Riwayat Pengiriman
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Informasi perjalanan paket berdasarkan riwayat status.
                        </p>
                    </div>

                    <div class="space-y-0">

                        @forelse ($hasil['riwayat'] as $h)
                            <div class="relative flex gap-4 pb-7 last:pb-0">

                                {{-- TIMELINE DOT --}}
                                <div class="relative flex flex-col items-center">
                                    <div class="z-10 h-3.5 w-3.5 rounded-full border-4 border-amber-100 bg-amber-700"></div>

                                    @if (!$loop->last)
                                        <div class="absolute top-4 h-full w-px bg-amber-200"></div>
                                    @endif
                                </div>

                                {{-- HISTORY CONTENT --}}
                                <div class="min-w-0 flex-1 pb-2">

                                    <p class="text-sm font-bold text-slate-800">
                                        {{ $h['status'] }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $h['waktu'] }}
                                    </p>

                                    @if (!empty($h['catatan']))
                                        <p class="mt-2 rounded-lg bg-slate-50 p-3 text-sm text-slate-600">
                                            {{ $h['catatan'] }}
                                        </p>
                                    @endif

                                </div>

                            </div>

                        @empty

                            <p class="text-sm text-slate-500">
                                Belum ada riwayat pengiriman.
                            </p>
                        @endforelse

                    </div>

                </div>
            </div>

        @endif

    </div>

@endsection
