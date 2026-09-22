@extends('layouts.app')

@section('title', 'My Orders - Zaldoris')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">

    <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;">My Orders & Escrow Tracking</h1>

    <div style="display: flex; flex-direction: column; gap: 16px;">
        @forelse($orders as $order)
            <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; padding-bottom: 12px; margin-bottom: 14px;">
                    <div>
                        <strong style="color: #fff; font-size: 15px;">{{ $order->order_number }}</strong>
                        <span style="color: #888; font-size: 12px; margin-left: 10px;">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span style="background: rgba(37,244,238,0.15); color: #25F4EE; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 13px; color: #aaa;">Seller: <strong style="color: #fff;">{{ $order->seller->name }}</strong></div>
                        <div style="font-size: 13px; color: #aaa;">Tracking: <strong style="color: #25F4EE;">{{ $order->tracking_number ?? 'Pending Dispatch' }}</strong></div>
                        <div style="font-size: 13px; color: #aaa;">Total: <strong style="color: #FE2C55;">${{ number_format($order->total_amount, 2) }}</strong> (incl. 13% HST)</div>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        @if($order->isWithinCancellationGracePeriod())
                            <form action="/api/v1/shop/orders/{{ $order->id }}/cancel" method="POST">
                                @csrf
                                <button type="submit" style="background: #222; border: 1px solid #444; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                    Cancel (Store Credit)
                                </button>
                            </form>
                        @endif
                        <button onclick="alert('EasyPost Live Tracking: Package prepared for courier dispatch in Toronto, ON.')" style="background: #FE2C55; color: #fff; border: none; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                            Track Order
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="zal-card" style="text-align: center; padding: 50px; color: #888;">
                <i class="bi bi-box-seam" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                You have not placed any orders yet.
            </div>
        @endforelse
    </div>

</div>
@endsection
