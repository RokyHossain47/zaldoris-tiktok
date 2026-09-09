@extends('layouts.app')

@section('title', 'Zaldoris - TikTok-Style Live Commerce & Auction Platform')

@section('content')
    <!-- AD BANNER -->
    @if($banners->isNotEmpty())
        @php $banner = $banners->first(); @endphp
        <a href="{{ $banner->link_url ?? route('shop.index') }}" style="text-decoration: none; display: block;">
            <div class="ad-banner-card" style="background: linear-gradient(135deg, rgba(254,44,85,0.2), rgba(37,244,238,0.2)), #16161f;">
                <div class="ad-banner-text" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <span style="background: #FE2C55; color: #fff; font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 800;">AD</span>
                    <span>{{ $banner->title }}</span>
                </div>
            </div>
        </a>
    @else
        <div class="ad-banner-card">
            <div class="ad-banner-text">🔥 Experience Live Social Commerce & $1 Start Whatnot-Style Auctions</div>
        </div>
    @endif

    <!-- TWO-COLUMN CONTENT GRID -->
    <div class="content-grid-layout">

        <!-- LEFT MAIN COLUMN -->
        <div class="left-main-column">

            <!-- SECTION 1: FEATURED LIVE SHOPPING -->
            <section class="section-spacing">
                <div class="section-header-row">
                    <h2 class="section-title"><i class="bi bi-bag-heart-fill" style="color: #FE2C55;"></i> Featured Live Shopping</h2>
                    <a href="{{ route('streams.index', ['type' => 'live_shopping']) }}" class="view-all-link">View All <i class="bi bi-arrow-right"></i></a>
                </div>

                <div class="grid-3-col">
                    @forelse($liveShoppingStreams as $stream)
                        <a href="{{ route('streams.show', $stream->id) }}" class="zal-card" style="text-decoration: none; color: inherit;">
                            <div class="live-card-thumb">
                                <img src="{{ $stream->thumbnail_url }}" alt="{{ $stream->title }}">
                                <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                                <div class="badge-viewers-top"><i class="bi bi-eye-fill"></i> {{ number_format($stream->viewer_count) }}</div>
                                @if($stream->is_boosted)
                                    <div style="position: absolute; bottom: 8px; left: 8px; background: rgba(37,244,238,0.85); color: #000; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px;">
                                        ✨ NEW SELLER
                                    </div>
                                @endif
                            </div>
                            <div class="live-card-body">
                                <div class="live-card-title">{{ Str::limit($stream->title, 40) }}</div>
                                <div class="host-row">
                                    <img src="{{ $stream->host->avatar_url }}" alt="{{ $stream->host->name }}" class="host-avatar">
                                    <span class="host-name">{{ $stream->host->name }}</span>
                                    @if($stream->host->fast_shipper_badge)
                                        <span title="Fast Shipper: 95%+ 48h dispatch" style="color: #25F4EE; font-size: 12px;"><i class="bi bi-lightning-charge-fill"></i></span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div style="grid-column: span 3; text-align: center; padding: 40px; color: #888;">
                            No live shopping streams currently active.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- SECTION 2: WHATNOT-STYLE LIVE AUCTIONS -->
            <section class="section-spacing">
                <div class="section-header-row">
                    <h2 class="section-title"><i class="bi bi-hammer" style="color: #FFB800;"></i> Live Auctions (Whatnot-Style)</h2>
                    <a href="{{ route('auctions.index') }}" class="view-all-link">View All <i class="bi bi-arrow-right"></i></a>
                </div>

                <div class="grid-3-col">
                    @forelse($activeAuctions as $auction)
                        <a href="{{ route('auctions.show', $auction->id) }}" class="zal-card" style="text-decoration: none; color: inherit;">
                            <div class="live-card-thumb">
                                <img src="{{ $auction->image_url ?? $auction->product->primary_image ?? 'https://images.unsplash.com/photo-1613771404784-3a5686aa2be3?w=600' }}" alt="{{ $auction->title }}">
                                <div class="badge-live-top" style="background: #FFB800; color: #000;"><i class="bi bi-stopwatch"></i> ACTIVE BID</div>
                                <div class="badge-viewers-top" style="background: rgba(0,0,0,0.7);">${{ number_format($auction->current_bid, 2) }}</div>
                            </div>
                            <div class="live-card-body">
                                <div class="live-card-title">{{ Str::limit($auction->title, 40) }}</div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; font-size: 12px; color: #aaa;">
                                    <span>High Bid: <strong style="color: #25F4EE;">${{ number_format($auction->current_bid, 2) }}</strong></span>
                                    <span>Min Step: +${{ number_format($auction->min_bid_step, 2) }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div style="grid-column: span 3; text-align: center; padding: 40px; color: #888;">
                            No live auctions active.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- SECTION 3: FEATURED SHOP PRODUCTS -->
            <section class="section-spacing">
                <div class="section-header-row">
                    <h2 class="section-title"><i class="bi bi-tag-fill" style="color: #25F4EE;"></i> Trending Products & Live Drops</h2>
                    <a href="{{ route('shop.index') }}" class="view-all-link">Explore Shop <i class="bi bi-arrow-right"></i></a>
                </div>

                <div class="grid-4-col" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                    @foreach($featuredProducts as $product)
                        <div class="zal-card" style="padding: 12px;">
                            <a href="{{ route('shop.product', $product->id) }}" style="text-decoration: none; color: inherit;">
                                <div style="border-radius: 8px; overflow: hidden; height: 160px; margin-bottom: 10px;">
                                    <img src="{{ $product->primary_image }}" alt="{{ $product->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div style="font-weight: 700; font-size: 13px; line-height: 1.4; height: 38px; overflow: hidden;">{{ $product->title }}</div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                                    <span style="color: #FE2C55; font-weight: 800; font-size: 15px;">${{ number_format($product->price, 2) }}</span>
                                    <span style="font-size: 11px; color: #888;">Stock: {{ $product->available_stock }}</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>

        <!-- RIGHT SIDEBAR COLUMN -->
        <aside class="right-sidebar-column">

            <!-- WIDGET: TOP CREATORS & PK BATTLERS -->
            <div class="zal-card sidebar-widget">
                <div class="widget-header">
                    <h3 class="widget-title"><i class="bi bi-trophy-fill" style="color: #FFB800;"></i> Top Creators</h3>
                    <a href="{{ route('streams.index', ['type' => 'pk_battle']) }}" class="view-all-link">Battles</a>
                </div>
                <div class="creator-list">
                    @foreach($topCreators as $creator)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #222;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="{{ $creator->avatar_url }}" alt="{{ $creator->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #FE2C55;">
                                <div>
                                    <div style="font-weight: 700; font-size: 13px;">{{ $creator->name }}</div>
                                    <div style="font-size: 11px; color: #888;">{{ number_format($creator->creatorProfile->follower_count ?? 1200) }} Followers</div>
                                </div>
                            </div>
                            <a href="{{ route('wallet.subscribe', $creator->id) }}" style="background: rgba(254,44,85,0.15); color: #FE2C55; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; text-decoration: none;">
                                Sub $7.99
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- WIDGET: COIN ECONOMY & 8 CORE GIFTS -->
            <div class="zal-card sidebar-widget" style="margin-top: 20px;">
                <div class="widget-header">
                    <h3 class="widget-title"><i class="bi bi-coin" style="color: #FFB800;"></i> Coins & Gift Economy</h3>
                    <a href="{{ route('wallet.coins') }}" class="view-all-link">Top Up</a>
                </div>
                <p style="font-size: 12px; color: #888; margin-bottom: 14px; line-height: 1.5;">
                    Support creators during live streams and PK battles! Creators receive <strong>60% revenue share</strong>.
                </p>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; text-align: center;">
                    <div style="background: #1a1a24; padding: 8px 4px; border-radius: 8px;">
                        <span style="font-size: 20px;">💖</span>
                        <div style="font-size: 10px; color: #aaa; margin-top: 4px;">10 Coins</div>
                    </div>
                    <div style="background: #1a1a24; padding: 8px 4px; border-radius: 8px;">
                        <span style="font-size: 20px;">⭐</span>
                        <div style="font-size: 10px; color: #aaa; margin-top: 4px;">50 Coins</div>
                    </div>
                    <div style="background: #1a1a24; padding: 8px 4px; border-radius: 8px;">
                        <span style="font-size: 20px;">⚡</span>
                        <div style="font-size: 10px; color: #aaa; margin-top: 4px;">100 Coins</div>
                    </div>
                    <div style="background: #1a1a24; padding: 8px 4px; border-radius: 8px;">
                        <span style="font-size: 20px;">👑</span>
                        <div style="font-size: 10px; color: #aaa; margin-top: 4px;">1000 Coins</div>
                    </div>
                </div>
                <a href="{{ route('wallet.coins') }}" class="zal-btn-primary" style="display: block; text-align: center; margin-top: 14px; text-decoration: none; padding: 8px; border-radius: 8px; font-size: 12px; font-weight: 700; background: linear-gradient(135deg, #FFB800, #FF8A00); color: #000;">
                    Get Coin Packages (13% HST)
                </a>
            </div>

            <!-- WIDGET: SELLER 48H DISPATCH GUARANTEE -->
            <div class="zal-card sidebar-widget" style="margin-top: 20px; border-left: 3px solid #25F4EE;">
                <h4 style="font-size: 13px; color: #25F4EE; margin-bottom: 6px;"><i class="bi bi-shield-check"></i> Escrow Protected</h4>
                <p style="font-size: 12px; color: #aaa; line-height: 1.5; margin: 0;">
                    All live commerce purchases are held in escrow until EasyPost delivery confirmation. 48-hour mandatory seller dispatch.
                </p>
            </div>

        </aside>

    </div>
@endsection
