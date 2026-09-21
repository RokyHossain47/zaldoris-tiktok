<?php $__env->startSection('title', 'Products Management - Super Admin'); ?>

<?php $__env->startSection('content'); ?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Products Management</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Manage inventory, pricing, featured normal products (static purchase), and live streaming shopping items.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="<?php echo e(route('admin.products.create', ['type' => 'normal'])); ?>" style="background: rgba(0, 240, 200, 0.15); border: 1px solid var(--cyan-accent, #00F0C8); color: var(--cyan-accent, #00F0C8); text-decoration: none; padding: 12px 18px; border-radius: 10px; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease;">
            <i class="bi bi-bag-plus"></i> + Add Normal Product
        </a>
        <a href="<?php echo e(route('admin.products.create', ['type' => 'live'])); ?>" style="background: linear-gradient(135deg, var(--pink-accent, #FE2C55), #FF0055); color: #fff; text-decoration: none; padding: 12px 18px; border-radius: 10px; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4); transition: all 0.2s ease;">
            <i class="bi bi-broadcast"></i> 🔴 + Add Live Shopping Product
        </a>
    </div>
</div>

<!-- FILTERS & SEARCH BAR -->
<div class="admin-table-container">
    <div class="table-header-bar">
        <div class="table-filter-pills">
            <a href="<?php echo e(route('admin.products.index')); ?>" class="<?php echo e(!request('filter') ? 'active' : ''); ?>">All Products</a>
            <a href="<?php echo e(route('admin.products.index', ['filter' => 'normal'])); ?>" class="<?php echo e(request('filter') === 'normal' ? 'active' : ''); ?>">🛍️ Normal Products</a>
            <a href="<?php echo e(route('admin.products.index', ['filter' => 'live'])); ?>" class="<?php echo e(request('filter') === 'live' ? 'active' : ''); ?>">🔴 Live Products</a>
            <a href="<?php echo e(route('admin.products.index', ['filter' => 'featured'])); ?>" class="<?php echo e(request('filter') === 'featured' ? 'active' : ''); ?>">🔥 Featured</a>
            <a href="<?php echo e(route('admin.products.index', ['filter' => 'trending'])); ?>" class="<?php echo e(request('filter') === 'trending' ? 'active' : ''); ?>">⚡ Trending</a>
            <a href="<?php echo e(route('admin.products.index', ['filter' => 'in_stock'])); ?>" class="<?php echo e(request('filter') === 'in_stock' ? 'active' : ''); ?>">📦 In Stock</a>
        </div>

        <form method="GET" action="<?php echo e(route('admin.products.index')); ?>" style="display: flex; gap: 10px; align-items: center;">
            <select name="category_id" onchange="this.form.submit()" style="background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 13px;">
                <option value="">All Categories</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <div class="search-box-wrap">
                <i class="bi bi-search" style="color: var(--text-muted); margin-left: 12px;"></i>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search products..." style="background: transparent; border: none; padding: 8px 12px; color: #fff; font-size: 13px; outline: none; width: 180px;">
            </div>
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                <th style="padding: 16px 20px;">Product Info</th>
                <th style="padding: 16px 20px;">Type / Target Room</th>
                <th style="padding: 16px 20px;">Category</th>
                <th style="padding: 16px 20px;">Price</th>
                <th style="padding: 16px 20px;">Stock</th>
                <th style="padding: 16px 20px; text-align: center;">Featured</th>
                <th style="padding: 16px 20px; text-align: center;">Trending</th>
                <th style="padding: 16px 20px;">Status</th>
                <th style="padding: 16px 20px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr style="border-bottom: 1px solid var(--border-color); color: #fff;">
                    <td style="padding: 16px 20px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="<?php echo e($p->primary_image); ?>" alt="<?php echo e($p->title); ?>" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                            <div>
                                <div style="font-weight: 700; color: #fff; max-width: 220px; line-height: 1.3;"><?php echo e($p->title); ?></div>
                                <div style="font-size: 11px; color: var(--text-muted);">SKU: PRD-<?php echo e($p->id); ?> &bull; Seller: <?php echo e($p->seller ? $p->seller->name : 'Admin'); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px 20px;">
                        <?php if($p->is_live_product): ?>
                            <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                <span style="background: rgba(254, 44, 85, 0.18); border: 1px solid rgba(254, 44, 85, 0.4); color: var(--pink-accent, #FE2C55); font-weight: 700; font-size: 11px; padding: 2px 8px; border-radius: 4px;">
                                    🔴 Live Shopping Product
                                </span>
                                <a href="<?php echo e($p->stream_id ? route('streams.show', $p->stream_id) : url('/live-product-room/' . $p->id)); ?>" target="_blank" style="font-size: 11px; color: var(--cyan-accent, #00F0C8); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="bi bi-play-circle-fill"></i> Go Live / Room &rarr;
                                </a>
                            </div>
                        <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                <span style="background: rgba(0, 240, 200, 0.1); border: 1px solid rgba(0, 240, 200, 0.3); color: var(--cyan-accent, #00F0C8); font-weight: 700; font-size: 11px; padding: 2px 8px; border-radius: 4px;">
                                    🛍️ Normal Product
                                </span>
                                <a href="<?php echo e(route('shop.product', $p->id)); ?>" target="_blank" style="font-size: 11px; color: var(--text-muted); text-decoration: none;">
                                    Static Details &rarr;
                                </a>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 16px 20px;">
                        <span style="background: rgba(255, 255, 255, 0.05); padding: 4px 10px; border-radius: 6px; font-size: 12px;">
                            <?php echo e($p->category_name); ?>

                        </span>
                    </td>
                    <td style="padding: 16px 20px;">
                        <div style="font-weight: 800; color: #fff;"><?php echo e(setting('currency_symbol', '$')); ?><?php echo e(number_format($p->price, 2)); ?></div>
                        <?php if($p->compare_price): ?>
                            <div style="font-size: 11px; color: var(--text-muted); text-decoration: line-through;"><?php echo e(setting('currency_symbol', '$')); ?><?php echo e(number_format($p->compare_price, 2)); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 16px 20px;">
                        <?php if($p->stock <= 5): ?>
                            <span style="color: var(--pink-accent); font-weight: 700;"><?php echo e($p->stock); ?> left (Low)</span>
                        <?php else: ?>
                            <span style="color: var(--cyan-accent); font-weight: 700;"><?php echo e($p->stock); ?> units</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 16px 20px; text-align: center;">
                        <form action="<?php echo e(route('admin.products.toggle_featured', $p->id)); ?>" method="POST" style="margin: 0; display: inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" style="background: <?php echo e($p->is_featured ? 'rgba(254, 44, 85, 0.2)' : 'rgba(255,255,255,0.05)'); ?>; border: 1px solid <?php echo e($p->is_featured ? 'var(--pink-accent)' : 'var(--border-color)'); ?>; color: <?php echo e($p->is_featured ? 'var(--pink-accent)' : 'var(--text-muted)'); ?>; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                <?php echo e($p->is_featured ? '⭐ Featured' : 'Standard'); ?>

                            </button>
                        </form>
                    </td>
                    <td style="padding: 16px 20px; text-align: center;">
                        <form action="<?php echo e(route('admin.products.toggle_trending', $p->id)); ?>" method="POST" style="margin: 0; display: inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" style="background: <?php echo e($p->is_trending ? 'rgba(37, 244, 238, 0.2)' : 'rgba(255,255,255,0.05)'); ?>; border: 1px solid <?php echo e($p->is_trending ? 'var(--cyan-accent)' : 'var(--border-color)'); ?>; color: <?php echo e($p->is_trending ? 'var(--cyan-accent)' : 'var(--text-muted)'); ?>; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                <?php echo e($p->is_trending ? '🔥 Trending' : 'Standard'); ?>

                            </button>
                        </form>
                    </td>
                    <td style="padding: 16px 20px;">
                        <span class="status-pill <?php echo e($p->status === 'active' ? 'status-active' : 'status-blocked'); ?>">
                            <?php echo e(ucfirst($p->status)); ?>

                        </span>
                    </td>
                    <td style="padding: 16px 20px; text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="<?php echo e($p->is_live_product ? ($p->stream_id ? route('streams.show', $p->stream_id) : url('/live-product-room/' . $p->id)) : route('shop.product', $p->id)); ?>" target="_blank" class="action-btn" title="Preview Product Details">
                                <i class="bi bi-eye-fill" style="color: var(--text-muted);"></i>
                            </a>
                            <a href="<?php echo e(route('admin.products.edit', $p->id)); ?>" class="action-btn" title="Edit Product">
                                <i class="bi bi-pencil-fill" style="color: var(--cyan-accent);"></i>
                            </a>
                            <form action="<?php echo e(route('admin.products.delete', $p->id)); ?>" method="POST" onsubmit="return confirm('Delete this product permanently?');" style="margin: 0;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="action-btn" title="Delete Product">
                                    <i class="bi bi-trash-fill" style="color: var(--pink-accent);"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" style="padding: 32px; text-align: center; color: var(--text-muted);">
                        No products found matching your filters.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
        <?php echo e($products->appends(request()->query())->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\zaldoris-tiktok\resources\views/admin/products/index.blade.php ENDPATH**/ ?>