<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Zaldoris - Vintage Rolex Submariner Live Auction Details. Place real-time bids on rare collectibles and luxury watches.">
    <title>Auction Details > Rolex Submariner - Zaldoris Live Commerce Platform</title>
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

    <!-- PAGE HEADER TITLE ROW WITH BREADCRUMB CHEVRON -->
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <a href="{{ route('auctions.index') }}" class="auth-back-btn" title="Back to Auctions">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 700; color: #FFFFFF; margin: 0;">
            Auction Details &gt; Rolex Submariner
        </h1>
    </div>

    <!-- MAIN TWO-COLUMN AUCTION DETAILS GRID -->
    <div class="auction-details-grid">

        <!-- LEFT COLUMN: GALLERY & DESCRIPTION & GUARANTEES -->
        <div class="auction-left-column">
            <!-- Main Image Card Showcase -->
            <div class="auction-gallery-main">
                <span class="badge-live-auction-pill">
                    <span class="live-pulse-dot" style="background: #FFFFFF;"></span> Live Auction
                </span>
                <img id="mainGalleryImg" src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1000&auto=format&fit=crop&q=80" alt="Vintage Rolex Submariner">
            </div>

            <!-- Thumbnail Selector Row -->
            <div class="auction-thumbs-row">
                <div class="auction-thumb-box active" onclick="switchMainImage(this, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1000&auto=format&fit=crop&q=80')">
                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200&auto=format&fit=crop&q=80" alt="Thumb 1">
                </div>
                <div class="auction-thumb-box" onclick="switchMainImage(this, 'https://images.unsplash.com/photo-1542496658-e33a6d0d50f6?w=1000&auto=format&fit=crop&q=80')">
                    <img src="https://images.unsplash.com/photo-1542496658-e33a6d0d50f6?w=200&auto=format&fit=crop&q=80" alt="Thumb 2">
                </div>
                <div class="auction-thumb-box" onclick="switchMainImage(this, 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=1000&auto=format&fit=crop&q=80')">
                    <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=200&auto=format&fit=crop&q=80" alt="Thumb 3">
                </div>
            </div>

            <!-- Description -->
            <div>
                <h2 style="font-family: 'Outfit', sans-serif; font-size: 1.15rem; font-weight: 700; color: #FFFFFF; margin-bottom: 0.65rem;">Description</h2>
                <p style="font-size: 0.88rem; color: #8A99AD; line-height: 1.55; margin-bottom: 1.5rem;">
                    This exquisite Vintage Rolex Submariner (Ref. 5513) dates back to approximately 1974. It features the highly sought-after "Pre-Comex" dial with a stunning, even vanilla patina on the tritium plots. The "Ghost" bezel has faded naturally over decades of use to a beautiful misty grey, making it a unique piece for serious collectors. This timepiece has been professionally serviced by our master watchmakers while preserving all original vintage components.
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
                        Includes a physical Certificate of Authenticity and a digital NFT verification of ownership.
                    </p>
                </div>
                <div class="guarantee-card">
                    <div class="guarantee-card-header">
                        <i class="bi bi-truck"></i>
                        <span>Fully Insured Shipping</span>
                    </div>
                    <p class="guarantee-card-text">
                        Free worldwide express shipping via Ferrari Logistics with full transit insurance included.
                    </p>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: BIDDING & LIVE ACTIVITY CARD -->
        <div class="auction-right-card">

            <!-- Top Tags -->
            <div class="auction-top-meta">
                <span class="lot-tag">LOT #88219</span>
                <span class="top-rated-tag">Top Rated Item</span>
            </div>

            <!-- Title & Ref Subtitle -->
            <h2 class="auction-detail-title">Vintage Rolex Submariner</h2>
            <div class="auction-detail-sub">Ref. 5513 • Circa 1974 • Ghost Bezel</div>

            <!-- Current Bid Stats Box -->
            <div class="bidding-stats-row">
                <div class="stat-block">
                    <span class="stat-label">Current Bid</span>
                    <span class="stat-val-bid">C$2,850.00</span>
                    <span class="stat-sub">14 Bids placed</span>
                </div>
                <div class="stat-block" style="align-items: flex-end;">
                    <span class="stat-label">Auction Ends In</span>
                    <span class="stat-val-timer" id="auctionTimer">00:12:35</span>
                    <span class="stat-sub">Ending Dec 24, 6:00 PM</span>
                </div>
            </div>

            <!-- Bid Input Form Group -->
            <div class="bid-input-group">
                <input type="text" class="bid-input-box" id="customBidInput" value="C$ 2900">
                <button type="button" class="btn-place-bid-cyan" style="width: auto; padding: 0 1.25rem;" onclick="handlePlaceBid()">Place Bid</button>
            </div>

            <!-- Quick Bid Buttons -->
            <div class="stat-label" style="margin-bottom: 0.4rem;">Quick Buttons</div>
            <div class="quick-bids-row">
                <button type="button" class="btn-quick-bid" onclick="setQuickBid('C$ 2,900')">C$2,900</button>
                <button type="button" class="btn-quick-bid" onclick="setQuickBid('C$ 3,000')">C$3,000</button>
                <button type="button" class="btn-quick-bid" onclick="setQuickBid('C$ 3,100')">C$3,100</button>
            </div>

            <!-- Seller Row -->
            <div class="seller-profile-row">
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

            <!-- Live Bid Activity List -->
            <div class="bid-activity-header">
                <h3 class="bid-activity-title">Live Bid Activity</h3>
                <span class="live-updates-tag">
                    <span class="live-pulse-dot" style="width: 6px; height: 6px;"></span> Live Updates
                </span>
            </div>

            <div class="bid-history-list" id="bidHistoryList">
                <!-- Row 1 -->
                <div class="bid-history-item">
                    <div class="bidder-user-info">
                        <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=60&auto=format&fit=crop&q=80" alt="JohnD_88" class="bidder-avatar-sm">
                        <div>
                            <div class="bidder-name">JohnD_88</div>
                            <div class="bidder-time">Just now</div>
                        </div>
                    </div>
                    <span class="bid-amount-text highlight">C$2,850</span>
                </div>

                <!-- Row 2 -->
                <div class="bid-history-item">
                    <div class="bidder-user-info">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&auto=format&fit=crop&q=80" alt="Marc_Lux" class="bidder-avatar-sm">
                        <div>
                            <div class="bidder-name">Marc_Lux</div>
                            <div class="bidder-time">2 mins ago</div>
                        </div>
                    </div>
                    <span class="bid-amount-text">C$2,825</span>
                </div>

                <!-- Row 3 -->
                <div class="bid-history-item">
                    <div class="bidder-user-info">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=60&auto=format&fit=crop&q=80" alt="EliteVintage" class="bidder-avatar-sm">
                        <div>
                            <div class="bidder-name">EliteVintage</div>
                            <div class="bidder-time">5 mins ago</div>
                        </div>
                    </div>
                    <span class="bid-amount-text">C$2,800</span>
                </div>

                <!-- Row 4 -->
                <div class="bid-history-item">
                    <div class="bidder-user-info">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=60&auto=format&fit=crop&q=80" alt="Sub1974" class="bidder-avatar-sm">
                        <div>
                            <div class="bidder-name">Sub1974</div>
                            <div class="bidder-time">12 mins ago</div>
                        </div>
                    </div>
                    <span class="bid-amount-text">C$2,775</span>
                </div>

                <!-- Row 5 -->
                <div class="bid-history-item">
                    <div class="bidder-user-info">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=60&auto=format&fit=crop&q=80" alt="Sub1974" class="bidder-avatar-sm">
                        <div>
                            <div class="bidder-name">Sub1974</div>
                            <div class="bidder-time">12 mins ago</div>
                        </div>
                    </div>
                    <span class="bid-amount-text">C$2,775</span>
                </div>

                <!-- Row 6 -->
                <div class="bid-history-item">
                    <div class="bidder-user-info">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=60&auto=format&fit=crop&q=80" alt="Sub1974" class="bidder-avatar-sm">
                        <div>
                            <div class="bidder-name">Sub1974</div>
                            <div class="bidder-time">12 mins ago</div>
                        </div>
                    </div>
                    <span class="bid-amount-text">C$2,775</span>
                </div>

                <!-- Row 7 -->
                <div class="bid-history-item">
                    <div class="bidder-user-info">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=60&auto=format&fit=crop&q=80" alt="Sub1974" class="bidder-avatar-sm">
                        <div>
                            <div class="bidder-name">Sub1974</div>
                            <div class="bidder-time">12 mins ago</div>
                        </div>
                    </div>
                    <span class="bid-amount-text">C$2,775</span>
                </div>
            </div>

            <!-- View All Link at Bottom Right -->
            <div style="text-align: right; margin-top: 0.65rem;">
                <a href="#" class="view-all-link" style="font-size: 0.82rem;">View All <i class="bi bi-arrow-right"></i></a>
            </div>

        </div>

    </div>

    <!-- SECTION: SIMILAR AUCTIONS -->
    <section class="section-spacing">
        <div class="section-header-row">
            <h2 class="section-title">Similar Auctions</h2>
        </div>

        <div class="grid-4-col">
            <!-- Card 1 -->
            <div class="auction-card">
                <div class="auction-card-thumb">
                    <img src="https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=600&auto=format&fit=crop&q=80" alt="Luxury Dress For..">
                    <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                    <div class="badge-viewers-top"><i class="bi bi-eye-fill"></i> 1.2k</div>
                </div>
                <div class="auction-card-body">
                    <h3 class="auction-card-title">Luxury Dress For..</h3>
                    <div class="auction-info-row">
                        <div class="auction-bid-label">
                            <span>Current Bid</span>
                            <span class="auction-bid-val">C$2,850</span>
                        </div>
                        <div class="auction-time-label">
                            <span>Time Left</span>
                            <span class="auction-time-val">02:14:55</span>
                        </div>
                    </div>
                    <button type="button" class="btn-place-bid-cyan" onclick="window.location='/auction/1'">Place Bid</button>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="auction-card">
                <div class="auction-card-thumb">
                    <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600&auto=format&fit=crop&q=80" alt="First Edition Rare">
                    <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                    <div class="badge-viewers-top"><i class="bi bi-eye-fill"></i> 1.2k</div>
                </div>
                <div class="auction-card-body">
                    <h3 class="auction-card-title">First Edition Rare...</h3>
                    <div class="auction-info-row">
                        <div class="auction-bid-label">
                            <span>Current Bid</span>
                            <span class="auction-bid-val">C$2,850</span>
                        </div>
                        <div class="auction-time-label">
                            <span>Time Left</span>
                            <span class="auction-time-val">02:14:55</span>
                        </div>
                    </div>
                    <button type="button" class="btn-place-bid-cyan" onclick="window.location='/auction/1'">Place Bid</button>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="auction-card">
                <div class="auction-card-thumb">
                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80" alt="Vintage Rolex Su">
                    <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                    <div class="badge-viewers-top"><i class="bi bi-eye-fill"></i> 1.2k</div>
                </div>
                <div class="auction-card-body">
                    <h3 class="auction-card-title">Vintage Rolex Su...</h3>
                    <div class="auction-info-row">
                        <div class="auction-bid-label">
                            <span>Current Bid</span>
                            <span class="auction-bid-val">C$2,850</span>
                        </div>
                        <div class="auction-time-label">
                            <span>Time Left</span>
                            <span class="auction-time-val">02:14:55</span>
                        </div>
                    </div>
                    <button type="button" class="btn-place-bid-cyan" onclick="window.location='/auction/1'">Place Bid</button>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="auction-card">
                <div class="auction-card-thumb">
                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80" alt="Vintage Rolex Su">
                    <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                    <div class="badge-viewers-top"><i class="bi bi-eye-fill"></i> 1.2k</div>
                </div>
                <div class="auction-card-body">
                    <h3 class="auction-card-title">Vintage Rolex Su...</h3>
                    <div class="auction-info-row">
                        <div class="auction-bid-label">
                            <span>Current Bid</span>
                            <span class="auction-bid-val">C$2,850</span>
                        </div>
                        <div class="auction-time-label">
                            <span>Time Left</span>
                            <span class="auction-time-val">02:14:55</span>
                        </div>
                    </div>
                    <button type="button" class="btn-place-bid-cyan" onclick="window.location='/auction/1'">Place Bid</button>
                </div>
            </div>
        </div>
    </section>

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
        if (input) input.value = val;
    }

    // Place Bid Handler
    function handlePlaceBid() {
        const input = document.getElementById('customBidInput');
        const bidVal = input ? input.value : 'C$2,900';
        alert(`🎉 Success! Your bid of ${bidVal} has been placed successfully.`);
    }

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
