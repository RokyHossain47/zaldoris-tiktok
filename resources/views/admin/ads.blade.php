@extends('layouts.admin')

@section('title', 'Banner & Ads Management - GenZ Live Admin')

@section('content')
<div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 4px;">Banner & Advertisement System</h1>
    <p style="color: var(--text-muted); font-size: 13px;">Manage pre-roll ads, banners, host ads, and interactive live stream promotion banners (SRS Page 5).</p>
</div>

<div class="admin-table-container">
    <div class="table-header-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px; color: #FE2C55;"><i class="bi bi-image-fill"></i></span>
            <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin: 0;">Active Ad Campaigns</h3>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>CAMPAIGN TITLE</th>
                    <th>AD FORMAT</th>
                    <th>INTERVAL</th>
                    <th>IMPRESSIONS</th>
                    <th>STATUS</th>
                    <th style="text-align: right;">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ads as $ad)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 700;">#{{ $ad->id }}</td>
                        <td style="font-weight: 700; color: #fff;">{{ $ad->title }}</td>
                        <td>
                            <span style="background: rgba(37, 244, 238, 0.15); color: #25F4EE; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                                {{ str_replace('_', ' ', $ad->ad_type) }}
                            </span>
                        </td>
                        <td style="color: #aaa;">Every {{ $ad->interval_minutes }} mins</td>
                        <td style="color: #FFB800; font-weight: 800;">{{ number_format($ad->impressions) }}</td>
                        <td>
                            @if($ad->is_active)
                                <span class="status-badge status-active">ACTIVE</span>
                            @else
                                <span class="status-badge status-blocked">DISABLED</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <form action="{{ route('admin.ads.toggle', $ad->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" style="background: {{ $ad->is_active ? 'rgba(254, 44, 85, 0.15)' : 'rgba(39, 201, 137, 0.15)' }}; border: 1px solid {{ $ad->is_active ? '#FE2C55' : '#27c989' }}; color: {{ $ad->is_active ? '#FE2C55' : '#27c989' }}; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                    {{ $ad->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">No ads configured.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
