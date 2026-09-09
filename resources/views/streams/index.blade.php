@extends('layouts.app')

@section('title', 'Live Streams & PK Battles - Zaldoris')

@section('content')
<div style="margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
        <h1 style="font-size: 24px; font-weight: 800; margin: 0;">Live Streams & Broadcasts</h1>
        
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('streams.index') }}" class="zal-btn-filter {{ $type === 'all' ? 'active' : '' }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 600; {{ $type === 'all' ? 'background: #FE2C55; color: #fff;' : 'background: #1a1a24; color: #aaa;' }}">
                All Streams
            </a>
            <a href="{{ route('streams.index', ['type' => 'live_shopping']) }}" class="zal-btn-filter {{ $type === 'live_shopping' ? 'active' : '' }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 600; {{ $type === 'live_shopping' ? 'background: #FE2C55; color: #fff;' : 'background: #1a1a24; color: #aaa;' }}">
                <i class="bi bi-bag-fill"></i> Live Shopping
            </a>
            <a href="{{ route('streams.index', ['type' => 'live_auction']) }}" class="zal-btn-filter {{ $type === 'live_auction' ? 'active' : '' }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 600; {{ $type === 'live_auction' ? 'background: #FE2C55; color: #fff;' : 'background: #1a1a24; color: #aaa;' }}">
                <i class="bi bi-hammer"></i> Live Auction
            </a>
            <a href="{{ route('streams.index', ['type' => 'pk_battle']) }}" class="zal-btn-filter {{ $type === 'pk_battle' ? 'active' : '' }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 600; {{ $type === 'pk_battle' ? 'background: #FE2C55; color: #fff;' : 'background: #1a1a24; color: #aaa;' }}">
                <i class="bi bi-fire"></i> PK Battles
            </a>
        </div>
    </div>

    <div class="grid-4-col" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        @forelse($streams as $stream)
            <a href="{{ route('streams.show', $stream->id) }}" class="zal-card" style="text-decoration: none; color: inherit;">
                <div class="live-card-thumb" style="height: 360px; position: relative;">
                    <img src="{{ $stream->thumbnail_url }}" alt="{{ $stream->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                    <div class="badge-viewers-top"><i class="bi bi-eye-fill"></i> {{ number_format($stream->viewer_count) }}</div>
                    
                    @if($stream->stream_type === 'pk_battle')
                        <div style="position: absolute; top: 12px; right: 12px; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff; font-size: 11px; font-weight: 800; padding: 4px 8px; border-radius: 6px;">
                            ⚔️ PK BATTLE
                        </div>
                    @elseif($stream->stream_type === 'live_shopping')
                        <div style="position: absolute; top: 12px; right: 12px; background: rgba(37,244,238,0.9); color: #000; font-size: 11px; font-weight: 800; padding: 4px 8px; border-radius: 6px;">
                            🛍️ SHOP
                        </div>
                    @endif

                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.9)); padding: 20px 14px 14px;">
                        <div style="font-weight: 700; font-size: 15px; color: #fff; line-height: 1.3; margin-bottom: 8px;">
                            {{ Str::limit($stream->title, 50) }}
                        </div>
                        <div class="host-row" style="display: flex; align-items: center; gap: 8px;">
                            <img src="{{ $stream->host->avatar_url }}" alt="{{ $stream->host->name }}" class="host-avatar" style="width: 28px; height: 28px; border-radius: 50%;">
                            <span class="host-name" style="color: #eee; font-size: 13px; font-weight: 600;">{{ $stream->host->name }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #888;">
                <i class="bi bi-broadcast" style="font-size: 48px; display: block; margin-bottom: 12px;"></i>
                <p>No streams available in this category.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px;">
        {{ $streams->links() }}
    </div>
</div>
@endsection
