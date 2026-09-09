@extends('layouts.app')

@section('title', $auction->title . ' - Live Auction Details')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    
    <div style="margin-bottom: 20px;">
        <a href="{{ route('auctions.index') }}" style="color: #888; text-decoration: none; font-size: 13px;">
            <i class="bi bi-arrow-left"></i> Back to Live Auctions
        </a>
    </div>

    <div class="zal-card" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; background: #16161f; border-radius: 20px; padding: 30px;">
        
        <div>
            <div style="border-radius: 12px; overflow: hidden; background: #0b0b10; height: 380px;">
                <img src="{{ $auction->image_url ?? ($auction->product->primary_image ?? 'https://images.unsplash.com/photo-1613771404784-3a5686aa2be3?w=800') }}" alt="{{ $auction->title }}" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
        </div>

        <div>
            <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                <span style="background: rgba(255,184,0,0.2); color: #FFB800; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px;">
                    AUCTION #{{ $auction->id }}
                </span>
                <span style="background: rgba(37,244,238,0.2); color: #25F4EE; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px;">
                    WHATNOT RULES
                </span>
            </div>

            <h1 style="font-size: 22px; font-weight: 800; color: #fff; line-height: 1.3; margin-bottom: 12px;">{{ $auction->title }}</h1>
            <p style="color: #aaa; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">{{ $auction->description }}</p>

            <div style="background: #1f1f2a; padding: 16px; border-radius: 12px; margin-bottom: 20px;">
                <div style="font-size: 12px; color: #888; text-transform: uppercase;">Current High Bid</div>
                <div style="font-size: 32px; font-weight: 900; color: #25F4EE; margin: 4px 0;">
                    ${{ number_format($auction->current_bid, 2) }}
                </div>
                <div style="font-size: 12px; color: #aaa;">
                    High Bidder: <strong style="color: #fff;">{{ $auction->highestBidder->name ?? 'No bids yet' }}</strong>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <a href="{{ route('auctions.index') }}" class="zal-btn-primary" style="flex: 1; text-align: center; padding: 12px; border-radius: 10px; text-decoration: none; font-weight: 800; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff;">
                    Join Live Bidding
                </a>
            </div>

            <div style="font-size: 12px; color: #777; line-height: 1.5; border-top: 1px solid #222; padding-top: 14px;">
                <strong>Auction Policy:</strong> All auction sales final. 24-hour grace period for un-dispatched items for store credit. Bidding accounts require phone verification.
            </div>
        </div>

    </div>

</div>
@endsection
