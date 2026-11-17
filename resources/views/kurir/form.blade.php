```php
@extends('layout')

@section('isi')
    <div class="max-w-2xl mx-auto space-y-6">

        <div>
            <p class="text-sm text-amber-700 font-medium mb-1">Manajemen Pengguna</p>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Kurir</h1>
            <p class="text-sm text-slate-500 mt-1">
                Buat akun baru untuk petugas kurir Priangan Kilat Express.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl">
                <p class="font-semibold mb-1">Periksa kembali data berikut:</p>
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/kurir') }}"
            class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            @csrf

            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-amber-100 text-amber-700 rounded-xl">
                        <i data-lucide="user-plus" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Informasi Kurir</h2>
                        <p class="text-xs text-slate-500">Lengkapi identitas akun kurir.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Lengkap
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama kurir"
                        required
                        class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Nomor Telepon
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Masukkan nomor telepon"
                        required
                        class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Password Sementara
                    </label>
                    <input type="password" name="password" placeholder="Minimal 8 karakter" minlength="8" required
                        class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <p class="text-xs text-slate-400 mt-2">
                        Kurir dapat menggunakan password ini untuk login pertama kali.
                    </p>
                </div>

            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 bg-slate-50 border-t border-slate-100 p-5">
                <a href="{{ url('/kurir') }}"
                    class="px-5 py-2.5 text-center text-sm font-medium text-slate-600 border border-slate-300 rounded-xl hover:bg-slate-100">
                    Batal
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-800 hover:bg-amber-900 text-white text-sm font-semibold rounded-xl transition">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Kurir
                </button>
            </div>
        </form>
    </div>

    <script>
        if (window.lucide) lucide.createIcons();
    </script>
@endsection
```
