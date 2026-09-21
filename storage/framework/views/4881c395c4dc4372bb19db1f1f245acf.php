<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e(setting('meta_description', 'Zaldoris - Live Shopping Feed. Experience real-time live video shopping, featured streams, and exclusive deals.')); ?>">
    <title>Live Shopping Feed - <?php echo e(setting('site_name', 'Zaldoris Live Commerce Platform')); ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo e(setting('site_favicon') ? asset(setting('site_favicon')) : asset('assets/favicon.png')); ?>">
    <link rel="shortcut icon" href="<?php echo e(setting('site_favicon') ? asset(setting('site_favicon')) : asset('assets/favicon.png')); ?>">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">
</head>
<body>

<!-- NAVBAR / HEADER -->
<header class="zal-navbar">
    <div class="zal-navbar-inner">
        <!-- Logo -->
        <a class="zal-brand" href="<?php echo e(route('home')); ?>">
            <img src="<?php echo e(setting('site_logo') ? asset(setting('site_logo')) : asset('assets/logo.png')); ?>" alt="<?php echo e(setting('site_name', 'Zaldoris')); ?>" class="zal-brand-logo">
        </a>

        <!-- Center Nav Links -->
        <ul class="zal-nav-menu">
            <li><a href="<?php echo e(route('home')); ?>" class="zal-nav-link">Home</a></li>
            <li><a href="<?php echo e(route('shop.index')); ?>" class="zal-nav-link active">Live Shopping</a></li>
            <li><a href="<?php echo e(route('auctions.index')); ?>" class="zal-nav-link">Live Auction</a></li>
            <li><a href="#" class="zal-nav-link">Live Academy</a></li>
            <li><a href="<?php echo e(route('streams.index')); ?>" class="zal-nav-link">Live Streaming</a></li>
            <li><a href="<?php echo e(route('streams.pk_battle', 1)); ?>" class="zal-nav-link">PK Battle</a></li>
        </ul>

                <!-- Right Action Icons -->
        <div class="zal-nav-actions">
            <a href="<?php echo e(route('search')); ?>" class="nav-icon-btn" title="Search"><i class="bi bi-search"></i></a>
            <?php if(auth()->guard()->check()): ?>
                <button class="nav-icon-btn" id="navTicketBtn" title="Wallet"><i class="bi bi-wallet2"></i></button>
                <a href="<?php echo e(route('notifications')); ?>" class="nav-icon-btn" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <span class="icon-badge-dot"></span>
                </a>
                <a href="<?php echo e(route('shop.cart')); ?>" class="nav-icon-btn" title="Cart">
                    <i class="bi bi-cart3"></i>
                    <span class="icon-badge-num" id="globalCartBadge">2</span>
                </a>
                <a href="<?php echo e(route('dashboard.creator')); ?>" class="nav-avatar-btn" title="Profile">
                    <img src="<?php echo e(auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&auto=format&fit=crop&q=80'); ?>" alt="<?php echo e(auth()->user()->name); ?>">
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn-login-nav" style="background: linear-gradient(135deg, var(--cyan-accent, #00F0C8), #1ed6d0); color: #090D10; text-decoration: none; padding: 7px 18px; border-radius: 20px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; margin-left: 8px;">
                    <i class="bi bi-box-arrow-in-right"></i> Log In
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- MAIN CONTAINER -->
<main class="page-container">

    <!-- TOP CATEGORIES SECTION -->
    <section class="section-spacing">
        <div class="section-header-row mb-3">
            <h2 class="section-title">Categories</h2>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('shop.index', ['filter' => 'featured'])); ?>" class="btn-outline-follow <?php echo e(request('filter') === 'featured' ? 'active' : ''); ?>" style="text-decoration:none; padding: 4px 12px; font-size: 13px;">Featured Products</a>
                <a href="<?php echo e(route('shop.index', ['filter' => 'trending'])); ?>" class="btn-outline-follow <?php echo e(request('filter') === 'trending' ? 'active' : ''); ?>" style="text-decoration:none; padding: 4px 12px; font-size: 13px;">Trending Products</a>
            </div>
        </div>
        <div class="top-categories-row" style="overflow-x: auto; padding-bottom: 8px;">
            <a href="<?php echo e(route('shop.index')); ?>" class="category-pill-btn <?php echo e(!request('category') || request('category') === 'all' ? 'active' : ''); ?>" style="text-decoration:none; display: inline-flex; align-items:center; gap:6px;">
                <i class="bi bi-grid-fill"></i> All Categories
            </a>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('shop.index', ['category' => $cat->slug])); ?>" class="category-pill-btn <?php echo e(request('category') === $cat->slug ? 'active' : ''); ?>" style="text-decoration:none; display: inline-flex; align-items:center; gap:6px;">
                <?php if($cat->icon): ?>
                    <i class="<?php echo e($cat->icon); ?>"></i>
                <?php else: ?>
                    <i class="bi bi-tag-fill"></i>
                <?php endif; ?>
                <?php echo e($cat->name); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <!-- DYNAMIC PRODUCTS CATALOG -->
    <?php if(isset($products) && $products->count() > 0): ?>
    <section class="section-spacing">
        <div class="section-header-row">
            <h2 class="section-title">
                <?php if(request('filter') === 'featured'): ?>
                    🌟 Featured Products
                <?php elseif(request('filter') === 'trending'): ?>
                    🔥 Trending Products
                <?php elseif(request('category')): ?>
                    📁 Category: <?php echo e(ucfirst(str_replace('-', ' ', request('category')))); ?>

                <?php else: ?>
                    🛍️ Live Shopping Products
                <?php endif; ?>
            </h2>
            <span class="text-muted" style="font-size: 13px;">Showing <?php echo e($products->count()); ?> of <?php echo e($products->total()); ?> items</span>
        </div>

        <div class="grid-5-col">
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $img = $p->main_image ?? ($p->images[0] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80');
                if ($img && !str_starts_with($img, 'http')) {
                    $img = asset($img);
                }
            ?>
            <a href="<?php echo e(route('shop.product', $p->id)); ?>" class="zal-card" style="text-decoration:none;">
                <div class="live-card-thumb" style="position:relative;">
                    <img src="<?php echo e($img); ?>" alt="<?php echo e($p->title); ?>" style="width:100%; height:180px; object-fit:cover; border-radius:12px;">
                    <?php if($p->is_featured): ?>
                        <div class="badge-live-top" style="background:#00F0C8; color:#090D10; font-weight:700;"><i class="bi bi-star-fill"></i> FEATURED</div>
                    <?php elseif($p->is_trending): ?>
                        <div class="badge-live-top" style="background:#7033FF; color:#fff; font-weight:700;"><i class="bi bi-fire"></i> TRENDING</div>
                    <?php endif; ?>
                    <div class="badge-viewers-top" style="background:rgba(9,13,16,0.85); color:#00F0C8; font-weight:700;">
                        <?php echo e(setting('currency_symbol', '$')); ?><?php echo e(number_format($p->price, 2)); ?>

                    </div>
                </div>
                <div class="live-card-body" style="padding:10px 0;">
                    <div class="live-card-title" style="font-size:14px; font-weight:600; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <?php echo e($p->title); ?>

                    </div>
                    <div class="host-row" style="margin-top:6px; display:flex; align-items:center; justify-content:space-between;">
                        <span style="font-size:12px; color:#6B7C93;">
                            <?php echo e($p->category->name ?? $p->category ?? 'Product'); ?>

                        </span>
                        <span style="font-size:11px; color:#00F0C8; font-weight:600;">
                            Stock: <?php echo e($p->stock); ?>

                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($products->appends(request()->query())->links()); ?>

        </div>
    </section>
    <?php endif; ?>

    <!-- FEATURED LIVE SECTION -->
    <section class="section-spacing">
        <div class="section-header-row">
            <h2 class="section-title">Featured Live</h2>
            <a href="<?php echo e(route('streams.index')); ?>" class="view-all-link">View All <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="grid-5-col">
            <!-- Card 1 -->
            <a href="<?php echo e(route('streams.show', 1)); ?>" class="zal-card">
                <div class="live-card-thumb">
                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80" alt="Limited Sneaker">
                    <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                    <div class="badge-viewers-top"><i class="bi bi-eye-fill"></i> 1.2k</div>
                </div>
                <div class="live-card-body">
                    <div class="live-card-title">Limited Sneaker Drop...</div>
                    <div class="host-row">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&auto=format&fit=crop&q=80" alt="Sarah Fashion Studio" class="host-avatar">
                        <span class="host-name">Sarah Fashion Studio</span>
                    </div>
                </div>
            </a>

            <!-- Card 2 -->
            <a href="<?php echo e(route('streams.show', 1)); ?>" class="zal-card">
                <div class="live-card-thumb">
                    <img src="https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=600&auto=format&fit=crop&q=80" alt="Glass Skin Secret">
                    <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                    <div class="badge-viewers-top"><i class="bi bi-eye-fill"></i> 1.2k</div>
                </div>
                <div class="live-card-body">
                    <div class="live-card-title">Glass Skin Secret...</div>
                    <div class="host-row">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80" alt="Gadget Hub Pro" class="host-avatar">
                        <span class="host-name">Gadget Hub Pro</span>
                    </div>
                </div>
            </a>

            <!-- Card 3 -->
            <a href="<?php echo e(route('streams.show', 1)); ?>" class="zal-card">
                <div class="live-card-thumb">
                    <img src="https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=600&auto=format&fit=crop&q=80" alt="Luxury Handbags">
                    <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                    <div class="badge-viewers-top"><i class="bi bi-eye-fill"></i> 1.2k</div>
                </div>
                <div class="live-card-body">
                    <div class="live-card-title">Luxury Handbags...</div>
                    <div class="host-row">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" alt="Beauty Glow Official" class="host-avatar">
                        <span class="host-name">Beauty Glow Official</span>
                    </div>
                </div>
            </a>

            <!-- Card 4 -->
            <a href="<?php echo e(route('streams.show', 1)); ?>" class="zal-card">
                <div class="live-card-thumb">
                    <img src="https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=600&auto=format&fit=crop&q=80" alt="Smart Tech Showcase">
                    <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                    <div class="badge-viewers-top"><i class="bi bi-eye-fill"></i> 1.2k</div>
                </div>
                <div class="live-card-body">
                    <div class="live-card-title">Smart Tech Showcase...</div>
                    <div class="host-row">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" alt="Tech Prime" class="host-avatar">
                        <span class="host-name">Tech Prime</span>
                    </div>
                </div>
            </a>

            <!-- Card 5 -->
            <a href="<?php echo e(route('streams.show', 1)); ?>" class="zal-card">
                <div class="live-card-thumb">
                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80" alt="Urban Streetwear">
                    <div class="badge-live-top"><span class="live-pulse-dot"></span> LIVE</div>
                    <div class="badge-viewers-top"><i class="bi bi-eye-fill"></i> 1.2k</div>
                </div>
                <div class="live-card-body">
                    <div class="live-card-title">Urban Streetwear Live...</div>
                    <div class="host-row">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" alt="Kicks City" class="host-avatar">
                        <span class="host-name">Kicks City</span>
                    </div>
                </div>
            </a>
        </div>
    </section>

    <!-- NEVER MISS A DROP NEWSLETTER BANNER -->
    <div class="never-miss-banner">
        <div class="never-miss-content">
            <h2 class="never-miss-title">Never Miss A Drop</h2>
            <p class="never-miss-sub">
                Get notifications for your favorite creators, early access to limited auctions, and weekly live shopping highlights.
            </p>
        </div>
        <form onsubmit="handleSubscribeNewsletter(event)" class="never-miss-form">
            <input type="email" id="newsletterEmail" placeholder="Enter Your Email" class="newsletter-input" required autocomplete="email">
            <button type="submit" class="btn-newsletter-subscribe" id="btnSubscribe">Subscribe</button>
        </form>
    </div>

</main>

<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
        <!-- Center Logo -->
        <a href="<?php echo e(route('home')); ?>">
            <img src="<?php echo e(setting('site_logo') ? asset(setting('site_logo')) : asset('assets/logo.png')); ?>" alt="<?php echo e(setting('site_name', 'Zaldoris')); ?>" class="footer-logo-img">
        </a>
        <p class="footer-tagline-text">
            <?php echo e(setting('site_tagline', 'Experience the future of shopping with realtime interaction, live demonstrations, and exclusive community deals.')); ?>

        </p>
        <div class="footer-copyright-line">
            <?php echo e(setting('copyright_text', '© 2026 Zaldoris LiveStreamShop. All rights reserved.')); ?>

        </div>
    </div>
</footer>

<!-- FLOATING WIDGET BUTTON -->
<button class="floating-action-widget" title="Live Chat">
    <i class="bi bi-chat-dots-fill"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(asset('js/main.js')); ?>"></script>
<script>
    // Newsletter subscription form handler
    function handleSubscribeNewsletter(event) {
        event.preventDefault();
        const btn = document.getElementById('btnSubscribe');
        const input = document.getElementById('newsletterEmail');
        if (btn && input) {
            btn.disabled = true;
            btn.textContent = 'Subscribed ✓';
            btn.style.background = '#12181E';
            btn.style.color = '#00F0C8';
            input.value = '';
            setTimeout(() => {
                btn.disabled = false;
                btn.textContent = 'Subscribe';
                btn.style.background = '#090D10';
            }, 3000);
        }
    }
</script>
</body>
</html>
<?php /**PATH C:\wamp64\www\zaldoris-tiktok\resources\views/shop/index.blade.php ENDPATH**/ ?>