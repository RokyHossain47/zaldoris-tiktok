@extends('layouts.admin')

@section('title', 'General Settings - Super Admin')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">General Settings</h1>
            <p style="font-size: 14px; color: var(--text-muted);">Manage site identity, branding assets, logos, and support contacts.</p>
        </div>
    </div>

    <!-- SETTINGS SUB-NAV TABS -->
    <div style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
        <a href="{{ route('admin.settings.general') }}" style="background: var(--pink-accent); color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
            <i class="bi bi-sliders"></i> General
        </a>
        <a href="{{ route('admin.settings.seo') }}" style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
            <i class="bi bi-search-heart"></i> SEO & Metadata
        </a>
        <a href="{{ route('admin.settings.system') }}" style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
            <i class="bi bi-cpu"></i> System & Localization
        </a>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Site Name *</label>
                    <input type="text" name="site_name" required value="{{ setting('site_name', 'Zaldoris Live Commerce') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Tagline / Slogan</label>
                    <input type="text" name="site_tagline" value="{{ setting('site_tagline', 'TikTok-Style Live Commerce & Whatnot Auctions') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <!-- LOGO & FAVICON UPLOADS -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Website Logo</label>
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 10px;">
                        <img src="{{ asset(setting('site_logo', 'assets/logo.png')) }}" alt="Logo" style="height: 38px; background: rgba(0,0,0,0.5); padding: 4px 10px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <input type="file" name="logo_file" accept="image/*" style="flex: 1; color: #fff; font-size: 12px;">
                    </div>
                    <div style="font-size: 11px; color: var(--text-muted);">Recommended format: PNG or SVG with transparent background.</div>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Favicon Icon</label>
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 10px;">
                        <img src="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}" alt="Favicon" style="width: 32px; height: 32px; object-fit: contain; border-radius: 6px; border: 1px solid var(--border-color);">
                        <input type="file" name="favicon_file" accept="image/*" style="flex: 1; color: #fff; font-size: 12px;">
                    </div>
                    <div style="font-size: 11px; color: var(--text-muted);">Square icon (32x32px or 64x64px PNG/ICO).</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Support Email</label>
                    <input type="email" name="contact_email" value="{{ setting('contact_email', 'support@zaldoris.com') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Support Phone / WhatsApp</label>
                    <input type="text" name="contact_phone" value="{{ setting('contact_phone', '+1 (416) 555-0100') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <div style="margin-bottom: 28px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Footer Copyright Text</label>
                <input type="text" name="copyright_text" value="{{ setting('copyright_text', '© 2026 Zaldoris Live Commerce Ltd. All rights reserved.') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" style="background: linear-gradient(135deg, var(--pink-accent), var(--pink-hover)); color: #fff; border: none; padding: 14px 32px; border-radius: 10px; font-weight: 800; font-size: 14px; cursor: pointer; box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4);">
                    Save General Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
