@extends('layouts.admin')

@section('title', 'System & Localization Settings - Super Admin')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">System & Localization Settings</h1>
            <p style="font-size: 14px; color: var(--text-muted);">Manage platform default currency, timezones, date formatting, and commission parameters.</p>
        </div>
    </div>

    <!-- SETTINGS SUB-NAV TABS -->
    <div style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
        <a href="{{ route('admin.settings.general') }}" style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
            <i class="bi bi-sliders"></i> General
        </a>
        <a href="{{ route('admin.settings.seo') }}" style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
            <i class="bi bi-search-heart"></i> SEO & Metadata
        </a>
        <a href="{{ route('admin.settings.system') }}" style="background: var(--pink-accent); color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
            <i class="bi bi-cpu"></i> System & Localization
        </a>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <form action="{{ route('admin.settings.system.update') }}" method="POST">
            @csrf

            <!-- CURRENCY & SYMBOL -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Default Currency ISO Code *</label>
                    <select name="default_currency" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="USD" {{ setting('default_currency') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                        <option value="CAD" {{ setting('default_currency') === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                        <option value="EUR" {{ setting('default_currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                        <option value="GBP" {{ setting('default_currency') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                        <option value="BDT" {{ setting('default_currency') === 'BDT' ? 'selected' : '' }}>BDT - Bangladeshi Taka</option>
                        <option value="AUD" {{ setting('default_currency') === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Currency Symbol *</label>
                    <input type="text" name="currency_symbol" required value="{{ setting('currency_symbol', '$') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <!-- TIMEZONE & FORMATS -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Timezone *</label>
                    <select name="timezone" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="America/Toronto" {{ setting('timezone') === 'America/Toronto' ? 'selected' : '' }}>America/Toronto (EST/EDT)</option>
                        <option value="America/New_York" {{ setting('timezone') === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                        <option value="America/Los_Angeles" {{ setting('timezone') === 'America/Los_Angeles' ? 'selected' : '' }}>America/Los_Angeles (PST)</option>
                        <option value="UTC" {{ setting('timezone') === 'UTC' ? 'selected' : '' }}>UTC (Coordinated Universal Time)</option>
                        <option value="Asia/Dhaka" {{ setting('timezone') === 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (BST)</option>
                        <option value="Europe/London" {{ setting('timezone') === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT/BST)</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Date Format *</label>
                    <select name="date_format" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="Y-m-d" {{ setting('date_format') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2026-09-12)</option>
                        <option value="d/m/Y" {{ setting('date_format') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (12/09/2026)</option>
                        <option value="M d, Y" {{ setting('date_format') === 'M d, Y' ? 'selected' : '' }}>Month DD, YYYY (Sep 12, 2026)</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Time Format *</label>
                    <select name="time_format" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="h:i A" {{ setting('time_format') === 'h:i A' ? 'selected' : '' }}>12-hour (08:30 PM)</option>
                        <option value="H:i" {{ setting('time_format') === 'H:i' ? 'selected' : '' }}>24-hour (20:30)</option>
                    </select>
                </div>
            </div>

            <!-- COMMISSIONS & FEES -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Default Platform Commission (%)</label>
                    <input type="number" step="0.1" name="platform_commission_percent" value="{{ setting('platform_commission_percent', '7.0') }}" style="width: 100%; background: var(--bg-card); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Standard commission fee deducted from seller sales.</div>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Creator Monthly Subscription ($)</label>
                    <input type="number" step="0.01" name="creator_subscription_price" value="{{ setting('creator_subscription_price', '7.99') }}" style="width: 100%; background: var(--bg-card); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Base price for user VIP creator channel subscriptions.</div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" style="background: linear-gradient(135deg, var(--pink-accent), var(--pink-hover)); color: #fff; border: none; padding: 14px 32px; border-radius: 10px; font-weight: 800; font-size: 14px; cursor: pointer; box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4);">
                    Save System Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
