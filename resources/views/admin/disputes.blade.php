@extends('layouts.admin')

@section('title', 'Agency & Disputes Management - GenZ Live Admin')

@section('content')
<div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 4px;">Dispute Resolution Hub (4-Hour Hard SLA)</h1>
    <p style="color: var(--text-muted); font-size: 13px;">Manage buyer claims, 30s packing video verification, and escrow payout resolutions (SRS #10).</p>
</div>

<div class="admin-table-container">
    <div class="table-header-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px; color: #FE2C55;"><i class="bi bi-exclamation-octagon-fill"></i></span>
            <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin: 0;">Open Disputes</h3>
        </div>
        <span style="background: rgba(254, 44, 85, 0.15); color: #FE2C55; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">
            4h Response SLA Active
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>ORDER #</th>
                    <th>BUYER</th>
                    <th>SELLER / AGENCY</th>
                    <th>REASON</th>
                    <th>STATUS</th>
                    <th style="text-align: right;">RESOLUTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disputes as $disp)
                    <tr>
                        <td style="font-weight: 700; color: #fff;">{{ $disp->order->order_number ?? ('ORD-' . $disp->order_id) }}</td>
                        <td style="color: #aaa;">{{ $disp->buyer->name }}</td>
                        <td style="color: #aaa;">{{ $disp->seller->name }}</td>
                        <td style="color: #FE2C55; font-weight: 700;">{{ $disp->reason }}</td>
                        <td>
                            <span class="status-badge {{ $disp->status === 'open' ? 'status-blocked' : 'status-active' }}">
                                {{ ucfirst($disp->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            @if($disp->status === 'open')
                                <form action="{{ route('admin.disputes.resolve', $disp->id) }}" method="POST" style="display: inline-flex; gap: 6px;">
                                    @csrf
                                    <button type="submit" name="resolution" value="resolved_refund" style="background: #25F4EE; color: #000; border: none; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 800; cursor: pointer;">
                                        Refund
                                    </button>
                                    <button type="submit" name="resolution" value="resolved_credit" style="background: #FFB800; color: #000; border: none; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 800; cursor: pointer;">
                                        Store Credit
                                    </button>
                                </form>
                            @else
                                <span style="color: var(--text-muted); font-size: 12px;">Resolved</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">No open disputes pending.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
        {{ $disputes->links() }}
    </div>
</div>
@endsection
