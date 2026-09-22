<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Zaldoris - Auction Won Result. Congratulations! Complete your payment for Vintage Rolex Submariner.">
    <title>Auction Result - Zaldoris Live Commerce Platform</title>
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

    <!-- TOP HERO CELEBRATION CARD -->
    <div class="auction-won-hero-card">
        <!-- Back Button Top-Left -->
        <a href="{{ route('auctions.index') }}" class="auth-back-btn hero-back-btn" title="Back to Auctions">
            <i class="bi bi-chevron-left"></i>
        </a>

        <!-- Party Popper Icon Illustration -->
        <div class="party-popper-badge-wrap">
            <svg width="84" height="84" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Party Cone -->
                <path d="M22 66L42 22L66 46L22 66Z" fill="url(#party_cone_grad)"/>
                <path d="M22 66L32 44L44 56L22 66Z" fill="#F59E0B"/>
                <!-- Stripes -->
                <path d="M29 51.5L37.5 32.5L46 41L37.5 60L29 51.5Z" fill="#EF4444"/>
                <path d="M37 33.5L43 20L56 33L49.5 47L37 33.5Z" fill="#F59E0B"/>
                <!-- Confetti Ribbons & Dots -->
                <path d="M50 16C54 12 60 18 64 12" stroke="#3B82F6" stroke-width="3" stroke-linecap="round"/>
                <path d="M60 26C66 22 72 30 76 24" stroke="#EF4444" stroke-width="3" stroke-linecap="round"/>
                <path d="M34 12C38 8 42 14 46 10" stroke="#10B981" stroke-width="3" stroke-linecap="round"/>
                <circle cx="58" cy="12" r="3" fill="#F59E0B"/>
                <circle cx="68" cy="34" r="2.5" fill="#3B82F6"/>
                <circle cx="48" cy="24" r="3" fill="#EC4899"/>
                <circle cx="32" cy="20" r="2" fill="#10B981"/>
                <defs>
                    <linearGradient id="party_cone_grad" x1="22" y1="22" x2="66" y2="66" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#F59E0B"/>
                        <stop offset="1" stop-color="#D97706"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>

        <!-- Title & Subtitle -->
        <h1 class="auction-won-title">Congratulations! You Won This Auction.</h1>
        <p class="auction-won-sub">You are now the proud owner of an exceptional timepiece. Let's get it to your wrist.</p>
    </div>

    <!-- TWO-COLUMN AUCTION RESULT GRID -->
    <div class="auction-result-grid">

        <!-- LEFT COLUMN: WON PRODUCT CARD -->
        <div class="won-product-card">
            <div class="won-product-thumb">
                <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80" alt="Vintage Rolex Submariner">
            </div>
            <div class="won-product-content">
                <span class="badge-auction-won-tag">Auction Won</span>
                <h2 class="won-product-title">Vintage Rolex Submariner</h2>
                <p class="won-product-desc">
                    Reference 5513, circa 1970. Beautifully aged patina and original tritium dial. A true collector's piece.
                </p>

                <div class="won-divider"></div>

                <div class="won-detail-row">
                    <span class="won-label-text">Winning Bid</span>
                    <span class="won-val-amount">C$3,250.00</span>
                </div>
                <div class="won-detail-row">
                    <span class="won-label-text">Order Number</span>
                    <span class="won-val-order">#LX-992840-VX</span>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: SECURE CHECKOUT WIDGET & SELLER -->
        <div class="result-right-column">
            <!-- Secure Checkout Card -->
            <div class="secure-checkout-card">
                <h2 class="checkout-card-title">Secure Checkout</h2>

                <div class="secure-feature-box">
                    <i class="bi bi-shield-check"></i>
                    <span>Payment secured by LiveAuction Escrow</span>
                </div>

                <div class="secure-feature-box">
                    <i class="bi bi-truck"></i>
                    <span>Insured priority shipping included</span>
                </div>

                <div class="checkout-action-buttons">
                    <a href="{{ route('auctions.show', 1) }}" class="btn-outline-details">View Product Details</a>
                    <a href="{{ route('shop.checkout') }}" class="btn-solid-complete-pay">Complete Payment</a>
                </div>

                <p class="checkout-disclaimer-text">
                    By completing payment, you agree to our Terms of Service and Buyer Protection Policy.
                </p>
            </div>

            <!-- Seller Result Card -->
            <div class="seller-result-card">
                <div class="seller-left-info">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" alt="Alex_Velocity" class="seller-avatar-img">
                    <div>
                        <div class="seller-name-text">
                            Alex_Velocity
                            <i class="bi bi-patch-check-fill seller-badge-check"></i>
                        </div>
                        <div class="seller-sub-text">98% Positive Feedback</div>
                    </div>
                </div>
                <button type="button" class="btn-outline-follow" onclick="handleFollowSeller(this)">Follow</button>
            </div>
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
<script>
    // Follow Seller Handler
    function handleFollowSeller(btn) {
        if (btn.innerText === 'Follow') {
            btn.innerText = 'Following';
            btn.style.background = 'var(--accent)';
            btn.style.color = '#090D10';
        } else {
            btn.innerText = 'Follow';
            btn.style.background = 'transparent';
            btn.style.color = 'var(--accent)';
        }
    }
</script>
</body>
</html>
