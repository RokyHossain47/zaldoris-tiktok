<?php $__env->startSection('title', 'Auctions Management - Super Admin'); ?>

<?php $__env->startSection('content'); ?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Live Auctions Management</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Manage real-time live auctions, reserve prices, bidding increments, and active timers.</p>
    </div>
    <a href="<?php echo e(route('admin.auctions.create')); ?>" style="background: linear-gradient(135deg, var(--cyan-accent), #00d2c7); color: #000; text-decoration: none; padding: 12px 20px; border-radius: 10px; font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(37, 244, 238, 0.4);">
        <i class="bi bi-plus-lg"></i> Create New Auction
    </a>
</div>

<!-- STATS CARDS -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 18px 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(37, 244, 238, 0.15); color: var(--cyan-accent); display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="bi bi-hammer"></i>
        </div>
        <div>
            <div style="font-size: 22px; font-weight: 800; color: #fff;"><?php echo e($totalAuctions); ?></div>
            <div style="font-size: 12px; color: var(--text-muted);">Total Auctions</div>
        </div>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 18px 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(254, 44, 85, 0.15); color: var(--pink-accent); display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="bi bi-broadcast"></i>
        </div>
        <div>
            <div style="font-size: 22px; font-weight: 800; color: #fff;"><?php echo e($activeAuctions); ?></div>
            <div style="font-size: 12px; color: var(--text-muted);">Active Live Auctions</div>
        </div>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 18px 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(255, 193, 7, 0.15); color: #ffc107; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="bi bi-clock-history"></i>
        </div>
        <div>
            <div style="font-size: 22px; font-weight: 800; color: #fff;"><?php echo e($upcomingAuctions); ?></div>
            <div style="font-size: 12px; color: var(--text-muted);">Upcoming Drops</div>
        </div>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 18px 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(40, 167, 69, 0.15); color: #28a745; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
            <div style="font-size: 22px; font-weight: 800; color: #fff;"><?php echo e($soldAuctions); ?></div>
            <div style="font-size: 12px; color: var(--text-muted);">Sold Auctions</div>
        </div>
    </div>
</div>

<!-- FILTERS & SEARCH BAR -->
<div class="admin-table-container">
    <div class="table-header-bar">
        <div class="table-filter-pills">
            <a href="<?php echo e(route('admin.auctions.index')); ?>" class="<?php echo e($filter === 'all' ? 'active' : ''); ?>">All Auctions</a>
            <a href="<?php echo e(route('admin.auctions.index', ['status' => 'active'])); ?>" class="<?php echo e($filter === 'active' ? 'active' : ''); ?>">🔴 Active</a>
            <a href="<?php echo e(route('admin.auctions.index', ['status' => 'upcoming'])); ?>" class="<?php echo e($filter === 'upcoming' ? 'active' : ''); ?>">⏳ Upcoming</a>
            <a href="<?php echo e(route('admin.auctions.index', ['status' => 'sold'])); ?>" class="<?php echo e($filter === 'sold' ? 'active' : ''); ?>">✅ Sold</a>
            <a href="<?php echo e(route('admin.auctions.index', ['status' => 'failed'])); ?>" class="<?php echo e($filter === 'failed' ? 'active' : ''); ?>">❌ Failed/Expired</a>
            <a href="<?php echo e(route('admin.auctions.index', ['status' => 'cancelled'])); ?>" class="<?php echo e($filter === 'cancelled' ? 'active' : ''); ?>">⛔ Cancelled</a>
        </div>

        <form method="GET" action="<?php echo e(route('admin.auctions.index')); ?>" style="display: flex; gap: 10px; align-items: center;">
            <?php if(request('status')): ?>
                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
            <?php endif; ?>
            <div class="search-box-wrap">
                <i class="bi bi-search" style="color: var(--text-muted); margin-left: 12px;"></i>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search auctions..." style="background: transparent; border: none; padding: 8px 12px; color: #fff; font-size: 13px; outline: none; width: 200px;">
            </div>
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                <th style="padding: 16px 20px;">Auction Item</th>
                <th style="padding: 16px 20px;">Seller</th>
                <th style="padding: 16px 20px;">Starting / Current</th>
                <th style="padding: 16px 20px;">Bids Placed</th>
                <th style="padding: 16px 20px;">Ends At / Timer</th>
                <th style="padding: 16px 20px; text-align: center;">Blur</th>
                <th style="padding: 16px 20px;">Status</th>
                <th style="padding: 16px 20px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $auctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr style="border-bottom: 1px solid var(--border-color); color: #fff;">
                    <td style="padding: 16px 20px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="<?php echo e($a->image_url ?: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200'); ?>" alt="<?php echo e($a->title); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                            <div>
                                <div style="font-weight: 700; color: #fff; max-width: 240px; line-height: 1.3;">
                                    <a href="<?php echo e(route('auctions.show', $a->id)); ?>" target="_blank" style="color: #fff; text-decoration: none;">
                                        <?php echo e($a->title); ?> <i class="bi bi-box-arrow-up-right" style="font-size: 10px; color: var(--cyan-accent);"></i>
                                    </a>
                                </div>
                                <div style="font-size: 11px; color: var(--text-muted);">LOT #<?php echo e(str_pad($a->id, 5, '0', STR_PAD_LEFT)); ?> • Step: <?php echo e(setting('currency_symbol', '$')); ?><?php echo e(number_format($a->min_bid_step, 2)); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px 20px;">
                        <div style="font-weight: 600; color: #fff;"><?php echo e($a->seller ? $a->seller->name : 'Administrator'); ?></div>
                        <div style="font-size: 11px; color: var(--text-muted);"><?php echo e('@' . ($a->seller ? $a->seller->username : 'admin')); ?></div>
                    </td>
                    <td style="padding: 16px 20px;">
                        <div style="font-size: 11px; color: var(--text-muted);">Start: <?php echo e(setting('currency_symbol', '$')); ?><?php echo e(number_format($a->starting_bid, 2)); ?></div>
                        <div style="font-weight: 800; color: var(--cyan-accent); font-size: 15px;"><?php echo e(setting('currency_symbol', '$')); ?><?php echo e(number_format($a->current_bid, 2)); ?></div>
                        <?php if($a->reserve_price): ?>
                            <div style="font-size: 10px; color: #ffc107;">Reserve: <?php echo e(setting('currency_symbol', '$')); ?><?php echo e(number_format($a->reserve_price, 2)); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 16px 20px;">
                        <span style="background: rgba(37, 244, 238, 0.1); color: var(--cyan-accent); padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                            <?php echo e($a->bids->count()); ?> Bids
                        </span>
                        <?php if($a->highestBidder): ?>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Top: <?php echo e($a->highestBidder->name); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 16px 20px;">
                        <?php if($a->ends_at): ?>
                            <div style="font-size: 12px; color: #fff; font-weight: 600;"><?php echo e($a->ends_at->format('M d, Y h:i A')); ?></div>
                            <?php if($a->status === 'active'): ?>
                                <?php if($a->ends_at->isPast()): ?>
                                    <div style="font-size: 11px; color: var(--pink-accent); font-weight: 700;">Ended</div>
                                <?php else: ?>
                                    <div style="font-size: 11px; color: var(--cyan-accent); font-weight: 700;">Ends in <?php echo e($a->ends_at->diffForHumans(['parts' => 2, 'short' => true])); ?></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color: var(--text-muted); font-size: 12px;">Not scheduled</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 16px 20px; text-align: center;">
                        <form action="<?php echo e(route('admin.auctions.toggle_blur', $a->id)); ?>" method="POST" style="display: inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" style="background: <?php echo e($a->is_blurred ? 'rgba(254, 44, 85, 0.2)' : 'rgba(255,255,255,0.05)'); ?>; border: 1px solid <?php echo e($a->is_blurred ? 'var(--pink-accent)' : 'var(--border-color)'); ?>; color: <?php echo e($a->is_blurred ? 'var(--pink-accent)' : 'var(--text-muted)'); ?>; padding: 4px 8px; border-radius: 6px; font-size: 11px; cursor: pointer;" title="Toggle listing blur after expiration">
                                <?php echo e($a->is_blurred ? '🔒 Blurred' : '👁️ Visible'); ?>

                            </button>
                        </form>
                    </td>
                    <td style="padding: 16px 20px;">
                        <?php if($a->status === 'active'): ?>
                            <span style="background: rgba(37, 244, 238, 0.15); color: var(--cyan-accent); border: 1px solid rgba(37, 244, 238, 0.3); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                                Active Live
                            </span>
                        <?php elseif($a->status === 'upcoming'): ?>
                            <span style="background: rgba(255, 193, 7, 0.15); color: #ffc107; border: 1px solid rgba(255, 193, 7, 0.3); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                                Upcoming
                            </span>
                        <?php elseif($a->status === 'sold'): ?>
                            <span style="background: rgba(40, 167, 69, 0.15); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.3); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                                Sold
                            </span>
                        <?php else: ?>
                            <span style="background: rgba(254, 44, 85, 0.15); color: var(--pink-accent); border: 1px solid rgba(254, 44, 85, 0.3); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                                <?php echo e(ucfirst($a->status)); ?>

                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 16px 20px; text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                            <form action="<?php echo e(route('admin.auctions.toggle_status', $a->id)); ?>" method="POST" style="margin: 0;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: #fff; padding: 6px 10px; border-radius: 6px; font-size: 12px; cursor: pointer;" title="Toggle Active / Cancelled">
                                    <i class="bi bi-power"></i>
                                </button>
                            </form>
                            <a href="<?php echo e(route('admin.auctions.edit', $a->id)); ?>" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color); color: #fff; text-decoration: none; padding: 6px 10px; border-radius: 6px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="<?php echo e(route('admin.auctions.delete', $a->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this auction?');" style="margin: 0;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" style="background: rgba(254, 44, 85, 0.1); border: 1px solid rgba(254, 44, 85, 0.2); color: var(--pink-accent); padding: 6px 10px; border-radius: 6px; font-size: 12px; cursor: pointer;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: var(--text-muted);">
                        <i class="bi bi-hammer" style="font-size: 32px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                        No auctions found matching criteria.
                        <div style="margin-top: 12px;">
                            <a href="<?php echo e(route('admin.auctions.create')); ?>" style="color: var(--cyan-accent); font-weight: 700; text-decoration: none;">+ Create your first auction</a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="padding: 20px; border-top: 1px solid var(--border-color);">
        <?php echo e($auctions->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\zaldoris-tiktok\resources\views/admin/auctions/index.blade.php ENDPATH**/ ?>