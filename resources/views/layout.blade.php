
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Priangan Kilat Express')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

    {{-- HEADER --}}
    <header class="fixed inset-x-0 top-0 z-50 h-16 border-b bg-white">
        <div class="flex h-full items-center justify-between px-5">

            <div class="flex items-center gap-3">
                <button id="menuBtn" class="rounded-lg p-2 hover:bg-amber-50"
                        aria-label="Buka menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl
                                bg-amber-800 font-bold text-white">PK</div>
                    <div>
                        <p class="font-bold text-slate-900">Priangan Kilat Express</p>
                        <p class="text-xs text-slate-500">Sistem Informasi Logistik</p>
                    </div>
                </a>
            </div>

            @auth
                <div class="flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs capitalize text-slate-500">
                            {{ auth()->user()->role }}
                        </p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-full
                                bg-amber-100 font-bold text-amber-800">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            @else
                <a href="{{ url('/login') }}"
                   class="rounded-lg bg-amber-800 px-4 py-2 text-sm text-white">
                    Masuk Staf
                </a>
            @endauth
        </div>
    </header>

    {{-- OVERLAY --}}
    <div id="overlay" class="fixed inset-0 z-30 hidden bg-slate-900/40"></div>

    {{-- SIDEBAR --}}
    <aside id="sidebar"
        class="fixed bottom-0 left-0 top-16 z-40 w-72 -translate-x-full
               overflow-y-auto border-r border-amber-100 bg-white
               shadow-xl transition-transform duration-300">

        <div class="flex items-center justify-between border-b p-5">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                Menu Navigasi
            </p>

            <button id="lockBtn"
                class="rounded-lg border px-3 py-2 text-xs font-semibold
                       text-slate-600 hover:bg-amber-50">
                Lock
            </button>
        </div>

        <nav class="space-y-2 p-5">
            @auth
                @php $role = auth()->user()->role; @endphp

                @if ($role === 'admin')
                    @foreach ([
                        'Paket' => url('/paket'),
                        'Input Paket' => url('/paket/create'),
                        'Setoran COD' => url('/cod'),
                        'Pelanggan' => url('/pelanggan'),
                        'Kurir' => url('/kurir'),
                        'Import Data' => route('admin.import.index'),
                    ] as $label => $url)
                        <a href="{{ $url }}"
                           class="block rounded-xl px-4 py-3 text-sm font-medium
                                  hover:bg-amber-50 hover:text-amber-800">
                            {{ $label }}
                        </a>
                    @endforeach
                @elseif ($role === 'manajer')
                    <a href="{{ url('/paket') }}"
                       class="block rounded-xl px-4 py-3 text-sm hover:bg-amber-50">
                        Daftar Paket
                    </a>
                    <a href="{{ url('/laporan') }}"
                       class="block rounded-xl px-4 py-3 text-sm hover:bg-amber-50">
                        Laporan
                    </a>
                @endif

                @if (in_array($role, ['admin', 'manajer']))
                    <a href="{{ url('/tarif') }}"
                       class="block rounded-xl px-4 py-3 text-sm hover:bg-amber-50">
                        Tarif
                    </a>
                    <a href="{{ route('akun.password') }}"
                       class="block rounded-xl px-4 py-3 text-sm hover:bg-amber-50">
                        Pengaturan Akun
                    </a>
                @endif

                <div class="my-4 border-t"></div>

                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-xl px-4 py-3 text-left text-sm
                               text-red-600 hover:bg-red-50">
                        Keluar dari Akun
                    </button>
                </form>
            @else
                <a href="{{ url('/cek-resi') }}"
                   class="block rounded-xl px-4 py-3 text-sm hover:bg-amber-50">
                    Cek Resi
                </a>
                <a href="{{ url('/login') }}"
                   class="block rounded-xl px-4 py-3 text-sm hover:bg-amber-50">
                    Masuk Staf
                </a>
            @endauth
        </nav>

        <div class="border-t p-5 text-center text-xs text-slate-400">
            © {{ date('Y') }} Priangan Kilat Express
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main id="mainContent"
        class="mx-auto min-h-[calc(100vh-64px)] max-w-7xl px-5 pb-8 pt-24 transition-all duration-300 lg:px-8">

        @hasSection('header')
            <div class="mb-7">@yield('header')</div>
        @endif

        @if (session('ok'))
            <div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                <b>Berhasil:</b> {{ session('ok') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <b>Gagal:</b> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="mb-2 font-semibold">Periksa kembali data:</p>
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('isi')
    </main>

    {{-- FOOTER --}}
    <footer class="border-t bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap justify-between gap-2
                    px-5 py-5 text-xs text-slate-500 lg:px-8">
            <p>© {{ date('Y') }} Priangan Kilat Express</p>
            <p>Sistem Informasi Pengelolaan Logistik</p>
        </div>
    </footer>

    {{-- SIDEBAR SCRIPT --}}
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const menuBtn = document.getElementById('menuBtn');
        const lockBtn = document.getElementById('lockBtn');
        const main = document.getElementById('mainContent');

        let locked = localStorage.getItem('sidebarLocked') === 'true';
        let opened = locked;

        function updateSidebar() {
            sidebar.classList.toggle('translate-x-0', opened);
            sidebar.classList.toggle('-translate-x-full', !opened);
            overlay.classList.toggle('hidden', !opened || locked);

            main.classList.toggle('lg:ml-72', opened && locked);
            main.classList.toggle('lg:max-w-none', opened && locked);

            lockBtn.textContent = locked ? 'Locked' : 'Lock';
            lockBtn.classList.toggle('bg-amber-100', locked);
            lockBtn.classList.toggle('text-amber-800', locked);
        }

        menuBtn.addEventListener('click', () => {
            opened = !opened;
            updateSidebar();
        });

        lockBtn.addEventListener('click', () => {
            locked = !locked;
            opened = locked;
            localStorage.setItem('sidebarLocked', locked);
            updateSidebar();
        });

        overlay.addEventListener('click', () => {
            opened = false;
            updateSidebar();
        });

        updateSidebar();
    </script>

</body>
</html>
