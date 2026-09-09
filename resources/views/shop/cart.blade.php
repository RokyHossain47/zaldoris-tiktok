@extends('layouts.app')

@section('title', 'Shopping Cart - Zaldoris')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">

    <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;">Shopping Cart</h1>

    <div style="display: grid; grid-template-columns: 1fr 360px; gap: 30px;">
        
        <!-- CART ITEMS -->
        <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
            <div style="display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid #222;">
                <img src="https://images.unsplash.com/photo-1552346154-21d32810aba3?w=200" alt="Jordan 1" style="width: 80px; height: 80px; border-radius: 10px; object-fit: cover;">
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 15px; margin-bottom: 4px;">Nike Air Jordan 1 Retro High OG Chicago</div>
                    <div style="color: #888; font-size: 12px; margin-bottom: 8px;">Size: US 10.5 | Seller: Supreme Kicks</div>
                    <div style="color: #FE2C55; font-weight: 800; font-size: 16px;">$380.00</div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-weight: 700;">Qty: 1</span>
                </div>
            </div>
        </div>

        <!-- SUMMARY & 13% HST TAX BREAKDOWN -->
        <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 24px;">
            <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Order Summary</h3>

            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px; margin-bottom: 20px; border-bottom: 1px solid #222; padding-bottom: 16px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #888;">Item Subtotal</span>
                    <strong style="color: #fff;">$380.00</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #888;">Shipping (Prepaid EasyPost)</span>
                    <strong style="color: #fff;">$10.00</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #888;">Mandatory Insurance (> $50)</span>
                    <strong style="color: #25F4EE;">$1.90</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #888;">13% HST Canadian Tax</span>
                    <strong style="color: #fff;">$50.95</strong>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 900; margin-bottom: 20px;">
                <span>Estimated Total</span>
                <span style="color: #25F4EE;">$442.85</span>
            </div>

            <a href="{{ route('shop.checkout') }}" class="zal-btn-primary" style="display: block; text-align: center; padding: 14px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 14px; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff;">
                Proceed to Checkout
            </a>

            <div style="margin-top: 14px; text-align: center; font-size: 11px; color: #777;">
                🔒 Held in Escrow until delivery confirmation.
            </div>
        </div>

    </div>

</div>
@endsection
