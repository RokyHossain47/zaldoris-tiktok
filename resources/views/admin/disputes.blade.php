@extends('layouts.app')

@section('title', 'Dispute Management (4h SLA) - Zaldoris Admin')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.index') }}" style="color: #888; text-decoration: none; font-size: 13px;">
            <i class="bi bi-arrow-left"></i> Back to Admin Dashboard
        </a>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0;">Buyer & Seller Dispute Resolution</h1>
            <p style="color: #888; font-size: 13px; margin-top: 4px;">Mandatory 4-hour response SLA (SRS #10). Requires photo proof & seller 30s packing video comparison.</p>
        </div>
        <span style="background: rgba(254,44,85,0.15); color: #FE2C55; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 800;">
            Hard 4-Hour Response SLA Active
        </span>
    </div>

    <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid #333; color: #888;">
                    <th style="padding: 10px;">Order #</th>
                    <th style="padding: 10px;">Buyer</th>
                    <th style="padding: 10px;">Seller</th>
                    <th style="padding: 10px;">Reason</th>
                    <th style="padding: 10px;">Status</th>
                    <th style="padding: 10px; text-align: right;">Resolution Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disputes as $disp)
                    <tr style="border-bottom: 1px solid #222;">
                        <td style="padding: 12px 10px; font-weight: 700; color: #fff;">{{ $disp->order->order_number ?? ('ORD-' . $disp->order_id) }}</td>
                        <td style="padding: 12px 10px; color: #aaa;">{{ $disp->buyer->name }}</td>
                        <td style="padding: 12px 10px; color: #aaa;">{{ $disp->seller->name }}</td>
                        <td style="padding: 12px 10px; color: #FE2C55; font-weight: 700;">{{ $disp->reason }}</td>
                        <td style="padding: 12px 10px;">
                            <span style="color: #FFB800; font-weight: 700;">{{ ucfirst($disp->status) }}</span>
                        </td>
                        <td style="padding: 12px 10px; text-align: right;">
                            @if($disp->status === 'open')
                                <form action="{{ route('admin.disputes.resolve', $disp->id) }}" method="POST" style="display: inline-flex; gap: 6px;">
                                    @csrf
                                    <button type="submit" name="resolution" value="resolved_refund" style="background: #25F4EE; color: #000; border: none; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; cursor: pointer;">
                                        Refund Buyer
                                    </button>
                                    <button type="submit" name="resolution" value="resolved_credit" style="background: #FFB800; color: #000; border: none; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; cursor: pointer;">
                                        Store Credit
                                    </button>
                                </form>
                            @else
                                <span style="color: #888; font-size: 11px;">Resolved</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #888;">No active disputes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
