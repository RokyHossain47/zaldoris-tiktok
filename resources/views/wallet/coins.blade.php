@extends('layouts.app')

@section('title', 'Coins & Gifts Wallet - Zaldoris')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">

    <div class="zal-card" style="background: linear-gradient(135deg, #1f1f2d, #14141d); border-radius: 20px; padding: 30px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 13px; color: #aaa; text-transform: uppercase; font-weight: 700;">Your Wallet Balance</div>
            <div style="font-size: 40px; font-weight: 900; color: #FFB800; margin: 4px 0; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-coin"></i> {{ auth()->check() ? number_format(auth()->user()->coin_balance) : '100' }}
            </div>
            <div style="font-size: 12px; color: #25F4EE;">
                Use coins to send gifts to live stream creators and boost PK battles.
            </div>
        </div>

        @if(auth()->check() && auth()->user()->vip_badge_tier)
            <div style="background: rgba(255,184,0,0.15); border: 1px solid #FFB800; padding: 10px 20px; border-radius: 12px; text-align: center;">
                <div style="font-size: 11px; color: #FFB800; font-weight: 800;">VIP SUPPORTER</div>
                <div style="font-size: 16px; font-weight: 900; color: #fff;">{{ auth()->user()->vip_badge_tier }} Tier</div>
            </div>
        @endif
    </div>

    <!-- COIN PACKAGES (SRS Page 4 & 5 with 13% HST) -->
    <h2 style="font-size: 20px; font-weight: 800; margin-bottom: 16px;">Top Up Coins (13% HST Canadian Tax Included)</h2>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 40px;">
        @foreach($packages as $pkg)
            <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px; border: {{ $pkg->is_popular ? '2px solid #FE2C55' : '1px solid #222' }}; position: relative; display: flex; flex-direction: column;">
                @if($pkg->is_popular)
                    <div style="position: absolute; top: -12px; right: 16px; background: #FE2C55; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 10px; border-radius: 10px;">
                        MOST POPULAR
                    </div>
                @endif

                <div style="font-weight: 800; font-size: 16px; margin-bottom: 4px;">{{ $pkg->name }}</div>
                
                <div style="font-size: 30px; font-weight: 900; color: #FFB800; margin: 10px 0;">
                    <i class="bi bi-coin"></i> {{ number_format($pkg->total_coins) }}
                </div>

                @if($pkg->bonus_coins > 0)
                    <div style="font-size: 12px; color: #25F4EE; font-weight: 700; margin-bottom: 12px;">
                        + {{ $pkg->bonus_coins }} Bonus Coins Free!
                    </div>
                @else
                    <div style="font-size: 12px; color: #666; margin-bottom: 12px;">Standard Pack</div>
                @endif

                <div style="background: #1f1f2a; padding: 10px; border-radius: 8px; font-size: 12px; color: #aaa; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Price:</span>
                        <strong style="color: #fff;">${{ number_format($pkg->price_usd, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>13% HST Tax:</span>
                        <strong style="color: #fff;">${{ number_format($pkg->hst_tax, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #333; padding-top: 4px; margin-top: 4px; font-weight: 800; color: #fff;">
                        <span>Total:</span>
                        <span style="color: #25F4EE;">${{ number_format($pkg->total_price_with_tax, 2) }}</span>
                    </div>
                </div>

                <form action="{{ route('wallet.coins.buy') }}" method="POST" style="margin-top: auto;">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                    <button type="submit" class="zal-btn-primary" style="width: 100%; padding: 12px; border-radius: 8px; border: none; font-weight: 800; font-size: 13px; background: linear-gradient(135deg, #FFB800, #FF8A00); color: #000; cursor: pointer;">
                        Buy with 1-Tap (${{ number_format($pkg->total_price_with_tax, 2) }})
                    </button>
                </form>
            </div>
        @endforeach
    </div>

    <!-- RECENT TRANSACTIONS -->
    <h2 style="font-size: 18px; font-weight: 800; margin-bottom: 14px;">Recent Wallet Transactions</h2>
    <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 16px;">
        @forelse($transactions as $tx)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #222; font-size: 13px;">
                <div>
                    <div style="font-weight: 700; color: #fff;">{{ $tx->description }}</div>
                    <div style="font-size: 11px; color: #777;">{{ $tx->created_at->format('M d, Y H:i') }}</div>
                </div>
                <div style="font-weight: 800; font-size: 15px; color: {{ $tx->amount_coins > 0 ? '#25F4EE' : '#FE2C55' }};">
                    {{ $tx->amount_coins > 0 ? '+' : '' }}{{ $tx->amount_coins }} Coins
                </div>
            </div>
        @empty
            <div style="text-align: center; color: #777; padding: 20px;">No transactions recorded.</div>
        @endforelse
    </div>

</div>
@endsection
