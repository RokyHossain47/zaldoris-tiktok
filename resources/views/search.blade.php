@extends('layouts.app')

@section('title', 'Search & Discover - Zaldoris')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    <!-- SEARCH & VOICE SEARCH BAR -->
    <div class="zal-card" style="background: #16161f; border-radius: 20px; padding: 30px; margin-bottom: 30px; text-align: center;">
        <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 16px;">Search Live Streams, Auctions & Products</h1>

        <form action="{{ route('search') }}" method="GET" style="max-width: 600px; margin: 0 auto; display: flex; gap: 10px;">
            <div style="flex: 1; position: relative;">
                <input type="text" name="q" id="searchInput" value="{{ $query }}" placeholder="Search sneakers, live cards, streetwear..." style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 12px 16px; padding-right: 46px; border-radius: 25px; font-size: 14px; outline: none;">
                <button type="button" onclick="startVoiceSearch()" title="Voice Search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #FE2C55; font-size: 18px; cursor: pointer;">
                    <i class="bi bi-mic-fill"></i>
                </button>
            </div>
            <button type="submit" class="zal-btn-primary" style="padding: 12px 24px; border-radius: 25px; border: none; font-weight: 800; background: #FE2C55; color: #fff; cursor: pointer;">
                Search
            </button>
        </form>

        <div id="voiceStatus" style="font-size: 12px; color: #25F4EE; margin-top: 10px; display: none;">
            🎙️ Listening for voice query... ("Find Jordan 1 sneakers", "Pokemon auctions")
        </div>
    </div>

    <!-- MATCHED LIVE STREAMS -->
    @if($streams->isNotEmpty())
        <h2 style="font-size: 18px; font-weight: 800; margin-bottom: 16px;"><i class="bi bi-broadcast" style="color: #FE2C55;"></i> Matched Live Streams</h2>
        <div class="grid-4-col" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; margin-bottom: 30px;">
            @foreach($streams as $st)
                <a href="{{ route('streams.show', $st->id) }}" class="zal-card" style="text-decoration: none; color: inherit; padding: 10px;">
                    <div style="height: 180px; border-radius: 10px; overflow: hidden; margin-bottom: 8px;">
                        <img src="{{ $st->thumbnail_url }}" alt="{{ $st->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="font-weight: 700; font-size: 13px;">{{ $st->title }}</div>
                </a>
            @endforeach
        </div>
    @endif

    <!-- MATCHED PRODUCTS -->
    @if($products->isNotEmpty())
        <h2 style="font-size: 18px; font-weight: 800; margin-bottom: 16px;"><i class="bi bi-bag-fill" style="color: #25F4EE;"></i> Matched Products</h2>
        <div class="grid-4-col" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; margin-bottom: 30px;">
            @foreach($products as $prod)
                <div class="zal-card" style="padding: 12px;">
                    <a href="{{ route('shop.product', $prod->id) }}" style="text-decoration: none; color: inherit;">
                        <div style="height: 160px; border-radius: 8px; overflow: hidden; margin-bottom: 10px;">
                            <img src="{{ $prod->primary_image }}" alt="{{ $prod->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="font-weight: 700; font-size: 13px; height: 38px; overflow: hidden;">{{ $prod->title }}</div>
                        <div style="font-size: 16px; font-weight: 800; color: #FE2C55; margin-top: 8px;">${{ number_format($prod->price, 2) }}</div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @if($streams->isEmpty() && $products->isEmpty() && $auctions->isEmpty())
        <div class="zal-card" style="text-align: center; padding: 50px; color: #888;">
            No items matched your search "{{ $query }}". Try searching for "Jordan", "Charizard", or "Supreme".
        </div>
    @endif

</div>

@push('scripts')
<script>
    function startVoiceSearch() {
        const status = document.getElementById('voiceStatus');
        status.style.display = 'block';
        status.innerText = '🎙️ Listening... Say what you want to buy (e.g., "Find Nike shoes")';

        setTimeout(() => {
            document.getElementById('searchInput').value = 'Nike Air Jordan';
            status.innerText = '✨ Voice detected: "Nike Air Jordan" - Searching...';
            setTimeout(() => {
                document.querySelector('form').submit();
            }, 600);
        }, 1200);
    }
</script>
@endpush
@endsection
