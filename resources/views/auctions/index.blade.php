@extends('layouts.app')

@section('title', 'Live Auctions (Whatnot-Style) - Zaldoris')

@section('content')
<div style="max-width: 1300px; margin: 0 auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-hammer" style="color: #FFB800;"></i> Live Whatnot-Style Auction Room
            </h1>
            <p style="color: #888; font-size: 13px; margin-top: 4px;">Fast-paced real-time live bidding with anti-sniping & escrow protection.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <span style="background: rgba(37,244,238,0.15); color: #25F4EE; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                <i class="bi bi-shield-check"></i> Escrow Guarantee
            </span>
        </div>
    </div>

    @if($activeAuction)
        <!-- MAIN AUCTION STAGE -->
        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 24px; margin-bottom: 40px;">
            
            <!-- LEFT: AUCTION ITEM SHOWCASE & TIMER -->
            <div class="zal-card" style="padding: 0; overflow: hidden; background: #16161f; border-radius: 16px; position: relative;">
                <div style="height: 480px; position: relative; background: #0a0a0f;">
                    <img src="{{ $activeAuction->image_url ?? ($activeAuction->product->primary_image ?? 'https://images.unsplash.com/photo-1613771404784-3a5686aa2be3?w=800') }}" alt="{{ $activeAuction->title }}" style="width: 100%; height: 100%; object-fit: contain;">
                    
                    <div style="position: absolute; top: 16px; left: 16px; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); padding: 6px 14px; border-radius: 20px; color: #fff; font-size: 12px; font-weight: 700;">
                        Seller: {{ $activeAuction->seller->name }}
                    </div>

                    <!-- COUNTDOWN TIMER BADGE -->
                    <div style="position: absolute; top: 16px; right: 16px; background: rgba(254,44,85,0.9); color: #fff; padding: 8px 16px; border-radius: 20px; font-weight: 900; font-size: 16px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 16px rgba(254,44,85,0.5);">
                        <i class="bi bi-stopwatch-fill"></i> <span id="auctionTimer">00:45</span>
                    </div>

                    <!-- BOTTOM BAR WITH HIGH BID INFO -->
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.95)); padding: 24px 20px 16px;">
                        <h2 style="font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 6px;">{{ $activeAuction->title }}</h2>
                        <div style="display: flex; gap: 20px; color: #aaa; font-size: 13px;">
                            <span>Starting Bid: <strong style="color: #fff;">${{ number_format($activeAuction->starting_bid, 2) }}</strong></span>
                            <span>Min Increment: <strong style="color: #25F4EE;">+${{ number_format($activeAuction->min_bid_step, 2) }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: REAL-TIME BIDDING CONSOLE & BID LOG -->
            <div class="zal-card" style="background: #16161f; border-radius: 16px; display: flex; flex-direction: column; overflow: hidden;">
                
                <div style="padding: 16px; border-bottom: 1px solid #222;">
                    <div style="font-size: 12px; color: #aaa; text-transform: uppercase; font-weight: 700;">CURRENT HIGHEST BID</div>
                    <div style="font-size: 32px; font-weight: 900; color: #25F4EE; margin: 4px 0;">
                        $<span id="currentBidVal">{{ number_format($activeAuction->current_bid, 2) }}</span>
                    </div>
                    <div style="font-size: 12px; color: #888;">
                        Winning Bidder: <strong id="highestBidderName" style="color: #fff;">{{ $activeAuction->highestBidder->name ?? 'None yet' }}</strong>
                    </div>
                </div>

                <!-- LIVE BID HISTORY -->
                <div id="bidsStreamContainer" style="flex: 1; padding: 16px; overflow-y: auto; max-height: 240px; display: flex; flex-direction: column; gap: 8px;">
                    @foreach($activeAuction->bids as $bid)
                        <div style="display: flex; justify-content: space-between; align-items: center; background: #1f1f2a; padding: 8px 12px; border-radius: 8px; font-size: 13px;">
                            <span style="color: #ddd;">{{ $bid->user->name ?? 'Anonymous' }}</span>
                            <span style="color: #25F4EE; font-weight: 800;">${{ number_format($bid->amount, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- BID ACTION BUTTONS & CUSTOM BID INPUT -->
                <div style="padding: 16px; border-top: 1px solid #222; background: #121218;">
                    
                    @php $nextMin = $activeAuction->current_bid + $activeAuction->min_bid_step; @endphp

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                        <button onclick="placeQuickBid({{ $nextMin }})" style="padding: 10px; border-radius: 10px; border: none; background: #25F4EE; color: #000; font-weight: 800; font-size: 13px; cursor: pointer;">
                            Bid ${{ number_format($nextMin, 2) }}
                        </button>
                        <button onclick="placeQuickBid({{ $nextMin + 20 }})" style="padding: 10px; border-radius: 10px; border: none; background: #FE2C55; color: #fff; font-weight: 800; font-size: 13px; cursor: pointer;">
                            Bid ${{ number_format($nextMin + 20, 2) }}
                        </button>
                    </div>

                    <form onsubmit="placeCustomBid(event)" style="display: flex; gap: 8px;">
                        <input type="number" id="customBidInput" min="{{ $nextMin }}" step="1" placeholder="Custom Bid ($)" required style="flex: 1; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 10px 12px; border-radius: 8px; font-size: 14px; font-weight: 700; outline: none;">
                        <button type="submit" style="background: linear-gradient(135deg, #FFB800, #FF8A00); color: #000; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 800; font-size: 13px; cursor: pointer;">
                            Place Bid
                        </button>
                    </form>

                    <p style="font-size: 11px; color: #777; margin-top: 8px; text-align: center;">
                        🛡️ Anti-Sniping active: Bids in last 15s extend timer.
                    </p>
                </div>

            </div>

        </div>
    @endif

    <!-- OTHER LIVE AUCTIONS LIST -->
    <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 16px;">More Active Auctions</h3>
    <div class="grid-4-col" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">
        @foreach($auctions as $auc)
            <div class="zal-card" style="padding: 12px;">
                <div style="height: 180px; border-radius: 8px; overflow: hidden; margin-bottom: 10px;">
                    <img src="{{ $auc->image_url ?? ($auc->product->primary_image ?? 'https://images.unsplash.com/photo-1613771404784-3a5686aa2be3?w=600') }}" alt="{{ $auc->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="font-weight: 700; font-size: 13px; height: 38px; overflow: hidden;">{{ $auc->title }}</div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                    <span style="color: #25F4EE; font-weight: 800; font-size: 16px;">${{ number_format($auc->current_bid, 2) }}</span>
                    <a href="{{ route('auctions.show', $auc->id) }}" class="zal-btn-primary" style="padding: 4px 10px; font-size: 11px; border-radius: 6px; text-decoration: none; background: #FE2C55; color: #fff; font-weight: 700;">
                        View Item
                    </a>
                </div>
            </div>
        @endforeach
    </div>

</div>

@push('scripts')
<script>
    const auctionId = {{ $activeAuction->id ?? 0 }};
    let currentBid = {{ $activeAuction->current_bid ?? 0 }};

    async function placeQuickBid(amount) {
        await submitBid(amount);
    }

    async function placeCustomBid(e) {
        e.preventDefault();
        const input = document.getElementById('customBidInput');
        const amount = parseFloat(input.value);
        if (isNaN(amount)) return;
        await submitBid(amount);
        input.value = '';
    }

    async function submitBid(amount) {
        if (!auctionId) return;

        try {
            const res = await fetch(`/api/v1/auctions/${auctionId}/bid`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ amount: amount })
            });

            const data = await res.json();
            if (data.success) {
                currentBid = data.current_bid;
                document.getElementById('currentBidVal').innerText = Number(data.current_bid).toFixed(2);
                document.getElementById('highestBidderName').innerText = data.highest_bidder;
                
                const container = document.getElementById('bidsStreamContainer');
                const div = document.createElement('div');
                div.style.display = 'flex';
                div.style.justifyContent = 'space-between';
                div.style.alignItems = 'center';
                div.style.background = 'rgba(37,244,238,0.15)';
                div.style.padding = '8px 12px';
                div.style.borderRadius = '8px';
                div.style.fontSize = '13px';
                div.innerHTML = `<span style="color: #fff; font-weight: 700;">${data.highest_bidder}</span> <span style="color: #25F4EE; font-weight: 800;">$${Number(data.current_bid).toFixed(2)}</span>`;
                container.insertBefore(div, container.firstChild);

                alert(`🎉 Bid of $${amount} accepted! You are currently the highest bidder.`);
            } else {
                alert(data.message || 'Could not place bid.');
            }
        } catch (e) {
            alert('Failed to place bid. Please login to bid.');
        }
    }
</script>
@endpush
@endsection
