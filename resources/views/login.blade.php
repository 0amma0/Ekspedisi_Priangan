@extends('layout-login')

@section('judul', 'Login Staf - Priangan Kilat Express')

@section('isi')

    <div class="flex min-h-screen items-center justify-center px-4 py-10 sm:py-14">

        <div
            class="grid w-full max-w-4xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:grid-cols-2">

            {{-- LEFT PANEL --}}
            <div class="hidden flex-col justify-between bg-amber-900 p-9 text-white md:flex">

                <div>
                    <div class="mb-8 flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 text-lg font-bold">
                        PK
                    </div>

                    <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-amber-200">
                        Priangan Kilat Express
                    </p>

                    <h1 class="text-3xl font-bold leading-tight">
                        Sistem Pengelolaan Logistik
                    </h1>

                    <p class="mt-4 text-sm leading-relaxed text-amber-100">
                        Kelola pengiriman, data paket, kurir, dan laporan
                        melalui satu sistem terintegrasi.
                    </p>
                </div>

                <p class="text-xs text-amber-200">
                    © {{ date('Y') }} Priangan Kilat Express
                </p>

            </div>

            {{-- LOGIN FORM --}}
            <div class="p-7 sm:p-10">

                <div class="mb-8">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800">
                        Staff Portal
                    </span>

                    <h2 class="mt-2 text-2xl font-bold text-slate-900">
                        Selamat Datang
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Silakan masuk menggunakan akun staf Anda.
                    </p>
                </div>

                <form method="POST" action="{{ url('/login') }}" class="space-y-5">
                    @csrf

                    {{-- EMAIL --}}
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">
                            Email
                        </label>

                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            placeholder="nama@email.com" autocomplete="username" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-700 focus:bg-white focus:ring-2 focus:ring-amber-100">

                        @error('email')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PASSWORD --}}
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">
                            Password
                        </label>

                        <input id="password" name="password" type="password" placeholder="Masukkan password"
                            autocomplete="current-password" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-700 focus:bg-white focus:ring-2 focus:ring-amber-100">

                        @error('password')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- LOGIN BUTTON --}}
                    <button type="submit"
                        class="w-full rounded-xl bg-amber-800 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-amber-950 focus:outline-none focus:ring-4 focus:ring-amber-100">
                        Masuk ke Sistem
                    </button>

                </form>

                <div class="mt-7 border-t border-slate-100 pt-5 text-center">
                    <a href="{{ url('/cek-resi') }}"
                        class="text-sm font-semibold text-amber-800 transition hover:text-amber-950">
                        Kembali ke halaman Cek Resi
                    </a>
                </div>

            </div>

        </div>

    </div>

@endsection
