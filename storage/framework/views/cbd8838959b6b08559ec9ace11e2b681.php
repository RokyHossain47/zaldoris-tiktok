<?php $__env->startSection('title', 'Create New Auction - Super Admin'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Create New Live Auction</h1>
            <p style="font-size: 14px; color: var(--text-muted);">Set up a live auction event with bidding thresholds, reserve price, and countdown timer.</p>
        </div>
        <a href="<?php echo e(route('admin.auctions.index')); ?>" style="color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600;">
            <i class="bi bi-arrow-left"></i> Back to Auctions
        </a>
    </div>

    <?php if($errors->any()): ?>
        <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid var(--pink-accent); color: #fff; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px;">
            <div style="font-weight: 700; margin-bottom: 6px; color: var(--pink-accent);">Please correct the following errors:</div>
            <ul style="margin-left: 20px; font-size: 13px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <form action="<?php echo e(route('admin.auctions.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <!-- BASIC DETAILS -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Auction Title *</label>
                <input type="text" name="title" value="<?php echo e(old('title')); ?>" required placeholder="e.g. Vintage Rolex Submariner 1974 'Pre-Comex'" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Seller / Host Creator *</label>
                    <select name="seller_id" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <?php $__currentLoopData = $sellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php echo e(old('seller_id', auth()->id()) == $s->id ? 'selected' : ''); ?>>
                                <?php echo e($s->name); ?> (<?php echo e('@' . $s->username); ?> - <?php echo e(ucfirst($s->role)); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Link to Catalog Product (Optional)</label>
                    <select name="product_id" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="">-- No specific catalog product --</option>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>" <?php echo e(old('product_id') == $p->id ? 'selected' : ''); ?>>
                                <?php echo e($p->title); ?> (<?php echo e(setting('currency_symbol', '$')); ?><?php echo e(number_format($p->price, 2)); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <!-- PRICING & BID STEPPING -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Starting Bid (<?php echo e(setting('currency_symbol', '$')); ?>) *</label>
                    <input type="number" step="0.01" min="0.01" name="starting_bid" value="<?php echo e(old('starting_bid', '1.00')); ?>" required placeholder="1.00" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Reserve Price (<?php echo e(setting('currency_symbol', '$')); ?>)</label>
                    <input type="number" step="0.01" min="0" name="reserve_price" value="<?php echo e(old('reserve_price')); ?>" placeholder="Optional minimum threshold" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Hidden threshold required to win</div>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Minimum Bid Step (<?php echo e(setting('currency_symbol', '$')); ?>) *</label>
                    <input type="number" step="0.01" min="0.01" name="min_bid_step" value="<?php echo e(old('min_bid_step', '5.00')); ?>" required placeholder="5.00" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Minimum increment per bid</div>
                </div>
            </div>

            <!-- SCHEDULE TIMINGS & STATUS -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Starts At</label>
                    <input type="datetime-local" name="starts_at" value="<?php echo e(old('starts_at', now()->format('Y-m-d\TH:i'))); ?>" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Ends At *</label>
                    <input type="datetime-local" name="ends_at" value="<?php echo e(old('ends_at', now()->addDays(1)->format('Y-m-d\TH:i'))); ?>" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Initial Status *</label>
                    <select name="status" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="active" <?php echo e(old('status') === 'active' ? 'selected' : ''); ?>>🔴 Active Live Now</option>
                        <option value="upcoming" <?php echo e(old('status') === 'upcoming' ? 'selected' : ''); ?>>⏳ Upcoming Scheduled Drop</option>
                    </select>
                </div>
            </div>

            <!-- IMAGE UPLOAD & URL -->
            <div style="margin-bottom: 24px; background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Auction Main Image</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Upload Image File (JPG, PNG, WEBP):</div>
                        <input type="file" name="image_file" accept="image/*" style="width: 100%; background: var(--bg-card); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 8px; font-size: 13px;">
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Or Paste Image URL:</div>
                        <input type="url" name="image_url" value="<?php echo e(old('image_url')); ?>" placeholder="https://images.unsplash.com/..." style="width: 100%; background: var(--bg-card); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 8px; font-size: 13px;">
                    </div>
                </div>
            </div>

            <!-- DESCRIPTION -->
            <div style="margin-bottom: 28px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Detailed Description & Authenticity Notes</label>
                <textarea name="description" rows="5" placeholder="Include serial number, condition grade, accessories, and shipping provenance details..." style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px; line-height: 1.5;"><?php echo e(old('description')); ?></textarea>
            </div>

            <!-- BUTTONS -->
            <div style="display: flex; gap: 16px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 24px;">
                <a href="<?php echo e(route('admin.auctions.index')); ?>" style="background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600;">
                    Cancel
                </a>
                <button type="submit" style="background: linear-gradient(135deg, var(--cyan-accent), #00d2c7); color: #000; border: none; padding: 12px 32px; border-radius: 10px; font-size: 14px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(37, 244, 238, 0.4);">
                    <i class="bi bi-check-lg"></i> Publish Auction
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\zaldoris-tiktok\resources\views/admin/auctions/create.blade.php ENDPATH**/ ?>