<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Posyandu-In | @yield('title', 'Dashboard Orang Tua')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <style>
        /* =========================================
   GLOBAL
========================================= */
        body {
            font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #1E293B;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        * {
            box-sizing: border-box;
        }

        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 10px;
        }

        /* =========================================
   APP SHELL
========================================= */
        #appShell {
            display: flex;
            align-items: flex-start;
            gap: 2rem;
            min-height: 100vh;
            padding: 2rem;
            background: #F4F6FB;
            transition: gap .3s ease;
        }

        /* =========================================
   SIDEBAR
========================================= */
        #sidebar {
            display: flex;
            flex-direction: column;
            flex: 0 0 18rem;
            width: 18rem;
            min-width: 0;
            height: calc(100vh - 4rem);
            position: sticky;
            top: 2rem;
            padding: 2rem;
            overflow: visible;
            background: #FFFFFF;
            border-radius: 2rem;
            box-shadow: 0 20px 35px rgba(148, 163, 184, .12);
            transition:
                width .3s ease,
                flex-basis .3s ease,
                padding .3s ease;
        }

        /* Bagian logo dan tombol toggle */
        #sidebar .brand-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            margin-bottom: 2rem;
            min-height: 2.25rem;
        }

        #sidebar .brand-name {
            margin: 0;
            color: #155DFC;
            white-space: nowrap;
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -.025em;
            transition: opacity .2s ease;
        }

        #sidebar .brand-mark {
            display: none;
        }

        /* Tombol perkecil/perbesar sidebar */
        #sidebar-toggle {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            border: 0;
            border-radius: .75rem;
            color: #64748B;
            background: transparent;
            cursor: pointer;
            transition: background .2s ease, color .2s ease;
        }

        #sidebar-toggle:hover {
            color: #155DFC;
            background: #EBF3FF;
        }

        #sidebar-toggle svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* =========================================
   NAVIGATION
========================================= */
        #sidebar nav {
            flex: 1 1 auto;
            min-height: 0;
            overflow: visible;
            scrollbar-width: none;
        }


        /* Pembungkus setiap item navigasi */
        #sidebar nav .nav-item {
            position: relative;
            overflow: visible;
        }

        /* Tautan menu dasar */
        #sidebar nav a {
            position: relative;
            display: flex;
            align-items: center;
            gap: 1rem;
            width: 100%;
            min-width: 0;
            padding: 1rem 1.5rem;
            border: 0;
            border-radius: 1rem;
            background: transparent;
            color: #94A3B8;
            font-size: .875rem;
            font-weight: 700;
            text-decoration: none;
            transition: color .2s ease, background-color .2s ease;
        }

        /* Ikon menu */
        #sidebar nav a svg {
            display: block;
            flex-shrink: 0;
            width: 1.25rem;
            height: 1.25rem;
            color: currentColor;
        }

        /* Label menu */
        #sidebar .nav-label,
        #sidebar .logout-label {
            white-space: nowrap;
            transition: opacity .2s ease;
        }

        /* Hover ketika sidebar diperbesar:
   hanya warna teks dan ikon yang berubah */
        #sidebar nav a:not(.active-link):hover {
            color: #64748B;
            background: transparent;
        }

        /* Menu aktif */
        #sidebar nav a.active-link {
            color: #0A63D8;
            background: #EBF3FF;
        }

        /* Indikator aktif di luar sisi kiri sidebar */
        #sidebar .sidebar-active-indicator {
            position: absolute;
            z-index: 10;
            left: -2rem;
            top: 50%;
            width: .5rem;
            height: 2.5rem;
            transform: translateY(-50%);
            border-radius: 0 9999px 9999px 0;
            background: #0A63D8;
            display: block;
            pointer-events: none;
        }

        /* Hilangkan semua indikator tambahan berbasis pseudo-element */
        #sidebar nav a::before,
        #sidebar nav a::after {
            content: none !important;
            display: none !important;
        }

        /* =========================================
   LOGOUT
========================================= */
        #sidebar .logout-section {
            flex-shrink: 0;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            border-top: 1px solid #F1F5F9;
        }

        #sidebar .logout-button {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 1rem;
            width: 100%;
            min-width: 0;
            padding: 1rem 1.5rem;
            border: 0;
            border-radius: .75rem;
            color: #94A3B8;
            background: transparent;
            font-size: .875rem;
            font-weight: 700;
            text-align: left;
            cursor: pointer;
            transition: color .2s ease, background-color .2s ease;
        }

        #sidebar .logout-button:hover {
            color: #EF4444;
            background: transparent;
        }

        #sidebar .logout-button svg {
            display: block;
            flex-shrink: 0;
            width: 1.25rem;
            height: 1.25rem;
        }

        /* =========================================
   SIDEBAR COLLAPSED
========================================= */
        #appShell.sidebar-collapsed #sidebar {
            flex: 0 0 5.5rem;
            width: 5.5rem;
            padding: 1rem;
        }

        /* Hilangkan logo teks dan label, bukan tombol toggle */
        #appShell.sidebar-collapsed #sidebar .brand-name,
        #appShell.sidebar-collapsed #sidebar .nav-label,
        #appShell.sidebar-collapsed #sidebar .logout-label,
        #appShell.sidebar-collapsed #sidebar .brand-mark {
            display: none !important;
        }

        /* Tombol toggle tetap terlihat di tengah */
        #appShell.sidebar-collapsed #sidebar .brand-section {
            justify-content: center;
            gap: 0;
            margin-bottom: 2rem;
        }

        /* Ikon menu berada di tengah */
        #appShell.sidebar-collapsed #sidebar nav a,
        #appShell.sidebar-collapsed #sidebar .logout-button {
            justify-content: center;
            gap: 0;
            padding: 1rem .75rem;
        }

        /* Indikator aktif tetap terlihat di luar sidebar */
        #appShell.sidebar-collapsed #sidebar .sidebar-active-indicator {
            display: none !important;
        }

        /* Hover ikon saat sidebar diperkecil */
        #appShell.sidebar-collapsed #sidebar nav a:not(.active-link):hover {
            color: #0A63D8;
            background: #EBF3FF;
        }

        /* Jangan beri latar merah pada logout saat sidebar kecil */
        #appShell.sidebar-collapsed #sidebar .logout-button:hover {
            color: #EF4444;
            background: transparent;
        }

        /* =========================================
   MAIN CONTENT
========================================= */
        #mainContent {
            flex: 1 1 0%;
            min-width: 0;
            padding: 2rem;
            background: #FFFFFF;
            border: 1px solid #F1F5F9;
            border-radius: 2rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .03);
        }

        /* =========================================
   TABLET
========================================= */
        @media (max-width: 768px) {
            #appShell {
                gap: 1rem;
                padding: 1rem;
            }

            #sidebar {
                flex: 0 0 5rem;
                width: 5rem;
                padding: .75rem;
                border-radius: 1.5rem;
            }

            #sidebar .brand-name,
            #sidebar .nav-label,
            #sidebar .logout-label,
            #sidebar .brand-mark {
                display: none !important;
            }

            #sidebar .brand-section {
                justify-content: center;
                gap: 0;
                margin-bottom: 1.5rem;
            }

            #sidebar nav a,
            #sidebar .logout-button {
                justify-content: center;
                gap: 0;
                padding: .75rem .5rem;
            }

            #sidebar .sidebar-active-indicator {
                left: -.75rem;
            }

            #appShell.sidebar-collapsed #sidebar {
                flex-basis: 4.5rem;
                width: 4.5rem;
            }

            #appShell.sidebar-collapsed #sidebar .sidebar-active-indicator {
                left: -.75rem;
            }

            #mainContent {
                padding: 1.25rem;
                border-radius: 1.5rem;
            }
        }

        /* =========================================
   SMALL MOBILE
========================================= */
        @media (max-width: 480px) {
            #appShell {
                gap: .65rem;
                padding: .65rem;
            }

            #sidebar {
                flex-basis: 3.75rem;
                width: 3.75rem;
                padding: .5rem;
                border-radius: 1rem;
            }

            #sidebar .brand-section {
                margin-bottom: 1rem;
            }

            #sidebar nav .nav-item {
                margin-bottom: .5rem;
            }

            #sidebar nav a,
            #sidebar .logout-button {
                justify-content: center;
                gap: 0;
                padding: .65rem .35rem;
            }

            #sidebar .sidebar-active-indicator {
                left: -.5rem;
                width: .25rem;
                height: 2rem;
            }

            #appShell.sidebar-collapsed #sidebar {
                flex-basis: 3.75rem;
                width: 3.75rem;
                padding: .5rem;
            }

            #appShell.sidebar-collapsed #sidebar .sidebar-active-indicator {
                left: -.5rem;
            }

            #mainContent {
                padding: .9rem;
                border-radius: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans">
    <div id="appShell">


        {{-- Sidebar --}}
        <aside id="sidebar" class="w-72 shrink-0 bg-white rounded-[2rem] shadow-xl shadow-slate-200/50
           flex flex-col p-8 sticky top-6 h-[calc(100vh-3rem)]
           transition-all duration-300">

            {{-- Logo --}}
            <div class="brand-section mb-8 flex min-h-10 items-center justify-between gap-2">
                <a href="{{ route('orang-tua.dashboard') }}"
                    class="brand-name whitespace-nowrap text-xl font-extrabold tracking-tight text-[#155DFC]">
                    Posyandu-In
                </a>

                <button type="button" id="sidebar-toggle" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl
                   text-slate-500 transition-colors hover:bg-[#EBF3FF]
                   hover:text-[#155DFC]" aria-label="Perkecil sidebar" aria-expanded="true" title="Perkecil sidebar">

                    <svg id="sidebar-toggle-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3" />
                        <path d="M9 3v18" />
                    </svg>
                </button>
            </div>


            <nav class="flex-1 space-y-3">


                <div class="nav-item">
                    @if(request()->routeIs('orang-tua.dashboard'))
                        <div class="sidebar-active-indicator"></div>
                    @endif

                    <a href="{{ route('orang-tua.dashboard') }}"
                        class="{{ request()->routeIs('orang-tua.dashboard') ? 'active-link' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </div>


                {{-- Perkembangan Anak --}}
                <div class="relative group">
                    <a href="{{ route('orang-tua.dashboard') }}#perkembangan-anak" class="flex items-center gap-4 rounded-2xl px-6 py-4
                  text-slate-400 transition-all duration-300
                  hover:bg-slate-50 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 14l4-4 4 4 6-7" />
                        </svg>
                        <span class="nav-label text-sm font-bold">Perkembangan Anak</span>
                    </a>
                </div>

                {{-- Profil Anak --}}
                <div class="relative group">
                    <a href="{{ route('orang-tua.dashboard') }}#profil-anak" class="flex items-center gap-4 rounded-2xl px-6 py-4
                  text-slate-400 transition-all duration-300
                  hover:bg-slate-50 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="8" r="4" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 21v-2a8 8 0 0116 0v2" />
                        </svg>
                        <span class="nav-label text-sm font-bold">Profil Anak</span>
                    </a>
                </div>

            </nav>

            {{-- Logout --}}
            <div class="mt-6 shrink-0 border-t border-slate-100 pt-6">
                <form action="{{ route('orang-tua.logout') }}" method="POST">
                    @csrf

                    <button type="submit" title="Keluar" class="logout-button flex w-full items-center gap-4 rounded-xl
                       px-6 py-4 text-sm font-bold text-slate-400
                       transition-all duration-300 hover:bg-red-50 hover:text-red-500">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" x2="9" y1="12" y2="12" />
                        </svg>

                        <span class="logout-label">Logout</span>
                    </button>
                </form>
            </div>
        </aside>


        {{-- Konten halaman --}}
        <main id="mainContent">
            @yield('content')
        </main>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const appShell = document.getElementById('appShell');
            const toggleButton = document.getElementById('sidebar-toggle');
            const toggleIcon = document.getElementById('sidebar-toggle-icon');

            if (!appShell || !toggleButton || !toggleIcon) return;

            const storageKey = 'posyandu-orang-tua-sidebar';

            function setSidebarState(collapsed) {
                appShell.classList.toggle('sidebar-collapsed', collapsed);

                toggleButton.setAttribute('aria-expanded', String(!collapsed));
                toggleButton.setAttribute(
                    'aria-label',
                    collapsed ? 'Perbesar sidebar' : 'Perkecil sidebar'
                );
                toggleButton.setAttribute(
                    'title',
                    collapsed ? 'Perbesar sidebar' : 'Perkecil sidebar'
                );

                toggleIcon.innerHTML = collapsed
                    ? '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M15 3v18"/>'
                    : '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 3v18"/>';

                try {
                    localStorage.setItem(storageKey, collapsed ? 'collapsed' : 'expanded');
                } catch (error) {
                    // Sidebar tetap dapat digunakan tanpa localStorage.
                }

                window.setTimeout(function () {
                    window.dispatchEvent(new Event('sidebar:changed'));
                }, 350);
            }

            let collapsed = false;

            try {
                collapsed = localStorage.getItem(storageKey) === 'collapsed';
            } catch (error) {
                // Gunakan kondisi terbuka sebagai default.
            }

            setSidebarState(collapsed);

            toggleButton.addEventListener('click', function () {
                setSidebarState(!appShell.classList.contains('sidebar-collapsed'));
            });
        });
    </script>

    @stack('scripts')
</body>

</html>