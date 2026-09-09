@extends('layouts.app')

@section('title', 'AI Content Moderation - Zaldoris Admin')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.index') }}" style="color: #888; text-decoration: none; font-size: 13px;">
            <i class="bi bi-arrow-left"></i> Back to Admin Dashboard
        </a>
    </div>

    <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 8px;">AI Content Moderation Log</h1>
    <p style="color: #888; font-size: 13px; margin-bottom: 24px;">
        Automated filter logs detecting scams, hate speech, counterfeit claims, and Health Canada prohibited ingredients (SRS Page 5 & 11).
    </p>

    <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 20px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid #333; color: #888;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Source</th>
                    <th style="padding: 10px;">Flagged Content</th>
                    <th style="padding: 10px;">Violation Type</th>
                    <th style="padding: 10px;">Confidence</th>
                    <th style="padding: 10px;">Action Taken</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr style="border-bottom: 1px solid #222;">
                        <td style="padding: 12px 10px; color: #888;">#{{ $log->id }}</td>
                        <td style="padding: 12px 10px; font-weight: 700; color: #fff;">{{ ucfirst(str_replace('_', ' ', $log->content_type)) }}</td>
                        <td style="padding: 12px 10px; color: #FE2C55; font-weight: 600;">"{{ $log->flagged_content }}"</td>
                        <td style="padding: 12px 10px; color: #FFB800; text-transform: uppercase; font-size: 11px; font-weight: 700;">{{ $log->violation_type }}</td>
                        <td style="padding: 12px 10px; color: #25F4EE; font-weight: 800;">{{ round($log->confidence_score * 100) }}%</td>
                        <td style="padding: 12px 10px;">
                            <span style="background: rgba(254,44,85,0.2); color: #FE2C55; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">
                                {{ strtoupper($log->action_taken) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #888;">No moderation violations logged.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
