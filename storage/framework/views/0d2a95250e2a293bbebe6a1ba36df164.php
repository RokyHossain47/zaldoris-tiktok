<?php if($paginator->hasPages()): ?>
    <nav class="d-flex justify-content-between align-items-center flex-wrap" style="display: flex; justify-content: space-between; align-items: center; width: 100%; flex-wrap: wrap; gap: 14px;">
        <!-- Left: Showing results count text -->
        <div style="font-size: 13px; color: var(--text-muted, #8e90a6); font-weight: 500;">
            Showing <strong style="color: #fff;"><?php echo e($paginator->firstItem() ?? 0); ?></strong> to <strong style="color: #fff;"><?php echo e($paginator->lastItem() ?? 0); ?></strong> of <strong style="color: #fff;"><?php echo e($paginator->total()); ?></strong> results
        </div>

        <!-- Right: Compact Numbered Pagination Links -->
        <div>
            <ul class="pagination" style="display: flex; align-items: center; gap: 6px; list-style: none; padding: 0; margin: 0;">
                
                <?php if($paginator->onFirstPage()): ?>
                    <li class="page-item disabled" aria-disabled="true" aria-label="<?php echo app('translator')->get('pagination.previous'); ?>">
                        <span class="page-link" aria-hidden="true" style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); color: rgba(255,255,255,0.25); border-radius: 8px; font-size: 14px; cursor: not-allowed;">‹</span>
                    </li>
                <?php else: ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" aria-label="<?php echo app('translator')->get('pagination.previous'); ?>" style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; background: #0c0d14; border: 1px solid var(--border-color, rgba(255,255,255,0.08)); color: #fff; border-radius: 8px; font-size: 14px; text-decoration: none;">‹</a>
                    </li>
                <?php endif; ?>

                
                <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if(is_string($element)): ?>
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link" style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 8px; background: transparent; border: none; color: var(--text-muted, #8e90a6); font-size: 13px;"><?php echo e($element); ?></span>
                        </li>
                    <?php endif; ?>

                    
                    <?php if(is_array($element)): ?>
                        <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($page == $paginator->currentPage()): ?>
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link" style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 12px; background: var(--cyan-accent, #25F4EE); border: 1px solid var(--cyan-accent, #25F4EE); color: #090D10; border-radius: 8px; font-size: 13px; font-weight: 800; box-shadow: 0 2px 10px rgba(37, 244, 238, 0.3);"><?php echo e($page); ?></span>
                                </li>
                            <?php else: ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo e($url); ?>" style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 12px; background: #0c0d14; border: 1px solid var(--border-color, rgba(255,255,255,0.08)); color: #fff; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;"><?php echo e($page); ?></a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php if($paginator->hasMorePages()): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" aria-label="<?php echo app('translator')->get('pagination.next'); ?>" style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; background: #0c0d14; border: 1px solid var(--border-color, rgba(255,255,255,0.08)); color: #fff; border-radius: 8px; font-size: 14px; text-decoration: none;">›</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled" aria-disabled="true" aria-label="<?php echo app('translator')->get('pagination.next'); ?>">
                        <span class="page-link" aria-hidden="true" style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); color: rgba(255,255,255,0.25); border-radius: 8px; font-size: 14px; cursor: not-allowed;">›</span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
<?php elseif($paginator->total() > 0): ?>
    <div style="font-size: 13px; color: var(--text-muted, #8e90a6); font-weight: 500;">
        Showing <strong style="color: #fff;">1</strong> to <strong style="color: #fff;"><?php echo e($paginator->total()); ?></strong> of <strong style="color: #fff;"><?php echo e($paginator->total()); ?></strong> results
    </div>
<?php endif; ?>
<?php /**PATH C:\wamp64\www\zaldoris-tiktok\resources\views/vendor/pagination/bootstrap-5.blade.php ENDPATH**/ ?>