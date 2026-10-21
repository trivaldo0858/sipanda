<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posyandu-In | @yield('title', 'Dashboard')</title>

    {{-- 1. Import Font Plus Jakarta Sans secara Lengkap --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- 2. Konfigurasi Tailwind untuk Font Global --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        // Menetapkan Plus Jakarta Sans sebagai font default (sans)
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
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
                    }
                }
            }
        }
    </script>

    {{-- 3. CSS Global untuk Halus & Scrollbar --}}
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
            background: #cbd5e1;
            border-radius: 10px;
        }


        /* Sidebar yang dapat diperkecil */
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
        #sidebar .logout-label {
            transition: opacity 0.2s ease;
        }

        #sidebar .brand-mark {
            display: none;
        }

        /* Tampilan sidebar saat minimize */
        #appShell.sidebar-collapsed #sidebar {
            width: 5.5rem;
            padding: 1rem;
        }

        #appShell.sidebar-collapsed #sidebar .brand-name,
        #appShell.sidebar-collapsed #sidebar .brand-mark {
            display: none !important;
        }

        #appShell.sidebar-collapsed #sidebar .nav-label,
        #appShell.sidebar-collapsed #sidebar .logout-label {
            display: none;
        }

        #appShell.sidebar-collapsed #sidebar>div:first-child {
            justify-content: center;
            margin-bottom: 2rem;
            min-height: 0;
        }

        #appShell.sidebar-collapsed #sidebar nav a {
            justify-content: center;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        #appShell.sidebar-collapsed #sidebar .logout-button {
            justify-content: center;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
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
            #sidebar .logout-label {
                display: none;
            }

            #sidebar .brand-mark {
                display: flex;
            }

            #sidebar nav a,
            #sidebar .logout-button {
                justify-content: center;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            #sidebar .brand-name {
                display: none;
            }

            #mainContent {
                padding: 1.25rem;
                border-radius: 1.5rem;
            }
        }

        /* Hilangkan garis indikator ketika sidebar minimize */
        #appShell.sidebar-collapsed .sidebar-active-indicator {
            display: none;
        }

        /* Warna ikon ketika menu di-hover pada sidebar minimize */
        #appShell.sidebar-collapsed #sidebar nav a:hover {
            color: #0A63D8;
            background-color: #EBF3FF;
        }

        /* Jangan tampilkan garis atau dekorasi tambahan pada hover */
        #appShell.sidebar-collapsed #sidebar nav .group::before,
        #appShell.sidebar-collapsed #sidebar nav .group::after {
            display: none;
        }
        
    </style>
    @stack('styles')
</head>

<body class="bg-softbg text-slate-800 font-sans">
    {{-- Sisa kode Container Utama (Sidebar & Content) tetap sama --}}

    <!-- Container Utama (Sidebar & Content) -->
    <div id="appShell" class="min-h-screen bg-[#f3f4f9] p-6 flex gap-6">

        <!-- Sidebar Modern Modern Minimalis -->
        <aside id="sidebar"
            class="w-72 bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 flex flex-col p-8 sticky top-6 h-[calc(100vh-3rem)] transition-all duration-300">

            {{-- Brand Section --}}
            <div class="flex items-center justify-between gap-2 mb-8">
                <h1 class="brand-name text-xl font-extrabold text-[#155DFC] tracking-tight">
                    Posyandu-In
                </h1>

                <span class="brand-mark items-center justify-center
        w-10 h-10 rounded-xl bg-[#155DFC] text-white font-extrabold">
                    P
                </span>

                <button type="button" id="sidebar-toggle" aria-label="Perkecil sidebar" aria-expanded="true"
                    title="Perkecil sidebar" class="flex items-center justify-center w-9 h-9
            rounded-xl text-slate-500 hover:bg-[#EBF3FF]
            hover:text-[#155DFC] transition-colors">

                    <svg id="sidebar-toggle-icon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3" />
                        <path d="M9 3v18" />
                    </svg>
                </button>
            </div>


            {{-- Navigasi Menu --}}
            <nav class="flex-1 space-y-3">

                <!-- Dashboard -->
                <div class="relative group">
                    {{-- Indikator Aktif (Garis Pink di mockup) --}}
                    @if(request()->routeIs('superadmin.dashboard'))
                        <div class="sidebar-active-indicator absolute -left-8 top-1/2
                                                        -translate-y-1/2 w-2 h-10 bg-[#0A63D8] rounded-r-full">
                        </div>
                    @endif

                    <a href="{{ route('superadmin.dashboard') }}"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('superadmin.dashboard') ? 'bg-[#EBF3FF] text-[#0A63D8]' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                        <span class="nav-label font-bold text-sm">Dashboard</span>
                    </a>
                </div>

                <!-- Posyandu -->
                <div class="relative group">
                    @if(request()->routeIs('superadmin.posyandu.*'))
                        <div class="sidebar-active-indicator absolute -left-8 top-1/2
                                                    -translate-y-1/2 w-2 h-10 bg-[#0A63D8] rounded-r-full">
                        </div>
                    @endif
                    <a href="{{ route('superadmin.posyandu.index') }}"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('superadmin.posyandu.*') ? 'bg-[#EBF3FF] text-[#0A63D8]' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <!-- Bentuk gedung layanan kesehatan -->
                            <path d="M3 21h18" />
                            <path d="M5 21V7l7-4 7 4v14" />
                            <!-- Pintu dan jendela -->
                            <path d="M9 21v-4h6v4" />
                            <path d="M8 10h2" />
                            <path d="M14 10h2" />
                            <path d="M8 13h2" />
                            <path d="M14 13h2" />
                            <!-- Tanda plus medis -->
                            <path d="M12 5v4" />
                            <path d="M10 7h4" />
                        </svg>

                        <span class="nav-label font-bold text-sm">Posyandu</span>
                    </a>
                </div>

                <!-- Pengguna -->
                <div class="relative group">
                    @if(request()->routeIs('superadmin.pengguna.*'))
                        <div class="sidebar-active-indicator absolute -left-8 top-1/2
                                                -translate-y-1/2 w-2 h-10 bg-[#0A63D8] rounded-r-full">
                        </div>
                    @endif

                    <a href="{{ route('superadmin.pengguna.index') }}"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('superadmin.pengguna.*') ? 'bg-[#EBF3FF] text-[#0A63D8]' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <span class="nav-label font-bold text-sm">Pengguna</span>
                    </a>
                </div>

            </nav>

            {{-- Logout Section --}}
            <div class="pt-6 mt-6 border-t border-slate-50">
                <form action="{{ route('superadmin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-button w-full flex items-center gap-4 px-6 py-4
        text-slate-400 hover:text-red-500 transition-all duration-300
        font-bold text-sm rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
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

        <!-- Konten Utama -->
        <main id="mainContent" class="flex-1 min-w-0 bg-white rounded-[2rem]
        shadow-sm border border-slate-100 p-8 overflow-y-auto">
            @yield('content')
        </main>

    </div>
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const appShell = document.getElementById('appShell');
            const toggleButton = document.getElementById('sidebar-toggle');
            const toggleIcon = document.getElementById('sidebar-toggle-icon');

            if (!appShell || !toggleButton || !toggleIcon) {
                return;
            }

            const storageKey = 'posyandu-superadmin-sidebar';

            function setSidebarState(collapsed) {
                appShell.classList.toggle('sidebar-collapsed', collapsed);
                // Beri tahu halaman bahwa ukuran sidebar telah berubah.
                window.setTimeout(function () {
                    window.dispatchEvent(new Event('sidebar:changed'));
                }, 350);

                toggleButton.setAttribute(
                    'aria-expanded',
                    String(!collapsed)
                );

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
                    localStorage.setItem(
                        storageKey,
                        collapsed ? 'collapsed' : 'expanded'
                    );
                } catch (error) {
                    // Sidebar tetap berfungsi jika penyimpanan tidak tersedia.
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