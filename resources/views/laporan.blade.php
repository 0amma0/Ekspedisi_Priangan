@extends('layout')

@section('isi')
    @php
        $statusLabels = ['Diterima', 'Transit', 'Dikirim', 'Terkirim', 'Gagal Kirim', 'Retur'];

        $statusValues = collect($statusLabels)->map(fn($status) => (int) ($perStatus[$status] ?? 0))->values()->all();

        $zonaLabels = $pendapatan->map(fn($z) => $z->zona ?? 'Tanpa zona')->values()->all();

        $ongkirValues = $pendapatan->map(fn($z) => (float) ($z->ongkir ?? 0))->values()->all();

        $biayaCodValues = $pendapatan->map(fn($z) => (float) ($z->biaya_cod ?? 0))->values()->all();

        $kurirLabels = $rekap->map(fn($k) => ($k->code ?? '-') . ' - ' . ($k->name ?? '-'))->values()->all();

        $belumValues = $rekap->map(fn($k) => (float) ($k->belum ?? 0))->values()->all();

        $sudahValues = $rekap->map(fn($k) => (float) ($k->sudah ?? 0))->values()->all();

        $totalPaket = $perStatus->sum();
        $totalPendapatan = $pendapatan->sum('ongkir');
        $totalBiayaCod = $pendapatan->sum('biaya_cod');
        $totalBelum = $totalBelum ?? $rekap->sum('belum');
        $totalSudah = $totalSudah ?? $rekap->sum('sudah');
        $totalCod = $totalBelum + $totalSudah;
    @endphp

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2 text-amber-800">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v12a2 2 0 01-2 2z" />
                    </svg>
                    <h1 class="text-2xl font-bold text-slate-800">Laporan Ekspedisi</h1>
                </div>
                <p class="mt-1 text-sm text-slate-500">
                    Ringkasan aktivitas pengiriman dan penyetoran COD.
                </p>
            </div>

            <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-2 text-sm text-amber-800">
                Periode: {{ $dari }} — {{ $sampai }}
            </div>
        </div>

        {{-- FILTER PERIODE --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">

                <div class="flex-1">
                    <label for="dari" class="mb-2 block text-sm font-semibold text-slate-700">
                        Tanggal Mulai
                    </label>
                    <input type="date" name="dari" id="dari" value="{{ $dari }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-amber-600 focus:ring-2 focus:ring-amber-100">
                </div>

                <div class="flex-1">
                    <label for="sampai" class="mb-2 block text-sm font-semibold text-slate-700">
                        Tanggal Selesai
                    </label>
                    <input type="date" name="sampai" id="sampai" value="{{ $sampai }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-amber-600 focus:ring-2 focus:ring-amber-100">
                </div>

                <button type="submit"
                    class="rounded-xl bg-amber-800 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-900">
                    Terapkan Filter
                </button>
            </form>
        </div>

        {{-- RINGKASAN --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Total Paket</p>
                    <div class="rounded-xl bg-blue-50 p-2 text-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-bold text-slate-800">
                    {{ number_format($totalPaket, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-400">Paket pada periode terpilih</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Total Pendapatan Ongkir</p>
                    <div class="rounded-xl bg-green-50 p-2 text-green-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-800">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-400">Akumulasi biaya pengiriman</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">COD Belum Disetor</p>
                    <div class="rounded-xl bg-orange-50 p-2 text-orange-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-800">
                    Rp {{ number_format($totalBelum, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-400">Menunggu penyetoran kurir</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">COD Sudah Disetor</p>
                    <div class="rounded-xl bg-emerald-50 p-2 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 3c-2.755 0-5.29.93-7.312 2.484A12.06 12.06 0 003 12c0 2.755.93 5.29 2.484 7.312A12.06 12.06 0 0012 21c2.755 0 5.29-.93 7.312-2.484A12.06 12.06 0 0021 12c0-1.418-.246-2.779-.698-4.016z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-800">
                    Rp {{ number_format($totalSudah, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-400">COD yang sudah diterima admin</p>
            </div>

        </div>

        {{-- GRAFIK STATUS DAN PENDAPATAN --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5">
                    <h2 class="font-bold text-slate-800">Statistik Status Paket</h2>
                    <p class="mt-1 text-xs text-slate-500">
                        Perbandingan jumlah paket berdasarkan status.
                    </p>
                </div>
                <div class="relative mx-auto h-72 max-w-md">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5">
                    <h2 class="font-bold text-slate-800">Pendapatan per Zona</h2>
                    <p class="mt-1 text-xs text-slate-500">
                        Perbandingan pendapatan ongkir dan biaya COD.
                    </p>
                </div>
                <div class="relative h-72">
                    <canvas id="pendapatanChart"></canvas>
                </div>
            </div>

        </div>

        {{-- GRAFIK COD KURIR --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="font-bold text-slate-800">Grafik Setoran COD per Kurir</h2>
                <p class="mt-1 text-xs text-slate-500">
                    Perbandingan nominal COD yang sudah dan belum disetor.
                </p>
            </div>
            <div class="relative h-80">
                <canvas id="codChart"></canvas>
            </div>
        </div>

        {{-- TABEL PENDAPATAN --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 p-5">
                <h2 class="font-bold text-slate-800">Rincian Pendapatan per Zona</h2>
                <p class="mt-1 text-xs text-slate-500">
                    Detail biaya pengiriman berdasarkan zona tujuan.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="p-3 text-left font-semibold">Zona</th>
                            <th class="p-3 text-right font-semibold">Pendapatan Ongkir</th>
                            <th class="p-3 text-right font-semibold">Biaya COD</th>
                            <th class="p-3 text-right font-semibold">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendapatan as $z)
                            <tr class="border-t border-slate-100 hover:bg-slate-50">
                                <td class="p-3 font-medium text-slate-700">
                                    {{ $z->zona ?? 'Tanpa zona' }}
                                </td>
                                <td class="p-3 text-right text-slate-600">
                                    Rp {{ number_format($z->ongkir ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right text-slate-600">
                                    Rp {{ number_format($z->biaya_cod ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right font-semibold text-slate-800">
                                    Rp {{ number_format(($z->ongkir ?? 0) + ($z->biaya_cod ?? 0), 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">
                                    Belum ada data pendapatan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL DETAIL SETORAN COD --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-bold text-slate-800">Detail Setoran COD per Kurir</h2>
                    <p class="mt-1 text-xs text-slate-500">
                        Rincian status penyetoran COD setiap kurir.
                    </p>
                </div>

                <button id="btnPerforma" type="button"
                    class="rounded-xl bg-[#245C45] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#194632]">
                    Laporan Performa Kurir
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="p-3 text-left">Kurir</th>
                            <th class="p-3 text-right">Belum Disetor</th>
                            <th class="p-3 text-right">Sudah Disetor</th>
                            <th class="p-3 text-right">Total COD</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($rekap as $k)
                            <tr class="border-t border-slate-100 hover:bg-slate-50">
                                <td class="p-3 font-medium text-slate-700">
                                    {{ $k->code }} - {{ $k->name }}
                                </td>

                                <td class="p-3 text-right font-medium text-orange-700">
                                    Rp {{ number_format($k->belum ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="p-3 text-right font-medium text-emerald-700">
                                    Rp {{ number_format($k->sudah ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="p-3 text-right font-bold text-slate-800">
                                    Rp {{ number_format(($k->belum ?? 0) + ($k->sudah ?? 0), 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">
                                    Belum ada data setoran COD.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if ($rekap->isNotEmpty())
                        <tfoot class="border-t-2 border-slate-200 bg-slate-50 font-bold text-slate-800">
                            <tr>
                                <td class="p-3">Total Keseluruhan</td>
                                <td class="p-3 text-right text-orange-700">
                                    Rp {{ number_format($totalBelum, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right text-emerald-700">
                                    Rp {{ number_format($totalSudah, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right">
                                    Rp {{ number_format($totalCod, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
    {{-- POPUP PERFORMA KURIR --}}
    <div id="modalPerforma" class="fixed inset-0 z-100] hidden items-center justify-center bg-black/50 p-4">

        <div class="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            <div class="flex items-center justify-between border-b p-5">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Laporan Performa Kurir</h2>
                    <p class="mt-1 text-xs text-slate-500">
                        Persentase paket terkirim dan gagal kirim.
                    </p>
                </div>
                <button id="tutupPerforma" type="button" class="rounded-lg px-3 py-2 text-slate-500 hover:bg-slate-100">
                    ✕
                </button>
            </div>

            <div class="max-h-[65vh] overflow-y-auto p-5">
                <p class="mb-4 rounded-lg bg-slate-50 p-3 text-xs text-slate-600">
                    Periode: {{ $dari }} — {{ $sampai }}
                </p>

                <div class="overflow-x-auto rounded-xl border">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="p-3 text-left">Kurir</th>
                                <th class="p-3 text-center">Terkirim</th>
                                <th class="p-3 text-center">Gagal</th>
                                <th class="p-3 text-center">% Terkirim</th>
                                <th class="p-3 text-center">% Gagal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($performa as $item)
                                <tr class="border-t">
                                    <td class="p-3">
                                        <p class="font-semibold">{{ $item->courier->code ?? '-' }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $item->courier->name ?? '-' }}
                                        </p>
                                    </td>
                                    <td class="p-3 text-center text-green-700">{{ $item->terkirim }}</td>
                                    <td class="p-3 text-center text-red-600">{{ $item->gagal }}</td>
                                    <td class="p-3 text-center font-semibold text-green-700">
                                        {{ number_format($item->persen_terkirim, 2) }}%
                                    </td>
                                    <td class="p-3 text-center font-semibold text-red-600">
                                        {{ number_format($item->persen_gagal, 2) }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400">
                                        Belum ada data performa kurir.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end border-t p-4">
                <button id="tutupPerformaBawah" type="button"
                    class="rounded-xl bg-slate-100 px-5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalPerforma');

            function bukaPerforma() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }

            function tutupPerforma() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }

            document.getElementById('btnPerforma')?.addEventListener('click', bukaPerforma);
            document.getElementById('tutupPerforma')?.addEventListener('click', tutupPerforma);
            document.getElementById('tutupPerformaBawah')?.addEventListener('click', tutupPerforma);

            modal?.addEventListener('click', function(e) {
                if (e.target === modal) tutupPerforma();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    tutupPerforma();
                }
            });

            const statusLabels = {!! json_encode($statusLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
            const statusValues = {!! json_encode($statusValues, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};

            const zonaLabels = {!! json_encode($zonaLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
            const ongkirValues = {!! json_encode($ongkirValues, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
            const biayaCodValues = {!! json_encode($biayaCodValues, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};

            const kurirLabels = {!! json_encode($kurirLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
            const belumValues = {!! json_encode($belumValues, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
            const sudahValues = {!! json_encode($sudahValues, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};

            if (typeof Chart === 'undefined') {
                console.error('Chart.js gagal dimuat.');
                return;
            }

            Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';
            Chart.defaults.color = '#64748b';

            // GRAFIK STATUS PAKET
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: [
                            '#f59e0b',
                            '#3b82f6',
                            '#8b5cf6',
                            '#10b981',
                            '#ef4444',
                            '#64748b'
                        ],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 16
                            }
                        }
                    }
                }
            });

            // GRAFIK PENDAPATAN ZONA
            new Chart(document.getElementById('pendapatanChart'), {
                type: 'bar',
                data: {
                    labels: zonaLabels,
                    datasets: [{
                            label: 'Ongkir',
                            data: ongkirValues,
                            backgroundColor: '#b45309',
                            borderRadius: 6
                        },
                        {
                            label: 'Biaya COD',
                            data: biayaCodValues,
                            backgroundColor: '#fbbf24',
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                        notation: 'compact'
                                    }).format(value);
                                }
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 16
                            }
                        }
                    }
                }
            });

            // GRAFIK SETORAN COD PER KURIR
            new Chart(document.getElementById('codChart'), {
                type: 'bar',
                data: {
                    labels: kurirLabels,
                    datasets: [{
                            label: 'Belum Disetor',
                            data: belumValues,
                            backgroundColor: '#f59e0b',
                            borderRadius: 5
                        },
                        {
                            label: 'Sudah Disetor',
                            data: sudahValues,
                            backgroundColor: '#059669',
                            borderRadius: 5
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                        notation: 'compact'
                                    }).format(value);
                                }
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 16
                            }
                        }
                    }
                }
            });

        });
    </script>
@endsection
