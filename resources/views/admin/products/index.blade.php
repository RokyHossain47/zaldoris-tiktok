@extends('layouts.admin')

@section('title', 'Products Management - Super Admin')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Products Management</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Manage inventory, pricing, featured and trending products across live shopping and marketplace.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" style="background: linear-gradient(135deg, var(--pink-accent), var(--pink-hover)); color: #fff; text-decoration: none; padding: 12px 20px; border-radius: 10px; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4);">
        <i class="bi bi-plus-lg"></i> Add New Product
    </a>
</div>

<!-- FILTERS & SEARCH BAR -->
<div class="admin-table-container">
    <div class="table-header-bar">
        <div class="table-filter-pills">
            <a href="{{ route('admin.products.index') }}" class="{{ !request('filter') ? 'active' : '' }}">All Products</a>
            <a href="{{ route('admin.products.index', ['filter' => 'featured']) }}" class="{{ request('filter') === 'featured' ? 'active' : '' }}">🔥 Featured</a>
            <a href="{{ route('admin.products.index', ['filter' => 'trending']) }}" class="{{ request('filter') === 'trending' ? 'active' : '' }}">⚡ Trending</a>
            <a href="{{ route('admin.products.index', ['filter' => 'in_stock']) }}" class="{{ request('filter') === 'in_stock' ? 'active' : '' }}">📦 In Stock</a>
        </div>

        <form method="GET" action="{{ route('admin.products.index') }}" style="display: flex; gap: 10px; align-items: center;">
            <select name="category_id" onchange="this.form.submit()" style="background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 13px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <div class="search-box-wrap">
                <i class="bi bi-search" style="color: var(--text-muted); margin-left: 12px;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." style="background: transparent; border: none; padding: 8px 12px; color: #fff; font-size: 13px; outline: none; width: 180px;">
            </div>
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                <th style="padding: 16px 20px;">Product Info</th>
                <th style="padding: 16px 20px;">Category</th>
                <th style="padding: 16px 20px;">Seller</th>
                <th style="padding: 16px 20px;">Price</th>
                <th style="padding: 16px 20px;">Stock</th>
                <th style="padding: 16px 20px; text-align: center;">Featured</th>
                <th style="padding: 16px 20px; text-align: center;">Trending</th>
                <th style="padding: 16px 20px;">Status</th>
                <th style="padding: 16px 20px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
                <tr style="border-bottom: 1px solid var(--border-color); color: #fff;">
                    <td style="padding: 16px 20px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="{{ $p->primary_image }}" alt="{{ $p->title }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                            <div>
                                <div style="font-weight: 700; color: #fff; max-width: 220px; line-height: 1.3;">{{ $p->title }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">SKU: PRD-{{ $p->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px 20px;">
                        <span style="background: rgba(255, 255, 255, 0.05); padding: 4px 10px; border-radius: 6px; font-size: 12px;">
                            {{ $p->category_name }}
                        </span>
                    </td>
                    <td style="padding: 16px 20px; color: var(--text-muted); font-size: 13px;">
                        {{ $p->seller ? $p->seller->name : 'Platform Seller' }}
                    </td>
                    <td style="padding: 16px 20px;">
                        <div style="font-weight: 800; color: #fff;">{{ setting('currency_symbol', '$') }}{{ number_format($p->price, 2) }}</div>
                        @if($p->compare_price)
                            <div style="font-size: 11px; color: var(--text-muted); text-decoration: line-through;">{{ setting('currency_symbol', '$') }}{{ number_format($p->compare_price, 2) }}</div>
                        @endif
                    </td>
                    <td style="padding: 16px 20px;">
                        @if($p->stock <= 5)
                            <span style="color: var(--pink-accent); font-weight: 700;">{{ $p->stock }} left (Low)</span>
                        @else
                            <span style="color: var(--cyan-accent); font-weight: 700;">{{ $p->stock }} units</span>
                        @endif
                    </td>
                    <td style="padding: 16px 20px; text-align: center;">
                        <form action="{{ route('admin.products.toggle_featured', $p->id) }}" method="POST" style="margin: 0; display: inline;">
                            @csrf
                            <button type="submit" style="background: {{ $p->is_featured ? 'rgba(254, 44, 85, 0.2)' : 'rgba(255,255,255,0.05)' }}; border: 1px solid {{ $p->is_featured ? 'var(--pink-accent)' : 'var(--border-color)' }}; color: {{ $p->is_featured ? 'var(--pink-accent)' : 'var(--text-muted)' }}; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                {{ $p->is_featured ? '⭐ Featured' : 'Standard' }}
                            </button>
                        </form>
                    </td>
                    <td style="padding: 16px 20px; text-align: center;">
                        <form action="{{ route('admin.products.toggle_trending', $p->id) }}" method="POST" style="margin: 0; display: inline;">
                            @csrf
                            <button type="submit" style="background: {{ $p->is_trending ? 'rgba(37, 244, 238, 0.2)' : 'rgba(255,255,255,0.05)' }}; border: 1px solid {{ $p->is_trending ? 'var(--cyan-accent)' : 'var(--border-color)' }}; color: {{ $p->is_trending ? 'var(--cyan-accent)' : 'var(--text-muted)' }}; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                {{ $p->is_trending ? '🔥 Trending' : 'Standard' }}
                            </button>
                        </form>
                    </td>
                    <td style="padding: 16px 20px;">
                        <span class="status-pill {{ $p->status === 'active' ? 'status-active' : 'status-blocked' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td style="padding: 16px 20px; text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="{{ route('shop.product', $p->id) }}" target="_blank" class="action-btn" title="View in Shop">
                                <i class="bi bi-eye-fill" style="color: var(--text-muted);"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $p->id) }}" class="action-btn" title="Edit Product">
                                <i class="bi bi-pencil-fill" style="color: var(--cyan-accent);"></i>
                            </a>
                            <form action="{{ route('admin.products.delete', $p->id) }}" method="POST" onsubmit="return confirm('Delete this product permanently?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn" title="Delete Product">
                                    <i class="bi bi-trash-fill" style="color: var(--pink-accent);"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="padding: 30px; text-align: center; color: var(--text-muted);">
                        No products found matching filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="padding: 16px 20px;">
        {{ $products->links() }}
    </div>
</div>
@endsection
