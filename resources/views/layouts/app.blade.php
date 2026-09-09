<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Zaldoris - TikTok-Style Live Commerce & Auction Platform. Experience real-time live shopping and auctions.')">
    <title>@yield('title', 'Zaldoris - TikTok-Style Live Commerce & Auction Platform')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.png') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auction.css') }}" rel="stylesheet">

    <style>
        .nav-role-badge {
            background: rgba(254, 44, 85, 0.15);
            color: #FE2C55;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 6px;
        }
        .user-coin-chip {
            background: linear-gradient(135deg, #FFB800, #FF8A00);
            color: #000;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-right: 8px;
            text-decoration: none;
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- NAVBAR / HEADER -->
<header class="zal-navbar">
    <div class="zal-navbar-inner">
        <!-- Logo -->
        <a class="zal-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/logo.png') }}" alt="Zaldoris" class="zal-brand-logo">
        </a>

        <!-- Center Nav Links -->
        <ul class="zal-nav-menu">
            <li><a href="{{ route('home') }}" class="zal-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('streams.index', ['type' => 'live_shopping']) }}" class="zal-nav-link {{ request()->fullUrlIs('*live_shopping*') ? 'active' : '' }}">Live Shopping</a></li>
            <li><a href="{{ route('auctions.index') }}" class="zal-nav-link {{ request()->routeIs('auctions.*') ? 'active' : '' }}">Live Auction</a></li>
            <li><a href="{{ route('streams.index') }}" class="zal-nav-link {{ request()->routeIs('streams.*') && !request()->fullUrlIs('*live_shopping*') && !request()->fullUrlIs('*pk_battle*') ? 'active' : '' }}">Live Streaming</a></li>
            <li><a href="{{ route('streams.index', ['type' => 'pk_battle']) }}" class="zal-nav-link {{ request()->fullUrlIs('*pk_battle*') ? 'active' : '' }}">PK Battle</a></li>
            <li><a href="{{ route('shop.index') }}" class="zal-nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">Shop</a></li>
            @auth
                @if(auth()->user()->isSeller())
                    <li><a href="{{ route('dashboard.seller') }}" class="zal-nav-link {{ request()->routeIs('dashboard.seller') ? 'active' : '' }}">Seller Hub</a></li>
                @endif
                @if(auth()->user()->isCreator())
                    <li><a href="{{ route('dashboard.creator') }}" class="zal-nav-link {{ request()->routeIs('dashboard.creator') ? 'active' : '' }}">Creator Studio</a></li>
                @endif
                @if(auth()->user()->isAdmin())
                    <li><a href="{{ route('admin.index') }}" class="zal-nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin Panel</a></li>
                @endif
            @endauth
        </ul>

        <!-- Right Action Icons -->
        <div class="zal-nav-actions">
            <a href="{{ route('search') }}" class="nav-icon-btn" title="Search"><i class="bi bi-search"></i></a>
            
            <a href="{{ route('wallet.coins') }}" class="user-coin-chip" title="Coins Wallet">
                <i class="bi bi-coin"></i> 
                <span>{{ auth()->check() ? number_format(auth()->user()->coin_balance) : '100' }}</span>
            </a>

            <a href="{{ route('notifications') }}" class="nav-icon-btn" title="Notifications">
                <i class="bi bi-bell"></i>
                <span class="icon-badge-dot"></span>
            </a>

            <a href="{{ route('shop.cart') }}" class="nav-icon-btn" title="Cart">
                <i class="bi bi-cart3"></i>
                <span class="icon-badge-num" id="globalCartBadge">1</span>
            </a>

            @auth
                <div style="display: flex; align-items: center; gap: 8px;">
                    <a href="{{ auth()->user()->isSeller() ? route('dashboard.seller') : (auth()->user()->isCreator() ? route('dashboard.creator') : route('shop.orders')) }}" class="nav-avatar-btn" title="{{ auth()->user()->name }}">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="nav-icon-btn" title="Logout" style="background: none; border: none; cursor: pointer; color: #888;">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="zal-btn-primary" style="padding: 6px 16px; font-size: 13px; text-decoration: none; border-radius: 20px; background: #FE2C55; color: #fff; font-weight: 700;">
                    Log In
                </a>
            @endauth
        </div>
    </div>
</header>

<!-- MAIN CONTAINER -->
<main class="page-container">
    @if(session('success'))
        <div style="background: rgba(37, 244, 238, 0.15); border: 1px solid #25F4EE; color: #25F4EE; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid #FE2C55; color: #FE2C55; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<!-- FOOTER -->
<footer style="background: #0f0f13; border-top: 1px solid #1f1f27; padding: 40px 20px; margin-top: 60px; color: #888; font-size: 13px;">
    <div style="max-width: 1400px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 30px;">
        <div>
            <img src="{{ asset('assets/logo.png') }}" alt="Zaldoris" style="height: 32px; margin-bottom: 12px;">
            <p style="max-width: 320px; line-height: 1.6;">TikTok-Style Live Commerce, PK Battles & Whatnot Real-Time Auctions Platform.</p>
            <p style="margin-top: 10px; color: #555;">© 2026 Zaldoris Inc. All rights reserved.</p>
        </div>
        <div style="display: flex; gap: 50px; flex-wrap: wrap;">
            <div>
                <h4 style="color: #fff; margin-bottom: 12px; font-size: 14px;">Platform</h4>
                <ul style="list-style: none; padding: 0; line-height: 2;">
                    <li><a href="{{ route('streams.index') }}" style="color: #888; text-decoration: none;">Live Streaming</a></li>
                    <li><a href="{{ route('streams.index', ['type' => 'live_shopping']) }}" style="color: #888; text-decoration: none;">Live Shopping</a></li>
                    <li><a href="{{ route('auctions.index') }}" style="color: #888; text-decoration: none;">Whatnot Auctions</a></li>
                    <li><a href="{{ route('streams.index', ['type' => 'pk_battle']) }}" style="color: #888; text-decoration: none;">PK Battles</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: #fff; margin-bottom: 12px; font-size: 14px;">Economy & Legal</h4>
                <ul style="list-style: none; padding: 0; line-height: 2;">
                    <li><a href="{{ route('wallet.coins') }}" style="color: #888; text-decoration: none;">Buy Coins (13% HST)</a></li>
                    <li><a href="{{ route('wallet.subscribe') }}" style="color: #888; text-decoration: none;">Creator Subscriptions ($7.99)</a></li>
                    <li><a href="#" style="color: #888; text-decoration: none;">Seller Terms & 48h Escrow</a></li>
                    <li><a href="#" style="color: #888; text-decoration: none;">Community Guidelines</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: #fff; margin-bottom: 12px; font-size: 14px;">Mobile Apps</h4>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button style="background: #1a1a24; border: 1px solid #333; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 12px; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                        <i class="bi bi-apple"></i> iOS App
                    </button>
                    <button style="background: #1a1a24; border: 1px solid #333; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 12px; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                        <i class="bi bi-google-play"></i> Android App
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- JS Scripts -->
<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/auction.js') }}"></script>
@stack('scripts')
</body>
</html>
