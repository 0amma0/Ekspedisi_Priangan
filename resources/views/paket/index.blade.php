@extends('layout')

@section('isi')
    <div class="space-y-5">

        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Daftar Paket</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola dan pantau seluruh pengiriman paket.
                </p>
            </div>

            <a href="{{ url('/paket/create') }}"
                class="inline-flex items-center justify-center gap-2 bg-amber-800 hover:bg-amber-900 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Input Paket
            </a>
        </div>

        {{-- Filter & Pencarian Resi --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <form method="GET" action="{{ url('/paket') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor resi..."
                        class="w-full border border-slate-200 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                </div>

                <select name="filter" class="border border-slate-200 rounded-lg px-3 py-2.5 text-sm">
                    <option value="semua" @selected($filter === 'semua')>Semua Paket</option>
                    <option value="tertahan" @selected($filter === 'tertahan')>Transit Tertahan</option>
                </select>

                <button type="submit"
                    class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    Cari Paket
                </button>

                @if (request('q'))
                    <a href="{{ url('/paket') }}"
                        class="border border-slate-200 hover:bg-slate-50 px-4 py-2.5 rounded-lg text-sm text-slate-600 text-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabel Daftar Paket --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div>
                    <h2 class="font-semibold text-slate-800">Data Pengiriman</h2>
                    <p class="text-xs text-slate-500 mt-1">Informasi paket yang terdaftar</p>
                </div>

                <span class="text-xs bg-amber-50 text-amber-800 px-3 py-1.5 rounded-full font-medium">
                    {{ $pakets->total() }} Paket
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm whitespace-nowrap">
                    <thead class="bg-amber-50 text-amber-900">
                        <tr class="text-left">
                            @foreach (['Nomor Resi', 'Penerima', 'Telepon', 'Kota Tujuan', 'Berat', 'Verifikasi', 'Status', 'Gagal', 'Kurir'] as $kolom)
                                <th class="px-4 py-3 font-semibold">{{ $kolom }}</th>
                            @endforeach
                            <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pakets as $p)
                            <tr class="hover:bg-amber-50/40 transition">

                                {{-- Nomor Resi --}}
                                <td class="px-4 py-3">
                                    <a href="{{ url('/paket/' . $p->id) }}"
                                        class="font-semibold text-amber-800 hover:text-amber-950">
                                        {{ $p->tracking_number }}
                                    </a>
                                </td>

                                {{-- Info Penerima & Tujuan --}}
                                <td class="px-4 py-3 text-slate-700">{{ $p->recipient_name }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ \App\Support\Telepon::rapikan($p->recipient_phone ?? '') ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $p->destination_city }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $p->weight_kg !== null ? $p->weight_kg . ' kg' : '-' }}
                                </td>

                                {{-- Status Verifikasi Berat/Data --}}
                                <td class="px-4 py-3">
                                    @php
                                        $perluVerifikasi = $p->needs_verification || $p->weight_kg === null;
                                    @endphp

                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $perluVerifikasi ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        <i data-lucide="{{ $perluVerifikasi ? 'alert-circle' : 'check-circle' }}"
                                            class="w-3.5 h-3.5"></i>
                                        {{ $perluVerifikasi ? 'Perlu Verifikasi' : 'Lengkap' }}
                                    </span>
                                </td>

                                {{-- Status Pengiriman --}}
                                <td class="px-4 py-3">
                                    @php
                                        $statusStyle = match ($p->status) {
                                            'Diterima' => 'bg-blue-100 text-blue-800',
                                            'Transit' => 'bg-amber-100 text-amber-800',
                                            'Dalam Pengiriman' => 'bg-indigo-100 text-indigo-800',
                                            'Terkirim' => 'bg-green-100 text-green-800',
                                            'Gagal Kirim' => 'bg-red-100 text-red-800',
                                            'Retur' => 'bg-slate-200 text-slate-700',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp

                                    <div class="flex flex-col items-start gap-1">
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $statusStyle }}">
                                            {{ $p->status }}
                                        </span>

                                        @if ($p->transit_tertahan)
                                            <span
                                                class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                Transit > 3 hari
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Percobaan Gagal & Nama Kurir --}}
                                <td class="px-4 py-3 text-center text-slate-600">{{ $p->failed_attempts }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $p->courier?->name ?? '-' }}</td>

                                {{-- Tombol Aksi sesuai alur status paket --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">

                                        @if ($p->status === 'Diterima')
                                            {{-- Tahap 1: Pindahkan paket masuk ke gudang transit --}}
                                            <form method="POST" action="{{ url('/paket/' . $p->id . '/transit') }}">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                                    Transit
                                                </button>
                                            </form>
                                        @elseif ($p->status === 'Transit' || ($p->status === 'Gagal Kirim' && $p->failed_attempts < 2))
                                            {{-- Tahap 2: Tugaskan kurir (boleh kirim ulang jika gagal kurang dari 2x) --}}
                                            <form method="POST" action="{{ url('/paket/' . $p->id . '/tugaskan') }}"
                                                class="flex items-center gap-1">
                                                @csrf

                                                <select name="courier_id"
                                                    class="border border-slate-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-amber-700">
                                                    @foreach ($kurirs as $k)
                                                        <option value="{{ $k->id }}">
                                                            {{ $k->code }} - {{ $k->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button type="submit"
                                                    class="bg-amber-800 hover:bg-amber-900 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                                    Tugaskan
                                                </button>
                                            </form>
                                        @elseif ($p->status === 'Gagal Kirim')
                                            {{-- Tahap 3: Jika gagal kirim sudah >= 2x, kembalikan ke pengirim (Retur) --}}
                                            <form method="POST" action="{{ url('/paket/' . $p->id . '/retur') }}"
                                                onsubmit="return confirm('Ubah paket ini ke status Retur?')">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                                    <i data-lucide="undo-2" class="w-3.5 h-3.5"></i>
                                                    Retur
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400">Tidak ada aksi</span>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center text-slate-400">
                                        <i data-lucide="package-search" class="w-10 h-10 mb-2"></i>
                                        <p class="font-medium text-slate-600">Belum ada paket</p>
                                        <p class="text-xs mt-1">Data paket akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $pakets->links() }}
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        if (window.lucide) lucide.createIcons();
    </script>
@endsection
