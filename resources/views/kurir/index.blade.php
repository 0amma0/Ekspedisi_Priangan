```php
@extends('layout')

@section('isi')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-sm text-amber-700 font-medium mb-1">Manajemen Pengguna</p>
                <h1 class="text-2xl font-bold text-slate-800">Data Kurir</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola akun dan status operasional kurir.
                </p>
            </div>

            @if (auth()->user()->role === 'admin')
                <a href="{{ url('/kurir/create') }}"
                    class="inline-flex items-center justify-center gap-2 bg-amber-800 hover:bg-amber-900 text-white px-4 py-3 rounded-xl text-sm font-semibold transition">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Tambah Kurir
                </a>
            @endif
        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div class="bg-white border border-slate-200 rounded-2xl p-5">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-amber-100 text-amber-700 rounded-xl">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Total Kurir</p>
                        <p class="text-2xl font-bold text-slate-800">
                            {{ $kurirs->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-green-100 text-green-700 rounded-xl">
                        <i data-lucide="user-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Kurir Aktif</p>
                        <p class="text-2xl font-bold text-slate-800">
                            {{ $kurirs->where('is_active', true)->count() }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Tabel Kurir --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="p-5 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800">Daftar Kurir</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Informasi akun kurir yang terdaftar dalam sistem.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-5 py-4 font-semibold">Kode</th>
                            <th class="px-5 py-4 font-semibold">Nama Kurir</th>
                            <th class="px-5 py-4 font-semibold">Telepon</th>
                            <th class="px-5 py-4 font-semibold">Status</th>

                            @if (auth()->user()->role === 'admin')
                                <th class="px-5 py-4 font-semibold">Kelola Akun</th>
                                <th class="px-5 py-4 font-semibold text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($kurirs as $k)
                            <tr class="hover:bg-amber-50/40 transition-colors">

                                <td class="px-5 py-4">
                                    <span class="font-semibold text-amber-800 bg-amber-50 px-3 py-1.5 rounded-lg">
                                        {{ $k->code }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 font-medium text-slate-800">
                                    {{ $k->name }}
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $k->phone }}
                                </td>

                                <td class="px-5 py-4">
                                    @if ($k->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                @if (auth()->user()->role === 'admin')
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col gap-3 min-w-48">

                                            {{-- Aktif / Nonaktif --}}
                                            <form method="POST"
                                                action="{{ url('/kurir/' . $k->id . ($k->is_active ? '/nonaktif' : '/aktif')) }}"
                                                onsubmit="return confirm('Yakin ingin mengubah status kurir ini?')">
                                                @csrf

                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $k->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-700 hover:text-green-900' }}">
                                                    <i data-lucide="{{ $k->is_active ? 'user-x' : 'user-check' }}"
                                                        class="w-4 h-4"></i>
                                                    {{ $k->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            {{-- Reset Password --}}
                                            <form method="POST" action="{{ url('/kurir/' . $k->id . '/reset') }}"
                                                class="flex items-center gap-2"
                                                onsubmit="return confirm('Yakin ingin mereset password kurir ini?')">
                                                @csrf

                                                <input type="password" name="password" placeholder="Password baru"
                                                    minlength="8" required
                                                    class="w-36 border border-slate-300 rounded-lg px-2 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">

                                                <button type="submit"
                                                    class="bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-800 px-3 py-2 rounded-lg text-xs font-semibold transition">
                                                    Reset
                                                </button>
                                            </form>

                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        <a href="{{ url('/kurir/' . $k->id . '/edit') }}"
                                            class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-2 rounded-lg text-xs font-semibold transition">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                            Edit
                                        </a>
                                    </td>
                                @endif

                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role === 'admin' ? 6 : 4 }}"
                                    class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-slate-100 p-4 rounded-full mb-3">
                                            <i data-lucide="users" class="w-8 h-8 text-slate-400"></i>
                                        </div>
                                        <p class="font-semibold text-slate-700">
                                            Belum Ada Data Kurir
                                        </p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            Data kurir akan muncul di halaman ini.
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
        if (window.lucide) lucide.createIcons();
    </script>
@endsection
```
