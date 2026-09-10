<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Console - GenZ Live / Zaldoris')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-main: #13141f;
            --bg-sidebar: #171826;
            --bg-card: #1c1d2e;
            --bg-card-inner: #161725;
            --pink-accent: #FE2C55;
            --pink-hover: #ff436b;
            --cyan-accent: #25F4EE;
            --text-main: #ffffff;
            --text-muted: #8e90a6;
            --border-color: rgba(255, 255, 255, 0.07);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .admin-sidebar {
            width: 250px;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            min-height: 100vh;
            position: sticky;
            top: 0;
        }

        .admin-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
            border-bottom: 1px solid var(--border-color);
        }

        .admin-brand span.brand-live {
            color: var(--pink-accent);
        }

        .sidebar-menu {
            list-style: none;
            padding: 16px 10px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-link-inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-link i.nav-icon {
            font-size: 18px;
            width: 22px;
            text-align: center;
        }

        .sidebar-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.03);
        }

        .sidebar-link.active {
            color: #fff;
            background: rgba(254, 44, 85, 0.1);
        }

        .sidebar-link.active::after {
            content: '';
            position: absolute;
            right: 0;
            top: 6px;
            bottom: 6px;
            width: 3px;
            background: var(--pink-accent);
            border-radius: 4px 0 0 4px;
        }

        .sidebar-link.active i.nav-icon {
            color: var(--pink-accent);
        }

        /* MAIN CONTENT */
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* TOP HEADER */
        .admin-header {
            height: 70px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            background: var(--bg-main);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .notif-bell-btn {
            background: var(--pink-accent);
            color: #fff;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 0 4px 14px rgba(254, 44, 85, 0.3);
        }

        .admin-profile-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 4px 12px 4px 4px;
            border-radius: 30px;
        }

        .admin-profile-badge img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--pink-accent);
        }

        .admin-content-body {
            padding: 30px;
            flex: 1;
        }

        /* STAT CARDS */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        @media (max-width: 1200px) {
            .stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .stat-val {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        /* TABLE */
        .admin-table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            overflow: hidden;
        }

        .table-header-bar {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .table-filter-pills {
            display: flex;
            gap: 6px;
            background: var(--bg-card-inner);
            padding: 4px;
            border-radius: 10px;
        }

        .table-filter-pills a {
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            color: var(--text-muted);
            transition: all 0.2s;
        }

        .table-filter-pills a.active {
            background: var(--pink-accent);
            color: #fff;
        }

        .search-box-wrap {
            display: flex;
            align-items: center;
            background: var(--bg-card-inner);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 0 14px;
            height: 38px;
        }

        .search-box-wrap input {
            background: none;
            border: none;
            color: #fff;
            font-size: 13px;
            outline: none;
            margin-left: 8px;
            width: 200px;
        }

        .admin-data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .admin-data-table th {
            padding: 14px 20px;
            color: var(--text-muted);
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        .admin-data-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            color: #ddd;
            vertical-align: middle;
        }

        .coin-pill-badge {
            background: rgba(254, 44, 85, 0.15);
            color: #ff6b8b;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 12px;
            display: inline-block;
            text-align: center;
        }

        .coin-pill-badge span {
            display: block;
            font-size: 9px;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-active { background: rgba(37, 244, 238, 0.15); color: #25F4EE; }
        .status-blocked { background: rgba(254, 44, 85, 0.15); color: #FE2C55; }
    </style>
    @stack('admin_styles')
</head>
<body>

    <!-- LEFT SIDEBAR NAVIGATION -->
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            <span style="font-weight: 900; font-size: 22px;">GenZ <span class="brand-live">Live</span></span>
        </a>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-activity nav-icon"></i>
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-shield-lock nav-icon"></i>
                        <span>Admin Panel</span>
                    </div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.ads') }}" class="sidebar-link {{ request()->routeIs('admin.ads') ? 'active' : '' }}">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-image nav-icon"></i>
                        <span>Banner</span>
                    </div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-people nav-icon"></i>
                        <span>User</span>
                    </div>
                    <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.disputes') }}" class="sidebar-link {{ request()->routeIs('admin.disputes') ? 'active' : '' }}">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-building nav-icon"></i>
                        <span>Agency</span>
                    </div>
                    <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
                </a>
            </li>
        </ul>

        <!-- BACK TO WEBSITE LINK -->
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            <a href="{{ route('home') }}" style="color: var(--text-muted); font-size: 12px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-box-arrow-up-right"></i> View Main Website
            </a>
        </div>
    </aside>

    <!-- RIGHT MAIN CONTENT AREA -->
    <div class="admin-main">
        <!-- TOP HEADER -->
        <header class="admin-header">
            <div class="header-actions">
                <button class="notif-bell-btn" title="Notifications">
                    <i class="bi bi-bell-fill"></i>
                </button>

                <div class="admin-profile-badge">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=Admin&background=FE2C55&color=fff' }}" alt="Admin">
                    <span style="font-weight: 700; font-size: 13px;">{{ auth()->user()->name ?? 'Administrator' }}</span>
                </div>

                <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" title="Logout" style="background: rgba(254, 44, 85, 0.15); border: 1px solid var(--pink-accent); color: var(--pink-accent); padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- BODY CONTENT -->
        <main class="admin-content-body">
            @if(session('success'))
                <div style="background: rgba(37, 244, 238, 0.15); border: 1px solid var(--cyan-accent); color: var(--cyan-accent); padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid var(--pink-accent); color: var(--pink-accent); padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 13px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
