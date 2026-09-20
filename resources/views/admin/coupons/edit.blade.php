@extends('layouts.admin')

@section('title', 'Edit Coupon - ' . $coupon->code)

@section('content')
<div class="admin-content-inner" style="max-width: 900px; margin: 0 auto;">
    <!-- Back Header -->
    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 24px;">
        <a href="{{ route('admin.coupons.index') }}" style="background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #fff; margin: 0;">Edit Coupon <span style="color: #00F0C8; font-family: monospace;">{{ $coupon->code }}</span></h1>
            <p style="color: var(--text-muted); font-size: 13px; margin: 0;">Update discount rates, usage caps, and validity criteria.</p>
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
        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <!-- Coupon Code -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Coupon Code <span style="color: #FE2C55;">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code', $coupon->code) }}" placeholder="e.g. WELCOME20, SUMMER50" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #00F0C8; padding: 10px 14px; border-radius: 8px; font-size: 14px; font-family: monospace; font-weight: 800; text-transform: uppercase;" required>
                </div>

                <!-- Coupon Name / Title -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Internal Campaign Name
                    </label>
                    <input type="text" name="name" value="{{ old('name', $coupon->name) }}" placeholder="e.g. Welcome Discount 20% Off" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                </div>
            </div>

            <!-- Description -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                    Description / Terms
                </label>
                <textarea name="description" rows="2" placeholder="Optional details or terms for buyers..." style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">{{ old('description', $coupon->description) }}</textarea>
            </div>

            <!-- Discount Type & Value -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Discount Type <span style="color: #FE2C55;">*</span>
                    </label>
                    <select name="type" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;" required>
                        <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage (%) Discount</option>
                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Dollar ($) Discount</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Discount Value <span style="color: #FE2C55;">*</span>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="value" value="{{ old('value', $coupon->value) }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;" required>
                </div>
            </div>

            <!-- Min Order & Max Discount Cap -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Minimum Order Amount ($)
                    </label>
                    <input type="number" step="0.01" min="0" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Max Discount Cap ($)
                    </label>
                    <input type="number" step="0.01" min="0" name="max_discount_amount" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" placeholder="Optional (e.g. 50.00)" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                </div>
            </div>

            <!-- Usage Limits -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Global Usage Limit
                    </label>
                    <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" placeholder="Leave blank for unlimited" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                    <small style="color: var(--text-muted); font-size: 11px;">Currently redeemed: <strong>{{ $coupon->used_count }}</strong> times.</small>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Per User Usage Limit
                    </label>
                    <input type="number" min="1" name="per_user_limit" value="{{ old('per_user_limit', $coupon->per_user_limit) }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;" required>
                </div>
            </div>

            <!-- Date Range -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Start Date (Optional)
                    </label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                        Expiry Date (Optional)
                    </label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
                </div>
            </div>

            <!-- Status Checkbox -->
            <div style="margin-bottom: 28px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 14px; font-weight: 600;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #00F0C8;">
                    Coupon is Active and Usable
                </label>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('admin.coupons.index') }}" style="background: rgba(255,255,255,0.06); border: 1px solid var(--border-color); color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">
                    Cancel
                </a>
                <button type="submit" style="background: linear-gradient(135deg, #00F0C8, #1ed6d0); border: none; color: #090D10; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">
                    Update Coupon
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
