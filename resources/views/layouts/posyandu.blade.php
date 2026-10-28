
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Posyandu-In | @yield('title', 'Posyandu')</title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- Ikon --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: [
                            '"Plus Jakarta Sans"',
                            'ui-sans-serif',
                            'system-ui',
                            'sans-serif'
                        ],
                    },
                    colors: {
                        primary: '#155DFC',
                        softbg: '#F4F6FB',
                        line: '#E5E7EB',
                    },
                    boxShadow: {
                        card: '0 10px 30px rgba(0,0,0,.04)',
                    },
                    borderRadius: {
                        xl3: '24px',
                    },
                },
            },
        };
    </script>

    {{-- Gaya global dan sidebar --}}
    <style>
        body {
            font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #1E293B;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 10px;
        }

        #appShell {
            transition: gap 0.3s ease;
        }

        #sidebar {
            width: 18rem;
            flex-shrink: 0;
            transition: width 0.3s ease, padding 0.3s ease;
        }

        #sidebar .brand-name,
        #sidebar .nav-label,
        #sidebar .logout-label,
        #sidebar .unit-info {
            transition: opacity 0.2s ease;
        }

        #sidebar .brand-mark {
            display: none;
        }

        /* Sidebar diperkecil */
        #appShell.sidebar-collapsed #sidebar {
            width: 5.5rem;
            padding: 1rem;
        }

        #appShell.sidebar-collapsed #sidebar .brand-name,
        #appShell.sidebar-collapsed #sidebar .brand-mark,
        #appShell.sidebar-collapsed #sidebar .nav-label,
        #appShell.sidebar-collapsed #sidebar .logout-label,
        #appShell.sidebar-collapsed #sidebar .nav-section-label,
        #appShell.sidebar-collapsed #sidebar .unit-info {
            display: none !important;
        }

        #appShell.sidebar-collapsed #sidebar .brand-mark {
            display: flex !important;
        }

        #appShell.sidebar-collapsed #sidebar .brand-section {
            justify-content: center;
            margin-bottom: 2rem;
        }


#appShell.sidebar-collapsed #sidebar nav a,
#appShell.sidebar-collapsed #sidebar .logout-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-width: 0;
    box-sizing: border-box;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    gap: 0;
}

#appShell.sidebar-collapsed #sidebar .logout-button svg {
    display: block;
    width: 1.25rem;
    height: 1.25rem;
    flex-shrink: 0;
}

#appShell.sidebar-collapsed #sidebar .logout-label {
    display: none !important;
}


        #appShell.sidebar-collapsed #sidebar .sidebar-active-indicator {
            display: none;
        }

        #appShell.sidebar-collapsed #sidebar nav a:hover {
            color: #0A63D8;
            background-color: #EBF3FF;
        }

        #sidebar-toggle {
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            #appShell {
                gap: 1rem;
                padding: 1rem;
            }

            #sidebar {
                width: 5rem;
                padding: 0.75rem;
            }

            #sidebar .brand-name,
            #sidebar .nav-label,
            #sidebar .logout-label,
            #sidebar .nav-section-label,
            #sidebar .unit-info {
                display: none;
            }

            #sidebar .brand-mark {
                display: flex;
            }

            #sidebar .brand-section {
                justify-content: center;
                margin-bottom: 1.5rem;
            }

            #sidebar nav a,
            #sidebar .logout-button {
                justify-content: center;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            #mainContent {
                padding: 1.25rem;
                border-radius: 1.5rem;
            }

            #sidebar-toggle {
                display: none;
            }

            #sidebar .logout-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-width: 0;
    gap: 0;
}

#sidebar .logout-button svg {
    display: block;
    flex-shrink: 0;
}

#sidebar .logout-label {
    display: none !important;
}
        }
    </style>

    @stack('head')
    @stack('styles')
</head>

<body class="bg-softbg text-slate-800 font-sans">

    <div id="appShell" class="min-h-screen bg-[#F3F4F9] p-6 flex gap-6">

        {{-- Sidebar --}}
        <aside
            id="sidebar"
            class="min-w-0 bg-white rounded-[2rem] shadow-xl shadow-slate-200/50
                   flex flex-col p-8 sticky top-6 h-[calc(100vh-3rem)]
                   transition-all duration-300">

            {{-- Brand --}}
            <div class="brand-section flex items-center justify-between gap-2 mb-8">

                <h1 class="brand-name text-xl font-extrabold text-[#155DFC] tracking-tight">
                    Posyandu-In
                </h1>

                <span
                    class="brand-mark items-center justify-center
                           w-10 h-10 rounded-xl bg-[#155DFC]
                           text-white font-extrabold">
                    P
                </span>

                <button
                    type="button"
                    id="sidebar-toggle"
                    aria-label="Perkecil sidebar"
                    aria-expanded="true"
                    title="Perkecil sidebar"
                    class="flex items-center justify-center w-9 h-9
                           rounded-xl text-slate-500 hover:bg-[#EBF3FF]
                           hover:text-[#155DFC] transition-colors">

                    <svg
                        id="sidebar-toggle-icon"
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">

                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <path d="M9 3v18"/>
                    </svg>
                </button>
            </div>


            <nav class="flex-1 space-y-3" aria-label="Navigasi utama">

            <div class="relative group">
    @if (request()->routeIs('posyandu.dashboard'))
        <div class="sidebar-active-indicator absolute -left-8 top-1/2
                    -translate-y-1/2 w-2 h-10
                    bg-[#0A63D8] rounded-r-full"></div>
    @endif

    <a href="{{ route('posyandu.dashboard') }}"
       title="Dashboard"
       @if (request()->routeIs('posyandu.dashboard')) aria-current="page" @endif
       class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300
       {{ request()->routeIs('posyandu.dashboard')
           ? 'bg-[#EBF3FF] text-[#0A63D8]'
           : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">

        <i class="fa-solid fa-house w-5 shrink-0 text-center" aria-hidden="true"></i>

        <span class="nav-label whitespace-nowrap text-sm font-bold">
            Dashboard
        </span>
    </a>
</div>
            
            {{-- Data Anak --}}
                <div class="relative group">
                    @if (request()->routeIs('posyandu.anak.*'))
                        <div
                            class="sidebar-active-indicator absolute -left-8 top-1/2
                                   -translate-y-1/2 w-2 h-10
                                   bg-[#0A63D8] rounded-r-full">
                        </div>
                    @endif

                    <a
                        href="{{ route('posyandu.anak.index') }}"
                        @if (request()->routeIs('posyandu.anak.*'))
                            aria-current="page"
                        @endif
                        title="Kelola Data Anak"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl
                               transition-all duration-300
                               {{ request()->routeIs('posyandu.anak.*')
                                   ? 'bg-[#EBF3FF] text-[#0A63D8]'
                                   : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>
                            <path d="M9 14h6"/>
                        </svg>

                        <span class="nav-label font-bold text-sm whitespace-nowrap">
                            Kelola Data Anak
                        </span>
                    </a>
                </div>

                {{-- Pemeriksaan --}}
                <div class="relative group">
                    @if (request()->routeIs('posyandu.pemeriksaan.*'))
                        <div
                            class="sidebar-active-indicator absolute -left-8 top-1/2
                                   -translate-y-1/2 w-2 h-10
                                   bg-[#0A63D8] rounded-r-full">
                        </div>
                    @endif

                    <a
                        href="{{ route('posyandu.pemeriksaan.index') }}"
                        @if (request()->routeIs('posyandu.pemeriksaan.*'))
                            aria-current="page"
                        @endif
                        title="Input Data Pemeriksaan"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl
                               transition-all duration-300
                               {{ request()->routeIs('posyandu.pemeriksaan.*')
                                   ? 'bg-[#EBF3FF] text-[#0A63D8]'
                                   : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="4" y="3" width="16" height="18" rx="2"/>
                            <path d="M8 8h8M8 12h3M8 16h3"/>
                            <path d="M16 12v4M14 14h4"/>
                        </svg>

                        <span class="nav-label font-bold text-sm whitespace-nowrap">
                            Input Data Pemeriksaan
                        </span>
                    </a>
                </div>

                {{-- Jadwal --}}
                <div class="relative group">
                    @if (request()->routeIs('posyandu.jadwal.*'))
                        <div
                            class="sidebar-active-indicator absolute -left-8 top-1/2
                                   -translate-y-1/2 w-2 h-10
                                   bg-[#0A63D8] rounded-r-full">
                        </div>
                    @endif

                    <a
                        href="{{ route('posyandu.jadwal.index') }}"
                        @if (request()->routeIs('posyandu.jadwal.*'))
                            aria-current="page"
                        @endif
                        title="Kelola Jadwal Posyandu"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl
                               transition-all duration-300
                               {{ request()->routeIs('posyandu.jadwal.*')
                                   ? 'bg-[#EBF3FF] text-[#0A63D8]'
                                   : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path d="M16 3v4M8 3v4M3 11h18"/>
                            <path d="m9 16 2 2 4-4"/>
                        </svg>

                        <span class="nav-label font-bold text-sm whitespace-nowrap">
                            Kelola Jadwal Posyandu
                        </span>
                    </a>
                </div>

                {{-- Laporan --}}
                <div class="relative group">
                    @if (request()->routeIs('posyandu.laporan.*'))
                        <div
                            class="sidebar-active-indicator absolute -left-8 top-1/2
                                   -translate-y-1/2 w-2 h-10
                                   bg-[#0A63D8] rounded-r-full">
                        </div>
                    @endif

                    <a
                        href="{{ route('posyandu.laporan.index') }}"
                        @if (request()->routeIs('posyandu.laporan.*'))
                            aria-current="page"
                        @endif
                        title="Laporan Bulanan"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl
                               transition-all duration-300
                               {{ request()->routeIs('posyandu.laporan.*')
                                   ? 'bg-[#EBF3FF] text-[#0A63D8]'
                                   : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M3 3v18h18"/>
                            <path d="M8 16V9M13 16V5M18 16v-5"/>
                        </svg>

                        <span class="nav-label font-bold text-sm whitespace-nowrap">
                            Laporan Bulanan
                        </span>
                    </a>
                </div>

            </nav>

            {{-- Informasi unit --}}
            <div
                class="unit-info mt-4 rounded-2xl border border-slate-100
                       bg-slate-50 p-4">

                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Unit Posyandu
                </p>

                <p class="mt-1 truncate text-sm font-bold text-slate-700">
                    {{ auth()->user()->posyandu?->nama_posyandu ?? 'Unit belum tersedia' }}
                </p>

                @if (auth()->user()->posyandu?->desa_kelurahan)
                    <p class="mt-1 truncate text-xs text-slate-500">
                        {{ auth()->user()->posyandu->desa_kelurahan }}
                    </p>
                @endif
            </div>

            {{-- Logout --}}
            <div class="pt-4 mt-4 border-t border-slate-100">
                <form action="{{ route('posyandu.logout') }}" method="POST">
                    @csrf

                    <button
                        type="submit"
                        title="Keluar"
                        class="logout-button w-full flex items-center gap-4 px-6 py-4
                               text-slate-400 hover:text-red-500
                               hover:bg-red-50 transition-all duration-300
                               font-bold text-sm rounded-xl">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" x2="9" y1="12" y2="12"/>
                        </svg>

                        <span class="logout-label">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Konten utama --}}
        <main
            id="mainContent"
            class="flex-1 min-w-0 bg-white rounded-[2rem]
                   shadow-sm border border-slate-100 p-8 overflow-y-auto">

            @yield('content')
        </main>

    </div>

    @stack('scripts')

    {{-- Interaksi sidebar --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const appShell = document.getElementById('appShell');
            const toggleButton = document.getElementById('sidebar-toggle');
            const toggleIcon = document.getElementById('sidebar-toggle-icon');

            if (!appShell || !toggleButton || !toggleIcon) {
                return;
            }

            // Gunakan penyimpanan terpisah dari Super Admin.
            const storageKey = 'posyandu-sidebar';

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

                // Beri kesempatan kalender atau komponen lain menyesuaikan ukuran.
                window.setTimeout(function () {
                    window.dispatchEvent(new Event('sidebar:changed'));
                }, 350);

                try {
                    localStorage.setItem(
                        storageKey,
                        collapsed ? 'collapsed' : 'expanded'
                    );
                } catch (error) {
                    // Sidebar tetap berfungsi tanpa localStorage.
                }
            }

            let initiallyCollapsed = false;

            try {
                initiallyCollapsed =
                    localStorage.getItem(storageKey) === 'collapsed';
            } catch (error) {
                // Gunakan sidebar terbuka jika penyimpanan tidak tersedia.
            }

            setSidebarState(initiallyCollapsed);

            toggleButton.addEventListener('click', function () {
                const isCollapsed =
                    appShell.classList.contains('sidebar-collapsed');

                setSidebarState(!isCollapsed);
            });
        });
    </script>
</body>

</html>