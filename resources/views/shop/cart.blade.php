<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Zaldoris - Cart & Checkout Summary. View added items, shipping methods, and order calculations.">
    <title>All Add to Cart List - Zaldoris Live Commerce Platform</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.png') }}">
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
            <img src="{{ asset('assets/logo.png') }}" alt="Zaldoris" class="zal-brand-logo">
        </a>

        <!-- Center Nav Links -->
        <ul class="zal-nav-menu">
            <li><a href="{{ route('home') }}" class="zal-nav-link active">Home</a></li>
            <li><a href="{{ route('shop.index') }}" class="zal-nav-link">Live Shopping</a></li>
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
                    <span class="icon-badge-num" id="globalCartBadge">2</span>
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
        <a href="{{ route('shop.product', 1) }}" class="notif-back-btn" title="Back to Product Details">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="notif-page-title">All Add to Cart List</h1>
    </div>

    <!-- TWO-COLUMN CART & SUMMARY GRID -->
    <div class="cart-page-grid">

        <!-- LEFT COLUMN: CART ITEMS LIST -->
        <div class="cart-items-column" id="cartItemsContainer">

            <!-- Cart Item 1 -->
            <div class="cart-item-card">
                <div class="cart-item-top">
                    <div class="cart-item-left">
                        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200&auto=format&fit=crop&q=80" alt="Titanium Smartwatch" class="cart-item-thumb">
                        <div class="cart-item-info">
                            <h2 class="cart-item-title">Titanium</h2>
                            <h2 class="cart-item-title">Smartwatch</h2>
                        </div>
                    </div>
                    <div class="cart-item-right">
                        <button type="button" class="btn-delete-cart-item" title="Remove Item" onclick="removeCartCard(this)">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                        <span class="cart-item-price">C$299.00</span>
                    </div>
                </div>

                <div class="cart-item-bottom">
                    <span class="cart-qty-label">Quantity</span>
                    <div class="quantity-stepper">
                        <button type="button" class="btn-step" onclick="updateItemQty(this, -1)">—</button>
                        <span class="step-num">1</span>
                        <button type="button" class="btn-step" onclick="updateItemQty(this, 1)">+</button>
                    </div>
                </div>
            </div>

            <!-- Cart Item 2 -->
            <div class="cart-item-card">
                <div class="cart-item-top">
                    <div class="cart-item-left">
                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&auto=format&fit=crop&q=80" alt="Titanium Smartwatch" class="cart-item-thumb">
                        <div class="cart-item-info">
                            <h2 class="cart-item-title">Titanium</h2>
                            <h2 class="cart-item-title">Smartwatch</h2>
                        </div>
                    </div>
                    <div class="cart-item-right">
                        <button type="button" class="btn-delete-cart-item" title="Remove Item" onclick="removeCartCard(this)">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                        <span class="cart-item-price">C$200.00</span>
                    </div>
                </div>

                <div class="cart-item-bottom">
                    <span class="cart-qty-label">Quantity</span>
                    <div class="quantity-stepper">
                        <button type="button" class="btn-step" onclick="updateItemQty(this, -1)">—</button>
                        <span class="step-num">1</span>
                        <button type="button" class="btn-step" onclick="updateItemQty(this, 1)">+</button>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: ORDER SUMMARY CARD -->
        <div class="order-summary-card">
            <h2 class="summary-title">Order Summary</h2>

            <!-- Calculation Lines Table -->
            <div class="summary-calc-table">
                <div class="summary-calc-row">
                    <span>Subtotal</span>
                    <span>C$899.00</span>
                </div>
                <div class="summary-calc-row discount-row">
                    <span>Discount (First Buy)</span>
                    <span>-C$50.00</span>
                </div>
                <div class="summary-calc-row">
                    <span>Processing Fee</span>
                    <span>C$12.50</span>
                </div>
                <div class="summary-calc-row">
                    <span>Platform Fee</span>
                    <span>C$12.50</span>
                </div>
                <div class="summary-calc-row">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                <div class="summary-calc-row">
                    <span>GST / HST / PST</span>
                    <span>C$111.93</span>
                </div>
                
                <div class="summary-divider"></div>

                <div class="summary-calc-row total-row">
                    <span>Total</span>
                    <span>C$973.43</span>
                </div>
            </div>

            <!-- Promo Code Input Wrap -->
            <div class="promo-code-wrap">
                <div class="promo-input-group">
                    <i class="bi bi-tag"></i>
                    <input type="text" placeholder="Add promo code" class="promo-input" id="promoCodeInput">
                </div>
                <button type="button" class="btn-apply-promo" onclick="handleApplyPromoCode()">Apply</button>
            </div>

            <!-- Shipping Methods Section -->
            <div class="shipping-methods-section">
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
                        <span>Express Delivery</span>
                    </div>
                    <span class="ship-price-val">+C$25.00</span>
                </label>
            </div>

            <!-- Total Payment Box -->
            <div class="total-pay-box">
                <span class="total-pay-label">Total Payment Amount</span>
                <span class="total-pay-val">C$973.43</span>
            </div>

            <!-- Checkout Button -->
            <button type="button" class="btn-checkout-cyan" onclick="handleProceedCheckout()">
                Go To Checkout
            </button>
        </div>

    </div>

</main>

<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
        <!-- Center Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/logo.png') }}" alt="Zaldoris" class="footer-logo-img">
        </a>
        <p class="footer-tagline-text">
            Experience the future of shopping with realtime interaction, live demonstrations, and exclusive community deals.
        </p>
        <div class="footer-copyright-line">
            © 2024 LiveStreamShop. All rights reserved.
        </div>
    </div>
</footer>

<!-- FLOATING WIDGET BUTTON -->
<button class="floating-action-widget" title="Live Chat">
    <i class="bi bi-chat-dots-fill"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
    // Quantity Stepper Handler
    function updateItemQty(btn, change) {
        const stepper = btn.parentElement;
        const numSpan = stepper.querySelector('.step-num');
        if (numSpan) {
            let current = parseInt(numSpan.textContent) || 1;
            current += change;
            if (current < 1) current = 1;
            numSpan.textContent = current;
        }
    }

    // Remove Item Handler
    function removeCartCard(btn) {
        const card = btn.closest('.cart-item-card');
        if (card) {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.remove();
            }, 200);
        }
    }

    // Shipping Option Switch Handler
    function selectShippingOption(selectedCard) {
        document.querySelectorAll('.shipping-option-card').forEach(card => card.classList.remove('active'));
        selectedCard.classList.add('active');
        const radio = selectedCard.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    // Promo Code Handler
    function handleApplyPromoCode() {
        const input = document.getElementById('promoCodeInput');
        if (input && input.value.trim() !== '') {
            alert(`Promo code '${input.value.trim()}' applied successfully!`);
            input.value = '';
        }
    }

    // Checkout Handler
    function handleProceedCheckout() {
        alert('Proceeding to Checkout Gateway...');
    }
</script>
</body>
</html>
