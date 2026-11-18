<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Priangan Kilat Express')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --hijau-utama: #92400E;
            --oranye: #D97706;
            --putih-hijau: #FFFBEB;
            --hijau-teks: #1F2937;
            --hijau-muda: #FEF3C7;
            --hijau-border: #E7D5B5;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--putih-hijau) !important;
            color: var(--hijau-teks) !important;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* HEADER */
        .app-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            height: 64px;
            background: var(--hijau-utama) !important;
            border-bottom: 1px solid #451A03;
            color: white !important;
            box-shadow: 0 2px 8px rgba(80, 40, 10, 0.12);
        }

        .header-inner {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        .header-left,
        .brand,
        .header-user {
            display: flex;
            align-items: center;
        }

        .header-left {
            gap: 12px;
        }

        .brand {
            gap: 12px;
            text-decoration: none;
            color: white !important;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: var(--oranye) !important;
            color: white !important;
            font-weight: 800;
            font-size: 16px;
        }

        .brand-title {
            margin: 0;
            color: white !important;
            font-size: 15px;
            font-weight: 700;
        }

        .brand-subtitle {
            margin: 3px 0 0;
            color: #FEF3C7 !important;
            font-size: 11px;
        }

        .menu-button {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 9px;
            border: 0;
            border-radius: 9px;
            background: transparent;
            color: white !important;
            cursor: pointer;
            transition: background 0.2s;
        }

        .menu-button:hover {
            background: rgba(255, 255, 255, 0.13);
        }

        .header-user {
            gap: 12px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            margin: 0;
            color: white !important;
            font-size: 13px;
            font-weight: 700;
        }

        .user-role {
            margin: 3px 0 0;
            color: #FEF3C7 !important;
            font-size: 11px;
            text-transform: capitalize;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            background: var(--oranye) !important;
            color: white !important;
            font-weight: 700;
        }

        .login-button {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 9px;
            background: var(--oranye) !important;
            color: white !important;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.2s;
        }

        .login-button:hover {
            background: #451A03 !important;
        }

        /* OVERLAY */
        #overlay {
            position: fixed;
            inset: 0;
            z-index: 30;
            background: rgba(69, 26, 3, 0.45);
            backdrop-filter: blur(2px);
        }

        /* SIDEBAR */
        #sidebar {
            position: fixed;
            top: 64px;
            bottom: 0;
            left: 0;
            z-index: 40;
            width: 288px;
            overflow-y: auto;
            background: #FFFBEB !important;
            border-right: 1px solid var(--hijau-border);
            box-shadow: 5px 0 20px rgba(80, 40, 10, 0.08);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        #sidebar.open {
            transform: translateX(0);
        }

        .sidebar-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid var(--hijau-border);
            background: white !important;
        }

        .sidebar-heading-title {
            margin: 0;
            color: var(--hijau-utama) !important;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .sidebar-heading-subtitle {
            margin: 5px 0 0;
            color: #A8A29E !important;
            font-size: 11px;
        }

        #lockBtn {
            padding: 8px 13px;
            border: 1px solid #E7D5B5;
            border-radius: 8px;
            background: #FEF3C7 !important;
            color: var(--hijau-utama) !important;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        #lockBtn:hover {
            background: #FDE68A !important;
        }

        #lockBtn.locked {
            background: var(--hijau-utama) !important;
            border-color: var(--hijau-utama);
            color: white !important;
        }

        .sidebar-nav {
            padding: 18px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 7px;
            padding: 12px 14px;
            border: 1px solid transparent;
            border-radius: 10px;
            color: var(--hijau-teks) !important;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .sidebar-nav a:hover {
            background: var(--hijau-muda) !important;
            border-color: var(--hijau-border);
            color: var(--hijau-utama) !important;
        }

        .menu-dot {
            width: 8px;
            height: 8px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #D6C5A8;
            transition: background 0.2s;
        }

        .sidebar-nav a:hover .menu-dot {
            background: var(--oranye);
        }

        .sidebar-divider {
            margin: 18px 0;
            border: 0;
            border-top: 1px solid var(--hijau-border);
        }

        .logout-button {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 12px 14px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: #C24141 !important;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
            transition: background 0.2s;
        }

        .logout-button:hover {
            background: #FEF0F0 !important;
        }

        .sidebar-footer {
            padding: 18px;
            border-top: 1px solid var(--hijau-border);
            background: white !important;
            color: #A8A29E !important;
            font-size: 11px;
            text-align: center;
        }

        /* MAIN CONTENT */
        #mainContent {
            min-height: calc(100vh - 64px);
            max-width: 1280px;
            margin: 0 auto;
            padding: 96px 32px 32px;
            color: var(--hijau-teks) !important;
            transition: margin 0.3s ease, max-width 0.3s ease;
        }

        #mainContent.sidebar-locked {
            max-width: none;
            margin-left: 288px;
        }

        /* NOTIFIKASI */
        .alert-success {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #E7D5B5;
            border-radius: 11px;
            background: #FFFBEB !important;
            color: var(--hijau-utama) !important;
            font-size: 13px;
        }

        .alert-error {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #F0CACA;
            border-radius: 11px;
            background: #FEF0F0 !important;
            color: #B42323 !important;
            font-size: 13px;
        }

        .alert-validation {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #E7D5B5;
            border-radius: 11px;
            background: #FEF3C7 !important;
            color: #92400E !important;
            font-size: 13px;
        }

        /* FOOTER */
        .app-footer {
            border-top: 1px solid var(--hijau-border);
            background: white !important;
        }

        .footer-inner {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px;
            max-width: 1280px;
            margin: auto;
            padding: 20px 32px;
            color: #A8A29E !important;
            font-size: 11px;
        }

        @media (max-width: 640px) {
            .brand-title {
                font-size: 12px;
            }

            .brand-subtitle {
                font-size: 10px;
            }

            .user-info {
                display: none;
            }

            #sidebar {
                width: 280px;
            }

            #mainContent {
                padding: 88px 16px 24px;
            }

            #mainContent.sidebar-locked {
                margin-left: 0;
            }

            .footer-inner {
                padding: 18px 16px;
            }
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <header class="app-header">
        <div class="header-inner">

            <div class="header-left">
                <button id="menuBtn" class="menu-button" aria-label="Buka menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <a href="{{ url('/') }}" class="brand">
                    <div class="brand-logo">PK</div>
                    <div>
                        <p class="brand-title">Priangan Kilat Express</p>
                        <p class="brand-subtitle">Sistem Informasi Logistik</p>
                    </div>
                </a>
            </div>

            @auth
                <div class="header-user">
                    <div class="user-info">
                        <p class="user-name">{{ auth()->user()->name }}</p>
                        <p class="user-role">{{ auth()->user()->role }}</p>
                    </div>

                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            @else
                <a href="{{ url('/login') }}" class="login-button">
                    Masuk Staf
                </a>
            @endauth

        </div>
    </header>

    {{-- OVERLAY --}}
    <div id="overlay" class="hidden"></div>

    {{-- SIDEBAR --}}
    <aside id="sidebar">

        <div class="sidebar-heading">
            <div>
                <p class="sidebar-heading-title">Menu Navigasi</p>
                <p class="sidebar-heading-subtitle">Priangan Kilat Express</p>
            </div>

            <button id="lockBtn" type="button">Lock</button>
        </div>

        <nav class="sidebar-nav">

            @auth
                @php
                    $role = auth()->user()->role;
                @endphp

                {{-- MENU ADMIN --}}
                @if ($role === 'admin')
                    @foreach ([
            'Paket' => url('/paket'),
            'Input Paket' => url('/paket/create'),
            'Setoran COD' => url('/cod'),
            'Pelanggan' => url('/pelanggan'),
            'Kurir' => url('/kurir'),
            'Import Data' => route('admin.import.index'),
            'Tarif' => url('/tarif'),
        ] as $label => $url)
                        <a href="{{ $url }}">
                            <span class="menu-dot"></span>
                            {{ $label }}
                        </a>
                    @endforeach

                    {{-- MENU MANAJER --}}
                @elseif ($role === 'manajer')
                    <a href="{{ url('/pelanggan/') }}">
                        <span class="menu-dot"></span>
                        Data pelanggan
                    </a>

                    {{-- hanya melihat data baik itu pelanggan dan kurir --}}
                    <a href="{{ url('/kurir') }}">
                        <span class="menu-dot"></span>
                        Data kurir
                    </a>

                    <a href="{{ url('/laporan') }}">
                        <span class="menu-dot"></span>
                        Laporan
                    </a>

                    <a href="{{ url('/tarif') }}">
                        <span class="menu-dot"></span>
                        Tarif
                    </a>
                @endif

                {{-- PENGATURAN AKUN --}}
                @if (in_array($role, ['admin', 'manajer']))
                    <hr class="sidebar-divider">

                    <a href="{{ route('akun.password') }}">
                        <span class="menu-dot"></span>
                        Pengaturan Akun
                    </a>
                @endif

                {{-- LOGOUT --}}
                <hr class="sidebar-divider">

                <form method="POST" action="{{ url('/logout') }}">
                    @csrf

                    <button type="submit" class="logout-button">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M15 17l5-5-5-5M20 12H9m4 7v1a2 2 0 01-2 2H5a2 2 0 01-2-2V4a2 2 0 012-2h6a2 2 0 012 2v1" />
                        </svg>
                        Keluar dari Akun
                    </button>
                </form>
            @else
                {{-- MENU PENGUNJUNG --}}
                <a href="{{ url('/cek-resi') }}">
                    <span class="menu-dot"></span>
                    Cek Resi
                </a>

                <a href="{{ url('/login') }}">
                    <span class="menu-dot"></span>
                    Masuk Staf
                </a>

            @endauth

        </nav>

        <div class="sidebar-footer">
            © {{ date('Y') }} Priangan Kilat Express
        </div>

    </aside>

    {{-- MAIN CONTENT --}}
    <main id="mainContent">

        @hasSection('header')
            <div class="mb-7">
                @yield('header')
            </div>
        @endif

        @if (session('ok'))
            <div class="alert-success">
                <b>Berhasil:</b> {{ session('ok') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert-error">
                <b>Gagal:</b> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-validation">
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
    <footer class="app-footer">
        <div class="footer-inner">
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
            sidebar.classList.toggle('open', opened);
            overlay.classList.toggle('hidden', !opened || locked);

            main.classList.toggle('sidebar-locked', opened && locked);

            lockBtn.textContent = locked ? 'Locked' : 'Lock';
            lockBtn.classList.toggle('locked', locked);
        }

        menuBtn.addEventListener('click', () => {
            if (locked) {
                locked = false;
                opened = false;
                localStorage.setItem('sidebarLocked', 'false');
            } else {
                opened = !opened;
            }

            updateSidebar();
        });

        lockBtn.addEventListener('click', () => {
            locked = !locked;
            opened = locked;

            localStorage.setItem('sidebarLocked', locked ? 'true' : 'false');

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
