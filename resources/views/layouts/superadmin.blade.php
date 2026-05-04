{{-- resources/views/layouts/superadmin.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

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
                        primary: '#0A63D8',
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
            /* antialiased membuat font Jakarta Sans terlihat lebih premium */
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
    </style>
</head>

<body class="bg-softbg text-slate-800 font-sans">
    {{-- Sisa kode Container Utama (Sidebar & Content) tetap sama --}}

    <!-- Container Utama (Sidebar & Content) -->
    <div class="min-h-screen bg-[#f3f4f9] p-6 flex gap-6">

        <!-- Sidebar Modern Modern Minimalis -->
        <aside
            class="w-72 bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 flex flex-col p-8 sticky top-6 h-[calc(100vh-3rem)] transition-all">

            {{-- Brand Section --}}
            <div class="flex items-center gap-4 mb-12 px-2">
                <h1 class="text-xl font-bold text-slate-700 tracking-tight">SIPANDA</h1>
            </div>

            {{-- Navigasi Menu --}}
            <nav class="flex-1 space-y-3">

                <!-- Dashboard -->
                <div class="relative group">
                    {{-- Indikator Aktif (Garis Pink di mockup) --}}
                    @if(request()->routeIs('superadmin.dashboard'))
                        <div class="absolute -left-8 top-1/2 -translate-y-1/2 w-2 h-10 bg-[#0A63D8] rounded-r-full"></div>
                    @endif

                    <a href="{{ route('superadmin.dashboard') }}"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('superadmin.dashboard') ? 'bg-[#EBF3FF] text-[#0A63D8]' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                        <span class="font-bold text-sm">Dashboard</span>
                    </a>
                </div>

                <!-- Posyandu -->
                <div class="relative group">
                    @if(request()->routeIs('superadmin.posyandu.*'))
                        <div class="absolute -left-8 top-1/2 -translate-y-1/2 w-2 h-10 bg-[#0A63D8] rounded-r-full"></div>
                    @endif

                    <a href="{{ route('superadmin.posyandu.index') }}"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('superadmin.posyandu.*') ? 'bg-[#EBF3FF] text-[#0A63D8]' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z" />
                            <path d="m3 9 2.45-4.91A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.79 1.09L21 9" />
                        </svg>
                        <span class="font-bold text-sm">Posyandu</span>
                    </a>
                </div>

                <!-- Pengguna -->
                <div class="relative group">
                    @if(request()->routeIs('superadmin.pengguna.*'))
                        <div class="absolute -left-8 top-1/2 -translate-y-1/2 w-2 h-10 bg-[#0A63D8] rounded-r-full"></div>
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
                        <span class="font-bold text-sm">Manajemen Pengguna</span>
                    </a>
                </div>

            </nav>

            {{-- Logout Section --}}
            <div class="pt-6 mt-6 border-t border-slate-50">
                <form action="{{ route('superadmin.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-red-500 transition-all duration-300 font-bold text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" x2="9" y1="12" y2="12" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Konten Utama -->
        <main class="flex-1 bg-white rounded-[2.5rem] shadow-sm border border-slate-50 p-10 overflow-y-auto">
            @yield('content')
        </main>

    </div>
    @stack('scripts') {{-- Tempat script dari halaman lain akan menempel --}}
</body>

</html>