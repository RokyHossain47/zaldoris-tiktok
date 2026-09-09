@extends('layouts.app')

@section('title', 'Subscribe to Creator - Zaldoris')

@section('content')
<div style="max-width: 680px; margin: 40px auto; text-align: center;">

    <div class="zal-card" style="background: #16161f; border-radius: 20px; padding: 40px 30px;">
        
        <img src="{{ $creator->avatar_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200' }}" alt="Creator" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid #FE2C55; margin-bottom: 16px;">

        <h1 style="font-size: 24px; font-weight: 900; color: #fff; margin-bottom: 6px;">Subscribe to {{ $creator->name ?? 'Creator' }}</h1>
        <p style="color: #aaa; font-size: 13px; margin-bottom: 24px;">{{ $creator->creatorProfile->bio ?? 'Exclusive live drops, custom badges, and subscriber-only streams!' }}</p>

        <div style="background: #1f1f2a; border-radius: 16px; padding: 24px; margin-bottom: 30px; text-align: left;">
            <div style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 14px;">Subscriber Perks Included:</div>
            
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 13px; color: #ddd;">
                <li style="display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-star-fill" style="color: #FFB800;"></i> Exclusive Subscriber Chat Badge in all live streams
                </li>
                <li style="display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-bag-check-fill" style="color: #25F4EE;"></i> 10-Minute Early Access to Live Commerce Drops
                </li>
                <li style="display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-chat-heart-fill" style="color: #FE2C55;"></i> Direct Creator Q&A and priority message highlight
                </li>
                <li style="display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-coin" style="color: #FFB800;"></i> 100 Bonus Coins per month
                </li>
            </ul>

            <div style="margin-top: 20px; border-top: 1px solid #333; padding-top: 14px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 12px; color: #888;">Monthly Subscription</span>
                    <div style="font-size: 24px; font-weight: 900; color: #25F4EE;">$7.99 <span style="font-size: 13px; color: #aaa;">/ month</span></div>
                </div>
                <div style="font-size: 11px; color: #aaa;">+ 13% HST ($1.04)</div>
            </div>
        </div>

        @if($creator)
            <form action="{{ route('wallet.subscribe.process', $creator->id) }}" method="POST">
                @csrf
                <button type="submit" class="zal-btn-primary" style="width: 100%; padding: 14px; border-radius: 12px; border: none; font-size: 15px; font-weight: 800; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff; cursor: pointer; box-shadow: 0 4px 16px rgba(254,44,85,0.4);">
                    Subscribe Now for $7.99/mo
                </button>
            </form>
        @endif

        <p style="font-size: 11px; color: #666; margin-top: 14px;">Cancel anytime in settings. Recurring monthly billing.</p>

    </div>

</div>
@endsection
