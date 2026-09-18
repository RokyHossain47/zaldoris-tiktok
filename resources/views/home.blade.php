<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ setting('meta_description', 'Zaldoris - TikTok-Style Live Commerce & Auction Platform. Experience real-time live shopping and auctions.') }}">
    <meta name="keywords" content="{{ setting('meta_keywords', 'live commerce, live shopping, tiktok shop, live auction, pk battle') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('meta_title', 'Zaldoris - TikTok-Style Live Commerce & Auction Platform') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}">
    <link rel="shortcut icon" href="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    {!! setting('custom_header_scripts') !!}
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

    <!-- AD BANNER SLIDER FOR HOMEPAGE (DYNAMIC FROM SUPER ADMIN) -->
    @php
        $sliderBanners = isset($mainBanners) && $mainBanners->count() ? $mainBanners : (isset($mainBanner) && $mainBanner ? collect([$mainBanner]) : collect());
    @endphp

    @if($sliderBanners->count() > 0)
        <div class="hero-slider-wrapper" id="heroBannerSlider">
            <div class="hero-slider-track" id="heroSliderTrack">
                @foreach($sliderBanners as $b)
                    <a href="{{ $b->link_url ?: route('shop.index') }}" class="hero-slider-slide">
                        <div class="zal-hero-banner" style="background-image: url('{{ $b->media_url }}');">
                            <div class="zal-hero-banner-overlay"></div>
                            <div class="zal-hero-banner-content">
                                <span class="zal-hero-badge">
                                    <i class="bi bi-stars"></i> Special Featured Promo
                                </span>
                                @if($b->title)
                                    <h1 class="zal-hero-title">{{ $b->title }}</h1>
                                @endif
                                @if($b->subtitle)
                                    <p class="zal-hero-subtitle">{{ $b->subtitle }}</p>
                                @endif
                                <div class="zal-hero-btn">
                                    {{ $b->button_text ?: 'Shop Live Now' }} <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($sliderBanners->count() > 1)
                <!-- Navigation Arrows -->
                <button type="button" class="hero-slider-arrow prev" id="heroSliderPrev" aria-label="Previous Slide">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="hero-slider-arrow next" id="heroSliderNext" aria-label="Next Slide">
                    <i class="bi bi-chevron-right"></i>
                </button>

                <!-- Indicator Dots -->
                <div class="hero-slider-dots" id="heroSliderDots">
                    @foreach($sliderBanners as $idx => $b)
                        <button type="button" class="hero-slider-dot {{ $idx === 0 ? 'active' : '' }}" data-slide="{{ $idx }}" aria-label="Slide {{ $idx + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="ad-banner-card">
            <div class="ad-banner-text">Banner for Add</div>
        </div>
    @endif

    <!-- TWO-COLUMN CONTENT GRID -->
    <div class="content-grid-layout">

        <!-- LEFT MAIN COLUMN -->
        <div class="left-main-column">

            <!-- SECTION 1: FEATURED LIVE SHOPPING (LATEST 3 PRODUCTS) -->
            <section class="section-spacing">
                <div class="section-header-row">
                    <h2 class="section-title">Featured Live Shopping</h2>
                    <a href="{{ route('shop.index') }}" class="view-all-link">View All <i class="bi bi-arrow-right"></i></a>
                </div>

                <div class="grid-3-col">
                    @forelse($featuredLiveProducts as $product)
                        <div class="zal-card">
                            <a href="{{ route('shop.product', $product->id) }}" style="text-decoration: none; color: inherit;">
                                <div class="live-card-thumb">
                                    <img src="{{ $product->primary_image }}" alt="{{ $product->title }}">
                                    @if($product->is_trending)
                                        <div class="badge-live-top" style="background: linear-gradient(135deg, #FF6B00, #FF0055);"><span class="live-pulse-dot"></span> HOT</div>
                                    @elseif($product->is_featured)
                                        <div class="badge-live-top" style="background: var(--pink-accent, #FE2C55);"><span class="live-pulse-dot"></span> FEATURED</div>
                                    @else
                                        <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                                    @endif
                                    <div class="badge-viewers-top"><i class="bi bi-box-seam"></i> {{ $product->stock }} in stock</div>
                                </div>
                            </a>
                            <div class="live-card-body">
                                <a href="{{ route('shop.product', $product->id) }}" style="text-decoration: none; color: inherit;">
                                    <div class="live-card-title">{{ Str::limit($product->title, 26) }}</div>
                                </a>
                                <div class="host-row" style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px;">
                                    <div style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                                        <img src="{{ $product->seller && $product->seller->avatar_url ? $product->seller->avatar_url : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100' }}" alt="{{ $product->seller ? $product->seller->name : 'Seller' }}" class="host-avatar">
                                        <span class="host-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 110px;">{{ $product->seller ? $product->seller->name : 'Verified Store' }}</span>
                                    </div>
                                    <div style="font-weight: 800; font-size: 15px; color: var(--pink-accent, #FE2C55); flex-shrink: 0;">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($product->price, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="color: var(--text-muted); grid-column: 1 / -1; padding: 24px; text-align: center; background: var(--bg-card, #1c1d2e); border-radius: 12px; border: 1px solid var(--border-color, rgba(255,255,255,0.07));">
                            <i class="bi bi-bag-x" style="font-size: 28px; display: block; margin-bottom: 8px; color: var(--pink-accent);"></i>
                            No live shopping products available at the moment.
                        </div>
                    @endforelse
                </div>
            </section>


            <!-- SECTION 2: BROWSE CATEGORIES -->
            <section class="section-spacing">
                <div class="section-header-row">
                    <h2 class="section-title">Browse Categories</h2>
                    <div class="nav-arrow-btns">
                        <button class="circle-arrow-btn" id="catPrevBtn"><i class="bi bi-arrow-left"></i></button>
                        <button class="circle-arrow-btn" id="catNextBtn"><i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <div class="categories-scroll-row" id="categoriesScrollRow">
                    @forelse($categories as $cat)
                        @php
                            $iconClass = $cat->icon ?? 'bi-tag-fill';
                            if (!str_starts_with($iconClass, 'bi-') && !str_starts_with($iconClass, 'bi ')) {
                                $iconClass = 'bi-' . $iconClass;
                            }
                            if (!str_starts_with($iconClass, 'bi ')) {
                                $iconClass = 'bi ' . $iconClass;
                            }
                        @endphp
                        <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="category-circle-item" style="text-decoration:none; color:inherit;">
                            <div class="category-circle-icon"><i class="{{ $iconClass }}"></i></div>
                            <span class="category-circle-label">{{ $cat->name }}</span>
                        </a>
                    @empty
                        <a href="{{ route('shop.index', ['category' => 'fashion']) }}" class="category-circle-item" style="text-decoration:none; color:inherit;">
                            <div class="category-circle-icon"><i class="bi bi-universal-access"></i></div>
                            <span class="category-circle-label">Fashion</span>
                        </a>
                        <a href="{{ route('shop.index', ['category' => 'electronics']) }}" class="category-circle-item" style="text-decoration:none; color:inherit;">
                            <div class="category-circle-icon"><i class="bi bi-plug"></i></div>
                            <span class="category-circle-label">Electronics</span>
                        </a>
                        <a href="{{ route('shop.index', ['category' => 'beauty']) }}" class="category-circle-item" style="text-decoration:none; color:inherit;">
                            <div class="category-circle-icon"><i class="bi bi-magic"></i></div>
                            <span class="category-circle-label">Beauty</span>
                        </a>
                        <a href="{{ route('shop.index', ['category' => 'home']) }}" class="category-circle-item" style="text-decoration:none; color:inherit;">
                            <div class="category-circle-icon"><i class="bi bi-house-door"></i></div>
                            <span class="category-circle-label">Home</span>
                        </a>
                        <a href="{{ route('shop.index', ['category' => 'gaming']) }}" class="category-circle-item" style="text-decoration:none; color:inherit;">
                            <div class="category-circle-icon"><i class="bi bi-controller"></i></div>
                            <span class="category-circle-label">Gaming</span>
                        </a>
                        <a href="{{ route('shop.index', ['category' => 'sports']) }}" class="category-circle-item" style="text-decoration:none; color:inherit;">
                            <div class="category-circle-icon"><i class="bi bi-dribbble"></i></div>
                            <span class="category-circle-label">Sports</span>
                        </a>
                        <a href="{{ route('shop.index', ['category' => 'toys']) }}" class="category-circle-item" style="text-decoration:none; color:inherit;">
                            <div class="category-circle-icon"><i class="bi bi-box-seam"></i></div>
                            <span class="category-circle-label">Toys</span>
                        </a>
                        <a href="{{ route('shop.index', ['category' => 'accessories']) }}" class="category-circle-item" style="text-decoration:none; color:inherit;">
                            <div class="category-circle-icon"><i class="bi bi-watch"></i></div>
                            <span class="category-circle-label">Accessories</span>
                        </a>
                    @endforelse
                </div>
            </section>


            <!-- SECTION 3: FEATURED & TRENDING PRODUCTS -->
            <section class="section-spacing">
                <div class="section-header-row">
                    <h2 class="section-title">Featured Products</h2>
                    <a href="{{ route('shop.index', ['filter' => 'featured']) }}" class="view-all-link">View All <i class="bi bi-arrow-right"></i></a>
                </div>

                <div class="grid-3-col">
                    @forelse($featuredProducts as $fp)
                        <div class="zal-card">
                            <a href="{{ route('shop.product', $fp->id) }}" style="text-decoration: none; color: inherit;">
                                <div class="live-card-thumb">
                                    <img src="{{ $fp->primary_image }}" alt="{{ $fp->title }}">
                                    @if($fp->is_trending)
                                        <div class="badge-live-top" style="background: linear-gradient(135deg, #FF6B00, #FF0055);"><span class="live-pulse-dot"></span> HOT</div>
                                    @else
                                        <div class="badge-live-top" style="background: var(--pink-accent);"><span class="live-pulse-dot"></span> FEATURED</div>
                                    @endif
                                    <div class="badge-viewers-top"><i class="bi bi-box-seam"></i> {{ $fp->stock }} in stock</div>
                                </div>
                            </a>
                            <div class="product-card-body">
                                <a href="{{ route('shop.product', $fp->id) }}" style="text-decoration: none; color: inherit;">
                                    <div class="product-card-title">{{ Str::limit($fp->title, 26) }}</div>
                                </a>
                                <div class="rating-row">
                                    <i class="bi bi-star-fill star-icon"></i>
                                    <span>4.9 ({{ $fp->seller ? $fp->seller->name : 'Authentic' }})</span>
                                </div>
                                <div class="product-price-cart-row">
                                    <div class="product-price-val">{{ setting('currency_symbol', '$') }}{{ number_format($fp->price, 2) }}</div>
                                    <button class="btn-add-cart-circle" onclick="window.location='{{ route('shop.product', $fp->id) }}'"><i class="bi bi-bag-plus-fill"></i></button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="color: var(--text-muted); grid-column: 1 / -1; padding: 20px;">No featured products available.</div>
                    @endforelse
                </div>

                <!-- SECTION 4: EARN REWARDS BANNER -->
                <div class="rewards-banner-card mt-3">
                    <div>
                        <div class="rewards-title">Earn More Rewards while you watch!</div>
                        <p class="rewards-desc">Complete daily tasks to unlock exclusive coin packages and vouchers.</p>
                    </div>
                    <div class="rewards-action-side">
                        <div class="rewards-balance-text">
                            <span class="rewards-balance-label">Balance</span> 🪙 1,450</div>
                        <button class="btn-claim-now" id="btnClaimRewards">Claim Now</button>
                    </div>
                </div>
            </section>


            <!-- SECTION 5: TRENDING AUCTIONS (DYNAMIC) -->
            <section class="section-spacing">
                <div class="section-header-row">
                    <div class="section-title-wrap">
                        <h2 class="section-title">Trending Auctions</h2>
                        <span class="badge-ends-soon">Live Bidding</span>
                    </div>
                    <a href="{{ route('auctions.index') }}" class="view-all-link">View All <i class="bi bi-arrow-right"></i></a>
                </div>

                <div class="grid-3-col">
                    @forelse($activeAuctions as $auc)
                        <div class="zal-card">
                            <a href="{{ route('auctions.show', $auc->id) }}" style="text-decoration: none; color: inherit;">
                                <div class="live-card-thumb">
                                    <img src="{{ $auc->image_url ?: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600' }}" alt="{{ $auc->title }}">
                                    <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                                    <div class="badge-viewers-top"><i class="bi bi-hammer"></i> {{ $auc->bids->count() }} bids</div>
                                </div>
                            </a>
                            <div class="live-card-body">
                                <a href="{{ route('auctions.show', $auc->id) }}" style="text-decoration: none; color: inherit;">
                                    <div class="live-card-title">{{ Str::limit($auc->title, 26) }}</div>
                                </a>
                                <div class="auction-info-table">
                                    <div class="auction-info-line">
                                        <span class="auction-info-key">Current Bid</span>
                                        <span class="auction-bid-val">{{ setting('currency_symbol', '$') }}{{ number_format($auc->current_bid, 2) }}</span>
                                    </div>
                                    <div class="auction-info-line">
                                        <span class="auction-info-key">Time Left</span>
                                        <span class="auction-time-val dynamic-auction-countdown" data-ends-at="{{ $auc->ends_at ? $auc->ends_at->toIso8601String() : '' }}">
                                            @if($auc->ends_at && $auc->ends_at->isPast())
                                                Ended
                                            @elseif($auc->ends_at)
                                                {{ $auc->ends_at->diffForHumans(['parts' => 2, 'short' => true]) }}
                                            @else
                                                Live Now
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ route('auctions.show', $auc->id) }}" class="btn-place-bid-cyan" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                    Place Bid
                                </a>
                            </div>
                        </div>
                    @empty
                        <div style="color: var(--text-muted); grid-column: 1 / -1; padding: 24px; text-align: center; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color);">
                            <i class="bi bi-hammer" style="font-size: 28px; display: block; margin-bottom: 8px; color: var(--cyan-accent);"></i>
                            No active live auctions at the moment. Stay tuned for upcoming drops!
                        </div>
                    @endforelse
                </div>
            </section>


            <!-- SECTION 6: RECENTLY VIEWED (STORED IN BROWSER SESSION) -->
            <section class="section-spacing">
                <div class="section-header-row">
                    <h2 class="section-title">Recently Viewed</h2>
                    <a href="{{ route('shop.index') }}" class="view-all-link">View All <i class="bi bi-arrow-right"></i></a>
                </div>

                <div class="grid-5-col">
                    @forelse($recentlyViewedProducts as $rv)
                        <div class="zal-card">
                            <a href="{{ route('shop.product', $rv->id) }}" style="text-decoration: none; color: inherit;">
                                <div class="rv-card-thumb">
                                    <img src="{{ $rv->primary_image }}" alt="{{ $rv->title }}">
                                </div>
                            </a>
                            <div class="rv-card-body">
                                <a href="{{ route('shop.product', $rv->id) }}" style="text-decoration: none; color: inherit;">
                                    <div class="rv-card-title">{{ Str::limit($rv->title, 20) }}</div>
                                </a>
                                <div class="rv-card-price">{{ setting('currency_symbol', '$') }}{{ number_format($rv->price, 2) }}</div>
                            </div>
                        </div>
                    @empty
                        <div style="color: var(--text-muted); grid-column: 1 / -1; padding: 20px;">No recently viewed items yet. Browse the shop to see your history here!</div>
                    @endforelse
                </div>
            </section>

        </div>


        <!-- RIGHT SIDEBAR COLUMN -->
        <aside class="right-sidebar-column">

            <!-- WIDGET 1: MY ORDERS (ONLY VISIBLE WHEN LOGGED IN) -->
            @auth
            <div class="sidebar-widget">
                <div class="widget-title-row">
                    <h3 class="widget-title">My Orders</h3>
                </div>
                <div class="my-orders-grid">
                    <div class="order-stat-box">
                        <div class="order-stat-num">{{ $myPendingOrdersCount ?? 0 }}</div>
                        <div class="order-stat-label">Pending</div>
                    </div>
                    <div class="order-stat-box">
                        <div class="order-stat-num">{{ $myInTransitOrdersCount ?? 0 }}</div>
                        <div class="order-stat-label">In transit</div>
                    </div>
                    <div class="order-stat-box">
                        <div class="order-stat-num">{{ $myDeliveredOrdersCount ?? 0 }}</div>
                        <div class="order-stat-label">Delivered</div>
                    </div>
                </div>
                <a href="{{ auth()->user()->isSeller() ? route('dashboard.seller') : route('dashboard.creator') }}" class="track-orders-link">Track All Orders</a>
            </div>
            @endauth

            <!-- WIDGET 2: TRENDING CREATORS -->
            <div class="sidebar-widget">
                <div class="widget-title-row">
                    <h3 class="widget-title">Trending Creators</h3>
                    <a href="javascript:void(0)" class="view-all-link" style="font-size:0.78rem" id="btnRefreshCreators" onclick="window.location.reload()">Refresh</a>
                </div>
                <div class="creator-list">
                    @forelse($topCreators as $creator)
                        @php
                            $isFollowing = auth()->check() && auth()->user()->following()->where('following_id', $creator->id)->exists();
                            $creatorBio = $creator->creatorProfile->bio ?? 'Verified Creator';
                        @endphp
                        <div class="creator-item-row">
                            <div class="creator-left-info">
                                <img src="{{ $creator->avatar_url }}" alt="{{ $creator->name }}" class="creator-avatar-img">
                                <div>
                                    <div class="creator-name">{{ Str::limit($creator->name, 16) }}</div>
                                    <div class="creator-sub">{{ Str::limit($creatorBio, 20) }}</div>
                                </div>
                            </div>
                            <button class="btn-follow-outline {{ $isFollowing ? 'following' : '' }}" data-user-id="{{ $creator->id }}" onclick="toggleCreatorFollow(this, {{ $creator->id }})">
                                {{ $isFollowing ? 'Following' : 'Follow' }}
                            </button>
                        </div>
                    @empty
                        <div style="color: var(--text-muted); font-size: 13px; text-align: center; padding: 10px;">No creators found.</div>
                    @endforelse
                </div>
            </div>

            <!-- WIDGET 3: UPCOMING EVENTS (DYNAMIC LIVE STREAMS) -->
            <div class="sidebar-widget">
                <div class="widget-title-row">
                    <h3 class="widget-title">Upcoming Events</h3>
                </div>
                <div class="event-list">
                    @forelse($upcomingEvents as $evt)
                        @php
                            $targetRoute = ($evt->stream_type === 'pk_battle') ? route('streams.pk_battle', $evt->id) : route('streams.show', $evt->id);
                        @endphp
                        <div class="event-item-card">
                            <div class="event-top-row">
                                @if($evt->is_live)
                                    <span class="event-date-badge" style="background: rgba(254, 44, 85, 0.15); color: #FE2C55; display: inline-flex; align-items: center; gap: 5px;">
                                        <span class="live-pulse-dot" style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #FE2C55;"></span> LIVE NOW
                                    </span>
                                @elseif($evt->started_at)
                                    <span class="event-date-badge">{{ $evt->started_at->format('M d, h:i A') }}</span>
                                @else
                                    <span class="event-date-badge">UPCOMING</span>
                                @endif
                                <i class="bi bi-bell event-bell-icon" onclick="toggleEventBell(this)" title="Set Reminder"></i>
                            </div>
                            <a href="{{ $targetRoute }}" style="text-decoration: none; color: inherit;">
                                <div class="event-title" style="transition: color 0.2s;" onmouseover="this.style.color='var(--cyan-accent, #25F4EE)'" onmouseout="this.style.color='var(--text-primary, #fff)'">
                                    {{ Str::limit($evt->title, 34) }}
                                </div>
                            </a>
                            <div class="event-interested-sub" style="display: flex; align-items: center; gap: 6px; margin-top: 5px;">
                                <span><i class="bi bi-person-video"></i> {{ $evt->host ? $evt->host->name : 'Live Host' }}</span>
                                <span>&bull;</span>
                                <span><i class="bi bi-people"></i> {{ number_format($evt->viewer_count ?: 450) }} Interested</span>
                            </div>
                        </div>
                    @empty
                        <div style="color: var(--text-muted); padding: 12px; font-size: 13px; text-align: center;">
                            No upcoming streaming events scheduled.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- WIDGET 4: FLASH DEALS PROMO CARD / DYNAMIC SIDEBAR BANNER -->
            @if(isset($sidebarBanner) && $sidebarBanner)
                <div class="flash-deals-promo-widget" style="background: linear-gradient(180deg, rgba(23,24,38,0.7), #171826), url('{{ $sidebarBanner->media_url }}'); background-size: cover; background-position: center;">
                    <span class="flash-promo-badge">PROMO DEAL</span>
                    <div class="flash-promo-title">{{ $sidebarBanner->title }}</div>
                    @if($sidebarBanner->subtitle)
                        <div class="flash-promo-sub">{{ $sidebarBanner->subtitle }}</div>
                    @endif
                    <button class="btn-shop-now-cyan" onclick="window.location='{{ $sidebarBanner->link_url ?: '/live-shopping' }}'">{{ $sidebarBanner->button_text ?: 'Shop Now' }}</button>
                </div>
            @else
                <div class="flash-deals-promo-widget">
                    <span class="flash-promo-badge">LIMITED TIME DEAL</span>
                    <div class="flash-promo-title">Up to 70% Off Drops</div>
                    <div class="flash-promo-sub">Stock remaining: 14 units</div>
                    <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600&auto=format&fit=crop&q=80" alt="Flash Deal" class="flash-promo-bg-img">
                    <button class="btn-shop-now-cyan" onclick="window.location='/live-shopping'">Shop Now</button>
                </div>
            @endif

        </aside>

    </div>

</main>


<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
        <!-- Center Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset(setting('site_logo', 'assets/logo.png')) }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="footer-logo-img">
        </a>
        <p class="footer-tagline-text">
            {{ setting('site_tagline', 'Experience the future of shopping with real-time interaction, live demonstrations, and exclusive community deals.') }}
        </p>
        <div class="footer-copyright-line">
            {{ setting('copyright_text', '© 2026 Zaldoris Live Commerce Ltd. All rights reserved.') }}
        </div>
    </div>
</footer>

<!-- FLOATING WIDGET BUTTON -->
<button class="floating-action-widget" onclick="window.location='/live-shopping'" title="Shop Live">
    <i class="bi bi-bag-fill"></i>
</button>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Category Carousel Controls
        const catRow = document.getElementById('categoriesScrollRow');
        const prevBtn = document.getElementById('catPrevBtn');
        const nextBtn = document.getElementById('catNextBtn');

        if (catRow && prevBtn && nextBtn) {
            prevBtn.addEventListener('click', function() {
                catRow.scrollBy({ left: -240, behavior: 'smooth' });
            });
            nextBtn.addEventListener('click', function() {
                catRow.scrollBy({ left: 240, behavior: 'smooth' });
            });
        }

        // Hero Banner Slider Controls & Autoplay
        const sliderWrapper = document.getElementById('heroBannerSlider');
        const sliderTrack = document.getElementById('heroSliderTrack');
        const prevSlideBtn = document.getElementById('heroSliderPrev');
        const nextSlideBtn = document.getElementById('heroSliderNext');
        const dotsContainer = document.getElementById('heroSliderDots');

        if (sliderTrack) {
            const slides = sliderTrack.querySelectorAll('.hero-slider-slide');
            const totalSlides = slides.length;

            if (totalSlides > 1) {
                let currentSlide = 0;
                let autoplayTimer = null;
                const dots = dotsContainer ? dotsContainer.querySelectorAll('.hero-slider-dot') : [];

                function goToSlide(index) {
                    if (index < 0) {
                        currentSlide = totalSlides - 1;
                    } else if (index >= totalSlides) {
                        currentSlide = 0;
                    } else {
                        currentSlide = index;
                    }
                    sliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;
                    dots.forEach((dot, i) => {
                        dot.classList.toggle('active', i === currentSlide);
                    });
                }

                function startAutoplay() {
                    stopAutoplay();
                    autoplayTimer = setInterval(() => {
                        goToSlide(currentSlide + 1);
                    }, 5000);
                }

                function stopAutoplay() {
                    if (autoplayTimer) clearInterval(autoplayTimer);
                }

                if (prevSlideBtn) {
                    prevSlideBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        goToSlide(currentSlide - 1);
                        startAutoplay();
                    });
                }

                if (nextSlideBtn) {
                    nextSlideBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        goToSlide(currentSlide + 1);
                        startAutoplay();
                    });
                }

                dots.forEach(function(dot) {
                    dot.addEventListener('click', function(e) {
                        e.preventDefault();
                        const targetIdx = parseInt(dot.getAttribute('data-slide'), 10);
                        goToSlide(targetIdx);
                        startAutoplay();
                    });
                });

                if (sliderWrapper) {
                    sliderWrapper.addEventListener('mouseenter', stopAutoplay);
                    sliderWrapper.addEventListener('mouseleave', startAutoplay);
                }

                startAutoplay();
            }
        }
    });
</script>
</body>
</html>
