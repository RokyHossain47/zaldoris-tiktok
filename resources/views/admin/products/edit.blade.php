@extends('layouts.admin')

@section('title', 'Edit Product #' . $product->id . ' - Super Admin')

@section('content')
<div style="max-width: 960px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Edit Product #{{ $product->id }}</h1>
            <p style="font-size: 14px; color: var(--text-muted);">Update product details, multi-image gallery, colors, sizes, specifications, and live stream connection.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('shop.product', $product->id) }}" target="_blank" style="background: rgba(0, 240, 200, 0.12); color: var(--cyan-accent); border: 1px solid var(--cyan-accent); text-decoration: none; padding: 10px 16px; border-radius: 10px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="bi bi-box-arrow-up-right"></i> View Details Page
            </a>
            <a href="{{ route('admin.products.index') }}" style="color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600; padding: 10px 12px;">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productEditForm">
            @csrf
            @method('PUT')

            <!-- PRODUCT TYPE SELECTION (CRITICAL DISTINCTION) -->
            <div style="background: var(--bg-card-inner); border: 2px solid {{ $product->is_live_product ? 'rgba(254, 44, 85, 0.4)' : 'rgba(0, 240, 200, 0.3)' }}; border-radius: 14px; padding: 20px; margin-bottom: 28px;">
                <label style="display: block; font-size: 15px; font-weight: 800; color: #fff; margin-bottom: 12px;">
                    Select Product Type <span style="color: var(--pink-accent);">*</span>
                </label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <label class="product-type-option" style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; cursor: pointer; display: flex; gap: 12px; align-items: flex-start; transition: all 0.2s ease;">
                        <input type="radio" name="is_live_product" value="0" {{ !$product->is_live_product ? 'checked' : '' }} onchange="toggleLiveStreamFields(false)" style="margin-top: 4px; accent-color: var(--cyan-accent);">
                        <div>
                            <div style="font-weight: 800; color: #fff; font-size: 14px; margin-bottom: 4px;">🛍️ Normal Product</div>
                            <div style="font-size: 12px; color: var(--text-muted); line-height: 1.4;">
                                Regular static e-commerce product. Displayed in <strong>Homepage Featured Products</strong> with static checkout (<strong>product-details.html</strong>).
                            </div>
                        </div>
                    </label>

                    <label class="product-type-option" style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; cursor: pointer; display: flex; gap: 12px; align-items: flex-start; transition: all 0.2s ease;">
                        <input type="radio" name="is_live_product" value="1" {{ $product->is_live_product ? 'checked' : '' }} onchange="toggleLiveStreamFields(true)" style="margin-top: 4px; accent-color: var(--pink-accent);">
                        <div>
                            <div style="font-weight: 800; color: #fff; font-size: 14px; margin-bottom: 4px;">🔴 Live Shopping Product</div>
                            <div style="font-size: 12px; color: var(--text-muted); line-height: 1.4;">
                                Broadcast video streaming product. Displayed in <strong>Featured Live Shopping</strong> with real-time video demo & chat (<strong>live-product-room.html</strong>).
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- LIVE STREAM ROOM ATTACHMENT -->
            <div id="liveStreamRoomBox" style="display: {{ $product->is_live_product ? 'block' : 'none' }}; background: rgba(254, 44, 85, 0.08); border: 1px solid rgba(254, 44, 85, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-block; width: 10px; height: 10px; background: #FF3565; border-radius: 50%;"></span>
                        <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin: 0;">Live Stream Broadcast Settings</h3>
                    </div>
                    @if($product->stream_id)
                        <a href="{{ route('streams.show', $product->stream_id) }}" target="_blank" style="background: var(--pink-accent); color: #fff; text-decoration: none; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="bi bi-play-circle-fill"></i> Open Live Stream Room
                        </a>
                    @endif
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 6px;">Attach to Live Stream Room</label>
                    <select name="stream_id" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="">-- Automatically Create/Maintain Stream Room --</option>
                        @foreach($streams as $st)
                            <option value="{{ $st->id }}" {{ $product->stream_id == $st->id ? 'selected' : '' }}>
                                {{ $st->title }} (Host: {{ $st->host ? $st->host->name : 'Admin' }} &bull; {{ $st->viewer_count }} viewers)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- BASIC DETAILS -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Product Title *</label>
                    <input type="text" name="title" required value="{{ old('title', $product->title) }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Brand / Manufacturer</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" placeholder="e.g. Apple, Nike, Sony, Zaldoris" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Category *</label>
                    <select name="category_id" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (old('category_id', $product->category_id) == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Seller / Host Vendor *</label>
                    <select name="seller_id" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        @foreach($sellers as $s)
                            <option value="{{ $s->id }}" {{ (old('seller_id', $product->seller_id) == $s->id) ? 'selected' : '' }}>{{ $s->name }} ({{ ucfirst($s->role) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Price ({{ setting('currency_symbol', 'C$') }}) *</label>
                    <input type="number" step="0.01" name="price" required value="{{ old('price', $product->price) }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Compare At Price ({{ setting('currency_symbol', 'C$') }})</label>
                    <input type="number" step="0.01" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Inventory Stock (Units) *</label>
                    <input type="number" name="stock" required value="{{ old('stock', $product->stock) }}" min="0" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <!-- MULTIPLE IMAGES UPLOAD & GALLERY -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 14px; padding: 22px; margin-bottom: 24px;">
                <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin-bottom: 6px;">
                    <i class="bi bi-images text-info"></i> Product Images & Multi-Image Gallery
                </h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
                    Manage existing gallery images or upload additional photos.
                </p>

                <!-- Current Images Preview -->
                @if(is_array($product->images) && count($product->images) > 0)
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px;">
                        @foreach($product->images as $img)
                            <div style="position: relative; width: 70px; height: 70px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color);">
                                <img src="{{ $img }}" alt="Gallery Thumb" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                @endif

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 6px;">Upload Additional Images</label>
                    <input type="file" name="image_files[]" multiple accept="image/*" style="width: 100%; background: #0c0d14; border: 1px dashed var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 13px;">
                </div>
            </div>

            <!-- VARIANT FINISH: COLORS BUILDER -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 14px; padding: 22px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin: 0;">
                            <i class="bi bi-palette text-warning"></i> Color Variants
                        </h3>
                        <p style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0 0;">Add or modify color finishes for the product.</p>
                    </div>
                    <button type="button" onclick="addColorRow()" style="background: rgba(0, 240, 200, 0.12); color: var(--cyan-accent); border: 1px solid var(--cyan-accent); padding: 6px 14px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer;">
                        + Add Color
                    </button>
                </div>

                <div id="colorVariantsList" style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Rows populated via JS -->
                </div>
            </div>

            <!-- VARIANT OPTIONS: SIZES / MODELS BUILDER -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 14px; padding: 22px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin: 0;">
                            <i class="bi bi-aspect-ratio text-info"></i> Sizes / Model Variants
                        </h3>
                        <p style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0 0;">Manage sizes or models with price modifiers.</p>
                    </div>
                    <button type="button" onclick="addSizeRow()" style="background: rgba(0, 240, 200, 0.12); color: var(--cyan-accent); border: 1px solid var(--cyan-accent); padding: 6px 14px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer;">
                        + Add Size / Model
                    </button>
                </div>

                <div id="sizeVariantsList" style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Rows populated via JS -->
                </div>
            </div>

            <!-- TECHNICAL SPECIFICATIONS BUILDER -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 14px; padding: 22px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin: 0;">
                            <i class="bi bi-cpu text-success"></i> Technical Specifications & Features
                        </h3>
                        <p style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0 0;">Add key-value specifications and feature highlights displayed on the product details page.</p>
                    </div>
                    <button type="button" onclick="addSpecRow()" style="background: rgba(0, 240, 200, 0.15); color: var(--cyan-accent); border: 1px solid var(--cyan-accent); padding: 8px 16px; border-radius: 8px; font-weight: 800; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-plus-circle-fill"></i> + Add Specification
                    </button>
                </div>                

                <div id="specificationsList" style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Spec Rows -->
                </div>
            </div>

            <!-- PROMOTIONAL FLAGS & BADGES -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin-bottom: 12px;">Promotional Flags & Badges</h3>
                <div style="display: flex; gap: 32px; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 14px; font-weight: 600;">
                        <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--pink-accent);">
                        <span>⭐ Mark as <strong>Featured Product</strong></span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 14px; font-weight: 600;">
                        <input type="checkbox" name="is_trending" value="1" {{ $product->is_trending ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--cyan-accent);">
                        <span>🔥 Mark as <strong>Trending Product</strong></span>
                    </label>
                </div>
            </div>

            <!-- DESCRIPTION -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Product Description *</label>
                <textarea name="description" rows="4" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- STATUS -->
            <div style="margin-bottom: 32px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Publish Status *</label>
                <select name="status" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                    <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active / Visible</option>
                    <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                    <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>Inactive / Archived</option>
                </select>
            </div>

            <div style="display: flex; gap: 16px;">
                <button type="submit" style="background: linear-gradient(135deg, var(--cyan-accent, #00F0C8), var(--cyan-hover, #00D8B4)); color: #090D10; border: none; padding: 14px 28px; border-radius: 10px; font-weight: 800; font-size: 15px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 16px rgba(0, 240, 200, 0.3);">
                    <i class="bi bi-check-circle-fill"></i> Update Product
                </button>
                <a href="{{ route('admin.products.index') }}" style="background: rgba(255, 255, 255, 0.05); color: #fff; text-decoration: none; padding: 14px 24px; border-radius: 10px; font-weight: 700; font-size: 15px;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleLiveStreamFields(isLive) {
        const box = document.getElementById('liveStreamRoomBox');
        if (box) {
            box.style.display = isLive ? 'block' : 'none';
        }
    }

    // Dynamic Colors Manager
    let colorIndex = 0;
    function addColorRow(name = '', code = '#1E293B') {
        const container = document.getElementById('colorVariantsList');
        const rowId = `color_row_${colorIndex++}`;
        const div = document.createElement('div');
        div.id = rowId;
        div.style = 'display: flex; gap: 12px; align-items: center; background: #0c0d14; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border-color);';
        div.innerHTML = `
            <input type="color" name="colors[${colorIndex}][code]" value="${code}" style="width: 40px; height: 36px; border: none; border-radius: 6px; cursor: pointer; background: transparent;">
            <input type="text" name="colors[${colorIndex}][name]" value="${name}" placeholder="Color Name (e.g. Midnight Stealth)" required style="flex: 1; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
            <button type="button" onclick="document.getElementById('${rowId}').remove()" style="background: rgba(254, 44, 85, 0.15); color: #FF5E85; border: 1px solid rgba(254, 44, 85, 0.3); width: 32px; height: 32px; border-radius: 6px; cursor: pointer;">
                <i class="bi bi-trash"></i>
            </button>
        `;
        container.appendChild(div);
    }

    // Dynamic Sizes Manager
    let sizeIndex = 0;
    function addSizeRow(name = '', priceMod = 0) {
        const container = document.getElementById('sizeVariantsList');
        const rowId = `size_row_${sizeIndex++}`;
        const div = document.createElement('div');
        div.id = rowId;
        div.style = 'display: flex; gap: 12px; align-items: center; background: #0c0d14; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border-color);';
        div.innerHTML = `
            <input type="text" name="sizes[${sizeIndex}][name]" value="${name}" placeholder="Size / Model Name (e.g. Standard / Large / Pro Ultra)" required style="flex: 2; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
            <div style="flex: 1; display: flex; align-items: center; gap: 4px;">
                <span style="font-size: 12px; color: var(--text-muted);">+{{ setting('currency_symbol', 'C$') }}</span>
                <input type="number" step="0.01" name="sizes[${sizeIndex}][price_modifier]" value="${priceMod}" placeholder="0.00" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
            </div>
            <button type="button" onclick="document.getElementById('${rowId}').remove()" style="background: rgba(254, 44, 85, 0.15); color: #FF5E85; border: 1px solid rgba(254, 44, 85, 0.3); width: 32px; height: 32px; border-radius: 6px; cursor: pointer;">
                <i class="bi bi-trash"></i>
            </button>
        `;
        container.appendChild(div);
    }

    // Dynamic Specs Manager
    let specIndex = 0;
    function addSpecRow(key = '', val = '') {
        const container = document.getElementById('specificationsList');
        const rowId = `spec_row_${specIndex++}`;
        const div = document.createElement('div');
        div.id = rowId;
        div.style = 'display: flex; gap: 12px; align-items: center; background: #0c0d14; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border-color);';
        div.innerHTML = `
            <input type="text" name="specifications[${specIndex}][key]" value="${key}" placeholder="Feature / Spec Name (e.g. Material)" required style="flex: 1; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
            <input type="text" name="specifications[${specIndex}][val]" value="${val}" placeholder="Value (e.g. Aerospace Titanium)" required style="flex: 2; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
            <button type="button" onclick="document.getElementById('${rowId}').remove()" style="background: rgba(254, 44, 85, 0.15); color: #FF5E85; border: 1px solid rgba(254, 44, 85, 0.3); width: 32px; height: 32px; border-radius: 6px; cursor: pointer;">
                <i class="bi bi-trash"></i>
            </button>
        `;
        container.appendChild(div);
    }

    // Pre-populate with existing product data on load
    document.addEventListener('DOMContentLoaded', () => {
        const existingColors = @json($product->colors_list);
        if (existingColors && existingColors.length > 0) {
            existingColors.forEach(c => addColorRow(c.name, c.code));
        } else {
            addColorRow('Midnight Stealth', '#1E293B');
            addColorRow('Teal Titanium', '#0D2626');
        }

        const existingSizes = @json($product->sizes_list);
        if (existingSizes && existingSizes.length > 0) {
            existingSizes.forEach(s => addSizeRow(s.name, s.price_modifier));
        } else {
            addSizeRow('Standard', 0.00);
            addSizeRow('Large (+30)', 30.00);
        }

        const existingSpecs = @json($product->specs_list);
        if (existingSpecs && Object.keys(existingSpecs).length > 0) {
            for (const [k, v] of Object.entries(existingSpecs)) {
                addSpecRow(k, v);
            }
        } else {
            addSpecRow('Material', 'Aerospace Grade Titanium Alloy');
            addSpecRow('Warranty', '2-Year Official Warranty');
        }
    });
</script>
@endsection
