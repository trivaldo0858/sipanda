<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPANDA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex">

    <aside class="w-64 min-h-screen bg-white border-r border-gray-200 flex flex-col">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-pink-600">SIPANDA</h1>
        </div>

        <nav class="flex-1 px-4 space-y-2">
            <a href="{{ route('superadmin.dashboard') }}"
                class="flex items-center px-4 py-3 bg-pink-50 text-pink-600 rounded-xl font-semibold">
                <span class="mr-3"></span> Dashboard
            </a>
            <a href="{{ route('superadmin.posyandu.index') }}"
                class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                <span class="mr-3"></span> Data Posyandu
            </a>
            <a href="{{ route('superadmin.pengguna.index') }}"
                class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                <span class="mr-3"></span> Data Pengguna
            </a>
        </nav>

        <div class="p-4 border-t border-gray-100">
            <form action="{{ route('superadmin.logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 text-red-500 font-medium flex items-center">
                    <span class="mr-3"></span> Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
            <h2 class="text-lg font-semibold text-gray-700">Selamat Datang, Super Admin</h2>
            <div class="flex items-center space-x-4">
                <div class="text-right text-sm">
                    <p class="font-bold text-gray-800">Admin Utama</p>
                    <p class="text-gray-500">Super Admin</p>
                </div>
                <img src="https://ui-avatars.com/api/?name=Admin&background=db2777&color=fff"
                    class="w-10 h-10 rounded-full border-2 border-pink-100">
            </div>
        </header>

        <div class="p-8">
            @yield('content')
        </div>
    </main>

</body>

</html>