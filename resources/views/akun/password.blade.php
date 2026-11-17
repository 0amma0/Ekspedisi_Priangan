@extends('layout')

@section('isi')
    <div class="max-w-xl mx-auto space-y-5">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">Pengaturan Akun</h1>
            <p class="text-sm text-slate-500 mt-1">
                Perbarui password akun untuk menjaga keamanan akses.
            </p>
        </div>

        @if (session('success'))
            <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 p-3 rounded-lg text-sm">
                <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

            <div class="p-5 sm:p-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Ganti Password</h2>
                        <p class="text-xs text-slate-500">
                            Gunakan password baru yang aman.
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('akun.password.update') }}" method="POST" class="p-5 sm:p-6 space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Password Lama
                    </label>
                    <input type="password" name="password_lama" autocomplete="current-password" required
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                    @error('password_lama')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Password Baru
                    </label>
                    <input type="password" name="password_baru" autocomplete="new-password" required
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                    @error('password_baru')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Konfirmasi Password Baru
                    </label>
                    <input type="password" name="password_baru_confirmation" autocomplete="new-password" required
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-3 border-t border-slate-100">
                    <a href="{{ url()->previous() }}"
                        class="text-center border border-slate-300 hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium">
                        Kembali
                    </a>

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 bg-amber-800 hover:bg-amber-900 text-white px-5 py-2.5 rounded-lg text-sm font-medium">
                        <i data-lucide="lock-keyhole" class="w-4 h-4"></i>
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
@endsection
