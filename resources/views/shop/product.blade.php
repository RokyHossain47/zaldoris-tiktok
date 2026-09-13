<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ Str::limit($product->description ?? setting('meta_description'), 160) }}">
    <title>{{ $product->title ?? 'Live Product Room' }} - {{ setting('site_name', 'Zaldoris Live Commerce Platform') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ setting('site_favicon') ? asset(setting('site_favicon')) : asset('assets/favicon.png') }}">
    <link rel="shortcut icon" href="{{ setting('site_favicon') ? asset(setting('site_favicon')) : asset('assets/favicon.png') }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        .chat-footer-box {
            position: relative;
        }

        .chat-popup-tooltip {
            display: none;
            position: absolute;
            bottom: calc(100% + 10px);
            left: 0;
            right: 0;
            background: #11131c;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 14px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.75), 0 0 20px rgba(0, 240, 200, 0.1);
            z-index: 1000;
            animation: tooltipSlideUp 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .chat-popup-tooltip.active {
            display: block;
        }

        @keyframes tooltipSlideUp {
            from {
                opacity: 0;
                transform: translateY(8px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .tooltip-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .tooltip-title {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tooltip-close-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 16px;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 4px;
            line-height: 1;
        }

        .tooltip-close-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        .tooltip-emoji-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 6px;
            max-height: 180px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .tooltip-emoji-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid transparent;
            border-radius: 8px;
            font-size: 22px;
            padding: 6px 2px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tooltip-emoji-btn:hover {
            background: rgba(37, 244, 238, 0.15);
            border-color: var(--cyan-accent);
            transform: scale(1.2);
        }

        .tooltip-gif-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            max-height: 220px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .tooltip-gif-btn {
            border-radius: 8px;
            overflow: hidden;
            height: 64px;
            background: #090D10;
            border: 1px solid rgba(255, 255, 255, 0.12);
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0;
            width: 100%;
        }

        .tooltip-gif-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tooltip-gif-btn:hover {
            border-color: var(--pink-accent);
            transform: scale(1.04);
            box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4);
        }

        .tooltip-gift-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            max-height: 230px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .tooltip-gift-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 8px 4px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .tooltip-gift-card:hover {
            background: rgba(254, 44, 85, 0.15);
            border-color: var(--pink-accent);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(254, 44, 85, 0.35);
        }

        .tooltip-gift-icon {
            width: 34px;
            height: 34px;
            object-fit: contain;
            margin-bottom: 4px;
        }

        .tooltip-gift-name {
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .tooltip-gift-cost {
            font-size: 11px;
            font-weight: 800;
            color: #FFB800;
            display: flex;
            align-items: center;
            gap: 2px;
            margin-top: 2px;
        }

        /* Floating Flying Reaction Particles */
        .floating-reaction-container {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 100px;
            height: 300px;
            pointer-events: none;
            overflow: hidden;
            z-index: 50;
        }

        .floating-bubble {
            position: absolute;
            bottom: 0;
            right: 20px;
            font-size: 28px;
            animation: floatUp 2.5s ease-out forwards;
            opacity: 1;
        }

        @keyframes floatUp {
            0% {
                transform: translateY(0) scale(0.5) rotate(0deg);
                opacity: 0;
            }
            15% {
                opacity: 1;
                transform: translateY(-40px) scale(1.2) rotate(10deg);
            }
            50% {
                transform: translateY(-140px) scale(1) rotate(-10deg);
            }
            100% {
                transform: translateY(-280px) scale(0.8) rotate(15deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR / HEADER -->
<header class="zal-navbar">
    <div class="zal-navbar-inner">
        <!-- Logo -->
        <a class="zal-brand" href="{{ route('home') }}">
            <img src="{{ setting('site_logo') ? asset(setting('site_logo')) : asset('assets/logo.png') }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="zal-brand-logo">
        </a>

        <!-- Center Nav Links -->
        <ul class="zal-nav-menu">
            <li><a href="{{ route('home') }}" class="zal-nav-link">Home</a></li>
            <li><a href="{{ route('shop.index') }}" class="zal-nav-link active">Live Shopping</a></li>
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

    @php
        $host = $product->seller ?? ($stream->host ?? null);
        $hostName = $host->name ?? 'Sneaker Boss';
        $hostAvatar = $host->avatar_url ?? 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=120&auto=format&fit=crop&q=80';
        $productImg = $product->primary_image ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';
        if ($productImg && !str_starts_with($productImg, 'http')) {
            $productImg = asset($productImg);
        }
        $streamVideo = $stream->playback_url ?? 'https://assets.mixkit.co/videos/preview/mixkit-hands-holding-a-smart-watch-41584-large.mp4';
    @endphp

    <!-- THREE-COLUMN LAYOUT GRID -->
    <div class="room-layout-grid">

        <!-- COLUMN 1: LEFT SIDEBAR (HOST & FEATURED PRODUCT) -->
        <aside class="room-left-card">
            <!-- Back Button -->
            <div class="room-back-btn-wrap">
                <a href="{{ route('shop.index') }}" class="auth-back-btn" title="Back to Shop">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </div>

            <!-- Host Identity Section -->
            <div class="room-host-section">
                <div class="host-avatar-wrap">
                    <img src="{{ $hostAvatar }}" alt="{{ $hostName }}" class="host-avatar-img">
                    <span class="badge-host-pill">Host</span>
                </div>
                <h1 class="room-host-name">{{ $hostName }}</h1>
                <div class="room-host-rating">
                    <i class="bi bi-star-fill"></i>
                    <span>4.9 (1.2k reviews)</span>
                </div>
                <div class="room-host-followers">245K Followers</div>
                <button type="button" class="btn-follow-cyan" onclick="toggleCreatorFollow(this)">Follow</button>
            </div>

            <!-- Divider Line -->
            <div class="room-divider"></div>

            <!-- Featured Product Showcase Box -->
            <div class="room-product-box">
                <div class="product-head-row">
                    <img src="{{ $productImg }}" alt="{{ $product->title }}" class="product-thumb-img">
                    <h2 class="product-title-text">{{ $product->title }}</h2>
                </div>

                <div class="product-rating-line">
                    <i class="bi bi-star-fill"></i>
                    <span>4.8</span>
                    <span style="font-size: 11px; color: var(--text-muted); margin-left: 6px;">({{ $product->category_name }})</span>
                </div>

                <div class="product-price-line">
                    <span>Price : </span>
                    <span class="product-price-val">{{ setting('currency_symbol', '$') }}{{ number_format($product->price, 2) }}</span>
                    @if($product->compare_price || $product->price)
                        <span class="product-price-strike">{{ setting('currency_symbol', '$') }}{{ number_format($product->compare_price ?? ($product->price * 1.25), 2) }}</span>
                    @endif
                </div>

                <p class="product-desc-text">
                    {{ Str::limit($product->description ?? 'Live exclusive drop with verified authenticity and 48h dispatch guarantee.', 120) }}
                </p>

                <div class="product-sound-line">
                    <i class="bi bi-music-note-beamed"></i>
                    <span>Original Sound - &#64;{{ Str::slug($hostName, '_') }}</span>
                </div>

                <div class="product-action-row">
                    <a href="{{ route('shop.checkout') }}" class="btn-outline-cyan" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">Buy Now</a>
                    <button type="button" class="btn-solid-cyan" onclick="addCartItem('{{ addslashes($product->title) }}')">Add To Cart</button>
                </div>
            </div>
        </aside>

        <!-- COLUMN 2: CENTER LIVE STREAM VIDEO PLAYER -->
        <div class="room-center-card">
            <!-- Stream Top Control Overlay Header -->
            <div class="stream-top-nav">
                <div class="stream-top-left">
                    <span class="badge-live-pill"><span class="live-pulse-dot"></span> LIVE</span>
                    <span class="badge-stream-meta"><i class="bi bi-eye-fill"></i> {{ number_format($stream->viewer_count ?? 2483) }} Viewers</span>
                    <span class="badge-stream-meta">HD</span>
                </div>
                <div class="stream-top-right">
                    <a href="{{ route('shop.index') }}" class="stream-nav-link">Product</a>
                    <a href="{{ route('streams.index') }}" class="stream-nav-link active">For you</a>
                </div>
            </div>

            <!-- Video Player Frame Box -->
            <div class="video-player-frame">
                <!-- Stream Video Player Background -->
                <video class="video-stream-bg" autoplay loop muted playsinline poster="{{ $productImg }}">
                    <source src="{{ $streamVideo }}" type="video/mp4">
                    <source src="https://assets.mixkit.co/videos/preview/mixkit-hands-holding-a-smart-watch-41584-large.mp4" type="video/mp4">
                    <source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4" type="video/mp4">
                </video>

                <!-- Overlay Translucent Banner -->
                <div class="video-overlay-banner">
                    Calculated based on your regional jurisdiction. Free returns within 30 days.
                </div>

                <!-- Floating Reactions Display Overlay -->
                <div class="floating-reaction-container" id="floatingReactionContainer"></div>

                <!-- Floating Right Action Column inside Video Frame -->
                <div class="video-action-column">
                    <!-- Heart Action -->
                    <div class="video-action-item">
                        <button type="button" class="video-action-btn" title="Like" onclick="toggleLike(this)">
                            <i class="bi bi-heart-fill" style="color: #FF3565;"></i>
                        </button>
                        <span class="video-action-label" id="likeCount">12.4K</span>
                    </div>

                    <!-- Share Action -->
                    <div class="video-action-item">
                        <button type="button" class="video-action-btn" title="Share" onclick="handleShare()">
                            <i class="bi bi-share-fill"></i>
                        </button>
                        <span class="video-action-label">12.4K</span>
                    </div>

                    <!-- Options Action -->
                    <div class="video-action-item">
                        <button type="button" class="video-action-btn" title="More Options" onclick="alert('Stream quality: 1080p60 HD')">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMN 3: RIGHT SIDEBAR (LIVE CHAT) -->
        <aside class="room-right-card">
            <!-- Chat Title Row -->
            <div class="chat-header-row">
                <h2 class="chat-title">Live Chat <span class="notif-dot-cyan"></span></h2>
            </div>

            <!-- Flash Sale Promo Banner -->
            <div class="flash-sale-banner" id="flashSaleBanner">
                <span><i class="bi bi-clock-history"></i> Flash Sale ends in 10 minutes</span>
                <button type="button" class="btn-close-banner" onclick="document.getElementById('flashSaleBanner').style.display='none'">✕</button>
            </div>

            <!-- Chat Messages Scroll Container -->
            <div class="chat-messages-container" id="chatMsgBox">
                <!-- Message 1 -->
                <div class="chat-msg-item">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=60&auto=format&fit=crop&q=80" alt="Fashionista92" class="chat-user-avatar">
                    <div class="chat-msg-body">
                        <div class="chat-username-row">
                            <span class="chat-username">Fashionista92</span>
                        </div>
                        <div class="chat-bubble">
                            Does this come in extra sizes?
                        </div>
                    </div>
                </div>

                <!-- Message 2 (Host Message) -->
                <div class="chat-msg-item">
                    <img src="{{ $hostAvatar }}" alt="{{ $hostName }}" class="chat-user-avatar">
                    <div class="chat-msg-body">
                        <div class="chat-username-row">
                            <span class="chat-username">{{ $hostName }}</span>
                            <span class="badge-host-mini">host</span>
                        </div>
                        <div class="chat-bubble host-bubble">
                            Yes! We have {{ $product->stock }} units left in stock right now!
                        </div>
                    </div>
                </div>

                <!-- Message 3 -->
                <div class="chat-msg-item">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&auto=format&fit=crop&q=80" alt="AlexTrend" class="chat-user-avatar">
                    <div class="chat-msg-body">
                        <div class="chat-username-row">
                            <span class="chat-username">AlexTrend</span>
                        </div>
                        <div class="chat-bubble">
                            The quality looks amazing in 1080p HD!
                        </div>
                    </div>
                </div>

                <!-- Message 4 -->
                <div class="chat-msg-item">
                    <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=60&auto=format&fit=crop&q=80" alt="Mila_Vibe" class="chat-user-avatar">
                    <div class="chat-msg-body">
                        <div class="chat-username-row">
                            <span class="chat-username">Mila_Vibe</span>
                        </div>
                        <div class="chat-bubble">
                            Just bought {{ Str::limit($product->title, 20) }}! So excited ✨
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Footer Input & Actions -->
            <!-- Chat Footer Input & Actions -->
            <div class="chat-footer-box">

                <!-- 1. EMOJIS / REACTIONS FLOATING TOOLTIP -->
                <div class="chat-popup-tooltip" id="emojiTooltipPicker">
                    <div class="tooltip-header">
                        <div class="tooltip-title">
                            <span>😃 Quick Reactions & Emojis</span>
                        </div>
                        <button type="button" class="tooltip-close-btn" onclick="toggleChatTooltip('emojiTooltipPicker')">✕</button>
                    </div>
                    <div class="tooltip-emoji-grid">
                        @if(isset($reactions) && $reactions->count())
                            @foreach($reactions as $r)
                                @php
                                    $rMedia = '';
                                    if (!empty($r->media_url)) {
                                        $rMedia = (str_starts_with($r->media_url, 'http') || str_starts_with($r->media_url, '/uploads') || str_starts_with($r->media_url, '/storage'))
                                            ? $r->media_url
                                            : asset($r->media_url);
                                    }
                                @endphp
                                <button type="button" class="tooltip-emoji-btn" title="{{ $r->name }}" onclick="selectEmojiReaction('{{ $r->code ?: '✨' }}', '{{ $rMedia }}')">
                                    @if($rMedia)
                                        <img src="{{ $rMedia }}" alt="{{ $r->name }}" style="width: 26px; height: 26px; object-fit: contain;">
                                    @else
                                        {{ $r->code ?: '✨' }}
                                    @endif
                                </button>
                            @endforeach
                        @else
                            @foreach(['🔥', '😍', '❤️', '🎉', '👏', '💯', '🚀', '🥳', '💰', '💎', '⚡', '✨', '👍', '🤩', '🛒', '👑', '🙌', '🌟'] as $em)
                                <button type="button" class="tooltip-emoji-btn" onclick="selectEmojiReaction('{{ $em }}', '')">
                                    {{ $em }}
                                </button>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- 2. ANIMATED GIFS FLOATING TOOLTIP -->
                <div class="chat-popup-tooltip" id="gifTooltipPicker">
                    <div class="tooltip-header">
                        <div class="tooltip-title">
                            <span>🎬 Trending Animated GIFs</span>
                        </div>
                        <button type="button" class="tooltip-close-btn" onclick="toggleChatTooltip('gifTooltipPicker')">✕</button>
                    </div>
                    <div class="tooltip-gif-grid">
                        @if(isset($gifs) && $gifs->count())
                            @foreach($gifs as $g)
                                @php
                                    $gMedia = (str_starts_with($g->media_url ?? '', 'http') || str_starts_with($g->media_url ?? '', '/uploads') || str_starts_with($g->media_url ?? '', '/storage'))
                                        ? $g->media_url
                                        : asset($g->media_url);
                                @endphp
                                <button type="button" class="tooltip-gif-btn" title="{{ $g->name }}" onclick="sendGifMessage('{{ $gMedia }}', '{{ $g->name }}')">
                                    <img src="{{ $gMedia }}" alt="{{ $g->name }}" loading="lazy">
                                </button>
                            @endforeach
                        @else
                            <button type="button" class="tooltip-gif-btn" onclick="sendGifMessage('https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif', 'Dance')">
                                <img src="https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif" alt="Dance">
                            </button>
                            <button type="button" class="tooltip-gif-btn" onclick="sendGifMessage('https://media.giphy.com/media/26ufdipQqU2lhNA4g/giphy.gif', 'Mind Blown')">
                                <img src="https://media.giphy.com/media/26ufdipQqU2lhNA4g/giphy.gif" alt="Mind Blown">
                            </button>
                            <button type="button" class="tooltip-gif-btn" onclick="sendGifMessage('https://media.giphy.com/media/artj92V8o75VPL7AeQ/giphy.gif', 'Hype')">
                                <img src="https://media.giphy.com/media/artj92V8o75VPL7AeQ/giphy.gif" alt="Hype">
                            </button>
                            <button type="button" class="tooltip-gif-btn" onclick="sendGifMessage('https://media.giphy.com/media/gl0mkIZOW6Nwc/giphy.gif', 'Popcorn')">
                                <img src="https://media.giphy.com/media/gl0mkIZOW6Nwc/giphy.gif" alt="Popcorn">
                            </button>
                            <button type="button" class="tooltip-gif-btn" onclick="sendGifMessage('https://media.giphy.com/media/sDcfxFDozb3bO/giphy.gif', 'Take My Money')">
                                <img src="https://media.giphy.com/media/sDcfxFDozb3bO/giphy.gif" alt="Take My Money">
                            </button>
                            <button type="button" class="tooltip-gif-btn" onclick="sendGifMessage('https://media.giphy.com/media/7rj2ZgttvgomY/giphy.gif', 'Clap')">
                                <img src="https://media.giphy.com/media/7rj2ZgttvgomY/giphy.gif" alt="Clap">
                            </button>
                        @endif
                    </div>
                </div>

                <!-- 3. LIVE GIFTS FLOATING TOOLTIP -->
                <div class="chat-popup-tooltip" id="giftTooltipPicker">
                    <div class="tooltip-header">
                        <div class="tooltip-title">
                            <span>🎁 Send Live Gift</span>
                        </div>
                        <span style="font-size: 11px; color: #FFB800; font-weight: 700;">🪙 1,450 Coins</span>
                        <button type="button" class="tooltip-close-btn" onclick="toggleChatTooltip('giftTooltipPicker')">✕</button>
                    </div>
                    <div class="tooltip-gift-grid">
                        @if(isset($gifts) && $gifts->count())
                            @foreach($gifts as $gt)
                                @php
                                    $iconPath = (str_starts_with($gt->icon_url ?? '', 'http') || str_starts_with($gt->icon_url ?? '', '/uploads') || str_starts_with($gt->icon_url ?? '', '/storage')) 
                                        ? $gt->icon_url 
                                        : asset($gt->icon_url ?: 'assets/gifts/heart.svg');
                                @endphp
                                <div class="tooltip-gift-card" onclick="sendGiftMessage('{{ $gt->name }}', {{ $gt->coin_cost }}, '{{ $iconPath }}')">
                                    <img src="{{ $iconPath }}" alt="{{ $gt->name }}" class="tooltip-gift-icon">
                                    <span class="tooltip-gift-name">{{ $gt->name }}</span>
                                    <span class="tooltip-gift-cost">🪙 {{ number_format($gt->coin_cost) }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="tooltip-gift-card" onclick="sendGiftMessage('Glow Heart', 10, '{{ asset('assets/gifts/heart.svg') }}')">
                                <img src="{{ asset('assets/gifts/heart.svg') }}" class="tooltip-gift-icon">
                                <span class="tooltip-gift-name">Glow Heart</span>
                                <span class="tooltip-gift-cost">🪙 10</span>
                            </div>
                            <div class="tooltip-gift-card" onclick="sendGiftMessage('Star Wink', 50, '{{ asset('assets/gifts/star.svg') }}')">
                                <img src="{{ asset('assets/gifts/star.svg') }}" class="tooltip-gift-icon">
                                <span class="tooltip-gift-name">Star Wink</span>
                                <span class="tooltip-gift-cost">🪙 50</span>
                            </div>
                            <div class="tooltip-gift-card" onclick="sendGiftMessage('Energy Shot', 100, '{{ asset('assets/gifts/energy.svg') }}')">
                                <img src="{{ asset('assets/gifts/energy.svg') }}" class="tooltip-gift-icon">
                                <span class="tooltip-gift-name">Energy Shot</span>
                                <span class="tooltip-gift-cost">🪙 100</span>
                            </div>
                            <div class="tooltip-gift-card" onclick="sendGiftMessage('Royal Crown', 1000, '{{ asset('assets/gifts/throne.svg') }}')">
                                <img src="{{ asset('assets/gifts/throne.svg') }}" class="tooltip-gift-icon">
                                <span class="tooltip-gift-name">Royal Crown</span>
                                <span class="tooltip-gift-cost">🪙 1,000</span>
                            </div>
                        @endif
                    </div>
                </div>

                <form onsubmit="handleSendChatMessage(event)" class="chat-input-row">
                    <input type="text" id="chatInputField" class="chat-input-field" placeholder="Say something..." required autocomplete="off">
                    <button type="submit" class="btn-chat-send" title="Send message">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>

                <div class="chat-toolbar-row">
                    <div class="chat-tools-left">
                        <button type="button" class="btn-tool-icon" id="btnToggleEmoji" title="Reactions & Emojis" onclick="toggleChatTooltip('emojiTooltipPicker')">
                            <i class="bi bi-emoji-smile"></i>
                        </button>
                        <button type="button" class="btn-gif-pill" id="btnToggleGif" style="cursor:pointer; background: none; border: 1px solid rgba(255,255,255,0.2);" title="Animated GIFs" onclick="toggleChatTooltip('gifTooltipPicker')">
                            GIF
                        </button>
                    </div>

                    <button type="button" class="btn-send-gift-link" id="btnToggleGift" onclick="toggleChatTooltip('giftTooltipPicker')">
                        <i class="bi bi-gift-fill"></i>
                        <span>Send Gift</span>
                    </button>
                </div>
            </div>
        </aside>

    </div>

</main>

<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
        <!-- Center Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ setting('site_logo') ? asset(setting('site_logo')) : asset('assets/logo.png') }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="footer-logo-img">
        </a>
        <p class="footer-tagline-text">
            {{ setting('site_tagline', 'Experience the future of shopping with realtime interaction, live demonstrations, and exclusive community deals.') }}
        </p>
        <div class="footer-copyright-line">
            {{ setting('copyright_text', '© 2026 Zaldoris LiveStreamShop. All rights reserved.') }}
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
    // Toggle tooltip popups
    function toggleChatTooltip(tooltipId) {
        const tooltips = ['emojiTooltipPicker', 'gifTooltipPicker', 'giftTooltipPicker'];
        tooltips.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                if (id === tooltipId) {
                    el.classList.toggle('active');
                } else {
                    el.classList.remove('active');
                }
            }
        });
    }

    // Close tooltips when clicking outside
    document.addEventListener('click', function(e) {
        const footerBox = document.querySelector('.chat-footer-box');
        if (footerBox && !footerBox.contains(e.target)) {
            ['emojiTooltipPicker', 'gifTooltipPicker', 'giftTooltipPicker'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('active');
            });
        }
    });

    // Select emoji: adds to chat input & triggers floating bubble
    function selectEmojiReaction(emoji, mediaUrl = '') {
        if (mediaUrl) {
            sendGifMessage(mediaUrl, emoji || 'Reaction');
            createFloatingReaction(mediaUrl);
        } else {
            const input = document.getElementById('chatInputField');
            if (input) {
                input.value += (input.value.length ? ' ' : '') + emoji;
                input.focus();
            }
            createFloatingReaction(emoji);
        }
        toggleChatTooltip('emojiTooltipPicker');
    }

    // Send GIF message into chat
    function sendGifMessage(gifUrl, name) {
        const box = document.getElementById('chatMsgBox');
        if (box) {
            const msgItem = document.createElement('div');
            msgItem.className = 'chat-msg-item';
            msgItem.innerHTML = `
                <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=60&auto=format&fit=crop&q=80" alt="You" class="chat-user-avatar">
                <div class="chat-msg-body">
                    <div class="chat-username-row">
                        <span class="chat-username" style="color: var(--cyan-accent, #00F0C8);">You</span>
                    </div>
                    <div class="chat-bubble" style="padding: 6px; background: transparent; border: none;">
                        <img src="${gifUrl}" alt="${name}" style="max-width: 170px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.18); box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                    </div>
                </div>
            `;
            box.appendChild(msgItem);
            box.scrollTop = box.scrollHeight;
        }
        createFloatingReaction(gifUrl);
        toggleChatTooltip('gifTooltipPicker');
    }

    // Send Gift message into chat
    function sendGiftMessage(giftName, coinCost, iconUrl) {
        const box = document.getElementById('chatMsgBox');
        if (box) {
            const msgItem = document.createElement('div');
            msgItem.className = 'chat-msg-item';
            msgItem.innerHTML = `
                <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=60&auto=format&fit=crop&q=80" alt="You" class="chat-user-avatar">
                <div class="chat-msg-body" style="width: 100%;">
                    <div style="background: linear-gradient(135deg, rgba(254, 44, 85, 0.2), rgba(255, 0, 85, 0.08)); border: 1px solid rgba(254, 44, 85, 0.4); border-radius: 12px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="${iconUrl}" alt="${giftName}" style="width: 32px; height: 32px; object-fit: contain;">
                            <div>
                                <div style="font-size: 13px; font-weight: 800; color: #fff;">Sent a ${escapeHtml(giftName)}!</div>
                                <div style="font-size: 11px; color: var(--text-muted);">🪙 ${coinCost} Coins Gift</div>
                            </div>
                        </div>
                        <span style="background: #FE2C55; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 10px;">VIP GIFT</span>
                    </div>
                </div>
            `;
            box.appendChild(msgItem);
            box.scrollTop = box.scrollHeight;
        }
        createFloatingReaction(iconUrl);
        toggleChatTooltip('giftTooltipPicker');
    }

    // Floating reaction animation
    function createFloatingReaction(content) {
        const container = document.getElementById('floatingReactionContainer');
        if (!container) return;
        const bubble = document.createElement('div');
        bubble.className = 'floating-bubble';
        
        if (content && (content.startsWith('http') || content.startsWith('/uploads') || content.startsWith('/storage') || content.startsWith('assets/'))) {
            bubble.innerHTML = `<img src="${content}" style="width: 32px; height: 32px; object-fit: contain;">`;
        } else {
            bubble.innerText = content || '✨';
        }

        bubble.style.right = (10 + Math.random() * 50) + 'px';
        container.appendChild(bubble);
        setTimeout(() => bubble.remove(), 2500);
    }

    // Live Chat Send Message Handler
    function handleSendChatMessage(event) {
        event.preventDefault();
        const input = document.getElementById('chatInputField');
        const box = document.getElementById('chatMsgBox');
        if (input && input.value.trim() !== '' && box) {
            const msgText = input.value.trim();
            const msgItem = document.createElement('div');
            msgItem.className = 'chat-msg-item';
            msgItem.innerHTML = `
                <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=60&auto=format&fit=crop&q=80" alt="You" class="chat-user-avatar">
                <div class="chat-msg-body">
                    <div class="chat-username-row">
                        <span class="chat-username" style="color: var(--cyan-accent, #00F0C8);">You</span>
                    </div>
                    <div class="chat-bubble">
                        ${escapeHtml(msgText)}
                    </div>
                </div>
            `;
            box.appendChild(msgItem);
            box.scrollTop = box.scrollHeight;
            input.value = '';
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function addCartItem(name) {
        alert(name + ' has been added to your cart!');
    }

    function toggleCreatorFollow(btn) {
        if (btn.innerText === 'Follow') {
            btn.innerText = 'Following ✓';
            btn.style.background = '#00F0C8';
            btn.style.color = '#090D10';
        } else {
            btn.innerText = 'Follow';
            btn.style.background = 'transparent';
            btn.style.color = '#00F0C8';
        }
    }

    let liked = false;
    function toggleLike(btn) {
        const countEl = document.getElementById('likeCount');
        if (!liked) {
            liked = true;
            btn.querySelector('i').style.color = '#FE2C55';
            countEl.innerText = '12.5K';
            createFloatingReaction('❤️');
        } else {
            liked = false;
            countEl.innerText = '12.4K';
        }
    }

    function handleShare() {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(window.location.href);
            alert('Product room link copied to clipboard!');
        } else {
            alert('Link: ' + window.location.href);
        }
    }
</script>
</body>
</html>
