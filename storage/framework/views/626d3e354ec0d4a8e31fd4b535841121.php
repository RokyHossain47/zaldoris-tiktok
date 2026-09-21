<?php $__env->startSection('title', 'Categories Management - Super Admin'); ?>

<?php $__env->startSection('content'); ?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Product Categories</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Manage categories for live shopping, feeds, auctions, and product showcases.</p>
    </div>
    <button onclick="document.getElementById('createCategoryModal').style.display='flex'" style="background: linear-gradient(135deg, var(--pink-accent), var(--pink-hover)); color: #fff; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4);">
        <i class="bi bi-plus-lg"></i> Add Category
    </button>
</div>

<!-- CATEGORIES TABLE -->
<div class="admin-table-container">
    <div class="table-header-bar">
        <div style="font-weight: 700; font-size: 16px; color: #fff;">
            All Categories (<?php echo e($categories->total()); ?>)
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                <th style="padding: 16px 20px;">Image / Icon</th>
                <th style="padding: 16px 20px;">Category Name</th>
                <th style="padding: 16px 20px;">Slug</th>
                <th style="padding: 16px 20px;">Products Count</th>
                <th style="padding: 16px 20px;">Order</th>
                <th style="padding: 16px 20px;">Status</th>
                <th style="padding: 16px 20px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr style="border-bottom: 1px solid var(--border-color); color: #fff;">
                    <td style="padding: 16px 20px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <?php if($cat->image): ?>
                                <img src="<?php echo e($cat->image); ?>" alt="<?php echo e($cat->name); ?>" style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                            <?php endif; ?>
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(37, 244, 238, 0.1); color: var(--cyan-accent); display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                <i class="bi <?php echo e($cat->icon ?: 'bi-tag'); ?>"></i>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px 20px;">
                        <div style="font-weight: 700; color: #fff;"><?php echo e($cat->name); ?></div>
                        <?php if($cat->description): ?>
                            <div style="font-size: 12px; color: var(--text-muted);"><?php echo e(Str::limit($cat->description, 40)); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 16px 20px; color: var(--text-muted); font-family: monospace;">
                        <?php echo e($cat->slug); ?>

                    </td>
                    <td style="padding: 16px 20px;">
                        <span style="background: rgba(255, 255, 255, 0.06); padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 13px;">
                            <?php echo e($cat->products_count); ?> products
                        </span>
                    </td>
                    <td style="padding: 16px 20px; color: var(--text-muted);">
                        #<?php echo e($cat->sort_order); ?>

                    </td>
                    <td style="padding: 16px 20px;">
                        <span class="status-pill <?php echo e($cat->is_active ? 'status-active' : 'status-blocked'); ?>">
                            <?php echo e($cat->is_active ? 'Active' : 'Disabled'); ?>

                        </span>
                    </td>
                    <td style="padding: 16px 20px; text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <button onclick="openEditCategoryModal(<?php echo e(json_encode($cat)); ?>)" class="action-btn" title="Edit Category">
                                <i class="bi bi-pencil-fill" style="color: var(--cyan-accent);"></i>
                            </button>
                            <form action="<?php echo e(route('admin.categories.delete', $cat->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" style="margin: 0;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="action-btn" title="Delete Category">
                                    <i class="bi bi-trash-fill" style="color: var(--pink-accent);"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="padding: 30px; text-align: center; color: var(--text-muted);">
                        No categories found. Click "Add Category" to create one.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="padding: 16px 20px;">
        <?php echo e($categories->links()); ?>

    </div>
</div>

<!-- CREATE CATEGORY MODAL -->
<div id="createCategoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 550px; max-height: 90vh; overflow-y: auto; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 800; color: #fff;">Add New Category</h2>
            <button onclick="document.getElementById('createCategoryModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 22px; cursor: pointer;">&times;</button>
        </div>

        <form action="<?php echo e(route('admin.categories.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. Sneakers & Footwear" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Slug (Optional, auto-generated)</label>
                <input type="text" name="slug" placeholder="e.g. sneakers-footwear" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Bootstrap Icon Class</label>
                    <input type="text" name="icon" placeholder="bi-fire, bi-bag, bi-watch" value="bi-tag" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Sort Order</label>
                    <input type="number" name="sort_order" value="0" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Upload Category Image</label>
                <input type="file" name="image_file" accept="image/*" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 10px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Or Image URL</label>
                <input type="url" name="image_url" placeholder="https://..." style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Description</label>
                <textarea name="description" rows="3" placeholder="Category description..." style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('createCategoryModal').style.display='none'" style="background: rgba(255,255,255,0.08); color: #fff; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="background: var(--pink-accent); color: #fff; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 700; cursor: pointer;">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT CATEGORY MODAL -->
<div id="editCategoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 550px; max-height: 90vh; overflow-y: auto; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 800; color: #fff;">Edit Category</h2>
            <button onclick="document.getElementById('editCategoryModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 22px; cursor: pointer;">&times;</button>
        </div>

        <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Category Name *</label>
                <input type="text" id="edit_cat_name" name="name" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Slug</label>
                <input type="text" id="edit_cat_slug" name="slug" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Icon Class</label>
                    <input type="text" id="edit_cat_icon" name="icon" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Sort Order</label>
                    <input type="number" id="edit_cat_sort" name="sort_order" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Replace Image</label>
                <input type="file" name="image_file" accept="image/*" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 10px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Or Image URL</label>
                <input type="url" id="edit_cat_image" name="image_url" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Description</label>
                <textarea id="edit_cat_desc" name="description" rows="3" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('editCategoryModal').style.display='none'" style="background: rgba(255,255,255,0.08); color: #fff; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="background: var(--cyan-accent); color: #13141f; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 800; cursor: pointer;">Update Category</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditCategoryModal(cat) {
    document.getElementById('edit_cat_name').value = cat.name || '';
    document.getElementById('edit_cat_slug').value = cat.slug || '';
    document.getElementById('edit_cat_icon').value = cat.icon || 'bi-tag';
    document.getElementById('edit_cat_sort').value = cat.sort_order || 0;
    document.getElementById('edit_cat_image').value = cat.image || '';
    document.getElementById('edit_cat_desc').value = cat.description || '';
    
    document.getElementById('editCategoryForm').action = '/admin/categories/' + cat.id;
    document.getElementById('editCategoryModal').style.display = 'flex';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\zaldoris-tiktok\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>