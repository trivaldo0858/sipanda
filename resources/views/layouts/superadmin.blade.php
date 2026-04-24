<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SIPANDA - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <aside class="sidebar-sipanda">
        <div class="logo-wrapper">
            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
            <div class="logo-text">
                <h1>SIPANDA</h1>
                <p>SISTEM POSYANDU ANAK DIGITAL</p>
            </div>
        </div>

        <nav style="flex: 1;">
            <a href="#" class="nav-link-sipanda nav-active">DASHBOARD</a>
            <a href="#" class="nav-link-sipanda nav-inactive">REGISTRASI</a>
            <a href="#" class="nav-link-sipanda nav-inactive">DIREKTORI STAFF</a>
            <a href="#" class="nav-link-sipanda nav-inactive">DIREKTORI AKUN STAFF</a>
        </nav>

        <div class="sidebar-bottom">
            <button class="nav-link-sipanda" style="color: #EF4444; border:none; background:none; cursor:pointer;">KELUAR</button>
        </div>
    </aside>

    <div class="main-content">
        <header class="header-sipanda">
            <h2 class="header-title">Dashboard Utama</h2>
            </header>

        <div class="content-padding">
            @yield('content')
        </div>
    </div>

</body>
</html>