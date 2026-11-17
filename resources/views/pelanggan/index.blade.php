```php
@extends('layout')

@section('isi')
    <div class="space-y-5">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Pelanggan</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Daftar pelanggan Priangan Kilat Express.
                </p>
            </div>

            @if (auth()->user()->role === 'admin')
                <a href="{{ url('/pelanggan/create') }}"
                    class="inline-flex items-center justify-center gap-2 bg-amber-800 hover:bg-amber-900 text-white px-4 py-2.5 rounded-lg text-sm font-medium">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Tambah Pelanggan
                </a>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-slate-800">Data Pelanggan</h2>
                    <p class="text-xs text-slate-500 mt-1">Informasi kontak pelanggan terdaftar.</p>
                </div>

                <span class="bg-amber-50 text-amber-800 text-xs font-medium px-3 py-1.5 rounded-full">
                    {{ $pelanggans->count() }} Data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-amber-50 text-amber-900">
                        <tr class="text-left">
                            <th class="px-5 py-3 font-semibold">No.</th>
                            <th class="px-5 py-3 font-semibold">Nama Pelanggan</th>
                            <th class="px-5 py-3 font-semibold">Telepon</th>
                            <th class="px-5 py-3 font-semibold">Alamat</th>
                            @if (auth()->user()->role === 'admin')
                                <th class="px-5 py-3 font-semibold text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pelanggans as $p)
                            <tr class="hover:bg-amber-50/40 transition">
                                <td class="px-5 py-3 text-slate-500">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                            <i data-lucide="user-round" class="w-4 h-4"></i>
                                        </div>
                                        <span class="font-medium text-slate-800">
                                            {{ $p->name }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-5 py-3 text-slate-600 whitespace-nowrap">
                                    {{ $p->phone }}
                                </td>

                                <td class="px-5 py-3 text-slate-600 min-w-48">
                                    {{ $p->address ?: '-' }}
                                </td>

                                @if (auth()->user()->role === 'admin')
                                    <td class="px-5 py-3 text-center">
                                        <a href="{{ url('/pelanggan/' . $p->id . '/edit') }}"
                                            class="inline-flex items-center gap-1.5 text-amber-800 hover:bg-amber-50 px-3 py-1.5 rounded-lg text-xs font-semibold">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                            Edit
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role === 'admin' ? 5 : 4 }}" class="px-5 py-12 text-center">
                                    <i data-lucide="users" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                                    <p class="font-medium text-slate-600">Belum ada pelanggan</p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        Data pelanggan akan ditampilkan di sini.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
@endsection
```
