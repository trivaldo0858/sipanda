<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posyandu-In | @yield('title', 'Input Pemeriksaan')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                },
            },
        };
    </script>
    <style>
        body { font-family: "Plus Jakarta Sans", sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
    @stack('head')
</head>

<body class="min-h-screen bg-[#F8FAFC] text-[#0F172A]">
    <div class="min-h-screen md:flex">
        <aside class="flex w-full flex-col bg-[#155DFC] px-4 py-5 text-white md:sticky md:top-0 md:h-screen md:w-64 md:shrink-0 md:px-5">
            <a href="{{ route('posyandu.pemeriksaan.index') }}" class="mb-6 flex shrink-0 items-center gap-3 px-2">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 text-xl"><i class="fa-solid fa-house-medical" aria-hidden="true"></i></span>
                <span>
                    <span class="block text-lg font-extrabold leading-tight">Posyandu-In</span>
                </span>
            </a>

            <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.14em] text-white/75">Menu Utama</p>
            <nav class="flex gap-1 overflow-x-auto pb-2 md:flex-1 md:flex-col md:overflow-visible" aria-label="Navigasi utama">
                <a href="{{ route('posyandu.anak.index') }}" class="flex shrink-0 items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-white/90 transition hover:bg-white/10 md:w-full">
                    <i class="fa-solid fa-child-reaching w-5 text-center" aria-hidden="true"></i><span>Kelola Data Anak</span>
                </a>
                <a href="{{ route('posyandu.pemeriksaan.index') }}" aria-current="page" class="flex shrink-0 items-center gap-3 rounded-lg bg-white/20 px-3 py-3 text-sm font-bold text-white shadow-sm ring-1 ring-inset ring-white/10 md:w-full">
                    <i class="fa-solid fa-notes-medical w-5 text-center" aria-hidden="true"></i><span>Input Data Pemeriksaan</span>
                </a>
                <a href="{{ route('posyandu.jadwal.index') }}" class="flex shrink-0 items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-white/90 transition hover:bg-white/10 md:w-full">
                    <i class="fa-regular fa-calendar-days w-5 text-center" aria-hidden="true"></i><span>Kelola Jadwal Posyandu</span>
                </a>
                <a href="#" class="flex shrink-0 items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-white/90 transition hover:bg-white/10 md:w-full">
                    <i class="fa-solid fa-chart-column w-5 text-center" aria-hidden="true"></i><span>Laporan Bulanan</span>
                </a>
            </nav>

            <form action="{{ route('posyandu.logout') }}" method="POST" class="mt-2 shrink-0 border-t border-white/15 pt-3 md:mt-4">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-left text-sm font-semibold text-white/90 transition hover:bg-white/10">
                    <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center" aria-hidden="true"></i>Keluar
                </button>
            </form>
        </aside>

        <div class="min-w-0 flex-1">
            <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto max-w-[1600px]">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>

</html>