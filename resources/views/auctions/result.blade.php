@extends('layouts.app')

@section('title', 'Auction Results - Zaldoris')

@section('content')
<div style="max-width: 680px; margin: 40px auto; text-align: center;">

    <div class="zal-card" style="background: #16161f; border-radius: 20px; padding: 40px 30px;">
        <div style="font-size: 56px; margin-bottom: 16px;">🏆</div>
        
        <h1 style="font-size: 26px; font-weight: 900; color: #fff; margin-bottom: 8px;">Auction Ended</h1>
        <p style="color: #aaa; font-size: 14px; margin-bottom: 24px;">{{ $auction->title }}</p>

        <div style="background: #1f1f2a; border-radius: 12px; padding: 20px; margin-bottom: 30px; text-align: left;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
                <span style="color: #888;">Winning Bidder:</span>
                <strong style="color: #fff;">{{ $auction->highestBidder->name ?? 'None' }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
                <span style="color: #888;">Final Hammer Price:</span>
                <strong style="color: #25F4EE; font-size: 18px;">${{ number_format($auction->current_bid, 2) }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 14px;">
                <span style="color: #888;">Seller:</span>
                <strong style="color: #fff;">{{ $auction->seller->name }}</strong>
            </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: center;">
            <a href="{{ route('shop.checkout') }}" class="zal-btn-primary" style="padding: 12px 28px; border-radius: 10px; text-decoration: none; font-weight: 800; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff;">
                Proceed to Checkout (13% HST)
            </a>
            <a href="{{ route('auctions.index') }}" class="zal-btn-secondary" style="padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; background: #222; color: #fff;">
                Explore More Auctions
            </a>
        </div>
    </div>

</div>
@endsection
