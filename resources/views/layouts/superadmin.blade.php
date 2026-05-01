{{-- resources/views/layouts/superadmin.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin') - SIPANDA</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Tailwind Config --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
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

    <style>
        body {
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
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

<body class="bg-softbg text-slate-800">

    <div class="min-h-screen flex">

        {{-- Sidebar --}}
        <aside class="w-[270px] bg-white border-r border-line flex flex-col">

            {{-- Brand --}}
            <div class="px-6 py-6 border-b border-line">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-primary text-white flex items-center justify-center font-bold text-xl shadow-card">
                        +
                    </div>

                    <div>
                        <h1 class="text-3xl font-extrabold text-primary leading-none">
                            SIPANDA
                        </h1>
                        <p class="text-[11px] uppercase tracking-wider text-slate-400 mt-1">
                            Sistem Posyandu Anak Digital
                        </p>
                    </div>
                </div>
            </div>

            {{-- Menu --}}
            <nav class="px-4 py-6 space-y-2 flex-1">

                <a href="{{ route('superadmin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-2xl transition
               {{ request()->routeIs('superadmin.dashboard') ? 'bg-blue-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('superadmin.posyandu.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-2xl transition
               {{ request()->routeIs('superadmin.posyandu.*') ? 'bg-blue-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>🏥</span>
                    <span>Posyandu</span>
                </a>

                <a href="{{ route('superadmin.pengguna.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-2xl transition
               {{ request()->routeIs('superadmin.pengguna.*') ? 'bg-blue-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>👥</span>
                    <span>Pengguna</span>
                </a>

                <a href="{{ route('superadmin.laporan.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-2xl transition
               {{ request()->routeIs('superadmin.laporan.*') ? 'bg-blue-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>📄</span>
                    <span>Laporan</span>
                </a>
            </nav>

            {{-- Bottom --}}
            <div class="p-4 border-t border-line space-y-2">

                <div class="px-4 py-3 rounded-2xl bg-slate-50 text-sm text-slate-500">
                    Login sebagai:
                    <div class="font-semibold text-slate-700 mt-1">
                        {{ auth()->user()->username ?? 'Super Admin' }}
                    </div>
                </div>

                <form method="POST" action="{{ route('superadmin.logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-red-50 text-red-600 hover:bg-red-100 transition font-medium">
                        <span>↩</span>
                        <span>Keluar</span>
                    </button>
                </form>

            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 min-w-0">

            {{-- Topbar --}}
            <header class="h-[78px] bg-white border-b border-line px-8 flex items-center justify-between">

                <div>
                    <h2 class="text-xl font-bold text-slate-800">
                        @yield('title', 'Dashboard')
                    </h2>
                </div>

                <div class="flex items-center gap-4">

                    <div class="hidden md:block text-right">
                        <p class="text-sm font-semibold text-slate-700">
                            {{ auth()->user()->username ?? 'Super Admin' }}
                        </p>
                        <p class="text-xs text-slate-400">
                            Super Administrator
                        </p>
                    </div>

                    <div
                        class="w-11 h-11 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr(auth()->user()->username ?? 'S', 0, 1)) }}
                    </div>

                </div>
            </header>

            {{-- Page Content --}}
            <main class="p-8">
                @yield('content')
            </main>

        </div>
    </div>
    @stack('scripts') {{-- Tempat script dari halaman lain akan menempel --}}
</body>

</html>