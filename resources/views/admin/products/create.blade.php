@extends('layouts.admin')

@section('title', 'Add New Product - Super Admin')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Add New Product</h1>
            <p style="font-size: 14px; color: var(--text-muted);">Create a new marketplace & live shopping catalog item.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" style="color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600;">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- BASIC DETAILS -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Product Title *</label>
                <input type="text" name="title" required placeholder="e.g. Nike Air Jordan 1 Retro High OG Chicago" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Category *</label>
                    <select name="category_id" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Seller / Vendor *</label>
                    <select name="seller_id" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        @foreach($sellers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ ucfirst($s->role) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Price ($) *</label>
                    <input type="number" step="0.01" name="price" required placeholder="0.00" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Compare At Price ($)</label>
                    <input type="number" step="0.01" name="compare_price" placeholder="Original price" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Inventory Stock (Units) *</label>
                    <input type="number" name="stock" required value="10" min="0" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <!-- FEATURED & TRENDING FLAGS -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin-bottom: 12px;">Promotional Flags & Badges</h3>
                <div style="display: flex; gap: 32px; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 14px; font-weight: 600;">
                        <input type="checkbox" name="is_featured" value="1" style="width: 18px; height: 18px; accent-color: var(--pink-accent);">
                        <span>⭐ Mark as <strong>Featured Product</strong> (Shown in Homepage Featured Section)</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 14px; font-weight: 600;">
                        <input type="checkbox" name="is_trending" value="1" style="width: 18px; height: 18px; accent-color: var(--cyan-accent);">
                        <span>🔥 Mark as <strong>Trending Product</strong> (Shown in Trending Drops)</span>
                    </label>
                </div>
            </div>

            <!-- IMAGE UPLOAD -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Upload Primary Product Image</label>
                <input type="file" name="image_file" accept="image/*" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 16px; border-radius: 10px; font-size: 14px;">
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">Or enter comma-separated image URLs below:</div>
                <input type="text" name="images" placeholder="https://image1.jpg, https://image2.jpg" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 16px; border-radius: 10px; font-size: 14px; margin-top: 8px;">
            </div>

            <!-- DESCRIPTION -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Product Description</label>
                <textarea name="description" rows="4" placeholder="Detailed product specifications, condition, authenticity details..." style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;"></textarea>
            </div>

            <!-- STATUS -->
            <div style="margin-bottom: 28px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Publication Status</label>
                <select name="status" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                    <option value="active">Active & Available on Marketplace</option>
                    <option value="inactive">Inactive / Hidden</option>
                    <option value="draft">Draft</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 14px;">
                <a href="{{ route('admin.products.index') }}" style="background: rgba(255,255,255,0.08); color: #fff; text-decoration: none; padding: 14px 24px; border-radius: 10px; font-weight: 600; font-size: 14px;">Cancel</a>
                <button type="submit" style="background: linear-gradient(135deg, var(--pink-accent), var(--pink-hover)); color: #fff; border: none; padding: 14px 32px; border-radius: 10px; font-weight: 800; font-size: 14px; cursor: pointer; box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4);">
                    Save & Publish Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
