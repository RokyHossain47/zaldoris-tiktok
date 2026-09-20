<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Zaldoris - Checkout Page. Review order details, select shipping methods, and complete your purchase securely.">
    <title>Checkout - {{ setting('site_name', 'Zaldoris Live Commerce Platform') }}</title>
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
        <a href="{{ route('shop.cart') }}" class="notif-back-btn" title="Back to Cart">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="notif-page-title">Secure Checkout</h1>
    </div>

    @if($errors->any())
        <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid #FE2C55; color: #FE2C55; padding: 14px 18px; border-radius: 10px; font-size: 13px; margin-bottom: 24px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- TWO-COLUMN CHECKOUT GRID -->
    <form action="{{ route('shop.checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        <input type="hidden" name="coupon_code" id="hiddenCouponCode" value="{{ $couponSession['code'] ?? '' }}">

        <div class="checkout-page-grid">

            <!-- LEFT COLUMN: SHIPPING ADDRESS & ORDER ITEMS -->
            <div class="cart-items-column">

                <!-- Shipping Address Card -->
                <div class="cart-item-card" style="display: block; padding: 24px; margin-bottom: 20px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-geo-alt-fill" style="color: #00F0C8;"></i> Shipping & Delivery Address
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Street Address *</label>
                            <input type="text" name="street" value="{{ old('street', '100 King Street West') }}" placeholder="123 Main St" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 9px 12px; border-radius: 8px; font-size: 13px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">City *</label>
                            <input type="text" name="city" value="{{ old('city', 'Toronto') }}" placeholder="City" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 9px 12px; border-radius: 8px; font-size: 13px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Province / State *</label>
                            <input type="text" name="province" value="{{ old('province', 'Ontario') }}" placeholder="ON" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 9px 12px; border-radius: 8px; font-size: 13px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Postal / ZIP Code *</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', 'M5X 1A9') }}" placeholder="M5X 1A9" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 9px 12px; border-radius: 8px; font-size: 13px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Country *</label>
                            <input type="text" name="country" value="{{ old('country', 'Canada') }}" placeholder="Country" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 9px 12px; border-radius: 8px; font-size: 13px;">
                        </div>
                    </div>
                </div>

                <!-- Payment Method Card -->
                <div class="cart-item-card" style="display: block; padding: 24px; margin-bottom: 20px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-credit-card-2-front-fill" style="color: #00F0C8;"></i> Payment Method
                    </h3>

                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 8px; background: rgba(0, 240, 200, 0.1); border: 1px solid #00F0C8; padding: 10px 16px; border-radius: 8px; cursor: pointer; color: #fff; font-size: 13px; font-weight: 600;">
                            <input type="radio" name="payment_method" value="stripe_card" checked style="accent-color: #00F0C8;">
                            <span>Credit / Debit Card (Stripe)</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; background: var(--bg-card-inner); border: 1px solid var(--border-color); padding: 10px 16px; border-radius: 8px; cursor: pointer; color: #fff; font-size: 13px; font-weight: 600;">
                            <input type="radio" name="payment_method" value="apple_pay" style="accent-color: #00F0C8;">
                            <span>Apple Pay / Google Pay</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; background: var(--bg-card-inner); border: 1px solid var(--border-color); padding: 10px 16px; border-radius: 8px; cursor: pointer; color: #fff; font-size: 13px; font-weight: 600;">
                            <input type="radio" name="payment_method" value="escrow_balance" style="accent-color: #00F0C8;">
                            <span>Escrow Wallet Balance</span>
                        </label>
                    </div>
                </div>

                <!-- Items Review List -->
                <div style="margin-bottom: 12px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 14px;">Review Items ({{ count($cart) }})</h3>
                </div>

                @php $itemIndex = 0; @endphp
                @forelse($cart as $id => $item)
                    <div class="cart-item-card" style="margin-bottom: 14px;">
                        <input type="hidden" name="items[{{ $itemIndex }}][product_id]" value="{{ $item['id'] ?? $id }}">
                        <input type="hidden" name="items[{{ $itemIndex }}][quantity]" value="{{ $item['quantity'] }}">
                        <input type="hidden" name="items[{{ $itemIndex }}][unit_price]" value="{{ $item['price'] }}">
                        <input type="hidden" name="items[{{ $itemIndex }}][selected_color]" value="{{ $item['selected_color'] ?? '' }}">
                        <input type="hidden" name="items[{{ $itemIndex }}][selected_size]" value="{{ $item['selected_size'] ?? '' }}">
                        <input type="hidden" name="items[{{ $itemIndex }}][image]" value="{{ $item['image'] ?? '' }}">

                        <div class="cart-item-top">
                            <div class="cart-item-left">
                                <img src="{{ $item['image'] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200' }}" alt="{{ $item['title'] }}" class="cart-item-thumb">
                                <div class="cart-item-info">
                                    <h2 class="cart-item-title">{{ Str::limit($item['title'], 36) }}</h2>
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 4px;">
                                        @if(!empty($item['selected_color']))
                                            <span style="background: rgba(0, 240, 200, 0.12); color: #00F0C8; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">
                                                {{ $item['selected_color'] }}
                                            </span>
                                        @endif
                                        @if(!empty($item['selected_size']))
                                            <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px;">
                                                {{ $item['selected_size'] }}
                                            </span>
                                        @endif
                                        <span style="font-size: 12px; color: var(--text-muted);">Qty: {{ $item['quantity'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="cart-item-right">
                                <span class="cart-item-price">{{ setting('currency_symbol', '$') }}{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    @php $itemIndex++; @endphp
                @empty
                    <div class="cart-item-card" style="text-align: center; padding: 36px;">
                        <p style="color: var(--text-muted); margin-bottom: 12px;">Your shopping cart is currently empty.</p>
                        <a href="{{ route('shop.index') }}" style="color: #00F0C8; font-weight: 700;">Continue Shopping</a>
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
                        <input type="text" placeholder="Promo code (e.g. WELCOME10)" class="promo-input" id="checkoutPromoInput" style="text-transform: uppercase;">
                    </div>
                    <button type="button" class="btn-apply-promo" id="btnApplyCoupon" onclick="handleApplyPromoCode()">Apply</button>
                </div>
                <div id="promoFeedbackMsg" style="display: none; font-size: 12px; margin-top: 6px; padding: 4px 8px; border-radius: 4px;"></div>

                <!-- Total Payment Box -->
                <div class="total-pay-box" style="margin-top: 20px;">
                    <span class="total-pay-label">Total Payment Amount</span>
                    <span class="total-pay-val" id="summaryPayTotal">{{ setting('currency_symbol', '$') }}{{ number_format($total, 2) }}</span>
                </div>

                <!-- Complete Purchase Submit Button -->
                @if(count($cart) > 0)
                    <button type="submit" class="btn-checkout-cyan" style="border: none; cursor: pointer; width: 100%;">
                        <i class="bi bi-lock-fill" style="margin-right: 6px;"></i> Place Order & Pay
                    </button>
                @else
                    <a href="{{ route('shop.index') }}" class="btn-checkout-cyan" style="text-decoration: none; display: flex; align-items: center; justify-content: center; opacity: 0.6;">
                        Browse Products
                    </a>
                @endif
            </div>

        </div>
    </form>

</main>

<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
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

    function updateSummaryDisplay(data) {
        const subtotalEl = document.getElementById('summarySubtotal');
        const taxEl = document.getElementById('summaryTax');
        const totalEl = document.getElementById('summaryTotal');
        const payTotalEl = document.getElementById('summaryPayTotal');
        const discountRow = document.getElementById('summaryDiscountRow');
        const discountVal = document.getElementById('summaryDiscount');
        const couponTag = document.getElementById('summaryCouponCode');
        const appliedBox = document.getElementById('appliedCouponBox');
        const appliedTag = document.getElementById('appliedCouponTag');
        const inputWrap = document.getElementById('promoInputWrap');
        const hiddenCode = document.getElementById('hiddenCouponCode');

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
            if (hiddenCode && data.coupon) hiddenCode.value = data.coupon.code;
        } else {
            if (discountRow) discountRow.style.display = 'none';
            if (appliedBox) appliedBox.style.display = 'none';
            if (inputWrap) inputWrap.style.display = 'flex';
            if (hiddenCode) hiddenCode.value = '';
        }
    }

    function handleApplyPromoCode() {
        const input = document.getElementById('checkoutPromoInput');
        const btn = document.getElementById('btnApplyCoupon');
        const code = input ? input.value.trim() : '';

        if (!code) {
            showPromoFeedback('Please enter a coupon code.', false);
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
