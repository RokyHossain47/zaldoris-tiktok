<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Zaldoris - Live Auction List Page. Bid in real-time on luxury fashion, rare collectibles, vintage watches, and upcoming drops.">
    <title>Live Auction List - Zaldoris Live Commerce Platform</title>
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
            <li><a href="{{ route('home') }}" class="zal-nav-link">Home</a></li>
            <li><a href="{{ route('shop.index') }}" class="zal-nav-link">Live Shopping</a></li>
            <li><a href="{{ route('auctions.index') }}" class="zal-nav-link active">Live Auction</a></li>
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

    <!-- SECTION 1: ENDING SOON -->
    <section class="section-spacing">
        <div class="section-header-row">
            <h2 class="section-title">Ending Soon</h2>
            <span style="font-size: 13px; color: var(--cyan-accent); font-weight: 700;">Live Real-Time Drops</span>
        </div>

        <div class="grid-4-col">
            @forelse($auctions->take(4) as $auc)
                <div class="auction-card">
                    <a href="{{ route('auctions.show', $auc->id) }}" style="text-decoration: none; color: inherit;">
                        <div class="auction-card-thumb">
                            <img src="{{ $auc->image_url ?: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600' }}" alt="{{ $auc->title }}">
                            <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                            <div class="badge-viewers-top"><i class="bi bi-hammer"></i> {{ $auc->bids->count() }} bids</div>
                            <div class="auction-badge-tag">LOT #{{ str_pad($auc->id, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </a>
                    <div class="auction-card-body">
                        <a href="{{ route('auctions.show', $auc->id) }}" style="text-decoration: none; color: inherit;">
                            <h3 class="auction-card-title">{{ Str::limit($auc->title, 24) }}</h3>
                        </a>
                        <div class="auction-info-row">
                            <div class="auction-bid-label">
                                <span>Current Bid</span>
                                <span class="auction-bid-val">{{ setting('currency_symbol', '$') }}{{ number_format($auc->current_bid, 2) }}</span>
                            </div>
                            <div class="auction-time-label">
                                <span>Time Left</span>
                                <span class="auction-time-val">
                                    @if($auc->ends_at && $auc->ends_at->isPast())
                                        Ended
                                    @elseif($auc->ends_at)
                                        {{ $auc->ends_at->diffForHumans(['parts' => 2, 'short' => true]) }}
                                    @else
                                        Live
                                    @endif
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('auctions.show', $auc->id) }}" class="btn-place-bid-cyan" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">Place Bid</a>
                    </div>
                </div>
            @empty
                <div style="color: var(--text-muted); grid-column: 1 / -1; padding: 24px; text-align: center; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color);">
                    No active auctions ending soon.
                </div>
            @endforelse
        </div>
    </section>

    <!-- SECTION 2: TRENDING LIVE AUCTIONS -->
    <section class="section-spacing">
        <div class="section-header-row">
            <h2 class="section-title">Trending Live Auctions</h2>
            <span style="font-size: 13px; color: var(--text-muted);">{{ $auctions->count() }} Active Auctions</span>
        </div>

        <!-- Grid of active auctions -->
        <div class="grid-4-col">
            @forelse($auctions as $auc)
                <div class="auction-card">
                    <a href="{{ route('auctions.show', $auc->id) }}" style="text-decoration: none; color: inherit;">
                        <div class="auction-card-thumb">
                            <img src="{{ $auc->image_url ?: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600' }}" alt="{{ $auc->title }}">
                            <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                            <div class="badge-viewers-top"><i class="bi bi-hammer"></i> {{ $auc->bids->count() }} bids</div>
                            @if($auc->seller)
                                <div class="auction-badge-rare">{{ $auc->seller->name }}</div>
                            @endif
                        </div>
                    </a>
                    <div class="auction-card-body">
                        <a href="{{ route('auctions.show', $auc->id) }}" style="text-decoration: none; color: inherit;">
                            <h3 class="auction-card-title">{{ Str::limit($auc->title, 24) }}</h3>
                        </a>
                        <div class="auction-info-row">
                            <div class="auction-bid-label">
                                <span>Current Bid</span>
                                <span class="auction-bid-val">{{ setting('currency_symbol', '$') }}{{ number_format($auc->current_bid, 2) }}</span>
                            </div>
                            <div class="auction-time-label">
                                <span>Time Left</span>
                                <span class="auction-time-val">
                                    @if($auc->ends_at && $auc->ends_at->isPast())
                                        Ended
                                    @elseif($auc->ends_at)
                                        {{ $auc->ends_at->diffForHumans(['parts' => 2, 'short' => true]) }}
                                    @else
                                        Live
                                    @endif
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('auctions.show', $auc->id) }}" class="btn-place-bid-cyan" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">Place Bid</a>
                    </div>
                </div>
            @empty
                <div style="color: var(--text-muted); grid-column: 1 / -1; padding: 24px; text-align: center; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color);">
                    No live auctions available right now. Check back soon!
                </div>
            @endforelse
        </div>
    </section>

    <!-- SECTION 3: UPCOMING DROPS -->
    @if(isset($upcomingAuctions) && $upcomingAuctions->count() > 0)
        <section class="section-spacing">
            <div class="section-header-row">
                <h2 class="section-title">Upcoming Drops</h2>
                <span style="font-size: 13px; color: var(--cyan-accent); font-weight: 700;">Scheduled Events</span>
            </div>

            @foreach($upcomingAuctions as $up)
                <div class="upcoming-drop-card" style="margin-bottom: 16px;">
                    <div class="drop-card-left">
                        <img src="{{ $up->image_url ?: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=300' }}" alt="{{ $up->title }}" class="drop-thumb-img">
                        <div class="drop-card-info">
                            <span class="drop-tag-line">
                                @if($up->starts_at)
                                    STARTS {{ $up->starts_at->format('M d, Y • h:i A') }}
                                @else
                                    COMING SOON
                                @endif
                            </span>
                            <h3 class="drop-title-text">{{ $up->title }}</h3>
                            <p class="drop-desc-text">{{ Str::limit($up->description, 120) ?: 'Exclusive upcoming collectible drop with low starting bid of ' . setting('currency_symbol', '$') . number_format($up->starting_bid, 2) }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-notify-me" onclick="handleNotifyMe(this)">
                        <i class="bi bi-bell-fill"></i>
                        <span>Notify Me</span>
                    </button>
                </div>
            @endforeach
        </section>
    @endif

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
    // Notify Me Button Toggle Handler
    function handleNotifyMe(btn) {
        const textSpan = btn.querySelector('span');
        if (btn.classList.contains('subscribed')) {
            btn.classList.remove('subscribed');
            btn.style.background = '#00F0C8';
            btn.style.color = '#090D10';
            if (textSpan) textSpan.textContent = 'Notify Me';
        } else {
            btn.classList.add('subscribed');
            btn.style.background = '#12181E';
            btn.style.color = '#00F0C8';
            btn.style.border = '1px solid #00F0C8';
            if (textSpan) textSpan.textContent = 'Subscribed ✓';
        }
    }
</script>
</body>
</html>
