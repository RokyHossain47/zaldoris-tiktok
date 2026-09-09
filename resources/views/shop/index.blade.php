@extends('layouts.app')

@section('title', 'Shop & Live Drops - Zaldoris')

@section('content')
<div style="max-width: 1300px; margin: 0 auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0;">Live Drops & Marketplace</h1>
            <p style="color: #888; font-size: 13px; margin-top: 4px;">Verified authentic products from approved sellers with 48h dispatch escrow.</p>
        </div>

        <!-- CATEGORIES -->
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('shop.index', ['category' => 'all']) }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 600; {{ $category === 'all' ? 'background: #FE2C55; color: #fff;' : 'background: #1a1a24; color: #aaa;' }}">All</a>
            <a href="{{ route('shop.index', ['category' => 'shoes']) }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 600; {{ $category === 'shoes' ? 'background: #FE2C55; color: #fff;' : 'background: #1a1a24; color: #aaa;' }}">Sneakers</a>
            <a href="{{ route('shop.index', ['category' => 'collectibles']) }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 600; {{ $category === 'collectibles' ? 'background: #FE2C55; color: #fff;' : 'background: #1a1a24; color: #aaa;' }}">Collectibles</a>
            <a href="{{ route('shop.index', ['category' => 'fashion']) }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 600; {{ $category === 'fashion' ? 'background: #FE2C55; color: #fff;' : 'background: #1a1a24; color: #aaa;' }}">Streetwear</a>
        </div>
    </div>

    <!-- PRODUCTS GRID -->
    <div class="grid-4-col" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px;">
        @foreach($products as $product)
            <div class="zal-card" style="padding: 14px; background: #16161f; border-radius: 16px; display: flex; flex-direction: column;">
                <a href="{{ route('shop.product', $product->id) }}" style="text-decoration: none; color: inherit; flex: 1;">
                    <div style="height: 200px; border-radius: 12px; overflow: hidden; margin-bottom: 12px; position: relative;">
                        <img src="{{ $product->primary_image }}" alt="{{ $product->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @if($product->is_natural_lighting_declared)
                            <div style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.7); backdrop-filter: blur(6px); color: #25F4EE; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">
                                ☀️ Natural Lighting Verified
                            </div>
                        @endif
                    </div>

                    <div style="font-size: 11px; color: #888; text-transform: uppercase; margin-bottom: 4px;">{{ $product->category }}</div>
                    <h3 style="font-weight: 700; font-size: 14px; line-height: 1.4; height: 40px; overflow: hidden; margin-bottom: 10px;">{{ $product->title }}</h3>

                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: auto;">
                        <div>
                            <span style="font-size: 18px; font-weight: 900; color: #FE2C55;">${{ number_format($product->price, 2) }}</span>
                            @if($product->compare_price)
                                <span style="font-size: 12px; color: #666; text-decoration: line-through; margin-left: 6px;">${{ number_format($product->compare_price, 2) }}</span>
                            @endif
                        </div>
                        <span style="font-size: 11px; color: #aaa;">Stock: {{ $product->available_stock }}</span>
                    </div>
                </a>

                <div style="margin-top: 12px; border-top: 1px solid #222; padding-top: 10px; display: flex; gap: 8px;">
                    <a href="{{ route('shop.product', $product->id) }}" class="zal-btn-primary" style="flex: 1; text-align: center; padding: 8px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; background: #FE2C55; color: #fff;">
                        Buy Now
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top: 30px;">
        {{ $products->links() }}
    </div>

</div>
@endsection
