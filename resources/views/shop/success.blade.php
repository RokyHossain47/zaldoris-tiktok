@extends('layouts.app')

@section('title', 'Order Placed - Zaldoris')

@section('content')
<div style="max-width: 680px; margin: 40px auto; text-align: center;">

    <div class="zal-card" style="background: #16161f; border-radius: 20px; padding: 40px 30px;">
        <div style="font-size: 56px; margin-bottom: 16px;">🎉</div>
        
        <h1 style="font-size: 26px; font-weight: 900; color: #fff; margin-bottom: 8px;">Order Confirmed!</h1>
        <p style="color: #aaa; font-size: 14px; margin-bottom: 24px;">Order #{{ $order->order_number }}</p>

        <div style="background: #1f1f2a; border-radius: 12px; padding: 20px; margin-bottom: 24px; text-align: left; font-size: 13px; line-height: 1.8;">
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #888;">Payment Escrow Status:</span>
                <strong style="color: #25F4EE;">Secured in Escrow</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #888;">Dispatch Requirement:</span>
                <strong style="color: #FFB800;">Within 48 Hours</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #888;">Tracking Number:</span>
                <strong style="color: #fff;">{{ $order->tracking_number ?? 'EP982347102CA' }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #888;">Total (incl. 13% HST):</span>
                <strong style="color: #FE2C55; font-size: 15px;">${{ number_format($order->total_amount, 2) }}</strong>
            </div>
        </div>

        <div style="background: rgba(37,244,238,0.1); border: 1px solid rgba(37,244,238,0.3); border-radius: 10px; padding: 12px; margin-bottom: 30px; font-size: 12px; color: #eee; text-align: left;">
            <i class="bi bi-info-circle-fill" style="color: #25F4EE;"></i> 
            <strong>Buyer Protection:</strong> Seller must upload a 30-second packing video. Funds will remain in escrow until EasyPost delivers the package.
        </div>

        <div style="display: flex; gap: 12px; justify-content: center;">
            <a href="{{ route('shop.orders') }}" class="zal-btn-primary" style="padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 800; background: #FE2C55; color: #fff;">
                View My Orders
            </a>
            <a href="{{ route('home') }}" class="zal-btn-secondary" style="padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; background: #222; color: #fff;">
                Back to Home
            </a>
        </div>
    </div>

</div>
@endsection
