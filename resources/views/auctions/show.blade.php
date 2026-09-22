<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Zaldoris - {{ $auction->title }} Live Auction Details. Place real-time bids on rare collectibles, luxury watches, and trending items.">
    <title>{{ $auction->title }} - Auction Details | {{ setting('site_name', 'Zaldoris') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}">
    <link rel="shortcut icon" href="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}">
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
            <img src="{{ asset(setting('site_logo', 'assets/logo.png')) }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="zal-brand-logo">
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

    <!-- PAGE HEADER TITLE ROW WITH BREADCRUMB CHEVRON -->
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <a href="{{ route('auctions.index') }}" class="auth-back-btn" title="Back to Auctions">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 700; color: #FFFFFF; margin: 0;">
            Auction Details &gt; {{ $auction->title }}
        </h1>
    </div>

    <!-- MAIN TWO-COLUMN AUCTION DETAILS GRID -->
    <div class="auction-details-grid">

        <!-- LEFT COLUMN: GALLERY & DESCRIPTION & GUARANTEES -->
        <div class="auction-left-column">
            <!-- Main Image Card Showcase -->
            <div class="auction-gallery-main">
                <span class="badge-live-auction-pill">
                    <span class="live-pulse-dot" style="background: #FFFFFF;"></span>
                    @if($auction->status === 'active')
                        Live Auction
                    @else
                        {{ ucfirst($auction->status) }}
                    @endif
                </span>
                @php
                    $mainImg = $auction->image_url ?: ($auction->product ? $auction->product->primary_image : 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1000');
                    $galleryImages = [];
                    if ($auction->product && !empty($auction->product->images)) {
                        $galleryImages = is_array($auction->product->images) ? $auction->product->images : json_decode($auction->product->images, true);
                    }
                    if (empty($galleryImages)) {
                        $galleryImages = [$mainImg];
                    }
                @endphp
                <img id="mainGalleryImg" src="{{ $mainImg }}" alt="{{ $auction->title }}">
            </div>

            <!-- Thumbnail Selector Row -->
            @if(count($galleryImages) > 1)
                <div class="auction-thumbs-row">
                    @foreach($galleryImages as $idx => $img)
                        <div class="auction-thumb-box {{ $idx === 0 ? 'active' : '' }}" onclick="switchMainImage(this, '{{ $img }}')">
                            <img src="{{ $img }}" alt="Thumb {{ $idx + 1 }}">
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Description -->
            <div>
                <h2 style="font-family: 'Outfit', sans-serif; font-size: 1.15rem; font-weight: 700; color: #FFFFFF; margin-bottom: 0.65rem;">Description</h2>
                <p style="font-size: 0.88rem; color: #8A99AD; line-height: 1.55; margin-bottom: 1.5rem; white-space: pre-line;">
                    {{ $auction->description ?: 'This item is an authentic, verified listing hosted on the Zaldoris live commerce & auction network. All sales are protected by our Escrow & Authenticity Guarantee.' }}
                </p>
            </div>

            <!-- Guarantee Cards Row -->
            <div class="guarantee-cards-row">
                <div class="guarantee-card">
                    <div class="guarantee-card-header">
                        <i class="bi bi-shield-check"></i>
                        <span>Authenticity Guaranteed</span>
                    </div>
                    <p class="guarantee-card-text">
                        Includes verified condition checks and tamper-evident packaging before dispatch.
                    </p>
                </div>
                <div class="guarantee-card">
                    <div class="guarantee-card-header">
                        <i class="bi bi-truck"></i>
                        <span>Fully Insured Shipping</span>
                    </div>
                    <p class="guarantee-card-text">
                        Fast express shipping with real-time tracking and full transit insurance coverage.
                    </p>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: BIDDING & LIVE ACTIVITY CARD -->
        <div class="auction-right-card">

            <!-- Top Tags -->
            <div class="auction-top-meta">
                <span class="lot-tag">LOT #{{ str_pad($auction->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="top-rated-tag">
                    <i class="bi bi-star-fill" style="color: #FFC107; font-size: 10px;"></i> Verified Item
                </span>
            </div>

            <!-- Title & Ref Subtitle -->
            <h2 class="auction-detail-title">{{ $auction->title }}</h2>
            <div class="auction-detail-sub">
                Min Increment: {{ setting('currency_symbol', '$') }}{{ number_format($auction->min_bid_step, 2) }} • Starting: {{ setting('currency_symbol', '$') }}{{ number_format($auction->starting_bid, 2) }}
            </div>

            <!-- Current Bid Stats Box -->
            <div class="bidding-stats-row">
                <div class="stat-block">
                    <span class="stat-label">Current Bid</span>
                    <span class="stat-val-bid" id="currentBidDisplay">{{ setting('currency_symbol', '$') }}{{ number_format($auction->current_bid, 2) }}</span>
                    <span class="stat-sub" id="totalBidsCountDisplay">{{ $auction->bids->count() }} Bids placed</span>
                </div>
                <div class="stat-block" style="align-items: flex-end;">
                    <span class="stat-label">Auction Ends In</span>
                    <span class="stat-val-timer" id="auctionTimer">--:--:--</span>
                    <span class="stat-sub" id="auctionEndsAtLabel">
                        @if($auction->ends_at)
                            Ending {{ $auction->ends_at->format('M d, g:i A') }}
                        @else
                            Live Event
                        @endif
                    </span>
                </div>
            </div>

            @php
                $minNextBid = (float)($auction->current_bid + $auction->min_bid_step);
                $step = (float)($auction->min_bid_step ?: 5.00);
            @endphp

            @if($auction->status === 'active')
                <!-- Bid Input Form Group -->
                <div id="bidAlertContainer"></div>

                <div class="bid-input-group">
                    <input type="number" step="0.01" min="{{ $minNextBid }}" class="bid-input-box" id="customBidInput" value="{{ $minNextBid }}">
                    <button type="button" class="btn-place-bid-cyan" id="placeBidBtn" style="width: auto; padding: 0 1.25rem;" onclick="handlePlaceBid()">Place Bid</button>
                </div>

                <!-- Quick Bid Buttons -->
                <div class="stat-label" style="margin-bottom: 0.4rem;">Quick Bid Increments</div>
                <div class="quick-bids-row">
                    <button type="button" class="btn-quick-bid" onclick="setQuickBid({{ $minNextBid }})">
                        {{ setting('currency_symbol', '$') }}{{ number_format($minNextBid, 2) }}
                    </button>
                    <button type="button" class="btn-quick-bid" onclick="setQuickBid({{ $minNextBid + $step }})">
                        {{ setting('currency_symbol', '$') }}{{ number_format($minNextBid + $step, 2) }}
                    </button>
                    <button type="button" class="btn-quick-bid" onclick="setQuickBid({{ $minNextBid + ($step * 2) }})">
                        {{ setting('currency_symbol', '$') }}{{ number_format($minNextBid + ($step * 2), 2) }}
                    </button>
                </div>
            @else
                <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid var(--pink-accent); color: #fff; padding: 14px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: 700;">
                    This auction is currently {{ ucfirst($auction->status) }}. Bidding is closed.
                </div>
            @endif

            <!-- Seller Row -->
            <div class="seller-profile-row">
                <div class="seller-left-info">
                    <img src="{{ $auction->seller && $auction->seller->avatar_url ? $auction->seller->avatar_url : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80' }}" alt="{{ $auction->seller ? $auction->seller->name : 'Seller' }}" class="seller-avatar-img">
                    <div>
                        <div class="seller-name-text">
                            {{ $auction->seller ? $auction->seller->name : 'Zaldoris Official' }}
                            <i class="bi bi-patch-check-fill seller-badge-check"></i>
                        </div>
                        <div class="seller-sub-text">98% Positive Feedback • Verified Host</div>
                    </div>
                </div>
                <button type="button" class="btn-outline-follow" onclick="handleFollowSeller(this)">Follow</button>
            </div>

            <!-- Live Bid Activity List -->
            <div class="bid-activity-header">
                <h3 class="bid-activity-title">Live Bid Activity</h3>
                <span class="live-updates-tag">
                    <span class="live-pulse-dot" style="width: 6px; height: 6px;"></span> Live Updates
                </span>
            </div>

            <div class="bid-history-list" id="bidHistoryList">
                @forelse($auction->bids as $idx => $b)
                    <div class="bid-history-item">
                        <div class="bidder-user-info">
                            <img src="{{ $b->user && $b->user->avatar_url ? $b->user->avatar_url : 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=60&auto=format&fit=crop&q=80' }}" alt="{{ $b->user ? $b->user->name : 'Bidder' }}" class="bidder-avatar-sm">
                            <div>
                                <div class="bidder-name">{{ $b->user ? $b->user->name : 'Bidder' }}</div>
                                <div class="bidder-time">{{ $b->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <span class="bid-amount-text {{ $idx === 0 ? 'highlight' : '' }}">
                            {{ setting('currency_symbol', '$') }}{{ number_format($b->amount, 2) }}
                        </span>
                    </div>
                @empty
                    <div id="noBidsYet" style="color: var(--text-muted); padding: 20px; text-align: center; font-size: 13px;">
                        No bids placed yet. Be the first to place a bid!
                    </div>
                @endforelse
            </div>

            <!-- View All Link at Bottom Right -->
            <div style="text-align: right; margin-top: 0.65rem;">
                <a href="{{ route('auctions.index') }}" class="view-all-link" style="font-size: 0.82rem;">View All Auctions <i class="bi bi-arrow-right"></i></a>
            </div>

        </div>

    </div>

    <!-- SECTION: SIMILAR AUCTIONS -->
    @if(isset($similarAuctions) && $similarAuctions->count() > 0)
        <section class="section-spacing">
            <div class="section-header-row">
                <h2 class="section-title">Similar Auctions</h2>
                <a href="{{ route('auctions.index') }}" class="view-all-link">View All <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="grid-4-col">
                @foreach($similarAuctions as $sim)
                    <div class="auction-card">
                        <a href="{{ route('auctions.show', $sim->id) }}" style="text-decoration: none; color: inherit;">
                            <div class="auction-card-thumb">
                                <img src="{{ $sim->image_url ?: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600' }}" alt="{{ $sim->title }}">
                                <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                                <div class="badge-viewers-top"><i class="bi bi-hammer"></i> {{ $sim->bids->count() }} bids</div>
                            </div>
                        </a>
                        <div class="auction-card-body">
                            <a href="{{ route('auctions.show', $sim->id) }}" style="text-decoration: none; color: inherit;">
                                <h3 class="auction-card-title">{{ Str::limit($sim->title, 24) }}</h3>
                            </a>
                            <div class="auction-info-row">
                                <div class="auction-bid-label">
                                    <span>Current Bid</span>
                                    <span class="auction-bid-val">{{ setting('currency_symbol', '$') }}{{ number_format($sim->current_bid, 2) }}</span>
                                </div>
                                <div class="auction-time-label">
                                    <span>Time Left</span>
                                    <span class="auction-time-val">
                                        @if($sim->ends_at && $sim->ends_at->isPast())
                                            Ended
                                        @elseif($sim->ends_at)
                                            {{ $sim->ends_at->diffForHumans(['parts' => 2, 'short' => true]) }}
                                        @else
                                            Live
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('auctions.show', $sim->id) }}" class="btn-place-bid-cyan" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                Place Bid
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</main>

<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
        <a href="{{ route('home') }}">
            <img src="{{ asset(setting('site_logo', 'assets/logo.png')) }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="footer-logo-img">
        </a>
        <p class="footer-tagline-text">
            Experience the future of shopping with realtime interaction, live demonstrations, and exclusive community deals.
        </p>
        <div class="footer-copyright-line">
            © {{ date('Y') }} {{ setting('site_name', 'Zaldoris') }}. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
    // Global Config for Auction
    const AUCTION_ID = {{ $auction->id ?? 0 }};
    const CURRENCY_SYMBOL = "{{ setting('currency_symbol', '$') }}";
    const MIN_BID_STEP = {{ (float)($auction->min_bid_step ?: 5.00) }};
    let currentBid = {{ (float)($auction->current_bid ?: 1.00) }};
    let endsAtStr = "{{ $auction->ends_at ? $auction->ends_at->toIso8601String() : '' }}";

    // Countdown Timer Engine
    function updateCountdown() {
        const timerElem = document.getElementById('auctionTimer');
        if (!timerElem || !endsAtStr) return;

        const targetTime = new Date(endsAtStr).getTime();
        const now = new Date().getTime();
        const diff = targetTime - now;

        if (diff <= 0) {
            timerElem.innerText = "00:00:00";
            timerElem.style.color = "var(--pink-accent, #FE2C55)";
            return;
        }

        const hours = Math.floor(diff / (1000 * 60 * 60));
        const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const secs = Math.floor((diff % (1000 * 60)) / 1000);

        const pad = (n) => String(n).padStart(2, '0');
        timerElem.innerText = `${pad(hours)}:${pad(mins)}:${pad(secs)}`;
    }

    if (endsAtStr) {
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    // Thumbnail Image Switcher
    function switchMainImage(thumbBox, newSrc) {
        document.querySelectorAll('.auction-thumb-box').forEach(box => box.classList.remove('active'));
        thumbBox.classList.add('active');
        const mainImg = document.getElementById('mainGalleryImg');
        if (mainImg) mainImg.src = newSrc;
    }

    // Quick Bid Setter
    function setQuickBid(val) {
        const input = document.getElementById('customBidInput');
        if (input) {
            input.value = parseFloat(val).toFixed(2);
        }
    }

    // Place Bid Handler with AJAX
    function handlePlaceBid() {
        @if(!auth()->check())
            if (confirm("Please log in to place a live bid. Would you like to log in now?")) {
                window.location.href = "{{ route('login') }}";
            }
            return;
        @endif

        const input = document.getElementById('customBidInput');
        const bidBtn = document.getElementById('placeBidBtn');
        const alertBox = document.getElementById('bidAlertContainer');
        const amount = parseFloat(input.value);

        if (isNaN(amount) || amount <= currentBid) {
            alertBox.innerHTML = `
                <div style="background: rgba(254, 44, 85, 0.2); border: 1px solid var(--pink-accent); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 12px;">
                    ⚠️ Bid must be higher than current bid of ${CURRENCY_SYMBOL}${currentBid.toFixed(2)}
                </div>
            `;
            return;
        }

        bidBtn.disabled = true;
        bidBtn.innerText = "Placing...";

        fetch("{{ route('auctions.bid', $auction->id ?? 0) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ amount: amount })
        })
        .then(response => response.json())
        .then(data => {
            bidBtn.disabled = false;
            bidBtn.innerText = "Place Bid";

            if (data.success) {
                currentBid = amount;
                document.getElementById('currentBidDisplay').innerText = `${CURRENCY_SYMBOL}${amount.toFixed(2)}`;

                // Update next bid input
                const nextAllowed = (amount + MIN_BID_STEP).toFixed(2);
                input.value = nextAllowed;
                input.min = nextAllowed;

                // Update quick bid buttons
                const qButtons = document.querySelectorAll('.quick-bids-row button');
                if (qButtons.length >= 3) {
                    qButtons[0].innerText = `${CURRENCY_SYMBOL}${parseFloat(nextAllowed).toFixed(2)}`;
                    qButtons[0].setAttribute('onclick', `setQuickBid(${nextAllowed})`);
                    
                    const step2 = (parseFloat(nextAllowed) + MIN_BID_STEP).toFixed(2);
                    qButtons[1].innerText = `${CURRENCY_SYMBOL}${step2}`;
                    qButtons[1].setAttribute('onclick', `setQuickBid(${step2})`);

                    const step3 = (parseFloat(nextAllowed) + (MIN_BID_STEP * 2)).toFixed(2);
                    qButtons[2].innerText = `${CURRENCY_SYMBOL}${step3}`;
                    qButtons[2].setAttribute('onclick', `setQuickBid(${step3})`);
                }

                if (data.ends_at) {
                    endsAtStr = data.ends_at;
                }

                // Add to history list
                const historyList = document.getElementById('bidHistoryList');
                const noBidsElem = document.getElementById('noBidsYet');
                if (noBidsElem) noBidsElem.remove();

                const newRow = document.createElement('div');
                newRow.className = 'bid-history-item';
                newRow.innerHTML = `
                    <div class="bidder-user-info">
                        <img src="{{ auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=60' }}" alt="{{ auth()->user()->name ?? 'Me' }}" class="bidder-avatar-sm">
                        <div>
                            <div class="bidder-name">{{ auth()->user()->name ?? 'Me' }} (You)</div>
                            <div class="bidder-time">Just now</div>
                        </div>
                    </div>
                    <span class="bid-amount-text highlight">${CURRENCY_SYMBOL}${amount.toFixed(2)}</span>
                `;
                historyList.prepend(newRow);

                alertBox.innerHTML = `
                    <div style="background: rgba(37, 244, 238, 0.15); border: 1px solid var(--cyan-accent); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 12px;">
                        🎉 ${data.message}
                    </div>
                `;
            } else {
                alertBox.innerHTML = `
                    <div style="background: rgba(254, 44, 85, 0.2); border: 1px solid var(--pink-accent); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 12px;">
                        ⚠️ ${data.message || 'Failed to place bid.'}
                    </div>
                `;
            }
        })
        .catch(err => {
            bidBtn.disabled = false;
            bidBtn.innerText = "Place Bid";
            alertBox.innerHTML = `
                <div style="background: rgba(254, 44, 85, 0.2); border: 1px solid var(--pink-accent); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 12px;">
                    ⚠️ Error placing bid. Please try again.
                </div>
            `;
        });
    }

    // Follow Seller Handler
    function handleFollowSeller(btn) {
        if (btn.innerText === 'Follow') {
            btn.innerText = 'Following';
            btn.style.background = 'var(--cyan-accent, #00F0C8)';
            btn.style.color = '#090D10';
        } else {
            btn.innerText = 'Follow';
            btn.style.background = 'transparent';
            btn.style.color = 'var(--cyan-accent, #00F0C8)';
        }
    }
</script>
</body>
</html>
