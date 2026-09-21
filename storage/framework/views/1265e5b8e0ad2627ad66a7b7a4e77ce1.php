<?php $__env->startSection('title', 'Add New Product - Super Admin'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width: 960px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Add New Product</h1>
            <p style="font-size: 14px; color: var(--text-muted);">Configure static catalog products or live shopping streaming products with variants, images, and specifications.</p>
        </div>
        <a href="<?php echo e(route('admin.products.index')); ?>" style="color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600;">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <form action="<?php echo e(route('admin.products.store')); ?>" method="POST" enctype="multipart/form-data" id="productCreateForm">
            <?php echo csrf_field(); ?>

            <!-- PRODUCT TYPE SELECTION (CRITICAL DISTINCTION) -->
            <div style="background: var(--bg-card-inner); border: 2px solid rgba(0, 240, 200, 0.3); border-radius: 14px; padding: 20px; margin-bottom: 28px;">
                <label style="display: block; font-size: 15px; font-weight: 800; color: #fff; margin-bottom: 12px;">
                    Select Product Type <span style="color: var(--pink-accent);">*</span>
                </label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <label class="product-type-option" style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; cursor: pointer; display: flex; gap: 12px; align-items: flex-start; transition: all 0.2s ease;">
                        <input type="radio" name="is_live_product" value="0" <?php echo e(($defaultType ?? 'normal') === 'normal' ? 'checked' : ''); ?> onchange="toggleLiveStreamFields(false)" style="margin-top: 4px; accent-color: var(--cyan-accent);">
                        <div>
                            <div style="font-weight: 800; color: #fff; font-size: 14px; margin-bottom: 4px;">🛍️ Normal Product</div>
                            <div style="font-size: 12px; color: var(--text-muted); line-height: 1.4;">
                                Regular static e-commerce product. Displayed in <strong>Homepage Featured Products</strong> with static checkout (<strong>product-details.html</strong>).
                            </div>
                        </div>
                    </label>

                    <label class="product-type-option" style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; cursor: pointer; display: flex; gap: 12px; align-items: flex-start; transition: all 0.2s ease;">
                        <input type="radio" name="is_live_product" value="1" <?php echo e(($defaultType ?? 'normal') === 'live' ? 'checked' : ''); ?> onchange="toggleLiveStreamFields(true)" style="margin-top: 4px; accent-color: var(--pink-accent);">
                        <div>
                            <div style="font-weight: 800; color: #fff; font-size: 14px; margin-bottom: 4px;">🔴 Live Shopping Product</div>
                            <div style="font-size: 12px; color: var(--text-muted); line-height: 1.4;">
                                Broadcast video streaming product. Displayed in <strong>Featured Live Shopping</strong> with real-time video demo & chat (<strong>live-product-room.html</strong>).
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- LIVE STREAM ROOM ATTACHMENT (VISIBLE ONLY WHEN LIVE PRODUCT SELECTED) -->
            <div id="liveStreamRoomBox" style="display: <?php echo e(($defaultType ?? 'normal') === 'live' ? 'block' : 'none'); ?>; background: rgba(254, 44, 85, 0.08); border: 1px solid rgba(254, 44, 85, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <span style="display: inline-block; width: 10px; height: 10px; background: #FF3565; border-radius: 50%;"></span>
                    <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin: 0;">Live Stream Broadcast Settings</h3>
                </div>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;">
                    Admin / host will stream video live in this room while viewers interact with live chat and purchase this pinned product.
                </p>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 6px;">Attach to Existing Live Stream Room</label>
                    <select name="stream_id" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="">-- Automatically Create New Live Shopping Stream Room --</option>
                        <?php $__currentLoopData = $streams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st->id); ?>"><?php echo e($st->title); ?> (Host: <?php echo e($st->host ? $st->host->name : 'Admin'); ?> &bull; <?php echo e($st->viewer_count); ?> viewers)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <!-- BASIC DETAILS -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Product Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Titanium X-Pro Smartwatch Series 9" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Brand / Manufacturer</label>
                    <input type="text" name="brand" placeholder="e.g. Apple, Nike, Sony, Zaldoris" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Category *</label>
                    <select name="category_id" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="">Select Category</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Seller / Host Vendor *</label>
                    <select name="seller_id" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <?php $__currentLoopData = $sellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?> (<?php echo e(ucfirst($s->role)); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Price (<?php echo e(setting('currency_symbol', 'C$')); ?>) *</label>
                    <input type="number" step="0.01" name="price" required placeholder="299.00" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Compare At / Strike Price (<?php echo e(setting('currency_symbol', 'C$')); ?>)</label>
                    <input type="number" step="0.01" name="compare_price" placeholder="450.00" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Inventory Stock (Units) *</label>
                    <input type="number" name="stock" required value="25" min="0" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <!-- MULTIPLE IMAGES UPLOAD & GALLERY -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 14px; padding: 22px; margin-bottom: 24px;">
                <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin-bottom: 6px;">
                    <i class="bi bi-images text-info"></i> Product Images & Multi-Image Gallery
                </h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
                    Upload multiple high-resolution photos or provide image URLs. The first image will be the primary card thumbnail.
                </p>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 6px;">Upload Multiple Image Files</label>
                    <input type="file" name="image_files[]" multiple accept="image/*" style="width: 100%; background: #0c0d14; border: 1px dashed var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 13px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 6px;">Or Paste Image URLs (Comma Separated)</label>
                    <input type="text" name="images" placeholder="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800, https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 12px 16px; border-radius: 10px; font-size: 13px;">
                </div>
            </div>

            <!-- VARIANT FINISH: COLORS BUILDER -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 14px; padding: 22px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin: 0;">
                            <i class="bi bi-palette text-warning"></i> Color Variants
                        </h3>
                        <p style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0 0;">Add color options with visual color swatches for customer selection.</p>
                    </div>
                    <button type="button" onclick="addColorRow()" style="background: rgba(0, 240, 200, 0.12); color: var(--cyan-accent); border: 1px solid var(--cyan-accent); padding: 6px 14px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer;">
                        + Add Color
                    </button>
                </div>

                <!-- Color Presets -->
                <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px;">
                    <span style="font-size: 11px; color: var(--text-muted); align-self: center;">Quick Presets:</span>
                    <button type="button" class="preset-badge-btn" onclick="addPresetColor('Midnight Stealth', '#1E293B')">Midnight (#1E293B)</button>
                    <button type="button" class="preset-badge-btn" onclick="addPresetColor('Teal Titanium', '#0D2626')">Teal (#0D2626)</button>
                    <button type="button" class="preset-badge-btn" onclick="addPresetColor('Silver Steel', '#E2E8F0')">Silver (#E2E8F0)</button>
                    <button type="button" class="preset-badge-btn" onclick="addPresetColor('Deep Violet', '#7033FF')">Violet (#7033FF)</button>
                    <button type="button" class="preset-badge-btn" onclick="addPresetColor('Crimson Red', '#FF3565')">Red (#FF3565)</button>
                </div>

                <div id="colorVariantsList" style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Default Rows populated via JS -->
                </div>
            </div>

            <!-- VARIANT OPTIONS: SIZES / MODELS BUILDER -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 14px; padding: 22px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin: 0;">
                            <i class="bi bi-aspect-ratio text-info"></i> Sizes / Model Variants
                        </h3>
                        <p style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0 0;">Add sizes or models with optional price adjustments (e.g. +C$30 for Pro).</p>
                    </div>
                    <button type="button" onclick="addSizeRow()" style="background: rgba(0, 240, 200, 0.12); color: var(--cyan-accent); border: 1px solid var(--cyan-accent); padding: 6px 14px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer;">
                        + Add Size / Model
                    </button>
                </div>

                <div id="sizeVariantsList" style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Default Rows populated via JS -->
                </div>
            </div>

            <!-- TECHNICAL SPECIFICATIONS BUILDER -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 14px; padding: 22px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin: 0;">
                            <i class="bi bi-cpu text-success"></i> Technical Specifications
                        </h3>
                        <p style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0 0;">Add key-value specifications displayed on the product details page.</p>
                    </div>
                    <button type="button" onclick="addSpecRow()" style="background: rgba(0, 240, 200, 0.12); color: var(--cyan-accent); border: 1px solid var(--cyan-accent); padding: 6px 14px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer;">
                        + Add Specification
                    </button>
                </div>

                <div id="specificationsList" style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Default Spec Rows -->
                </div>
            </div>

            <!-- PROMOTIONAL FLAGS & BADGES -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <h3 style="font-size: 15px; font-weight: 800; color: #fff; margin-bottom: 12px;">Promotional Flags & Badges</h3>
                <div style="display: flex; gap: 32px; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 14px; font-weight: 600;">
                        <input type="checkbox" name="is_featured" value="1" style="width: 18px; height: 18px; accent-color: var(--pink-accent);">
                        <span>⭐ Mark as <strong>Featured Product</strong></span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 14px; font-weight: 600;">
                        <input type="checkbox" name="is_trending" value="1" style="width: 18px; height: 18px; accent-color: var(--cyan-accent);">
                        <span>🔥 Mark as <strong>Trending Product</strong></span>
                    </label>
                </div>
            </div>

            <!-- DESCRIPTION -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Product Description *</label>
                <textarea name="description" rows="4" placeholder="Detailed description of features, craftsmanship, materials, warranty, and endurance..." style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;"></textarea>
            </div>

            <!-- STATUS -->
            <div style="margin-bottom: 32px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Publish Status *</label>
                <select name="status" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                    <option value="active" selected>Active / Visible in Catalog</option>
                    <option value="draft">Draft (Hidden)</option>
                    <option value="inactive">Inactive / Archived</option>
                </select>
            </div>

            <div style="display: flex; gap: 16px;">
                <button type="submit" style="background: linear-gradient(135deg, var(--cyan-accent, #00F0C8), var(--cyan-hover, #00D8B4)); color: #090D10; border: none; padding: 14px 28px; border-radius: 10px; font-weight: 800; font-size: 15px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 16px rgba(0, 240, 200, 0.3);">
                    <i class="bi bi-check-circle-fill"></i> Save Product
                </button>
                <a href="<?php echo e(route('admin.products.index')); ?>" style="background: rgba(255, 255, 255, 0.05); color: #fff; text-decoration: none; padding: 14px 24px; border-radius: 10px; font-weight: 700; font-size: 15px;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    .preset-badge-btn {
        background: #0c0d14;
        border: 1px solid var(--border-color);
        color: #CBD5E1;
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .preset-badge-btn:hover {
        border-color: var(--cyan-accent);
        color: #fff;
    }
</style>

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

    function addPresetColor(name, code) {
        addColorRow(name, code);
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
                <span style="font-size: 12px; color: var(--text-muted);">+<?php echo e(setting('currency_symbol', 'C$')); ?></span>
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

    // Initialize with helpful default options on create
    document.addEventListener('DOMContentLoaded', () => {
        addColorRow('Midnight Stealth', '#1E293B');
        addColorRow('Teal Titanium', '#0D2626');
        addColorRow('Silver Steel', '#E2E8F0');
        addColorRow('Deep Violet', '#7033FF');

        addSizeRow('Standard', 0.00);
        addSizeRow('Large (+30)', 30.00);
        addSizeRow('Pro Ultra (+70)', 70.00);

        addSpecRow('Material', 'Aerospace Grade Titanium Alloy');
        addSpecRow('Battery Life', '36 Hours Continuous Use');
        addSpecRow('Water Resistance', '50m Water Resistant (IP68)');
        addSpecRow('Warranty', '2-Year Official Manufacturer Warranty');
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\zaldoris-tiktok\resources\views/admin/products/create.blade.php ENDPATH**/ ?>