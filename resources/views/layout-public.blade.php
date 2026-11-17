<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Priangan Kilat Express')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

    <header class="border-b border-amber-100 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl
                            bg-amber-800 text-lg font-bold text-white">PK</div>
                <div>
                    <p class="font-bold text-slate-900">Priangan Kilat Express</p>
                    <p class="text-xs text-slate-500">Sistem Informasi Logistik</p>
                </div>
            </a>

            <a href="{{ url('/login') }}"
                class="rounded-lg bg-amber-800 px-5 py-2.5 text-sm font-semibold
                       text-white hover:bg-amber-900">
                Masuk Staf
            </a>
        </div>
    </header>

    <main class="mx-auto min-h-[calc(100vh-140px)] max-w-7xl px-5 py-8 lg:px-8">
        @yield('isi')
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-5 py-5 text-xs text-slate-500 lg:px-8">
            © {{ date('Y') }} Priangan Kilat Express
        </div>
    </footer>

</body>
</html>