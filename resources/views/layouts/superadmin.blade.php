<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SIPANDA - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <aside class="sidebar-sipanda">
        <div class="logo-wrapper">
            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>
            <div class="logo-text">
                <h1>SIPANDA</h1>
                <p>SISTEM POSYANDU ANAK DIGITAL</p>
            </div>
        </div>

        <nav style="flex: 1;">
            <a href="{{ route('superadmin.dashboard') }}"
                class="nav-link-sipanda {{ request()->routeIs('superadmin.dashboard') ? 'nav-active' : 'nav-inactive' }}">DASHBOARD</a>

            <a href="{{ route('superadmin.posyandu.index') }}"
                class="nav-link-sipanda {{ request()->routeIs('superadmin.posyandu.*') ? 'nav-active' : 'nav-inactive' }}">UNIT
                POSYANDU</a>

            <a href="{{ route('superadmin.pengguna.index') }}"
                class="nav-link-sipanda {{ request()->routeIs('superadmin.pengguna.*') ? 'nav-active' : 'nav-inactive' }}">DIREKTORI
                STAFF</a>
        </nav>

        <div class="sidebar-bottom">
            <form action="{{ route('superadmin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link-sipanda"
                    style="color: #EF4444; border:none; background:none; cursor:pointer; width: 100%; text-align: left;">
                    KELUAR
                </button>
            </form>
        </div>
    </aside>

    <div class="main-content">
        <header class="header-sipanda">
            <h2 class="header-title">
                @yield('header_title', 'Dashboard Utama')
            </h2>

            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="text-align: right;">
                    <p style="font-weight: 800; font-size: 14px; color: #1F2937; margin: 0;">Super Admin</p>
                    <p style="font-size: 11px; color: #6B7280; margin: 0;">Administrator</p>
                </div>
                <div
                    style="width: 40px; height: 40px; background: #FDF2F8; border-radius: 12px; display: flex; align-items: center; justify-center; font-size: 18px;">
                    👤
                </div>
            </div>
        </header>

        <div class="content-padding">
            @yield('content')
        </div>
    </div>

</body>

</html>