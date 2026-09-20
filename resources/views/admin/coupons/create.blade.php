@extends('layouts.admin')

@section('title', 'Create Promo Coupon')

@section('content')
<div class="admin-content-inner" style="max-width: 900px; margin: 0 auto;">
    <!-- Back Header -->
    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 24px;">
        <a href="{{ route('admin.coupons.index') }}" style="background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #fff; margin: 0;">Create New Coupon</h1>
            <p style="color: var(--text-muted); font-size: 13px; margin: 0;">Set discount rates, usage caps, and validity criteria.</p>
        </div>
    </div>

    @if($errors->any())
        <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid #FE2C55; color: #FE2C55; padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="stat-card" style="display: block; padding: 28px;">
        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <!-- Coupon Code -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Coupon Code <span style="color: #FE2C55;">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. WELCOME20, SUMMER50" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #00F0C8; padding: 10px 14px; border-radius: 8px; font-size: 14px; font-family: monospace; font-weight: 800; text-transform: uppercase;" required>
                    <small style="color: var(--text-muted); font-size: 11px;">Buyers enter this code at checkout.</small>
                </div>

                <!-- Coupon Name / Title -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Internal Campaign Name
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Welcome Discount 20% Off" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                </div>
            </div>

            <!-- Description -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                    Description / Terms
                </label>
                <textarea name="description" rows="2" placeholder="Optional details or terms for buyers..." style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">{{ old('description') }}</textarea>
            </div>

            <!-- Discount Type & Value -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Discount Type <span style="color: #FE2C55;">*</span>
                    </label>
                    <select name="type" id="couponTypeSelect" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;" required>
                        <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Percentage (%) Discount</option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed Dollar ($) Discount</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Discount Value <span style="color: #FE2C55;">*</span>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="value" value="{{ old('value', '10') }}" placeholder="e.g. 15 for 15% or 20 for $20" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;" required>
                </div>
            </div>

            <!-- Min Order & Max Discount Cap -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Minimum Order Amount ($)
                    </label>
                    <input type="number" step="0.01" min="0" name="min_order_amount" value="{{ old('min_order_amount', '0.00') }}" placeholder="0.00" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                    <small style="color: var(--text-muted); font-size: 11px;">Order subtotal must reach this value.</small>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Max Discount Cap ($)
                    </label>
                    <input type="number" step="0.01" min="0" name="max_discount_amount" value="{{ old('max_discount_amount') }}" placeholder="Optional (e.g. 50.00)" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                    <small style="color: var(--text-muted); font-size: 11px;">Maximum savings cap for percentage discounts.</small>
                </div>
            </div>

            <!-- Usage Limits -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Global Usage Limit
                    </label>
                    <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit') }}" placeholder="Leave blank for unlimited" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Per User Usage Limit
                    </label>
                    <input type="number" min="1" name="per_user_limit" value="{{ old('per_user_limit', '1') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;" required>
                </div>
            </div>

            <!-- Date Range -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Start Date (Optional)
                    </label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Expiry Date (Optional)
                    </label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                </div>
            </div>

            <!-- Status Checkbox -->
            <div style="margin-bottom: 28px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 14px; font-weight: 600;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #00F0C8;">
                    Enable and Activate Coupon Immediately
                </label>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('admin.coupons.index') }}" style="background: rgba(255,255,255,0.06); border: 1px solid var(--border-color); color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">
                    Cancel
                </a>
                <button type="submit" style="background: linear-gradient(135deg, #00F0C8, #1ed6d0); border: none; color: #090D10; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">
                    Create Coupon
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
