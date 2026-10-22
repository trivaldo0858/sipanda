<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posyandu-In | @yield('title', 'Posyandu')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#155DFC',
                        softbg: '#F4F6FB',
                    },
                    boxShadow: {
                        card: '0 10px 30px rgba(0,0,0,.04)',
                    },
                },
            },
        };
    </script>

    <style>
        body {
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 14px;
            color: #1E293B;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>

<body class="min-h-screen bg-softbg">
    <div class="min-h-screen md:flex">

        <aside class="flex w-full flex-col bg-white p-5 md:min-h-screen md:w-64 md:border-r md:border-slate-200">
            <a href="{{ route('posyandu.anak.index') }}" class="mb-8 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z" />
                        <path d="M12 8v8M8 12h8" />
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-extrabold tracking-tight text-slate-800">
                        Posyandu-In
                    </p>
                    <p class="text-xs text-slate-500">Panel Posyandu</p>
                </div>
            </a>

            <p class="mb-3 px-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                Menu Utama
            </p>

            <a href="{{ route('posyandu.anak.index') }}"
                class="flex items-center gap-3 rounded-xl bg-blue-50 px-4 py-3 font-semibold text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="10" cy="7" r="4" />
                    <path d="M20 8v6M23 11h-6" />
                </svg>
                Kelola Data Anak
            </a>

            <div class="mt-auto pt-8">
                <form action="{{ route('posyandu.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-xl px-4 py-3 text-left font-semibold text-red-600 transition hover:bg-red-50">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <main class="min-w-0 flex-1 p-5 md:p-8">
            <div class="mx-auto max-w-7xl">
                @yield('content')
            </div>
        </main>

    </div>
</body>

</html>