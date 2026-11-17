@extends('layout')

@section('isi')
    <div class="max-w-2xl mx-auto space-y-5">

        <div>
            <a href="{{ url('/pelanggan') }}"
                class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-amber-800 mb-3">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Pelanggan
            </a>

            <h1 class="text-2xl font-bold text-slate-800">
                {{ $pelanggan->exists ? 'Ubah Pelanggan' : 'Tambah Pelanggan' }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Lengkapi informasi pelanggan dengan benar.
            </p>
        </div>

        <form method="POST" action="{{ $pelanggan->exists ? url('/pelanggan/' . $pelanggan->id) : url('/pelanggan') }}"
            class="bg-white border border-slate-200 rounded-xl overflow-hidden">

            @csrf
            @if ($pelanggan->exists)
                @method('PUT')
            @endif

            <div class="p-5 sm:p-6 space-y-5">

                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center">
                        <i data-lucide="user-round" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Informasi Pelanggan</h2>
                        <p class="text-xs text-slate-500">Data identitas dan kontak pelanggan.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Nama Pelanggan
                    </label>
                    <input name="name" value="{{ old('name', $pelanggan->name) }}" placeholder="Masukkan nama pelanggan"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                    @error('name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Nomor Telepon
                    </label>
                    <input name="phone" value="{{ old('phone', $pelanggan->phone) }}" placeholder="Contoh: 081234567890"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                    @error('phone')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Alamat
                    </label>
                    <textarea name="address" rows="4" placeholder="Masukkan alamat lengkap pelanggan"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">{{ old('address', $pelanggan->address) }}</textarea>

                    @error('address')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div class="bg-slate-50 border-t border-slate-100 p-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <a href="{{ url('/pelanggan') }}"
                    class="text-center border border-slate-300 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium">
                    Batal
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-amber-800 hover:bg-amber-900 text-white px-5 py-2.5 rounded-lg text-sm font-medium">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Pelanggan
                </button>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
@endsection
