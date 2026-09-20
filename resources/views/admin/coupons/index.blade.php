@extends('layouts.admin')

@section('title', 'Coupon & Discount Management')

@section('content')
<div class="admin-content-inner">
    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 4px;">Coupons & Promo Codes</h1>
            <p style="color: var(--text-muted); font-size: 13px; margin: 0;">Create and manage promotional discount coupons for checkout orders.</p>
        </div>
        <div>
            <a href="{{ route('admin.coupons.create') }}" class="btn-create" style="background: linear-gradient(135deg, #00F0C8, #1ed6d0); color: #090D10; font-weight: 700; padding: 10px 20px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 14px;">
                <i class="bi bi-plus-circle-fill"></i> Create New Coupon
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        <div class="stat-card">
            <div>
                <div class="stat-label">TOTAL COUPONS</div>
                <div class="stat-val">{{ number_format($totalCoupons) }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(0, 240, 200, 0.1); color: #00F0C8;">
                <i class="bi bi-ticket-perforated"></i>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">ACTIVE COUPONS</div>
                <div class="stat-val" style="color: #00F0C8;">{{ number_format($activeCoupons) }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(0, 240, 200, 0.1); color: #00F0C8;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">TOTAL REDEMPTIONS</div>
                <div class="stat-val">{{ number_format($totalUses) }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #3B82F6;">
                <i class="bi bi-cart-check-fill"></i>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">DISCOUNT SAVINGS GIVEN</div>
                <div class="stat-val" style="color: #FE2C55;">${{ number_format($totalDiscountGiven, 2) }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(254, 44, 85, 0.1); color: #FE2C55;">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
    </div>

    <!-- Coupons Table Card -->
    <div class="admin-table-container">
        <!-- Filter & Search Bar -->
        <div class="table-header-bar">
            <div class="table-filter-pills">
                <a href="{{ route('admin.coupons.index', ['status' => 'all', 'search' => $search]) }}" class="{{ $status === 'all' ? 'active' : '' }}" style="{{ $status === 'all' ? 'background: rgba(255,255,255,0.1); color: #fff;' : '' }}">All ({{ $totalCoupons }})</a>
                <a href="{{ route('admin.coupons.index', ['status' => 'active', 'search' => $search]) }}" class="{{ $status === 'active' ? 'active' : '' }}" style="{{ $status === 'active' ? 'background: rgba(0,240,200,0.15); color: #00F0C8;' : '' }}">Active ({{ $activeCoupons }})</a>
                <a href="{{ route('admin.coupons.index', ['status' => 'inactive', 'search' => $search]) }}" class="{{ $status === 'inactive' ? 'active' : '' }}" style="{{ $status === 'inactive' ? 'background: rgba(254,44,85,0.15); color: #FE2C55;' : '' }}">Inactive ({{ $totalCoupons - $activeCoupons }})</a>
            </div>

            <form action="{{ route('admin.coupons.index') }}" method="GET" style="display: flex; gap: 8px;">
                <input type="hidden" name="status" value="{{ $status }}">
                <div style="position: relative;">
                    <i class="bi bi-search" style="position: absolute; left: 12px; top: 10px; color: var(--text-muted); font-size: 13px;"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search coupon code or name..." style="background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 8px 12px 8px 34px; border-radius: 8px; font-size: 13px; width: 260px;">
                </div>
                <button type="submit" style="background: rgba(255,255,255,0.08); border: 1px solid var(--border-color); color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 13px; cursor: pointer;">Filter</button>
            </form>
        </div>

        <!-- Table Content -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); background: rgba(255,255,255,0.02); color: var(--text-muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 18px;">Code & Name</th>
                        <th style="padding: 14px 18px;">Discount Value</th>
                        <th style="padding: 14px 18px;">Min Order</th>
                        <th style="padding: 14px 18px;">Usage Limit</th>
                        <th style="padding: 14px 18px;">Validity Period</th>
                        <th style="padding: 14px 18px;">Status</th>
                        <th style="padding: 14px 18px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $c)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.15s ease;">
                        <td style="padding: 16px 18px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="background: rgba(0, 240, 200, 0.15); border: 1px dashed #00F0C8; color: #00F0C8; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-family: monospace; font-size: 14px; letter-spacing: 1px;">
                                    {{ $c->code }}
                                </span>
                                <div>
                                    <div style="font-weight: 700; color: #fff;">{{ $c->name ?: 'Promotional Coupon' }}</div>
                                    @if($c->description)
                                        <div style="color: var(--text-muted); font-size: 12px; max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $c->description }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px 18px;">
                            @if($c->type === 'percentage')
                                <span style="font-weight: 800; color: #00F0C8; font-size: 14px;">{{ (float)$c->value }}% OFF</span>
                                @if($c->max_discount_amount)
                                    <div style="font-size: 11px; color: var(--text-muted);">Max ${{ number_format($c->max_discount_amount, 2) }}</div>
                                @endif
                            @else
                                <span style="font-weight: 800; color: #FE2C55; font-size: 14px;">${{ number_format($c->value, 2) }} OFF</span>
                                <div style="font-size: 11px; color: var(--text-muted);">Fixed discount</div>
                            @endif
                        </td>
                        <td style="padding: 16px 18px; color: #fff;">
                            @if($c->min_order_amount > 0)
                                ${{ number_format($c->min_order_amount, 2) }}
                            @else
                                <span style="color: var(--text-muted);">No minimum</span>
                            @endif
                        </td>
                        <td style="padding: 16px 18px;">
                            <div style="font-weight: 600; color: #fff;">
                                {{ $c->used_count }} / {{ $c->usage_limit ? $c->usage_limit : '∞' }}
                            </div>
                            <div style="font-size: 11px; color: var(--text-muted);">
                                {{ $c->per_user_limit }} per user
                            </div>
                        </td>
                        <td style="padding: 16px 18px; font-size: 12px; color: var(--text-muted);">
                            @if($c->starts_at || $c->expires_at)
                                <div><i class="bi bi-calendar-event"></i> {{ $c->starts_at ? $c->starts_at->format('M d, Y') : 'Immediate' }}</div>
                                <div style="{{ $c->expires_at && $c->expires_at->isPast() ? 'color: #FE2C55;' : '' }}">
                                    to {{ $c->expires_at ? $c->expires_at->format('M d, Y') : 'No Expiry' }}
                                </div>
                            @else
                                <span style="color: #10B981;">Permanent</span>
                            @endif
                        </td>
                        <td style="padding: 16px 18px;">
                            @if($c->is_active)
                                @if($c->expires_at && $c->expires_at->isPast())
                                    <span style="background: rgba(239, 68, 68, 0.15); color: #EF4444; border: 1px solid #EF4444; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">Expired</span>
                                @elseif($c->usage_limit && $c->used_count >= $c->usage_limit)
                                    <span style="background: rgba(245, 158, 11, 0.15); color: #F59E0B; border: 1px solid #F59E0B; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">Depleted</span>
                                @else
                                    <span style="background: rgba(0, 240, 200, 0.15); color: #00F0C8; border: 1px solid #00F0C8; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">Active</span>
                                @endif
                            @else
                                <span style="background: rgba(148, 163, 184, 0.15); color: #94A3B8; border: 1px solid #94A3B8; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">Disabled</span>
                            @endif
                        </td>
                        <td style="padding: 16px 18px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                <!-- Toggle Active Status -->
                                <form action="{{ route('admin.coupons.toggle', $c->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" title="{{ $c->is_active ? 'Deactivate' : 'Activate' }}" style="background: rgba(255,255,255,0.06); border: 1px solid var(--border-color); color: {{ $c->is_active ? '#00F0C8' : '#94A3B8' }}; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>

                                <!-- Edit -->
                                <a href="{{ route('admin.coupons.edit', $c->id) }}" title="Edit Coupon" style="background: rgba(59, 130, 246, 0.1); border: 1px solid #3B82F6; color: #3B82F6; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('admin.coupons.delete', $c->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete coupon {{ $c->code }}?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Coupon" style="background: rgba(254, 44, 85, 0.1); border: 1px solid #FE2C55; color: #FE2C55; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            <i class="bi bi-ticket-perforated" style="font-size: 36px; display: block; margin-bottom: 12px; color: rgba(255,255,255,0.2);"></i>
                            No coupons found. Create your first promo code today!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            {{ $coupons->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
