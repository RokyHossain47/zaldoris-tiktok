@extends('layouts.app')

@section('title', 'Creator Studio - Zaldoris')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    <!-- CREATOR STATS & REVENUE SPLIT BREAKDOWN (SRS Page 4 & 5) -->
    <div class="zal-card" style="background: linear-gradient(135deg, #1f1422, #14141d); border-radius: 20px; padding: 28px; margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid #FE2C55;">
                <div>
                    <h1 style="font-size: 22px; font-weight: 800; color: #fff; margin: 0;">{{ $user->name }}</h1>
                    <div style="color: #aaa; font-size: 13px; margin-top: 4px;">
                        {{ number_format($profile->follower_count ?? 45200) }} Followers | <strong>60% Revenue Share Rate</strong>
                    </div>
                </div>
            </div>

            <div style="background: #16161f; padding: 14px 24px; border-radius: 14px; text-align: right;">
                <div style="font-size: 12px; color: #888; text-transform: uppercase;">Creator Net Payout</div>
                <div style="font-size: 28px; font-weight: 900; color: #25F4EE;">${{ number_format($user->earnings_usd, 2) }}</div>
            </div>
        </div>

        <!-- REVENUE DEDUCTION BREAKDOWN (SRS Page 5) -->
        <div style="background: #14141c; border-radius: 14px; padding: 18px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; font-size: 13px;">
            <div>
                <div style="color: #888; margin-bottom: 4px;">Gross Gift Volume</div>
                <div style="font-size: 18px; font-weight: 800; color: #fff;">${{ number_format($grossGiftsUsd, 2) }}</div>
            </div>
            <div>
                <div style="color: #888; margin-bottom: 4px;">App Store Fees (~15%)</div>
                <div style="font-size: 18px; font-weight: 800; color: #FE2C55;">-${{ number_format($appStoreFees, 2) }}</div>
            </div>
            <div>
                <div style="color: #888; margin-bottom: 4px;">13% HST Tax</div>
                <div style="font-size: 18px; font-weight: 800; color: #FE2C55;">-${{ number_format($hstTaxes, 2) }}</div>
            </div>
            <div>
                <div style="color: #888; margin-bottom: 4px;">60% Creator Cut</div>
                <div style="font-size: 18px; font-weight: 800; color: #25F4EE;">+${{ number_format($creatorNetPayout, 2) }}</div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        
        <!-- RECENT GIFTS RECEIVED -->
        <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
            <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Recent Live Gifts Received</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @forelse($giftsReceived as $giftTx)
                    <div style="display: flex; justify-content: space-between; align-items: center; background: #1f1f2a; padding: 10px 14px; border-radius: 10px; font-size: 13px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 20px;">🎁</span>
                            <div>
                                <div style="font-weight: 700; color: #fff;">{{ $giftTx->gift->name ?? 'Gift' }} from {{ $giftTx->sender->name ?? 'Fan' }}</div>
                                <div style="font-size: 11px; color: #777;">{{ $giftTx->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 800; color: #25F4EE;">+${{ number_format($giftTx->creator_earning_usd, 2) }}</div>
                            <div style="font-size: 11px; color: #FFB800;">{{ $giftTx->coin_amount }} coins</div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: #777; padding: 30px;">No live gifts received yet.</div>
                @endforelse
            </div>
        </div>

        <!-- SUBSCRIBERS ($7.99/mo) -->
        <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 800; margin: 0;">Active Subscribers ($7.99/mo)</h3>
                <span style="background: rgba(254,44,85,0.15); color: #FE2C55; font-size: 12px; font-weight: 800; padding: 2px 10px; border-radius: 12px;">
                    {{ $subscribers->count() }} VIPs
                </span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                @forelse($subscribers as $sub)
                    <div style="display: flex; justify-content: space-between; align-items: center; background: #1f1f2a; padding: 10px 14px; border-radius: 10px; font-size: 13px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="{{ $sub->subscriber->avatar_url }}" alt="Subscriber" style="width: 32px; height: 32px; border-radius: 50%;">
                            <div>
                                <div style="font-weight: 700; color: #fff;">{{ $sub->subscriber->name }}</div>
                                <div style="font-size: 11px; color: #777;">Active Subscriber</div>
                            </div>
                        </div>
                        <div style="font-weight: 800; color: #25F4EE;">$7.99/mo</div>
                    </div>
                @empty
                    <div style="text-align: center; color: #777; padding: 30px;">No active subscribers yet. Share your profile link!</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
