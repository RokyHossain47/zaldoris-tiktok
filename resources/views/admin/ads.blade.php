@extends('layouts.app')

@section('title', 'Advertisement System - Zaldoris Admin')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.index') }}" style="color: #888; text-decoration: none; font-size: 13px;">
            <i class="bi bi-arrow-left"></i> Back to Admin Dashboard
        </a>
    </div>

    <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 8px;">Advertisement Management System</h1>
    <p style="color: #888; font-size: 13px; margin-bottom: 24px;">
        Supported ad formats: Pre-roll ads, Banner ads, Host ads, Interactive ads (15-20 min interval), and Sponsored gifts (SRS Page 5).
    </p>

    <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid #333; color: #888;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Campaign Title</th>
                    <th style="padding: 10px;">Ad Format</th>
                    <th style="padding: 10px;">Interval</th>
                    <th style="padding: 10px;">Impressions</th>
                    <th style="padding: 10px;">Status</th>
                    <th style="padding: 10px; text-align: right;">Toggle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ads as $ad)
                    <tr style="border-bottom: 1px solid #222;">
                        <td style="padding: 12px 10px; color: #888;">#{{ $ad->id }}</td>
                        <td style="padding: 12px 10px; font-weight: 700; color: #fff;">{{ $ad->title }}</td>
                        <td style="padding: 12px 10px; text-transform: uppercase; font-size: 11px; font-weight: 700; color: #25F4EE;">{{ str_replace('_', ' ', $ad->ad_type) }}</td>
                        <td style="padding: 12px 10px; color: #aaa;">Every {{ $ad->interval_minutes }} mins</td>
                        <td style="padding: 12px 10px; font-weight: 700; color: #FFB800;">{{ number_format($ad->impressions) }}</td>
                        <td style="padding: 12px 10px;">
                            @if($ad->is_active)
                                <span style="background: rgba(37,244,238,0.2); color: #25F4EE; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">ACTIVE</span>
                            @else
                                <span style="background: rgba(255,255,255,0.1); color: #777; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">DISABLED</span>
                            @endif
                        </td>
                        <td style="padding: 12px 10px; text-align: right;">
                            <form action="{{ route('admin.ads.toggle', $ad->id) }}" method="POST">
                                @csrf
                                <button type="submit" style="background: {{ $ad->is_active ? '#222' : '#FE2C55' }}; color: #fff; border: 1px solid #444; padding: 6px 14px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer;">
                                    {{ $ad->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #888;">No advertisements configured.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
