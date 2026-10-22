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
        body {
            font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #1E293B;
            -webkit-font-smoothing: antialiased;
        }

        #appShell {
            display: flex;
            gap: 1.5rem;
            min-height: 100vh;
            padding: 1.5rem;
            background: #F3F4F9;
        }

        #sidebar {
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            width: 18rem;
            height: calc(100vh - 3rem);
            position: sticky;
            top: 1.5rem;
            padding: 2rem;
            background: #fff;
            border-radius: 2rem;
            box-shadow: 0 20px 35px rgba(148, 163, 184, .12);
            transition: width .3s ease, padding .3s ease;
        }

        #mainContent {
            flex: 1;
            min-width: 0;
            padding: 2rem;
            background: #fff;
            border: 1px solid #F1F5F9;
            border-radius: 2rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .03);
        }

        #sidebar .brand-name,
        #sidebar .nav-label,
        #sidebar .logout-label {
            transition: opacity .2s ease;
        }

        #sidebar .brand-mark {
            display: none;
        }

        #appShell.sidebar-collapsed #sidebar {
            width: 5.5rem;
            padding: 1rem;
        }

        #appShell.sidebar-collapsed #sidebar .brand-name,
        #appShell.sidebar-collapsed #sidebar .nav-label,
        #appShell.sidebar-collapsed #sidebar .logout-label {
            display: none;
        }

        #appShell.sidebar-collapsed #sidebar .brand-mark {
            display: flex;
        }

        #appShell.sidebar-collapsed #sidebar>div:first-child {
            justify-content: center;
            margin-bottom: 2rem;
        }

        #appShell.sidebar-collapsed #sidebar nav a,
        #appShell.sidebar-collapsed #sidebar .logout-button {
            justify-content: center;
            padding-left: .75rem;
            padding-right: .75rem;
        }

        #appShell.sidebar-collapsed #sidebar .sidebar-active-indicator {
            display: none;
        }

        #sidebar-toggle {
            flex-shrink: 0;
        }

        #sidebar nav a:hover {
            background: #F8FAFC;
            color: #0A63D8;
        }

        #sidebar nav a.active-link {
            background: #EBF3FF;
            color: #0A63D8;
        }

        @media (max-width: 768px) {
            #appShell {
                gap: 1rem;
                padding: 1rem;
            }

            #sidebar {
                width: 5rem;
                padding: .75rem;
                border-radius: 1.5rem;
            }

            #sidebar .brand-name,
            #sidebar .nav-label,
            #sidebar .logout-label {
                display: none;
            }

            #sidebar .brand-mark {
                display: flex;
            }

            #sidebar>div:first-child {
                justify-content: center;
                margin-bottom: 1.5rem;
            }

            #sidebar nav a,
            #sidebar .logout-button {
                justify-content: center;
                padding-left: .5rem;
                padding-right: .5rem;
            }

            #sidebar-toggle {
                display: none;
            }

            #mainContent {
                padding: 1.25rem;
                border-radius: 1.5rem;
            }

            #appShell.sidebar-collapsed #sidebar {
                width: 4.5rem;
            }
        }

        @media (max-width: 480px) {
            #appShell {
                gap: .65rem;
                padding: .65rem;
            }

            #sidebar {
                width: 3.75rem;
                padding: .5rem;
                border-radius: 1rem;
            }

            #sidebar nav {
                gap: .5rem;
            }

            #sidebar nav a {
                padding: .65rem .35rem;
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
        <aside id="sidebar">

            {{-- Logo --}}
            <div class="mb-8 flex min-h-10 items-center justify-between gap-2">
                <a href="{{ route('orang-tua.dashboard') }}"
                    class="brand-name whitespace-nowrap text-xl font-extrabold tracking-tight text-[#155DFC]">
                    Posyandu-In
                </a>

                <a href="{{ route('orang-tua.dashboard') }}"
                    class="brand-mark h-10 w-10 items-center justify-center rounded-xl bg-[#155DFC] font-extrabold text-white"
                    aria-label="Dashboard Posyandu-In">
                    P
                </a>

                <button type="button" id="sidebar-toggle"
                    class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-[#EBF3FF] hover:text-[#155DFC]"
                    aria-label="Perkecil sidebar" aria-expanded="true" title="Perkecil sidebar">
                    <svg id="sidebar-toggle-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3" />
                        <path d="M9 3v18" />
                    </svg>
                </button>
            </div>

            {{-- Navigasi --}}
            <nav class="flex-1 space-y-2">

                <a href="{{ route('orang-tua.dashboard') }}" title="Dashboard" class="relative flex items-center gap-4 rounded-2xl px-4 py-3.5 font-bold text-sm transition
               {{ request()->routeIs('orang-tua.dashboard') ? 'active-link' : 'text-slate-400' }}">
                    @if(request()->routeIs('orang-tua.dashboard'))
                        <span
                            class="sidebar-active-indicator absolute -left-8 top-1/2 h-9 w-2 -translate-y-1/2 rounded-r-full bg-[#0A63D8]"></span>
                    @endif

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    <span class="nav-label">Dashboard</span>
                </a>

                <a href="{{ route('orang-tua.dashboard') }}#profil-anak" title="Profil Anak"
                    class="flex items-center gap-4 rounded-2xl px-4 py-3.5 font-bold text-sm text-slate-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4" />
                        <path d="M5 21v-2a7 7 0 0 1 14 0v2" />
                    </svg>
                    <span class="nav-label">Profil Anak</span>
                </a>

                <a href="{{ route('orang-tua.dashboard') }}#pemeriksaan" title="Pemeriksaan Terakhir"
                    class="flex items-center gap-4 rounded-2xl px-4 py-3.5 font-bold text-sm text-slate-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 3h8l4 4v14H4V3h4" />
                        <path d="M8 3v5h8V3M8 13h8M8 17h5" />
                    </svg>
                    <span class="nav-label">Pemeriksaan</span>
                </a>

                <a href="{{ route('orang-tua.dashboard') }}#jadwal-posyandu" title="Jadwal Posyandu"
                    class="flex items-center gap-4 rounded-2xl px-4 py-3.5 font-bold text-sm text-slate-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2" />
                        <path d="M16 3v4M8 3v4M3 10h18" />
                    </svg>
                    <span class="nav-label">Jadwal Posyandu</span>
                </a>

                <a href="{{ route('orang-tua.dashboard') }}#prediksi-stunting" title="Prediksi Risiko Stunting"
                    class="flex items-center gap-4 rounded-2xl px-4 py-3.5 font-bold text-sm text-slate-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3 4 7v5c0 5 3.5 7.5 8 9 4.5-1.5 8-4 8-9V7l-8-4Z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                    <span class="nav-label">Prediksi Stunting</span>
                </a>

            </nav>

            {{-- Logout --}}
            <div class="mt-6 border-t border-slate-100 pt-5">
                <form action="{{ route('orang-tua.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar"
                        class="logout-button flex w-full items-center gap-4 rounded-xl px-4 py-3.5 text-sm font-bold text-slate-400 transition hover:bg-red-50 hover:text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" x2="9" y1="12" y2="12" />
                        </svg>
                        <span class="logout-label">Keluar</span>
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