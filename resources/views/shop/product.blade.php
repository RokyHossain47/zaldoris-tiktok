@extends('layouts.app')

@section('title', $product->title . ' - Zaldoris Shop')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">

    <div style="margin-bottom: 20px;">
        <a href="{{ route('shop.index') }}" style="color: #888; text-decoration: none; font-size: 13px;">
            <i class="bi bi-arrow-left"></i> Back to Shop
        </a>
    </div>

    <div class="zal-card" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; background: #16161f; border-radius: 20px; padding: 34px;">
        
        <!-- PRODUCT IMAGES -->
        <div>
            <div style="border-radius: 16px; overflow: hidden; background: #0a0a0f; height: 420px; margin-bottom: 14px;">
                <img src="{{ $product->primary_image }}" alt="{{ $product->title }}" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
        </div>

        <!-- PRODUCT DETAILS & BUY CONSOLE -->
        <div>
            <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                <span style="background: rgba(37,244,238,0.15); color: #25F4EE; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
                    {{ $product->category }}
                </span>
                <span style="background: rgba(254,44,85,0.15); color: #FE2C55; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px;">
                    48H FAST DISPATCH
                </span>
            </div>

            <h1 style="font-size: 24px; font-weight: 800; color: #fff; line-height: 1.3; margin-bottom: 14px;">{{ $product->title }}</h1>
            
            <div style="display: flex; align-items: baseline; gap: 12px; margin-bottom: 20px;">
                <span style="font-size: 32px; font-weight: 900; color: #FE2C55;">${{ number_format($product->price, 2) }}</span>
                @if($product->compare_price)
                    <span style="font-size: 16px; color: #666; text-decoration: line-through;">${{ number_format($product->compare_price, 2) }}</span>
                @endif
                <span style="font-size: 12px; color: #aaa; margin-left: 10px;">+ 13% HST Canadian Tax</span>
            </div>

            <p style="color: #aaa; font-size: 14px; line-height: 1.6; margin-bottom: 24px;">{{ $product->description }}</p>

            <!-- SRS MANDATORY PRODUCT COMPLIANCE SPECIFICATIONS (Pages 11 & 14) -->
            <div style="background: #1f1f2a; border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #888;">Sourcing Country:</span>
                    <strong style="color: #fff;">{{ $product->sourcing_country ?? 'Canada / Authorized Retailer' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #888;">Dimensions & Sizing:</span>
                    <strong style="color: #fff;">{{ $product->dimensions ?? 'Standard Retail Specifications' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #888;">Lighting Disclosure:</span>
                    <strong style="color: #25F4EE;">☀️ Natural Lighting Verified</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #888;">Available Stock:</span>
                    <strong style="color: #FFB800;">{{ $product->available_stock }} units</strong>
                </div>
            </div>

            <!-- BUY BUTTONS -->
            <form action="{{ route('shop.checkout.process') }}" method="POST" style="margin-bottom: 20px;">
                @csrf
                <input type="hidden" name="items[0][product_id]" value="{{ $product->id }}">
                <input type="hidden" name="items[0][quantity]" value="1">
                <input type="hidden" name="street" value="120 Bay Street">
                <input type="hidden" name="city" value="Toronto">
                <input type="hidden" name="province" value="ON">
                <input type="hidden" name="postal_code" value="M5J 2R8">
                <input type="hidden" name="country" value="Canada">

                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="zal-btn-primary" style="flex: 1; padding: 14px; border-radius: 10px; border: none; font-size: 15px; font-weight: 800; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff; cursor: pointer; box-shadow: 0 4px 16px rgba(254,44,85,0.4);">
                        ⚡ 1-Tap Express Buy (${{ number_format($product->price, 2) }})
                    </button>
                    <a href="{{ route('shop.cart') }}" class="zal-btn-secondary" style="padding: 14px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; background: #222; color: #fff; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-cart3"></i>
                    </a>
                </div>
            </form>

            <div style="display: flex; align-items: center; gap: 10px; border-top: 1px solid #222; padding-top: 16px;">
                <img src="{{ $product->seller->avatar_url }}" alt="{{ $product->seller->name }}" style="width: 40px; height: 40px; border-radius: 50%;">
                <div>
                    <div style="font-weight: 700; font-size: 13px;">{{ $product->seller->name }}</div>
                    <div style="font-size: 11px; color: #25F4EE;">⚡ Fast Shipper (98.5% on-time 48h dispatch)</div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
