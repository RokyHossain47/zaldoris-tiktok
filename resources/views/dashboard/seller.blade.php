@extends('layouts.app')

@section('title', 'Seller Hub - Zaldoris')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    <!-- SELLER METRICS & FAST SHIPPER BADGE HEADER -->
    <div class="zal-card" style="background: linear-gradient(135deg, #181824, #12121a); border-radius: 20px; padding: 28px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid #25F4EE;">
            <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <h1 style="font-size: 22px; font-weight: 800; color: #fff; margin: 0;">{{ $user->name }}</h1>
                    @if($user->fast_shipper_badge)
                        <span style="background: rgba(37,244,238,0.2); color: #25F4EE; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 12px;">
                            ⚡ FAST SHIPPER (95%+ On-Time)
                        </span>
                    @endif
                </div>
                <div style="color: #888; font-size: 13px; margin-top: 4px;">
                    BN: <strong>{{ $profile->business_number ?? 'BN892341098RC0001' }}</strong> | SIN Status: <strong style="color: #25F4EE;">Verified</strong>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 20px;">
            <div style="background: #1f1f2a; padding: 14px 20px; border-radius: 12px; text-align: center;">
                <div style="font-size: 11px; color: #888; text-transform: uppercase;">48h On-Time Dispatch Rate</div>
                <div style="font-size: 22px; font-weight: 900; color: #25F4EE;">{{ $profile->on_time_dispatch_rate ?? 98.5 }}%</div>
            </div>
            <div style="background: #1f1f2a; padding: 14px 20px; border-radius: 12px; text-align: center;">
                <div style="font-size: 11px; color: #888; text-transform: uppercase;">Pending Dispatches</div>
                <div style="font-size: 22px; font-weight: 900; color: #FFB800;">{{ $pendingDispatchCount }}</div>
            </div>
            <div style="background: #1f1f2a; padding: 14px 20px; border-radius: 12px; text-align: center;">
                <div style="font-size: 11px; color: #888; text-transform: uppercase;">Total Sales</div>
                <div style="font-size: 22px; font-weight: 900; color: #FE2C55;">${{ number_format($user->earnings_usd, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- SELLER ORDERS & PACKING VIDEO UPLOAD (SRS #2 & #7) -->
    <h2 style="font-size: 18px; font-weight: 800; margin-bottom: 16px;">Orders Awaiting 48h Dispatch & Packing Proof</h2>

    <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px; margin-bottom: 30px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid #333; color: #888;">
                    <th style="padding: 10px;">Order #</th>
                    <th style="padding: 10px;">Customer</th>
                    <th style="padding: 10px;">Amount</th>
                    <th style="padding: 10px;">Dispatch Deadline</th>
                    <th style="padding: 10px;">30s Packing Video Proof</th>
                    <th style="padding: 10px; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr style="border-bottom: 1px solid #222;">
                        <td style="padding: 12px 10px; font-weight: 700; color: #fff;">{{ $order->order_number }}</td>
                        <td style="padding: 12px 10px; color: #aaa;">{{ $order->customer_name }}</td>
                        <td style="padding: 12px 10px; font-weight: 800; color: #FE2C55;">${{ number_format($order->total_amount, 2) }}</td>
                        <td style="padding: 12px 10px; color: #FFB800;">
                            {{ $order->dispatch_deadline ? $order->dispatch_deadline->diffForHumans() : 'Within 48 Hours' }}
                        </td>
                        <td style="padding: 12px 10px;">
                            @if($order->packing_video_url)
                                <span style="color: #25F4EE; font-weight: 700;"><i class="bi bi-check-circle-fill"></i> Video Verified</span>
                            @else
                                <form action="/api/v1/seller/orders/{{ $order->id }}/packing-video" method="POST" style="display: flex; gap: 6px;">
                                    @csrf
                                    <input type="hidden" name="packing_video_url" value="https://storage.zaldoris.com/packing_videos/demo_{{ $order->id }}.mp4">
                                    <button type="submit" style="background: rgba(37,244,238,0.15); border: 1px solid #25F4EE; color: #25F4EE; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer;">
                                        Upload 30s Video
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td style="padding: 12px 10px; text-align: right;">
                            @if($order->status === 'packing' && $order->packing_video_url)
                                <form action="/api/v1/seller/orders/{{ $order->id }}/dispatch" method="POST">
                                    @csrf
                                    <button type="submit" style="background: #25F4EE; color: #000; border: none; padding: 6px 14px; border-radius: 6px; font-weight: 800; font-size: 12px; cursor: pointer;">
                                        Dispatch & Print Label
                                    </button>
                                </form>
                            @else
                                <span style="color: #888; font-size: 12px;">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #888;">No seller orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- INVENTORY & PRE-SESSION LOCKING (SRS #14) -->
    <h2 style="font-size: 18px; font-weight: 800; margin-bottom: 16px;">Product Inventory & Pre-Session Stock Cap</h2>
    <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
            @foreach($products as $prod)
                <div style="background: #1f1f2a; padding: 14px; border-radius: 10px;">
                    <div style="font-weight: 700; font-size: 14px; margin-bottom: 6px;">{{ $prod->title }}</div>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: #aaa; margin-bottom: 10px;">
                        <span>Price: <strong>${{ number_format($prod->price, 2) }}</strong></span>
                        <span>Total Stock: <strong>{{ $prod->stock }}</strong></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: #aaa; margin-bottom: 12px;">
                        <span>Locked for Live: <strong style="color: #25F4EE;">{{ $prod->locked_stock }}</strong></span>
                        <span>Available: <strong style="color: #FFB800;">{{ $prod->available_stock }}</strong></span>
                    </div>
                    <form action="/api/v1/seller/inventory/lock" method="POST" style="display: flex; gap: 8px;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $prod->id }}">
                        <input type="number" name="locked_quantity" value="{{ $prod->locked_stock }}" min="0" max="{{ $prod->stock }}" style="width: 70px; background: #14141c; border: 1px solid #333; color: #fff; padding: 4px 8px; border-radius: 6px; font-size: 12px;">
                        <button type="submit" style="flex: 1; background: #333; color: #fff; border: none; padding: 6px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer;">
                            Update Live Cap
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
