<?php $__env->startSection('title', 'Dashboard Overview - GenZ Live Admin'); ?>

<?php $__env->startSection('content'); ?>

    <!-- TOP TITLE ROW -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <span style="font-size: 24px; color: #FE2C55;"><i class="bi bi-pie-chart-fill"></i></span>
                <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin: 0;">Dashboard Overview</h1>
            </div>
            <p style="color: var(--text-muted); font-size: 13px;">Real-time statistics, live monitoring & user management overview.</p>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('admin.users.index')); ?>" style="background: var(--bg-card); border: 1px solid var(--border-color); color: #fff; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-people-fill"></i> User Management Page
            </a>
            <a href="<?php echo e(route('admin.dashboard')); ?>" style="background: var(--pink-accent); color: #fff; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4);">
                <i class="bi bi-shield-fill"></i> Admin Panel
            </a>
        </div>
    </div>

    <!-- 8 STAT CARDS IN 2 ROWS OF 4 -->
    <div class="stat-grid">
        
        <!-- CARD 1: Total Users -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Users</div>
                <div class="stat-val"><?php echo e(number_format($totalUsers)); ?></div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(254, 44, 85, 0.15); color: #FE2C55;">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>

        <!-- CARD 2: Active Users -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Active Users</div>
                <div class="stat-val"><?php echo e(number_format($activeUsers)); ?></div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(39, 201, 137, 0.15); color: #27c989;">
                <i class="bi bi-person-check-fill"></i>
            </div>
        </div>

        <!-- CARD 3: Live Streamers -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Live Streamers</div>
                <div class="stat-val"><?php echo e(number_format($liveStreamers)); ?></div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(37, 244, 238, 0.15); color: #25F4EE;">
                <i class="bi bi-broadcast-pin"></i>
            </div>
        </div>

        <!-- CARD 4: VIP Members -->
        <div class="stat-card">
            <div>
                <div class="stat-label">VIP Members</div>
                <div class="stat-val"><?php echo e(number_format($vipMembers)); ?></div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(255, 184, 0, 0.15); color: #FFB800;">
                <i class="bi bi-crown-fill"></i>
            </div>
        </div>

        <!-- CARD 5: Active Hosts -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Active Hosts</div>
                <div class="stat-val"><?php echo e(number_format($activeHosts)); ?></div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(168, 85, 247, 0.15); color: #a855f7;">
                <i class="bi bi-mic-fill"></i>
            </div>
        </div>

        <!-- CARD 6: Agencies -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Agencies</div>
                <div class="stat-val"><?php echo e(number_format($agencies)); ?></div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                <i class="bi bi-building-fill"></i>
            </div>
        </div>

        <!-- CARD 7: Feed Posts -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Feed Posts</div>
                <div class="stat-val"><?php echo e(number_format($feedPosts)); ?></div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(244, 63, 94, 0.15); color: #f43f5e;">
                <i class="bi bi-images"></i>
            </div>
        </div>

        <!-- CARD 8: Reported Users -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Reported Users</div>
                <div class="stat-val"><?php echo e(number_format($reportedUsers)); ?></div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(249, 115, 22, 0.15); color: #f97316;">
                <i class="bi bi-flag-fill"></i>
            </div>
        </div>

    </div>

    <!-- USER MANAGEMENT LIST TABLE (MATCHING SCREENSHOT) -->
    <div class="admin-table-container" id="users-section">
        
        <!-- Table Header Filter Bar -->
        <div class="table-header-bar">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px; color: #FE2C55;"><i class="bi bi-people-fill"></i></span>
                <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin: 0;">User Management List</h3>
            </div>

            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <!-- Filter Pills -->
                <div class="table-filter-pills">
                    <a href="<?php echo e(route('admin.dashboard', ['filter' => 'all', 'search' => $search])); ?>" class="<?php echo e($filter === 'all' ? 'active' : ''); ?>">All</a>
                    <a href="<?php echo e(route('admin.dashboard', ['filter' => 'active', 'search' => $search])); ?>" class="<?php echo e($filter === 'active' ? 'active' : ''); ?>">Active</a>
                    <a href="<?php echo e(route('admin.dashboard', ['filter' => 'blocked', 'search' => $search])); ?>" class="<?php echo e($filter === 'blocked' ? 'active' : ''); ?>">Blocked</a>
                    <a href="<?php echo e(route('admin.dashboard', ['filter' => 'hosts', 'search' => $search])); ?>" class="<?php echo e($filter === 'hosts' ? 'active' : ''); ?>">Hosts</a>
                </div>

                <!-- Search Input -->
                <form action="<?php echo e(route('admin.dashboard')); ?>" method="GET">
                    <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
                    <div class="search-box-wrap">
                        <i class="bi bi-search" style="color: var(--text-muted); font-size: 13px;"></i>
                        <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search users...">
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div style="overflow-x: auto;">
            <table class="admin-data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>USER</th>
                        <th>UID</th>
                        <th>GENDER</th>
                        <th>BALANCES</th>
                        <th>COUNTRY</th>
                        <th>LEVEL</th>
                        <th>ROLE</th>
                        <th style="text-align: right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: 700;">
                                <?php echo e($users->firstItem() + $index); ?>

                            </td>

                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="<?php echo e($u->avatar_url); ?>" alt="<?php echo e($u->name); ?>" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                                    <div>
                                        <div style="font-weight: 700; color: #fff;"><?php echo e($u->name); ?></div>
                                        <div style="font-size: 11px; color: var(--text-muted);"><?php echo e($u->email); ?></div>
                                    </div>
                                </div>
                            </td>

                            <td style="font-family: monospace; font-size: 13px; color: #25F4EE; font-weight: 700;">
                                <?php echo e(1400000 + $u->id); ?>

                            </td>

                            <td style="color: #aaa; font-size: 12px;">
                                <?php echo e($u->id % 2 === 0 ? 'Female' : 'Male'); ?>

                            </td>

                            <td>
                                <div class="coin-pill-badge">
                                    <?php echo e(number_format($u->coin_balance)); ?>

                                    <span>Coins</span>
                                </div>
                            </td>

                            <td style="color: #aaa; font-size: 13px;">
                                Canada
                            </td>

                            <td>
                                <?php if($u->is_vip): ?>
                                    <span style="background: rgba(255, 184, 0, 0.15); color: #FFB800; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 6px;">
                                        VIP Tier
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 12px;">Lv. <?php echo e(max(1, min(50, round($u->coin_balance / 100)))); ?></span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <span style="background: rgba(255, 255, 255, 0.05); color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                    <?php echo e($u->role); ?>

                                </span>
                            </td>

                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <!-- Coins Adjust Button -->
                                    <button onclick="openCoinsModal(<?php echo e($u->id); ?>, '<?php echo e($u->name); ?>', <?php echo e($u->coin_balance); ?>)" title="Adjust Coins" style="background: rgba(255, 184, 0, 0.15); border: 1px solid #FFB800; color: #FFB800; width: 30px; height: 30px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-coin"></i>
                                    </button>

                                    <!-- Block / Unblock Toggle -->
                                    <form action="<?php echo e(route('admin.users.toggle_block', $u->id)); ?>" method="POST" style="margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <?php if($u->is_suspended): ?>
                                            <button type="submit" title="Unblock User" style="background: rgba(39, 201, 137, 0.15); border: 1px solid #27c989; color: #27c989; width: 30px; height: 30px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-unlock-fill"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" title="Block User" style="background: rgba(254, 44, 85, 0.15); border: 1px solid #FE2C55; color: #FE2C55; width: 30px; height: 30px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-slash-circle-fill"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                No users found matching your criteria.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            <?php echo e($users->appends(['filter' => $filter, 'search' => $search])->links()); ?>

        </div>

    </div>

    <!-- ADJUST COINS MODAL -->
    <div id="coinsModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(6px); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 90%; max-width: 400px; padding: 24px; color: #fff;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 800;">Adjust User Coins</h3>
                <button onclick="closeCoinsModal()" style="background: none; border: none; color: #888; font-size: 20px; cursor: pointer;">&times;</button>
            </div>

            <form id="coinsForm" method="POST">
                <?php echo csrf_field(); ?>
                <p id="modalUserName" style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;"></p>

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 6px;">New Coin Balance</label>
                    <input type="number" name="coin_balance" id="modalCoinInput" min="0" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #FFB800; font-weight: 800; font-size: 18px; padding: 10px 14px; border-radius: 8px; outline: none;">
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeCoinsModal()" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: none; color: #fff; font-weight: 600; font-size: 13px; cursor: pointer;">
                        Cancel
                    </button>
                    <button type="submit" style="flex: 1; padding: 10px; border-radius: 8px; border: none; background: var(--pink-accent); color: #fff; font-weight: 800; font-size: 13px; cursor: pointer;">
                        Save Balance
                    </button>
                </div>
            </form>
        </div>
    </div>

<?php $__env->startPush('admin_styles'); ?>
<script>
    function openCoinsModal(userId, userName, currentCoins) {
        document.getElementById('modalUserName').innerText = `Editing balance for ${userName} (#${userId})`;
        document.getElementById('modalCoinInput').value = currentCoins;
        document.getElementById('coinsForm').action = `/admin/users/${userId}/update-coins`;
        document.getElementById('coinsModal').style.display = 'flex';
    }

    function closeCoinsModal() {
        document.getElementById('coinsModal').style.display = 'none';
    }
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\zaldoris-tiktok\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>