@extends('layouts.admin')

@section('title', 'Security Team & Fraud Detection - GenZ Live Admin')

@section('content')
<div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 4px;">Security & Fraud Detection Log</h1>
    <p style="color: var(--text-muted); font-size: 13px;">Anti-shill bidding detection, wash trading velocity checks, and duplicate IP flags (SRS Pages 11-14).</p>
</div>

<div class="admin-table-container">
    <div class="table-header-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px; color: #FE2C55;"><i class="bi bi-shield-exclamation"></i></span>
            <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin: 0;">Security Alerts</h3>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>TARGET USER</th>
                    <th>VIOLATION TYPE</th>
                    <th>SEVERITY</th>
                    <th>DETAILS</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $al)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 700;">#{{ $al->id }}</td>
                        <td style="font-weight: 700; color: #fff;">{{ $al->user->name ?? 'Anonymous' }}</td>
                        <td>
                            <span style="background: rgba(254, 44, 85, 0.15); color: #FE2C55; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                                {{ str_replace('_', ' ', $al->type) }}
                            </span>
                        </td>
                        <td>
                            <span style="background: rgba(255, 184, 0, 0.15); color: #FFB800; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800;">
                                {{ strtoupper($al->severity) }}
                            </span>
                        </td>
                        <td style="color: #aaa; max-width: 350px;">{{ $al->description }}</td>
                        <td>
                            <span class="status-badge status-active">{{ ucfirst($al->status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">No security alerts recorded. System healthy.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
        {{ $alerts->links() }}
    </div>
</div>
@endsection
