@extends('layouts.app')

@section('title', 'Admin Panel - Zaldoris')

@section('content')
<div style="max-width: 1300px; margin: 0 auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0;">Admin Operations & Compliance Control</h1>
            <p style="color: #888; font-size: 13px; margin-top: 4px;">Platform governance, fraud detection, AI moderation & 4-hour dispute SLA.</p>
        </div>
        
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('admin.disputes') }}" class="zal-btn-secondary" style="padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 700; background: #1f1f2a; color: #FE2C55;">
                <i class="bi bi-exclamation-octagon-fill"></i> Disputes ({{ $openDisputes }})
            </a>
            <a href="{{ route('admin.ads') }}" class="zal-btn-secondary" style="padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 700; background: #1f1f2a; color: #fff;">
                <i class="bi bi-badge-ad-fill"></i> Ads Manager
            </a>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
            <div style="font-size: 12px; color: #888; text-transform: uppercase;">Total Users & Creators</div>
            <div style="font-size: 28px; font-weight: 900; color: #fff; margin: 6px 0;">{{ number_format($totalUsers) }}</div>
            <div style="font-size: 12px; color: #25F4EE;">{{ $totalSellers }} Verified Sellers | {{ $totalCreators }} Creators</div>
        </div>

        <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
            <div style="font-size: 12px; color: #888; text-transform: uppercase;">Active Streams</div>
            <div style="font-size: 28px; font-weight: 900; color: #FE2C55; margin: 6px 0;">{{ $activeStreams }}</div>
            <div style="font-size: 12px; color: #aaa;">Low Latency Agora RTC Streams</div>
        </div>

        <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
            <div style="font-size: 12px; color: #888; text-transform: uppercase;">Total Gross Volume (USD)</div>
            <div style="font-size: 28px; font-weight: 900; color: #25F4EE; margin: 6px 0;">${{ number_format($totalSalesUsd, 2) }}</div>
            <div style="font-size: 12px; color: #aaa;">7% Platform Shop Cut + 40% Gifts</div>
        </div>
    </div>

    <!-- RECENT DISPUTES TABLE -->
    <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 800; margin: 0; color: #FE2C55;"><i class="bi bi-stopwatch"></i> Open Disputes (4h SLA)</h3>
                <a href="{{ route('admin.disputes') }}" style="font-size: 12px; color: #888; text-decoration: none;">View All</a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                @forelse($recentDisputes as $disp)
                    <div style="background: #1f1f2a; padding: 12px; border-radius: 10px; font-size: 12px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <strong style="color: #fff;">{{ $disp->reason }}</strong>
                            <span style="color: #FFB800; font-weight: 700;">Response SLA: 4 Hours</span>
                        </div>
                        <p style="color: #aaa; margin: 0 0 6px;">Buyer: {{ $disp->buyer->name }} | Seller: {{ $disp->seller->name }}</p>
                        <a href="{{ route('admin.disputes') }}" style="color: #25F4EE; text-decoration: none; font-weight: 700;">Review Dispute & Photos →</a>
                    </div>
                @empty
                    <div style="text-align: center; color: #777; padding: 20px;">No open disputes. Customer satisfaction high!</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
