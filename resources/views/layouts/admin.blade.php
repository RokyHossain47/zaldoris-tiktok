<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Console - GenZ Live / Zaldoris')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}">

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
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            text-decoration: none;
            border-bottom: 1px solid var(--border-color);
            min-height: 70px;
        }

        .admin-brand-logo {
            max-height: 38px;
            max-width: 180px;
            width: auto;
            object-fit: contain;
            display: block;
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

        /* SIDEBAR DROPDOWNS & SUBMENUS */
        .sidebar-dropdown {
            display: flex;
            flex-direction: column;
        }

        .dropdown-arrow {
            transition: transform 0.25s ease;
        }

        .sidebar-dropdown.open .dropdown-arrow {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            list-style: none;
            padding: 4px 0 6px 36px;
            display: none;
            flex-direction: column;
            gap: 2px;
        }

        .sidebar-dropdown.open .sidebar-submenu {
            display: flex;
        }

        .sidebar-sublink {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .sidebar-sublink:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.04);
        }

        .sidebar-sublink.active {
            color: var(--cyan-accent);
            background: rgba(37, 244, 238, 0.08);
            font-weight: 700;
        }

        .sidebar-sublink.active i {
            color: var(--cyan-accent);
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


        /* PAGINATION STYLES */
        .pagination {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
        }

        .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 6px 12px;
            background: #0c0d14;
            border: 1px solid var(--border-color);
            color: #fff;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .page-item.active .page-link {
            background: var(--cyan-accent);
            color: #090D10;
            border-color: var(--cyan-accent);
            font-weight: 800;
            box-shadow: 0 2px 10px rgba(37, 244, 238, 0.3);
        }

        .page-item.disabled .page-link {
            background: rgba(255, 255, 255, 0.03);
            color: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.05);
            cursor: not-allowed;
        }

        .page-item:not(.active):not(.disabled) .page-link:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            color: var(--cyan-accent);
        }

        /* Prevent oversized SVGs in Laravel pagination */
        nav[role="navigation"] svg,
        .pagination svg,
        .admin-table-container svg {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        nav[role="navigation"] {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            width: 100%;
        }

        nav[role="navigation"] > div {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        nav[role="navigation"] p {
            margin: 0;
            font-size: 13px;
            color: var(--text-muted);
        }
    </style>
    @stack('admin_styles')
</head>
<body>

    <!-- LEFT SIDEBAR NAVIGATION -->
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            @php
                $siteLogo = setting('site_logo', 'assets/logo.png');
            @endphp
            @if($siteLogo)
                <img src="{{ asset($siteLogo) }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="admin-brand-logo">
            @else
                <span style="font-weight: 900; font-size: 20px; color: #fff;">{{ setting('site_name', 'Zaldoris') }} <span class="brand-live">Admin</span></span>
            @endif
        </a>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-speedometer2 nav-icon"></i>
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>

            <!-- BANNERS CRUD -->
            <li>
                <a href="{{ route('admin.banners.index') }}" class="sidebar-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-images nav-icon"></i>
                        <span>Banners</span>
                    </div>
                </a>
            </li>

            <!-- PRODUCTS MENU & SUB-MENUS -->
            <li class="sidebar-dropdown {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') ? 'open' : '' }}">
                <div class="sidebar-link sidebar-dropdown-toggle {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') ? 'active' : '' }}" style="cursor: pointer;">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-box-seam nav-icon"></i>
                        <span>Products</span>
                    </div>
                    <i class="bi bi-chevron-down dropdown-arrow" style="font-size: 11px;"></i>
                </div>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.categories.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="bi bi-tag" style="font-size: 12px;"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                            <i class="bi bi-grid" style="font-size: 12px;"></i>
                            <span>All Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.create') }}" class="sidebar-sublink {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle" style="font-size: 12px;"></i>
                            <span>Add Product</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- ORDERS MANAGEMENT -->
            <li>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-cart-check nav-icon"></i>
                        <span>Orders</span>
                    </div>
                </a>
            </li>

            <!-- COUPONS & DISCOUNTS MANAGEMENT -->
            <li>
                <a href="{{ route('admin.coupons.index') }}" class="sidebar-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-ticket-perforated nav-icon"></i>
                        <span>Coupons</span>
                    </div>
                </a>
            </li>

            <!-- AUCTIONS MENU & SUB-MENUS -->
            <li class="sidebar-dropdown {{ request()->routeIs('admin.auctions.*') ? 'open' : '' }}">
                <div class="sidebar-link sidebar-dropdown-toggle {{ request()->routeIs('admin.auctions.*') ? 'active' : '' }}" style="cursor: pointer;">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-hammer nav-icon"></i>
                        <span>Auctions</span>
                    </div>
                    <i class="bi bi-chevron-down dropdown-arrow" style="font-size: 11px;"></i>
                </div>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.auctions.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.auctions.index') ? 'active' : '' }}">
                            <i class="bi bi-list-check" style="font-size: 12px;"></i>
                            <span>All Auctions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.auctions.create') }}" class="sidebar-sublink {{ request()->routeIs('admin.auctions.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle" style="font-size: 12px;"></i>
                            <span>Create Auction</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- GIFTS & REACTIONS MENU -->
            <li class="sidebar-dropdown {{ request()->routeIs('admin.gifts.*') || request()->routeIs('admin.reactions.*') ? 'open' : '' }}">
                <div class="sidebar-link sidebar-dropdown-toggle {{ request()->routeIs('admin.gifts.*') || request()->routeIs('admin.reactions.*') ? 'active' : '' }}" style="cursor: pointer;">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-gift nav-icon"></i>
                        <span>Gifts & Reactions</span>
                    </div>
                    <i class="bi bi-chevron-down dropdown-arrow" style="font-size: 11px;"></i>
                </div>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.gifts.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.gifts.*') ? 'active' : '' }}">
                            <i class="bi bi-gift-fill" style="font-size: 12px;"></i>
                            <span>Live Gifts</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reactions.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.reactions.*') ? 'active' : '' }}">
                            <i class="bi bi-emoji-smile" style="font-size: 12px;"></i>
                            <span>Reactions & GIFs</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- USERS -->
            <li>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-people nav-icon"></i>
                        <span>Users</span>
                    </div>
                </a>
            </li>

            <!-- DISPUTES -->
            <li>
                <a href="{{ route('admin.disputes') }}" class="sidebar-link {{ request()->routeIs('admin.disputes') ? 'active' : '' }}">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-shield-check nav-icon"></i>
                        <span>Disputes & Escrow</span>
                    </div>
                </a>
            </li>

            <!-- SETTINGS MENU & 3 SUB-MENUS -->
            <li class="sidebar-dropdown {{ request()->routeIs('admin.settings.*') ? 'open' : '' }}">
                <div class="sidebar-link sidebar-dropdown-toggle {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" style="cursor: pointer;">
                    <div class="sidebar-link-inner">
                        <i class="bi bi-gear nav-icon"></i>
                        <span>Settings</span>
                    </div>
                    <i class="bi bi-chevron-down dropdown-arrow" style="font-size: 11px;"></i>
                </div>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.settings.general') }}" class="sidebar-sublink {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                            <i class="bi bi-sliders" style="font-size: 12px;"></i>
                            <span>General Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.seo') }}" class="sidebar-sublink {{ request()->routeIs('admin.settings.seo') ? 'active' : '' }}">
                            <i class="bi bi-search-heart" style="font-size: 12px;"></i>
                            <span>SEO Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.system') }}" class="sidebar-sublink {{ request()->routeIs('admin.settings.system') ? 'active' : '' }}">
                            <i class="bi bi-cpu" style="font-size: 12px;"></i>
                            <span>System Settings</span>
                        </a>
                    </li>
                </ul>
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

            @if(isset($errors) && $errors->any())
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

    <!-- ACCORDION SIDEBAR DROPDOWN JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownToggles = document.querySelectorAll('.sidebar-dropdown-toggle');

            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    const parent = this.closest('.sidebar-dropdown');
                    const isOpen = parent.classList.contains('open');

                    // Close all other dropdowns (accordion behavior)
                    document.querySelectorAll('.sidebar-dropdown').forEach(dropdown => {
                        if (dropdown !== parent) {
                            dropdown.classList.remove('open');
                        }
                    });

                    // Toggle clicked dropdown
                    if (isOpen) {
                        parent.classList.remove('open');
                    } else {
                        parent.classList.add('open');
                    }
                });
            });
        });
    </script>
    @stack('admin_scripts')
</body>
</html>
