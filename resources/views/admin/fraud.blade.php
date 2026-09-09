@extends('layouts.app')

@section('title', 'Fraud Detection & Shield - Zaldoris Admin')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.index') }}" style="color: #888; text-decoration: none; font-size: 13px;">
            <i class="bi bi-arrow-left"></i> Back to Admin Dashboard
        </a>
    </div>

    <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 8px;">Fraud & Anti-Shill Detection Engine</h1>
    <p style="color: #888; font-size: 13px; margin-bottom: 24px;">
        Automated monitoring for duplicate IP bidding, high velocity gifting wash-trading, and friendly chargeback flags (SRS Pages 11-14).
    </p>

    <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid #333; color: #888;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">User</th>
                    <th style="padding: 10px;">Violation Type</th>
                    <th style="padding: 10px;">Severity</th>
                    <th style="padding: 10px;">Description</th>
                    <th style="padding: 10px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $al)
                    <tr style="border-bottom: 1px solid #222;">
                        <td style="padding: 12px 10px; color: #888;">#{{ $al->id }}</td>
                        <td style="padding: 12px 10px; font-weight: 700; color: #fff;">{{ $al->user->name ?? 'Guest/Anonymous' }}</td>
                        <td style="padding: 12px 10px; text-transform: uppercase; font-weight: 700; color: #FE2C55;">{{ str_replace('_', ' ', $al->type) }}</td>
                        <td style="padding: 12px 10px;">
                            <span style="background: rgba(254,44,85,0.2); color: #FE2C55; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">
                                {{ strtoupper($al->severity) }}
                            </span>
                        </td>
                        <td style="padding: 12px 10px; color: #aaa; max-width: 400px;">{{ $al->description }}</td>
                        <td style="padding: 12px 10px;">
                            <span style="color: #FFB800; font-weight: 700;">{{ ucfirst($al->status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #888;">No fraud records logged.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
