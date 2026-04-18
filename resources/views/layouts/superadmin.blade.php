<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIPANDA — Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #2E86AB;
            --primary-dark: #1a6a8a;
        }
        body { background: #f4f7fb; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #1a2a4a 0%, #0f1e35 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            transition: all .3s;
        }
        .sidebar-brand {
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-brand h5 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 16px;
        }
        .sidebar-brand small { color: rgba(255,255,255,.5); font-size: 11px; }
        .sidebar-nav { padding: 12px 0; }
        .nav-label {
            color: rgba(255,255,255,.35);
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 20px 4px;
        }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,.7);
            padding: 10px 20px;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            transition: all .2s;
        }
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(46,134,171,.3);
            border-left: 3px solid var(--primary);
        }
        .sidebar-nav .nav-link i { font-size: 16px; width: 20px; }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e8ecf0;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .topbar h6 { margin: 0; font-weight: 600; color: #333; }
        .content-area { padding: 24px; }

        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }
        .stat-card .icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .stat-card .value { font-size: 28px; font-weight: 700; color: #1a2a4a; }
        .stat-card .label { font-size: 13px; color: #888; margin-top: 2px; }

        .card { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; font-weight: 600; }
        .table th { font-size: 12px; text-transform: uppercase; color: #888; font-weight: 600; }
        .badge-aktif   { background: #d4f1e4; color: #1a7a4a; }
        .badge-nonaktif { background: #fde8e8; color: #c0392b; }
    </style>
</head>
<body>

{{-- Sidebar --}}
<div class="sidebar">
    <div class="sidebar-brand">
        <h5><i class="bi bi-heart-pulse-fill text-info me-2"></i>SIPANDA</h5>
        <small>Super Admin Panel</small>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Utama</div>
        <a href="{{ route('superadmin.dashboard') }}"
           class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="nav-label">Manajemen</div>
        <a href="{{ route('superadmin.posyandu.index') }}"
           class="nav-link {{ request()->routeIs('superadmin.posyandu.*') ? 'active' : '' }}">
            <i class="bi bi-hospital-fill"></i> Unit Posyandu
        </a>
        <a href="{{ route('superadmin.pengguna.index') }}"
           class="nav-link {{ request()->routeIs('superadmin.pengguna.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Akun Pengguna
        </a>

        <div class="nav-label">Laporan</div>
        <a href="{{ route('superadmin.laporan.index') }}"
           class="nav-link {{ request()->routeIs('superadmin.laporan.index') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
        </a>
        <a href="{{ route('superadmin.laporan.global') }}"
           class="nav-link {{ request()->routeIs('superadmin.laporan.global') ? 'active' : '' }}">
            <i class="bi bi-globe2"></i> Ringkasan Global
        </a>
    </nav>
</div>

{{-- Main Content --}}
<div class="main-content">
    {{-- Topbar --}}
    <div class="topbar">
        <h6>@yield('page-title', 'Dashboard')</h6>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">
                <i class="bi bi-person-circle me-1"></i>
                {{ Auth::user()->username }}
            </span>
            <form action="{{ route('superadmin.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Content --}}
    <div class="content-area">
        {{-- Alert --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>