<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Priangan Kilat Express')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800">
    <nav class="bg-amber-800 text-white px-6 py-3 flex flex-wrap gap-4 items-center">
        <b>Priangan Kilat Express</b>
        @auth
            @if (auth()->user()->role === 'admin')
                <a href="{{ url('/paket') }}">Paket</a>
                <a href="{{ url('/paket/create') }}">+ Paket</a>
                <a href="{{ url('/cod') }}">COD</a>
            @else
                <a href="{{ url('/laporan') }}">Laporan</a>
            @endif
            <a href="{{ url('/pelanggan') }}">Pelanggan</a>
            <a href="{{ url('/kurir') }}">Kurir</a>
            <a href="{{ url('/tarif') }}">Tarif</a>
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.import.index') }}">
                    Import Data
                </a>
            @endif
            <form method="POST" action="{{ url('/logout') }}" class="ml-auto">@csrf
                <button>Keluar ({{ auth()->user()->name }})</button>
            </form>
        @else
            <a href="{{ url('/cek-resi') }}">Cek Resi</a>
            <a href="{{ url('/login') }}" class="ml-auto">Masuk Staf</a>
        @endauth
        @auth
            @if (in_array(auth()->user()->role, ['admin', 'manajer']))
                <a href="{{ route('akun.password') }}" class="text-white hover:underline">
                    Pengaturan Akun
                </a>
            @endif
        @endauth
    </nav>

    <main class="p-6 max-w-6xl mx-auto">
        @if (session('ok'))
            <div class="bg-green-600 text-white p-3 mb-4 rounded">{{ session('ok') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-600 text-white p-3 mb-4 rounded">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 p-3 mb-4 rounded">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif
        @yield('isi')
    </main>
</body>

</html>
