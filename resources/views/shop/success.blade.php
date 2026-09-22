<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Zaldoris - Payment Successful. Order confirmation and status details.">
    <title>Payment Successful - Zaldoris Live Commerce Platform</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.png') }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
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
            <li><a href="{{ route('home') }}" class="zal-nav-link active">Home</a></li>
            <li><a href="{{ route('shop.index') }}" class="zal-nav-link">Live Shopping</a></li>
            <li><a href="{{ route('auctions.index') }}" class="zal-nav-link">Live Auction</a></li>
            <li><a href="#" class="zal-nav-link">Live Academy</a></li>
            <li><a href="{{ route('streams.index') }}" class="zal-nav-link">Live Streaming</a></li>
            <li><a href="{{ route('streams.pk_battle', 1) }}" class="zal-nav-link">PK Battle</a></li>
        </ul>

                <!-- Right Action Icons -->
        <div class="zal-nav-actions">
            <a href="{{ route('search') }}" class="nav-icon-btn" title="Search"><i class="bi bi-search"></i></a>
            @auth
                <button class="nav-icon-btn" id="navTicketBtn" title="Wallet"><i class="bi bi-wallet2"></i></button>
                <a href="{{ route('notifications') }}" class="nav-icon-btn" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <span class="icon-badge-dot"></span>
                </a>
                <a href="{{ route('shop.cart') }}" class="nav-icon-btn" title="Cart">
                    <i class="bi bi-cart3"></i>
                    <span class="icon-badge-num" id="globalCartBadge">2</span>
                </a>
                <a href="{{ route('dashboard.creator') }}" class="nav-avatar-btn" title="Profile">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&auto=format&fit=crop&q=80' }}" alt="{{ auth()->user()->name }}">
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-login-nav" style="background: linear-gradient(135deg, var(--cyan-accent, #00F0C8), #1ed6d0); color: #090D10; text-decoration: none; padding: 7px 18px; border-radius: 20px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; margin-left: 8px;">
                    <i class="bi bi-box-arrow-in-right"></i> Log In
                </a>
            @endauth
        </div>
    </div>
</header>

<!-- MAIN CONTAINER -->
<main class="page-container">

    <!-- CENTERED PAYMENT SUCCESS CONTAINER -->
    <div class="payment-success-container">

        <div class="payment-success-card">
            <!-- Ribbon Badge Icon -->
            <div class="modal-badge-icon-wrap">
                <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Ribbon Tails -->
                    <path d="M27 44L20 60L32 54L36 56L40 54L52 60L45 44" fill="url(#award_ribbon_grad)"/>
                    <!-- Outer Glow Ring -->
                    <circle cx="36" cy="28" r="22" fill="rgba(0, 240, 200, 0.1)" stroke="url(#award_ring_grad)" stroke-width="2"/>
                    <!-- Center Badge Circle -->
                    <circle cx="36" cy="28" r="18" fill="url(#award_grad)"/>
                    <!-- Checkmark -->
                    <path d="M28 28.5L33.5 34L44 23.5" stroke="#090D10" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <defs>
                        <linearGradient id="award_grad" x1="18" y1="10" x2="54" y2="46" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#00F0C8"/>
                            <stop offset="1" stop-color="#3B82F6"/>
                        </linearGradient>
                        <linearGradient id="award_ribbon_grad" x1="20" y1="44" x2="52" y2="60" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#00F0C8"/>
                            <stop offset="1" stop-color="#7033FF"/>
                        </linearGradient>
                        <linearGradient id="award_ring_grad" x1="14" y1="6" x2="58" y2="50" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#00F0C8"/>
                            <stop offset="1" stop-color="#7033FF"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            <!-- Title -->
            <h1 class="payment-success-title">Payment successful</h1>

            <!-- Subtitle -->
            <p class="payment-success-sub">
                Your purchase was successful. We've received your order and will notify you once it has been shipped.
            </p>

            <!-- Go To Home Button -->
            <a href="{{ route('home') }}" class="btn-go-home-cyan">Go To Home</a>
        </div>

    </div>

</main>

<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
        <!-- Center Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/logo.png') }}" alt="Zaldoris" class="footer-logo-img">
        </a>
        <p class="footer-tagline-text">
            Experience the future of shopping with realtime interaction, live demonstrations, and exclusive community deals.
        </p>
        <div class="footer-copyright-line">
            © 2024 LiveStreamShop. All rights reserved.
        </div>
    </div>
</footer>

<!-- FLOATING WIDGET BUTTON -->
<button class="floating-action-widget" title="Live Chat">
    <i class="bi bi-chat-dots-fill"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
