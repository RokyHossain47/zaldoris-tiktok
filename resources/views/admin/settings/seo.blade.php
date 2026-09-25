@extends('layouts.admin')

@section('title', 'SEO & Metadata Settings - Super Admin')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">SEO & Metadata Settings</h1>
            <p style="font-size: 14px; color: var(--text-muted);">Configure meta tags, OpenGraph sharing previews, Google search ranking, and tracking scripts.</p>
        </div>
    </div>

    <!-- SETTINGS SUB-NAV TABS -->
    <div style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; flex-wrap: wrap;">
        <a href="{{ route('admin.settings.general') }}" style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
            <i class="bi bi-sliders"></i> General
        </a>
        <a href="{{ route('admin.settings.seo') }}" style="background: var(--pink-accent); color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
            <i class="bi bi-search-heart"></i> SEO & Metadata
        </a>
        <a href="{{ route('admin.settings.system') }}" style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
            <i class="bi bi-cpu"></i> System & Localization
        </a>
        <a href="{{ route('admin.settings.shipping') }}" style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
            <i class="bi bi-truck"></i> Shipping Methods
        </a>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <form action="{{ route('admin.settings.seo.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Meta Title (Default Page Title for Google & Browser) *</label>
                <input type="text" name="meta_title" required value="{{ setting('meta_title', 'Zaldoris - Next-Gen Live Shopping & Instant Auctions') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Recommended length: 50–60 characters.</div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Meta Description</label>
                <textarea name="meta_description" rows="3" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">{{ setting('meta_description', 'Experience interactive real-time live shopping, live video stream drops, Whatnot-style fast auctions, and PK Battles on Zaldoris.') }}</textarea>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Summarizes the page content for search engines (150–160 characters).</div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Meta Keywords</label>
                <input type="text" name="meta_keywords" value="{{ setting('meta_keywords', 'live commerce, live shopping, tiktok shop, live auction, whatnot auction, pk battle, creator economy') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Comma separated keywords.</div>
            </div>

            <!-- OPEN GRAPH PREVIEW IMAGE -->
            <div style="margin-bottom: 24px; background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Social Share Preview Image (OpenGraph / Twitter Card)</label>
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 12px;">
                    @if(setting('og_image'))
                        <img src="{{ setting('og_image') }}" alt="OG Preview" style="width: 120px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                    @endif
                    <input type="file" name="og_image_file" accept="image/*" style="flex: 1; color: #fff; font-size: 13px;">
                </div>
                <div style="font-size: 11px; color: var(--text-muted);">Recommended resolution: 1200x630px. Shown when links are shared on Facebook, Twitter, WhatsApp, Discord.</div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Google Analytics Measurement ID / GTAG</label>
                <input type="text" name="google_analytics_id" placeholder="G-XXXXXXXXXX" value="{{ setting('google_analytics_id', 'G-ZALDORIS2026') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 28px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Custom Header Scripts / Verification Tags (&lt;head&gt;)</label>
                <textarea name="custom_header_scripts" rows="4" placeholder="<meta name='google-site-verification' content='...' />" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #25F4EE; font-family: monospace; padding: 14px 16px; border-radius: 10px; font-size: 13px;">{{ setting('custom_header_scripts') }}</textarea>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Inject custom verification tags, Facebook Pixel, or Google Search Console tokens.</div>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" style="background: linear-gradient(135deg, var(--pink-accent), var(--pink-hover)); color: #fff; border: none; padding: 14px 32px; border-radius: 10px; font-weight: 800; font-size: 14px; cursor: pointer; box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4);">
                    Save SEO Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
