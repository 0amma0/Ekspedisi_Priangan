```php
@extends('layout')

@section('isi')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <p class="text-sm text-amber-700 font-medium mb-1">Administrasi Sistem</p>
        <h1 class="text-2xl font-bold text-slate-800">Impor Data</h1>
        <p class="text-sm text-slate-500 mt-1">
            Tambahkan data kurir, pelanggan, dan paket historis melalui file CSV.
        </p>
    </div>

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl">
            <div class="flex items-center gap-2 font-semibold mb-2">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                Terjadi Kesalahan
            </div>

            <ul class="list-disc pl-5 space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Petunjuk --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <div class="flex gap-3">
            <div class="bg-amber-100 text-amber-800 p-3 rounded-xl h-fit">
                <i data-lucide="lightbulb" class="w-5 h-5"></i>
            </div>

            <div>
                <h3 class="font-semibold text-amber-900 text-sm">Petunjuk Impor</h3>
                <p class="text-sm text-amber-800 mt-1">
                    Pilih file CSV sesuai kategori data yang ingin dimasukkan.
                    Pastikan format dan kolom file sudah sesuai dengan ketentuan sistem.
                </p>
            </div>
        </div>
    </div>

    {{-- Form Upload --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="p-5 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="bg-amber-100 text-amber-800 p-3 rounded-xl">
                    <i data-lucide="file-up" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-slate-800">Upload File Data</h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Format file yang didukung: CSV dan TXT.
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.import.store') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="p-6 space-y-6">

                {{-- File Kurir --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        File Data Kurir
                    </label>

                    <div class="border border-dashed border-slate-300 rounded-xl p-5 hover:border-amber-500 transition">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-blue-50 text-blue-700 p-3 rounded-xl">
                                <i data-lucide="truck" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 text-sm">Data Kurir</p>
                                <p class="text-xs text-slate-500">
                                    Pilih file CSV berisi informasi kurir.
                                </p>
                            </div>
                        </div>

                        <input type="file"
                            name="kurir"
                            accept=".csv,.txt"
                            class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-amber-100 file:text-amber-800 file:font-semibold hover:file:bg-amber-200">
                    </div>
                </div>

                {{-- File Pelanggan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        File Data Pelanggan
                    </label>

                    <div class="border border-dashed border-slate-300 rounded-xl p-5 hover:border-amber-500 transition">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-green-50 text-green-700 p-3 rounded-xl">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 text-sm">Data Pelanggan</p>
                                <p class="text-xs text-slate-500">
                                    Pilih file CSV berisi informasi pelanggan.
                                </p>
                            </div>
                        </div>

                        <input type="file"
                            name="pelanggan"
                            accept=".csv,.txt"
                            class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-amber-100 file:text-amber-800 file:font-semibold hover:file:bg-amber-200">
                    </div>
                </div>

                {{-- File Paket --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        File Paket Historis
                    </label>

                    <div class="border border-dashed border-slate-300 rounded-xl p-5 hover:border-amber-500 transition">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-purple-50 text-purple-700 p-3 rounded-xl">
                                <i data-lucide="package" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 text-sm">Data Paket Historis</p>
                                <p class="text-xs text-slate-500">
                                    Pilih file CSV berisi riwayat paket.
                                </p>
                            </div>
                        </div>

                        <input type="file"
                            name="paket"
                            accept=".csv,.txt"
                            class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-amber-100 file:text-amber-800 file:font-semibold hover:file:bg-amber-200">
                    </div>
                </div>

            </div>

            {{-- Tombol --}}
            <div class="bg-slate-50 border-t border-slate-200 p-5 flex flex-col sm:flex-row sm:justify-end gap-3">
                <button type="reset"
                    class="px-5 py-2.5 border border-slate-300 text-slate-600 hover:bg-slate-100 rounded-xl text-sm font-semibold transition">
                    Reset Form
                </button>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-800 hover:bg-amber-900 text-white rounded-xl text-sm font-semibold transition">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    Mulai Impor
                </button>
            </div>

        </form>
    </div>

    {{-- Laporan Import --}}
    @if (session('import_report'))
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="p-5 border-b border-slate-200 flex items-center gap-3">
                <div class="bg-green-100 text-green-700 p-3 rounded-xl">
                    <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-slate-800">Laporan Hasil Impor</h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Ringkasan proses impor data.
                    </p>
                </div>
            </div>

            <div class="p-5 space-y-3">
                @foreach (session('import_report') as $hasil)
                    <div class="flex items-start gap-3 bg-slate-50 rounded-xl p-4">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 shrink-0"></i>
                        <p class="text-sm text-slate-700">{{ $hasil }}</p>
                    </div>
                @endforeach
            </div>

        </div>
    @endif

</div>

<script>
    if (window.lucide) lucide.createIcons();
</script>
@endsection
```