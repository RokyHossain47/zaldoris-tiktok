<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Zaldoris - Live Streaming. Watch realtime creator live streams, interact, and shop live deals.">
    <title>Live Streaming - Zaldoris Live Commerce Platform</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.png') }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <!-- EMBEDDED STYLES FOR LIVE STREAMING PAGE -->
    <style>
    /* ============================================================
       LIVE STREAMING PAGE STYLES (Pixel-Perfect Figma Match)
       ============================================================ */

    .live-stream-grid {
      display: grid !important;
      grid-template-columns: 240px 1fr 340px !important;
      gap: 1.25rem !important;
      align-items: start !important;
      max-width: 1400px !important;
      margin: 0 auto !important;
      padding: 1.5rem 1rem 3.5rem !important;
    }

    .live-stream-grid > * {
      min-width: 0 !important;
      max-width: 100% !important;
    }

    .stream-left-sidebar {
      display: flex !important;
      flex-direction: column !important;
      gap: 1.25rem !important;
    }

    .stream-host-card {
      background: #0B0F14 !important;
      border: 1px solid #16202C !important;
      border-radius: 16px !important;
      padding: 2rem 1.25rem !important;
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      text-align: center !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }

    .stream-host-avatar-wrap {
      width: 90px !important;
      height: 90px !important;
      min-width: 90px !important;
      max-width: 90px !important;
      border-radius: 50% !important;
      border: 2px solid #00F0C8 !important;
      padding: 3px !important;
      margin-bottom: 1rem !important;
      position: relative !important;
      overflow: hidden !important;
      box-shadow: 0 0 20px rgba(0, 240, 200, 0.25) !important;
    }

    .stream-host-avatar-wrap img {
      width: 100% !important;
      height: 100% !important;
      border-radius: 50% !important;
      object-fit: cover !important;
      display: block !important;
    }

    .stream-host-name {
      font-family: 'Outfit', sans-serif !important;
      font-size: 1.25rem !important;
      font-weight: 700 !important;
      color: #FFFFFF !important;
      margin-bottom: 0.3rem !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 6px !important;
    }

    .badge-host-cyan {
      background: rgba(0, 240, 200, 0.15) !important;
      color: #00F0C8 !important;
      border: 1px solid rgba(0, 240, 200, 0.4) !important;
      font-size: 0.7rem !important;
      font-weight: 700 !important;
      padding: 0.15rem 0.5rem !important;
      border-radius: 20px !important;
      text-transform: lowercase !important;
    }

    .stream-host-rating {
      font-size: 0.85rem !important;
      color: #94A3B8 !important;
      margin-bottom: 0.2rem !important;
      display: flex !important;
      align-items: center !important;
      gap: 4px !important;
    }

    .stream-host-rating i {
      color: #F59E0B !important;
    }

    .stream-host-followers {
      font-size: 0.85rem !important;
      color: #94A3B8 !important;
      margin-bottom: 1.5rem !important;
    }

    .stream-host-actions {
      display: flex !important;
      flex-direction: column !important;
      gap: 0.75rem !important;
      width: 100% !important;
    }

    .btn-stream-follow {
      width: 100% !important;
      height: 42px !important;
      background: #0B0F14 !important;
      border: 1px solid #1E293B !important;
      color: #00F0C8 !important;
      font-size: 0.9rem !important;
      font-weight: 600 !important;
      border-radius: 10px !important;
      cursor: pointer !important;
      transition: all 0.3s ease !important;
    }

    .btn-stream-subscribe {
      width: 100% !important;
      height: 42px !important;
      background: #00F0C8 !important;
      color: #090D10 !important;
      font-size: 0.9rem !important;
      font-weight: 700 !important;
      border-radius: 10px !important;
      border: none !important;
      cursor: pointer !important;
      transition: all 0.3s ease !important;
      text-decoration: none !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
    }

    .stream-middle-column {
      background: #080C10 !important;
      border: 1px solid #16202C !important;
      border-radius: 16px !important;
      padding: 1.25rem !important;
      position: relative !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
    }

    .stream-top-control-bar {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      width: 100% !important;
      margin-bottom: 1rem !important;
    }

    .stream-top-badges {
      display: flex !important;
      align-items: center !important;
      gap: 0.5rem !important;
    }

    .badge-live-pink {
      background: #FF2A6D !important;
      color: #FFFFFF !important;
      font-size: 0.75rem !important;
      font-weight: 800 !important;
      padding: 0.3rem 0.75rem !important;
      border-radius: 20px !important;
      display: flex !important;
      align-items: center !important;
      gap: 5px !important;
    }

    .badge-viewers-dark,
    .badge-hd-dark {
      background: rgba(255, 255, 255, 0.08) !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      color: #FFFFFF !important;
      font-size: 0.78rem !important;
      font-weight: 600 !important;
      padding: 0.3rem 0.75rem !important;
      border-radius: 20px !important;
      display: flex !important;
      align-items: center !important;
      gap: 5px !important;
    }

    .stream-top-tabs {
      display: flex !important;
      align-items: center !important;
      gap: 1.25rem !important;
      font-size: 0.88rem !important;
    }

    .stream-tab-link {
      color: #94A3B8 !important;
      text-decoration: none !important;
      font-weight: 500 !important;
      position: relative !important;
      padding-bottom: 4px !important;
    }

    .stream-tab-link.active {
      color: #FFFFFF !important;
      font-weight: 700 !important;
    }

    .stream-tab-link.active::after {
      content: '' !important;
      position: absolute !important;
      bottom: 0 !important;
      left: 0 !important;
      right: 0 !important;
      height: 2px !important;
      background: #00F0C8 !important;
      border-radius: 2px !important;
    }

    /* Single Portrait Stream Player Frame Container */
    .single-stream-frame-container {
      position: relative !important;
      width: 350px !important;
      height: 600px !important;
      border-radius: 14px !important;
      overflow: hidden !important;
      background: #000 !important;
      border: 1px solid rgba(255, 255, 255, 0.08) !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
    }

    .single-stream-frame-container img {
      width: 100% !important;
      height: 100% !important;
      object-fit: cover !important;
      display: block !important;
    }

    .stream-subtitle-caption {
      position: absolute !important;
      bottom: 1.25rem !important;
      left: 50% !important;
      transform: translateX(-50%) !important;
      background: rgba(255, 255, 255, 0.88) !important;
      backdrop-filter: blur(8px) !important;
      color: #090D10 !important;
      font-size: 0.82rem !important;
      font-weight: 600 !important;
      padding: 0.45rem 1.25rem !important;
      border-radius: 8px !important;
      white-space: nowrap !important;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
      z-index: 10 !important;
    }

    /* Floating Right Side Action Column inside Middle Stream Box */
    .stream-floating-actions {
      position: absolute !important;
      right: 1.5rem !important;
      bottom: 4rem !important;
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      gap: 1.5rem !important;
      z-index: 20 !important;
    }

    .stream-action-item {
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      gap: 4px !important;
    }

    .stream-circle-action-btn {
      width: 48px !important;
      height: 48px !important;
      border-radius: 50% !important;
      background: rgba(13, 17, 23, 0.65) !important;
      backdrop-filter: blur(10px) !important;
      border: 1px solid rgba(255, 255, 255, 0.15) !important;
      color: #FFFFFF !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      font-size: 1.2rem !important;
      cursor: pointer !important;
      transition: all 0.3s ease !important;
    }

    .stream-action-count {
      font-size: 0.72rem !important;
      font-weight: 700 !important;
      color: #FFFFFF !important;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8) !important;
    }

    /* Right Column Live Chat */
    .stream-right-sidebar {
      background: #0B0F14 !important;
      border: 1px solid #16202C !important;
      border-radius: 16px !important;
      padding: 1.25rem !important;
      display: flex !important;
      flex-direction: column !important;
      height: 660px !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }

    .stream-chat-header {
      font-family: 'Outfit', sans-serif !important;
      font-size: 1.1rem !important;
      font-weight: 700 !important;
      color: #FFFFFF !important;
      display: flex !important;
      align-items: center !important;
      gap: 8px !important;
      padding-bottom: 0.85rem !important;
      border-bottom: 1px solid #16202C !important;
      margin-bottom: 1rem !important;
    }

    .chat-live-dot {
      width: 8px !important;
      height: 8px !important;
      border-radius: 50% !important;
      background: #00F0C8 !important;
      box-shadow: 0 0 8px #00F0C8 !important;
    }

    .stream-chat-messages-list {
      flex: 1 !important;
      overflow-y: auto !important;
      display: flex !important;
      flex-direction: column !important;
      gap: 1.1rem !important;
      padding-right: 4px !important;
    }

    .stream-chat-item {
      display: flex !important;
      align-items: flex-start !important;
      gap: 0.65rem !important;
    }

    .stream-chat-avatar {
      width: 32px !important;
      height: 32px !important;
      border-radius: 50% !important;
      object-fit: cover !important;
      flex-shrink: 0 !important;
    }

    .stream-chat-content {
      flex: 1 !important;
    }

    .stream-chat-user-row {
      display: flex !important;
      align-items: center !important;
      gap: 6px !important;
      margin-bottom: 4px !important;
    }

    .stream-chat-username {
      font-size: 0.8rem !important;
      font-weight: 600 !important;
      color: #94A3B8 !important;
    }

    .badge-chat-host {
      background: #00F0C8 !important;
      color: #090D10 !important;
      font-size: 0.65rem !important;
      font-weight: 800 !important;
      padding: 0.1rem 0.4rem !important;
      border-radius: 4px !important;
    }

    .stream-chat-bubble {
      background: #111822 !important;
      border: 1px solid #1E293B !important;
      border-radius: 10px !important;
      padding: 0.6rem 0.85rem !important;
      font-size: 0.83rem !important;
      color: #FFFFFF !important;
      line-height: 1.4 !important;
      display: inline-block !important;
    }

    .stream-chat-bubble.host-bubble {
      background: rgba(0, 240, 200, 0.08) !important;
      border-color: rgba(0, 240, 200, 0.4) !important;
      color: #FFFFFF !important;
    }

    .stream-chat-input-area {
      margin-top: 1rem !important;
      padding-top: 0.85rem !important;
      border-top: 1px solid #16202C !important;
    }

    .stream-input-box-wrap {
      position: relative !important;
      width: 100% !important;
      margin-bottom: 0.65rem !important;
    }

    .stream-chat-input {
      width: 100% !important;
      height: 42px !important;
      background: #0B0F14 !important;
      border: 1px solid #1E293B !important;
      border-radius: 10px !important;
      padding: 0 2.5rem 0 0.85rem !important;
      color: #FFFFFF !important;
      font-size: 0.85rem !important;
      outline: none !important;
    }

    .btn-send-chat-stream {
      position: absolute !important;
      right: 0.5rem !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      background: transparent !important;
      border: none !important;
      color: #94A3B8 !important;
      font-size: 1rem !important;
      cursor: pointer !important;
    }

    .stream-chat-actions-bottom {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      font-size: 0.85rem !important;
    }

    .chat-extra-icons-stream {
      display: flex !important;
      align-items: center !important;
      gap: 0.85rem !important;
      color: #94A3B8 !important;
    }

    .chat-icon-btn-stream {
      background: transparent !important;
      border: none !important;
      color: #94A3B8 !important;
      cursor: pointer !important;
      font-size: 1.1rem !important;
      padding: 0 !important;
    }

    .gif-tag-stream {
      font-size: 0.72rem !important;
      font-weight: 800 !important;
      background: rgba(255, 255, 255, 0.1) !important;
      padding: 0.15rem 0.4rem !important;
      border-radius: 4px !important;
    }

    .btn-send-gift-stream {
      background: transparent !important;
      border: none !important;
      color: #94A3B8 !important;
      font-size: 0.82rem !important;
      font-weight: 600 !important;
      display: flex !important;
      align-items: center !important;
      gap: 5px !important;
      cursor: pointer !important;
    }
    </style>
</head>
<body>

<!-- NAVBAR -->
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
            <li><a href="{{ route('auctions.index') }}" class="zal-nav-link">Live Auction</a></li>
            <li><a href="#" class="zal-nav-link">Live Academy</a></li>
            <li><a href="{{ route('streams.index') }}" class="zal-nav-link active">Live Streaming</a></li>
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
<main class="page-container" style="max-width: 1440px; margin: 0 auto; padding: 1rem 0;">

    <!-- 3-COLUMN LIVE STREAM GRID -->
    <div class="live-stream-grid">

        <!-- LEFT COLUMN: STREAMER HOST PROFILE -->
        <aside class="stream-left-sidebar">
            <a href="{{ route('home') }}" class="auth-back-btn" title="Back to Home">
                <i class="bi bi-chevron-left"></i>
            </a>

            <div class="stream-host-card">
                <div class="stream-host-avatar-wrap">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&auto=format&fit=crop&q=80" alt="Sneaker Boss">
                </div>
                
                <h2 class="stream-host-name">
                    Sneaker Boss
                    <span class="badge-host-cyan">Host</span>
                </h2>

                <div class="stream-host-rating">
                    <i class="bi bi-star-fill"></i> 4.9 (1.2k reviews)
                </div>

                <div class="stream-host-followers">245K Followers</div>

                <div class="stream-host-actions">
                    <button class="btn-stream-follow" onclick="toggleStreamFollow(this)">Follow</button>
                    <a href="{{ route('wallet.subscribe') }}" class="btn-stream-subscribe">Subscribe</a>
                </div>
            </div>
        </aside>

        <!-- MIDDLE COLUMN: SINGLE TALL PORTRAIT LIVE STREAM FRAME -->
        <section class="stream-middle-column">
            <!-- Top Control Bar Overlay -->
            <div class="stream-top-control-bar">
                <div class="stream-top-badges">
                    <span class="badge-live-pink">● LIVE</span>
                    <span class="badge-viewers-dark"><i class="bi bi-eye"></i> 2,483 Viewers</span>
                    <span class="badge-hd-dark">HD</span>
                </div>
                <div class="stream-top-tabs">
                    <a href="#" class="stream-tab-link">Product</a>
                    <a href="#" class="stream-tab-link active">For you</a>
                </div>
            </div>

            <!-- Single Tall Video Player Frame Container -->
            <div class="single-stream-frame-container">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&auto=format&fit=crop&q=80" alt="Live Streamer Broadcast">
                
                <!-- Bottom Subtitle Caption Banner -->
                <div class="stream-subtitle-caption">
                    Calculated based on your regional jurisdiction.
                </div>
            </div>

            <!-- Floating Action Column Right of Video inside Middle Column -->
            <div class="stream-floating-actions">
                <div class="stream-action-item">
                    <button class="stream-circle-action-btn" onclick="toggleStreamLike(this)" title="Like">
                        <i class="bi bi-heart-fill" style="color: #FF2A6D;"></i>
                    </button>
                    <span class="stream-action-count">12.4K</span>
                </div>
                <div class="stream-action-item">
                    <button class="stream-circle-action-btn" title="Share">
                        <i class="bi bi-share"></i>
                    </button>
                    <span class="stream-action-count">12.4K</span>
                </div>
                <div class="stream-action-item">
                    <button class="stream-circle-action-btn" onclick="openSendGiftModal()" title="Send Gift">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- RIGHT COLUMN: LIVE CHAT SIDEBAR -->
        <aside class="stream-right-sidebar">
            <div class="stream-chat-header">
                Live Chat
                <span class="chat-live-dot"></span>
            </div>

            <!-- Messages List -->
            <div class="stream-chat-messages-list" id="streamChatMessages">
                <!-- User Message 1 -->
                <div class="stream-chat-item">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&auto=format&fit=crop&q=80" class="stream-chat-avatar" alt="Fashionista92">
                    <div class="stream-chat-content">
                        <div class="stream-chat-user-row">
                            <span class="stream-chat-username">Fashionista92</span>
                        </div>
                        <div class="stream-chat-bubble">
                            Does this blouse come in extra small?
                        </div>
                    </div>
                </div>

                <!-- Host Message 2 -->
                <div class="stream-chat-item">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80" class="stream-chat-avatar" alt="Sneaker Boss">
                    <div class="stream-chat-content">
                        <div class="stream-chat-user-row">
                            <span class="stream-chat-username">Sneaker Boss</span>
                            <span class="badge-chat-host">host</span>
                        </div>
                        <div class="stream-chat-bubble host-bubble">
                            Yes! We have 5 units of XS left in stock right now!
                        </div>
                    </div>
                </div>

                <!-- User Message 3 -->
                <div class="stream-chat-item">
                    <img src="https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=100&auto=format&fit=crop&q=80" class="stream-chat-avatar" alt="AlexTrend">
                    <div class="stream-chat-content">
                        <div class="stream-chat-user-row">
                            <span class="stream-chat-username">AlexTrend</span>
                        </div>
                        <div class="stream-chat-bubble">
                            The quality looks amazing on HD!
                        </div>
                    </div>
                </div>

                <!-- User Message 4 -->
                <div class="stream-chat-item">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&auto=format&fit=crop&q=80" class="stream-chat-avatar" alt="Mila_Vibe">
                    <div class="stream-chat-content">
                        <div class="stream-chat-user-row">
                            <span class="stream-chat-username">Mila_Vibe</span>
                        </div>
                        <div class="stream-chat-bubble">
                            Just bought the tote! So excited ✨
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Input Area -->
            <div class="stream-chat-input-area">
                <form onsubmit="handleSendStreamChat(event)" class="w-100">
                    <div class="stream-input-box-wrap">
                        <input type="text" class="stream-chat-input" id="streamChatInput" placeholder="Say something..." autocomplete="off">
                        <button type="submit" class="btn-send-chat-stream" title="Send">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                </form>
                
                <div class="stream-chat-actions-bottom">
                    <div class="chat-extra-icons-stream">
                        <button class="chat-icon-btn-stream" title="Emoji"><i class="bi bi-emoji-smile"></i></button>
                        <button class="chat-icon-btn-stream" title="GIF"><span class="gif-tag-stream">GIF</span></button>
                    </div>
                    <button class="btn-send-gift-stream" onclick="openSendGiftModal()" title="Send Gift">
                        <i class="bi bi-gift-fill" style="color: #00F0C8;"></i>
                        Send Gift
                    </button>
                </div>
            </div>
        </aside>

    </div>

</main>

<!-- SEND GIFT MODAL OVERLAY -->
<div class="modal-overlay-backdrop" id="sendGiftModal">
    <div class="send-gift-modal-card">
        <div class="modal-head-row">
            <h2 class="modal-head-title">Send Gift</h2>
            <button class="btn-modal-close-circle" onclick="closeSendGiftModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="insufficient-coins-alert" id="insufficientCoinsAlert">
            <i class="bi bi-exclamation-triangle-fill alert-warning-icon"></i>
            <p class="alert-warning-text">
                You don't have enough coins to send this gift. Please recharge your wallet to continue.
            </p>
        </div>
        <div class="gift-items-grid">
            <div class="gift-card-item" onclick="selectGiftItem(this, 20)">
                <div class="gift-icon-wrap">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none"><circle cx="21" cy="21" r="16" fill="url(#cosmic_grad)" opacity="0.9"/><circle cx="21" cy="21" r="19" stroke="#7033FF" stroke-width="1.5" stroke-dasharray="4 3"/><path d="M14 21L21 11L28 21L21 31L14 21Z" fill="#00F0C8" opacity="0.8"/><defs><linearGradient id="cosmic_grad" x1="5" y1="5" x2="37" y2="37" gradientUnits="userSpaceOnUse"><stop stop-color="#7033FF"/><stop offset="1" stop-color="#EC4899"/></linearGradient></defs></svg>
                </div>
                <div class="gift-item-title">Cosmic Empire</div>
                <div class="gift-price-tag"><span class="cyan-z-badge">Z</span><span class="gift-price-val">20</span></div>
            </div>
            <div class="gift-card-item" onclick="selectGiftItem(this, 120)">
                <div class="gift-icon-wrap">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none"><path d="M21 4C14 10 10 18 13 26C15 31 20 34 25 32C30 30 33 24 30 18C28 14 24 8 21 4Z" fill="url(#dragon_grad)"/><path d="M21 10C18 15 16 20 18 25C19 28 22 30 25 29C28 28 29 24 27 20C26 18 23 13 21 10Z" fill="#F59E0B"/><defs><linearGradient id="dragon_grad" x1="10" y1="4" x2="32" y2="34" gradientUnits="userSpaceOnUse"><stop stop-color="#EF4444"/><stop offset="0.5" stop-color="#F59E0B"/><stop offset="1" stop-color="#10B981"/></linearGradient></defs></svg>
                </div>
                <div class="gift-item-title">Dragon Ascend</div>
                <div class="gift-price-tag"><span class="cyan-z-badge">Z</span><span class="gift-price-val">120</span></div>
            </div>
            <div class="gift-card-item" onclick="selectGiftItem(this, 80)">
                <div class="gift-icon-wrap">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none"><circle cx="21" cy="21" r="16" fill="url(#energy_grad)" opacity="0.3"/><path d="M23 7L13 23H22L19 35L29 19H20L23 7Z" fill="url(#energy_bolt_grad)"/><defs><linearGradient id="energy_grad" x1="5" y1="5" x2="37" y2="37" gradientUnits="userSpaceOnUse"><stop stop-color="#3B82F6"/><stop offset="1" stop-color="#00F0C8"/></linearGradient><linearGradient id="energy_bolt_grad" x1="13" y1="7" x2="29" y2="35" gradientUnits="userSpaceOnUse"><stop stop-color="#60A5FA"/><stop offset="1" stop-color="#00F0C8"/></linearGradient></defs></svg>
                </div>
                <div class="gift-item-title">Energy Shot</div>
                <div class="gift-price-tag"><span class="cyan-z-badge">Z</span><span class="gift-price-val">80</span></div>
            </div>
        </div>
        <button class="btn-recharge-coins" id="giftModalCtaBtn" onclick="openSelectPackageModal()">Recharge Coins</button>
    </div>
</div>

<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
    function toggleStreamFollow(btn) {
        if (btn.innerText === 'Follow') {
            btn.innerText = 'Following';
            btn.style.background = '#00F0C8';
            btn.style.color = '#090D10';
        } else {
            btn.innerText = 'Follow';
            btn.style.background = '#0B0F14';
            btn.style.color = '#00F0C8';
        }
    }

    function toggleStreamLike(btn) {
        const icon = btn.querySelector('i');
        if (icon.classList.contains('bi-heart-fill')) {
            icon.classList.remove('bi-heart-fill');
            icon.style.color = '#FFFFFF';
        } else {
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill');
            icon.style.color = '#FF2A6D';
        }
    }

    function handleSendStreamChat(e) {
        e.preventDefault();
        const input = document.getElementById('streamChatInput');
        const list = document.getElementById('streamChatMessages');
        if (input && input.value.trim() !== '') {
            const item = document.createElement('div');
            item.className = 'stream-chat-item';
            item.innerHTML = `
                <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&auto=format&fit=crop&q=80" class="stream-chat-avatar" alt="You">
                <div class="stream-chat-content">
                    <div class="stream-chat-user-row">
                        <span class="stream-chat-username">You</span>
                    </div>
                    <div class="stream-chat-bubble">
                        ${input.value.trim()}
                    </div>
                </div>
            `;
            list.appendChild(item);
            input.value = '';
            list.scrollTop = list.scrollHeight;
        }
    }

    function openSendGiftModal() {
        const modal = document.getElementById('sendGiftModal');
        if (modal) modal.classList.add('show');
    }

    function closeSendGiftModal() {
        const modal = document.getElementById('sendGiftModal');
        if (modal) modal.classList.remove('show');
    }

    function openSelectPackageModal() {
        window.location.href = '/wallet/coins';
    }

    function selectGiftItem(card, price) {
        openSelectPackageModal();
    }
</script>
</body>
</html>
