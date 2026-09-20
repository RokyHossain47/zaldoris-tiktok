<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Zaldoris - Cart & Checkout Summary. View added items, shipping methods, and order calculations.">
    <title>My Cart - {{ setting('site_name', 'Zaldoris Live Commerce Platform') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}">
    <link rel="shortcut icon" href="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>

<!-- NAVBAR / HEADER -->
<header class="zal-navbar">
    <div class="zal-navbar-inner">
        <!-- Logo -->
        <a class="zal-brand" href="{{ route('home') }}">
            <img src="{{ asset(setting('site_logo', 'assets/logo.png')) }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="zal-brand-logo">
        </a>

        <!-- Center Nav Links -->
        <ul class="zal-nav-menu">
            <li><a href="{{ route('home') }}" class="zal-nav-link">Home</a></li>
            <li><a href="{{ route('shop.index') }}" class="zal-nav-link active">Live Shopping</a></li>
            <li><a href="{{ route('auctions.index') }}" class="zal-nav-link">Live Auction</a></li>
            <li><a href="#" class="zal-nav-link">Live Academy</a></li>
            <li><a href="{{ route('streams.index') }}" class="zal-nav-link">Live Streaming</a></li>
            <li><a href="{{ route('streams.pk_battle', 1) }}" class="zal-nav-link">PK Battle</a></li>
        </ul>

        <!-- Right Action Icons -->
        <div class="zal-nav-actions">
            <a href="{{ route('search') }}" class="nav-icon-btn" title="Search"><i class="bi bi-search"></i></a>
            @auth
                <button class="nav-icon-btn" id="navTicketBtn" title="Wallet"><i class="bi bi-wallet2"></i></button>
                <a href="{{ route('notifications') }}" class="nav-icon-btn" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <span class="icon-badge-dot"></span>
                </a>
                <a href="{{ route('shop.cart') }}" class="nav-icon-btn" title="Cart">
                    <i class="bi bi-cart3"></i>
                    <span class="icon-badge-num" id="globalCartBadge">{{ array_sum(array_column(session('cart', []), 'quantity')) ?: 0 }}</span>
                </a>
                <a href="{{ route('dashboard.creator') }}" class="nav-avatar-btn" title="Profile">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&auto=format&fit=crop&q=80' }}" alt="{{ auth()->user()->name }}">
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-login-nav" style="background: linear-gradient(135deg, var(--cyan-accent, #00F0C8), #1ed6d0); color: #090D10; text-decoration: none; padding: 7px 18px; border-radius: 20px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; margin-left: 8px;">
                    <i class="bi bi-box-arrow-in-right"></i> Log In
                </a>
            @endauth
        </div>
    </div>
</header>

<!-- MAIN CONTAINER -->
<main class="page-container">

    <!-- Header Row with Back Button -->
    <div class="notif-header-row mb-4">
        <a href="{{ url()->previous() ?: route('shop.index') }}" class="notif-back-btn" title="Back">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="notif-page-title">Shopping Cart ({{ count($cart) }} Items)</h1>
    </div>

    <!-- TWO-COLUMN CART & SUMMARY GRID -->
    <div class="cart-page-grid">

        <!-- LEFT COLUMN: CART ITEMS LIST -->
        <div class="cart-items-column" id="cartItemsContainer">

            @forelse($cart as $id => $item)
                <div class="cart-item-card" data-product-id="{{ $id }}">
                    <div class="cart-item-top">
                        <div class="cart-item-left">
                            <img src="{{ $item['image'] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200' }}" alt="{{ $item['title'] }}" class="cart-item-thumb">
                            <div class="cart-item-info">
                                <h2 class="cart-item-title">{{ Str::limit($item['title'], 32) }}</h2>
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 4px;">
                                    @if(!empty($item['selected_color']))
                                        <span style="background: rgba(0, 240, 200, 0.12); color: #00F0C8; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; border: 1px solid rgba(0, 240, 200, 0.25);">
                                            {{ $item['selected_color'] }}
                                        </span>
                                    @endif
                                    @if(!empty($item['selected_size']))
                                        <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px;">
                                            {{ $item['selected_size'] }}
                                        </span>
                                    @endif
                                    <span style="font-size: 12px; color: var(--text-muted);">{{ $item['seller_name'] ?? 'Verified Seller' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="cart-item-right">
                            <button type="button" class="btn-delete-cart-item" title="Remove Item" onclick="removeCartCard(this, {{ $id }})">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                            <span class="cart-item-price">{{ setting('currency_symbol', '$') }}{{ number_format($item['price'], 2) }}</span>
                        </div>
                    </div>

                    <div class="cart-item-bottom">
                        <span class="cart-qty-label">Quantity</span>
                        <div class="quantity-stepper">
                            <button type="button" class="btn-step" onclick="updateItemQty(this, -1, {{ $id }})">—</button>
                            <span class="step-num">{{ $item['quantity'] }}</span>
                            <button type="button" class="btn-step" onclick="updateItemQty(this, 1, {{ $id }})">+</button>
                        </div>
                    </div>
                </div>
            @empty
                <div style="background: var(--bg-card, #1c1d2e); border: 1px solid var(--border-color, rgba(255,255,255,0.07)); border-radius: 14px; padding: 40px; text-align: center; color: var(--text-muted);">
                    <i class="bi bi-cart-x" style="font-size: 48px; color: var(--pink-accent, #FE2C55); display: block; margin-bottom: 12px;"></i>
                    <h3 style="color: #fff; font-size: 18px; margin-bottom: 8px;">Your Shopping Cart is Empty</h3>
                    <p style="font-size: 14px; margin-bottom: 20px;">Discover trending items, live shopping auctions, and exclusive creator drops.</p>
                    <a href="{{ route('shop.index') }}" class="btn-shop-now-cyan" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; border-radius: 8px; font-weight: 700;">
                        <i class="bi bi-bag-plus-fill"></i> Explore Live Shopping
                    </a>
                </div>
            @endforelse

        </div>

        <!-- RIGHT COLUMN: ORDER SUMMARY CARD -->
        <div class="order-summary-card">
            <h2 class="summary-title">Order Summary</h2>

            <!-- Calculation Lines Table -->
            <div class="summary-calc-table">
                <div class="summary-calc-row">
                    <span>Subtotal</span>
                    <span id="summarySubtotal">{{ setting('currency_symbol', '$') }}{{ number_format($subtotal, 2) }}</span>
                </div>

                <!-- Discount Row -->
                <div class="summary-calc-row" id="summaryDiscountRow" style="{{ $discount > 0 ? '' : 'display: none;' }}; color: #00F0C8;">
                    <span>Discount (<span id="summaryCouponCode">{{ $couponSession['code'] ?? '' }}</span>)</span>
                    <span id="summaryDiscount">-{{ setting('currency_symbol', '$') }}{{ number_format($discount, 2) }}</span>
                </div>

                <div class="summary-calc-row">
                    <span>Shipping</span>
                    <span class="free-text">Free</span>
                </div>
                <div class="summary-calc-row">
                    <span>GST / HST (13%)</span>
                    <span id="summaryTax">{{ setting('currency_symbol', '$') }}{{ number_format($tax, 2) }}</span>
                </div>
                
                <div class="summary-divider"></div>

                <div class="summary-calc-row total-row">
                    <span>Total</span>
                    <span id="summaryTotal">{{ setting('currency_symbol', '$') }}{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <!-- Applied Coupon Display -->
            <div id="appliedCouponBox" style="{{ $couponSession ? 'display: flex;' : 'display: none;' }} justify-content-between; align-items: center; background: rgba(0, 240, 200, 0.1); border: 1px solid #00F0C8; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-ticket-perforated-fill" style="color: #00F0C8; font-size: 16px;"></i>
                    <div>
                        <div style="font-weight: 800; font-family: monospace; color: #00F0C8; font-size: 13px;" id="appliedCouponTag">{{ $couponSession['code'] ?? '' }}</div>
                        <small style="color: var(--text-muted); font-size: 11px;">Coupon applied</small>
                    </div>
                </div>
                <button type="button" onclick="handleRemoveCoupon()" title="Remove Coupon" style="background: none; border: none; color: #FE2C55; font-size: 18px; cursor: pointer; padding: 0;">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </div>

            <!-- Promo Code Input Wrap -->
            <div class="promo-code-wrap" id="promoInputWrap" style="{{ $couponSession ? 'display: none;' : '' }}">
                <div class="promo-input-group">
                    <i class="bi bi-tag"></i>
                    <input type="text" placeholder="Enter coupon (e.g. WELCOME10)" class="promo-input" id="promoCodeInput" style="text-transform: uppercase;">
                </div>
                <button type="button" class="btn-apply-promo" id="btnApplyCoupon" onclick="handleApplyPromoCode()">Apply</button>
            </div>
            <div id="promoFeedbackMsg" style="display: none; font-size: 12px; margin-top: 6px; padding: 4px 8px; border-radius: 4px;"></div>

            <!-- Shipping Methods Section -->
            <div class="shipping-methods-section" style="margin-top: 20px;">
                <h3 class="shipping-section-title">Shipping Methods</h3>
                
                <label class="shipping-option-card active" onclick="selectShippingOption(this)">
                    <div class="shipping-option-left">
                        <input type="radio" name="shipMethod" checked>
                        <span>Standard Delivery</span>
                    </div>
                    <span class="ship-price-val free-text">Free</span>
                </label>

                <label class="shipping-option-card" onclick="selectShippingOption(this)">
                    <div class="shipping-option-left">
                        <input type="radio" name="shipMethod">
                        <span>Express Priority Delivery</span>
                    </div>
                    <span class="ship-price-val">Free</span>
                </label>
            </div>

            <!-- Total Payment Box -->
            <div class="total-pay-box">
                <span class="total-pay-label">Total Payment Amount</span>
                <span class="total-pay-val" id="summaryPayTotal">{{ setting('currency_symbol', '$') }}{{ number_format($total, 2) }}</span>
            </div>

            <!-- Checkout Button -->
            @if(count($cart) > 0)
                <a href="{{ route('shop.checkout') }}" class="btn-checkout-cyan" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                    Go To Checkout <i class="bi bi-arrow-right" style="margin-left: 6px;"></i>
                </a>
            @else
                <a href="{{ route('shop.index') }}" class="btn-checkout-cyan" style="text-decoration: none; display: flex; align-items: center; justify-content: center; opacity: 0.6;">
                    Browse Shop
                </a>
            @endif
        </div>

    </div>

</main>

<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
        <!-- Center Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset(setting('site_logo', 'assets/logo.png')) }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="footer-logo-img">
        </a>
        <p class="footer-tagline-text">
            Experience the future of shopping with realtime interaction, live demonstrations, and exclusive community deals.
        </p>
        <div class="footer-copyright-line">
            {{ setting('copyright_text', '© 2026 Zaldoris Live Commerce Ltd. All rights reserved.') }}
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
    const currencySymbol = '{{ setting("currency_symbol", "$") }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Quantity Stepper Handler
    function updateItemQty(btn, change, productId) {
        const stepper = btn.parentElement;
        const numSpan = stepper.querySelector('.step-num');
        if (!numSpan || !productId) return;

        let current = parseInt(numSpan.textContent) || 1;
        current += change;
        if (current < 1) current = 1;
        numSpan.textContent = current;

        fetch('/cart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId, quantity: current })
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                updateSummaryDisplay(data);
            }
        })
        .catch(err => console.error('Cart update error:', err));
    }

    // Remove Item Handler
    function removeCartCard(btn, productId) {
        const card = btn.closest('.cart-item-card');
        if (!card || !productId) return;

        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        card.style.transition = 'all 0.25s ease';

        fetch(`/cart/remove/${productId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            setTimeout(() => {
                card.remove();
                if (data && data.success) {
                    updateSummaryDisplay(data);
                    if (data.cart_count === 0) {
                        window.location.reload();
                    }
                }
            }, 200);
        })
        .catch(err => console.error('Cart remove error:', err));
    }

    function updateSummaryDisplay(data) {
        const subtotalEl = document.getElementById('summarySubtotal');
        const taxEl = document.getElementById('summaryTax');
        const totalEl = document.getElementById('summaryTotal');
        const payTotalEl = document.getElementById('summaryPayTotal');
        const badge = document.getElementById('globalCartBadge');
        const discountRow = document.getElementById('summaryDiscountRow');
        const discountVal = document.getElementById('summaryDiscount');
        const couponTag = document.getElementById('summaryCouponCode');
        const appliedBox = document.getElementById('appliedCouponBox');
        const appliedTag = document.getElementById('appliedCouponTag');
        const inputWrap = document.getElementById('promoInputWrap');

        if (subtotalEl) subtotalEl.textContent = currencySymbol + data.subtotal;
        if (taxEl) taxEl.textContent = currencySymbol + data.tax;
        if (totalEl) totalEl.textContent = currencySymbol + data.total;
        if (payTotalEl) payTotalEl.textContent = currencySymbol + data.total;

        if (data.discount && parseFloat(data.discount) > 0) {
            if (discountRow) discountRow.style.display = 'flex';
            if (discountVal) discountVal.textContent = '-' + currencySymbol + data.discount;
            if (couponTag && data.coupon) couponTag.textContent = data.coupon.code;
            if (appliedBox) appliedBox.style.display = 'flex';
            if (appliedTag && data.coupon) appliedTag.textContent = data.coupon.code;
            if (inputWrap) inputWrap.style.display = 'none';
        } else {
            if (discountRow) discountRow.style.display = 'none';
            if (appliedBox) appliedBox.style.display = 'none';
            if (inputWrap) inputWrap.style.display = 'flex';
        }

        if (badge) {
            badge.textContent = data.cart_count || 0;
            badge.style.transform = 'scale(1.3)';
            setTimeout(() => badge.style.transform = 'scale(1)', 200);
        }
    }

    // Shipping Option Switch Handler
    function selectShippingOption(selectedCard) {
        document.querySelectorAll('.shipping-option-card').forEach(card => card.classList.remove('active'));
        selectedCard.classList.add('active');
        const radio = selectedCard.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    // Promo Code Apply Handler
    function handleApplyPromoCode() {
        const input = document.getElementById('promoCodeInput');
        const btn = document.getElementById('btnApplyCoupon');
        const feedback = document.getElementById('promoFeedbackMsg');
        const code = input ? input.value.trim() : '';

        if (!code) {
            showPromoFeedback('Please enter a promo code.', false);
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        fetch('/cart/coupon/apply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: code })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Apply';

            if (data && data.success) {
                showPromoFeedback(data.message, true);
                updateSummaryDisplay(data);
                if (input) input.value = '';
            } else {
                showPromoFeedback(data.message || 'Invalid coupon code.', false);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = 'Apply';
            showPromoFeedback('Failed to apply coupon. Please try again.', false);
        });
    }

    // Promo Code Remove Handler
    function handleRemoveCoupon() {
        fetch('/cart/coupon/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                showPromoFeedback('Coupon removed', true);
                updateSummaryDisplay(data);
            }
        })
        .catch(err => console.error('Coupon remove error:', err));
    }

    function showPromoFeedback(msg, isSuccess) {
        const feedback = document.getElementById('promoFeedbackMsg');
        if (!feedback) return;

        feedback.textContent = msg;
        feedback.style.display = 'block';
        feedback.style.background = isSuccess ? 'rgba(0, 240, 200, 0.15)' : 'rgba(254, 44, 85, 0.15)';
        feedback.style.color = isSuccess ? '#00F0C8' : '#FE2C55';
        feedback.style.border = isSuccess ? '1px solid #00F0C8' : '1px solid #FE2C55';

        setTimeout(() => {
            feedback.style.display = 'none';
        }, 4000);
    }
</script>
</body>
</html>
