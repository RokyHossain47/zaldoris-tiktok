@extends('layouts.app')

@section('title', 'Notifications - Zaldoris')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">

    <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;">Notification Center</h1>

    <div style="display: flex; flex-direction: column; gap: 14px;">
        @forelse($notifications as $notif)
            <div class="zal-card" style="background: #16161f; border-radius: 14px; padding: 18px; display: flex; gap: 16px; align-items: flex-start;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(254,44,85,0.15); color: #FE2C55; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 800; font-size: 14px; color: #fff; margin-bottom: 4px;">{{ $notif->title }}</div>
                    <div style="font-size: 13px; color: #aaa; line-height: 1.5; margin-bottom: 6px;">{{ $notif->message }}</div>
                    <div style="font-size: 11px; color: #666;">{{ $notif->created_at->diffForHumans() }}</div>
                </div>
            </div>
        @empty
            <div class="zal-card" style="text-align: center; padding: 50px; color: #888;">
                <i class="bi bi-bell-slash" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                No notifications right now.
            </div>
        @endforelse
    </div>

</div>
@endsection
