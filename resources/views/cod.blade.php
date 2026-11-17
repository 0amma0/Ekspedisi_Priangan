@extends('layout')

@section('isi')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-sm text-amber-700 font-medium mb-1">Keuangan / Operasional</p>
                <h1 class="text-2xl font-bold text-slate-800">Setoran COD</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola dan pantau setoran pembayaran COD dari kurir.
                </p>
            </div>

            <div
                class="flex items-center gap-2 text-sm text-slate-500 bg-white border border-slate-200 rounded-xl px-4 py-3">
                <i data-lucide="wallet" class="w-5 h-5 text-amber-700"></i>
                <span>Pengelolaan Setoran</span>
            </div>
        </div>

        {{-- Notifikasi --}}
        @if (session('ok'))
            <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl">
                <i data-lucide="check-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                <p class="text-sm">{{ session('ok') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Informasi --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-amber-100 text-amber-700 rounded-xl">
                        <i data-lucide="package" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Total Paket COD</p>
                        <p class="text-2xl font-bold text-slate-800">
                            {{ $pakets->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-green-100 text-green-700 rounded-xl">
                        <i data-lucide="hand-coins" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Total Nilai COD</p>
                        <p class="text-2xl font-bold text-slate-800">
                            Rp {{ number_format($pakets->sum('cod_amount'), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Setoran --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-5 border-b border-slate-200">
                <div>
                    <h2 class="font-semibold text-slate-800">Daftar Setoran COD</h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Daftar paket COD yang perlu dipantau setoran pembayarannya.
                    </p>
                </div>

                <span class="text-xs font-medium bg-amber-50 text-amber-800 px-3 py-2 rounded-lg">
                    {{ $pakets->count() }} Data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-5 py-4 font-semibold">No.</th>
                            <th class="px-5 py-4 font-semibold">Nomor Resi</th>
                            <th class="px-5 py-4 font-semibold">Kurir</th>
                            <th class="px-5 py-4 font-semibold">Nilai COD</th>
                            <th class="px-5 py-4 font-semibold">Status Setoran</th>
                            <th class="px-5 py-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pakets as $p)
                            <tr class="hover:bg-amber-50/40 transition-colors">

                                <td class="px-5 py-4 text-slate-500">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="font-semibold text-slate-800">
                                        {{ $p->tracking_number }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="user-round" class="w-4 h-4 text-slate-400"></i>
                                        {{ $p->courier?->name ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="font-semibold text-slate-800">
                                        Rp {{ number_format($p->cod_amount, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    @if ($p->cod_settled_at)
                                        <div
                                            class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                            Sudah Disetor
                                        </div>

                                        <p class="text-xs text-slate-400 mt-1">
                                            {{ $p->cod_settled_at->format('d-m-Y H:i') }}
                                        </p>
                                    @else
                                        <div
                                            class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                            Belum Disetor
                                        </div>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if (!$p->cod_settled_at)
                                        <form method="POST" action="{{ url('/paket/' . $p->id . '/setoran-cod') }}"
                                            onsubmit="return confirm('Catat setoran COD paket ini?')">
                                            @csrf

                                            <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-xs font-semibold transition-colors">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                                Catat Setoran
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400">Selesai</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-slate-100 p-4 rounded-full mb-3">
                                            <i data-lucide="package-open" class="w-8 h-8 text-slate-400"></i>
                                        </div>
                                        <p class="font-semibold text-slate-700">
                                            Belum Ada Paket COD
                                        </p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            Data paket COD terkirim akan ditampilkan di sini.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        if (window.lucide) {
            lucide.createIcons();
        }
    </script>
@endsection
