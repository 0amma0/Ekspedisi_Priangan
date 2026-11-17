```php
@extends('layout')

@section('isi')
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">
                    Administrasi Sistem
                </p>
                <h1 class="text-2xl font-bold text-slate-800 mt-1">Import Data</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola pemasukan data kurir, pelanggan, dan paket historis.
                </p>
            </div>

            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-3">
                <i data-lucide="database" class="w-5 h-5 text-amber-700"></i>
                <span class="text-sm font-medium text-slate-600">Data Management</span>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div class="flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl p-4">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-green-600 shrink-0"></i>
                <div>
                    <p class="font-semibold text-green-800 text-sm">Proses Import Selesai</p>
                    <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0"></i>
                <div>
                    <p class="font-semibold text-red-800 text-sm">Import Tidak Dapat Diproses</p>
                    <ul class="list-disc pl-5 mt-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- FORM IMPORT --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="bg-amber-100 text-amber-800 p-3 rounded-xl">
                        <i data-lucide="file-up" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-800">Upload File Import</h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Pilih file CSV yang akan dimasukkan ke dalam sistem.
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="p-6 space-y-5">

                    {{-- KURIR --}}
                    <div class="grid grid-cols-1 md:grid-cols-[190px_1fr] gap-3 md:items-center">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">
                                Data Kurir
                            </label>
                            <p class="text-xs text-slate-400 mt-1">Informasi akun kurir</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <input type="file" name="kurir" accept=".csv,.txt"
                                    class="block w-full text-sm text-slate-500 border border-slate-300 rounded-xl cursor-pointer
                                file:mr-3 file:py-3 file:px-4 file:border-0
                                file:bg-slate-100 file:text-slate-700 file:font-semibold
                                hover:file:bg-amber-100">
                            </div>
                            <span class="flex items-center gap-1.5 text-xs text-slate-400 sm:w-20">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                                CSV / TXT
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100"></div>

                    {{-- PELANGGAN --}}
                    <div class="grid grid-cols-1 md:grid-cols-[190px_1fr] gap-3 md:items-center">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">
                                Data Pelanggan
                            </label>
                            <p class="text-xs text-slate-400 mt-1">Informasi pelanggan</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <input type="file" name="pelanggan" accept=".csv,.txt"
                                    class="block w-full text-sm text-slate-500 border border-slate-300 rounded-xl cursor-pointer
                                file:mr-3 file:py-3 file:px-4 file:border-0
                                file:bg-slate-100 file:text-slate-700 file:font-semibold
                                hover:file:bg-amber-100">
                            </div>
                            <span class="flex items-center gap-1.5 text-xs text-slate-400 sm:w-20">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                                CSV / TXT
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100"></div>

                    {{-- PAKET --}}
                    <div class="grid grid-cols-1 md:grid-cols-[190px_1fr] gap-3 md:items-center">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">
                                Paket Historis
                            </label>
                            <p class="text-xs text-slate-400 mt-1">Riwayat pengiriman paket</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <input type="file" name="paket" accept=".csv,.txt"
                                    class="block w-full text-sm text-slate-500 border border-slate-300 rounded-xl cursor-pointer
                                file:mr-3 file:py-3 file:px-4 file:border-0
                                file:bg-slate-100 file:text-slate-700 file:font-semibold
                                hover:file:bg-amber-100">
                            </div>
                            <span class="flex items-center gap-1.5 text-xs text-slate-400 sm:w-20">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                                CSV / TXT
                            </span>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
                        <div class="flex items-start gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-amber-700 mt-0.5"></i>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                Pastikan file yang dipilih memiliki format kolom sesuai template
                                import sistem. File yang tidak sesuai dapat menyebabkan proses
                                import gagal.
                            </p>
                        </div>
                    </div>

                </div>

                <div
                    class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <button type="reset"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm font-semibold hover:bg-slate-100 transition">
                        Bersihkan
                    </button>

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-amber-800 hover:bg-amber-900 text-white text-sm font-semibold transition">
                        <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                        Mulai Import
                    </button>
                </div>

            </form>
        </div>

        {{-- TABEL LAPORAN IMPORT --}}
        @if (session('import_report'))
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                <div
                    class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-slate-800">Laporan Hasil Import</h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Informasi hasil proses pemasukan data.
                        </p>
                    </div>

                    <span
                        class="inline-flex items-center gap-2 text-xs font-semibold bg-slate-100 text-slate-600 px-3 py-2 rounded-lg">
                        <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                        Riwayat Proses Import
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-5 py-4 font-semibold">No.</th>
                                <th class="px-5 py-4 font-semibold">Keterangan Import</th>
                                <th class="px-5 py-4 font-semibold">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach (session('import_report') as $hasil)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-5 py-4 text-slate-400">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-700">
                                        {{ $hasil }}
                                    </td>

                                    <td class="px-5 py-4">
                                        {{-- Status belum disimpulkan dari teks laporan --}}
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-600 px-3 py-1.5 rounded-full text-xs font-semibold">
                                            <i data-lucide="info" class="w-3.5 h-3.5"></i>
                                            Laporan diterima
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        @endif

    </div>

    <script>
        if (window.lucide) lucide.createIcons();
    </script>
@endsection
```
