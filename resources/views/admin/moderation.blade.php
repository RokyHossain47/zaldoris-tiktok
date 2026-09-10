@extends('layouts.admin')

@section('title', 'AI Content Moderation - GenZ Live Admin')

@section('content')
<div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 4px;">Host & Stream AI Moderation Logs</h1>
    <p style="color: var(--text-muted); font-size: 13px;">Real-time automated content filtering across live chats, stream descriptions, and product declarations (SRS Page 5).</p>
</div>

<div class="admin-table-container">
    <div class="table-header-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px; color: #FE2C55;"><i class="bi bi-robot"></i></span>
            <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin: 0;">AI Moderation Incidents</h3>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>SOURCE</th>
                    <th>FLAGGED CONTENT</th>
                    <th>VIOLATION</th>
                    <th>CONFIDENCE</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 700;">#{{ $log->id }}</td>
                        <td style="font-weight: 700; color: #fff;">{{ ucfirst(str_replace('_', ' ', $log->content_type)) }}</td>
                        <td style="color: #FE2C55; font-weight: 600;">"{{ $log->flagged_content }}"</td>
                        <td>
                            <span style="background: rgba(255, 184, 0, 0.15); color: #FFB800; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                {{ $log->violation_type }}
                            </span>
                        </td>
                        <td style="color: #25F4EE; font-weight: 800;">{{ round($log->confidence_score * 100) }}%</td>
                        <td>
                            <span class="status-badge status-blocked">{{ strtoupper($log->action_taken) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">No AI moderation incidents found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
        {{ $logs->links() }}
    </div>
</div>
@endsection
