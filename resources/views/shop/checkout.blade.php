<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Zaldoris - Payment Details. Complete your order securely with instant payment and address verification.">
    <title>Payment Details - {{ setting('site_name', 'Zaldoris Live Commerce') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}">
    <link rel="shortcut icon" href="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Bootstrap CSS for modals -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    <style>
        body {
            background-color: #080C10 !important;
            color: #E2E8F0;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* Top Navbar */
        .zal-navbar-floating-wrap {
            max-width: 1200px;
            margin: 16px auto 0;
            padding: 0 16px;
        }
        .zal-navbar-floating {
            background: rgba(18, 24, 32, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 40px;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .zal-brand-logo {
            height: 32px;
            object-fit: contain;
        }
        .zal-nav-links-wrap {
            display: flex;
            align-items: center;
            gap: 24px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .zal-nav-links-wrap a {
            color: #94A3B8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .zal-nav-links-wrap a:hover,
        .zal-nav-links-wrap a.active {
            color: #00F0C8;
            font-weight: 700;
        }
        .zal-nav-actions-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .zal-nav-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            position: relative;
            transition: all 0.2s ease;
        }
        .zal-nav-btn:hover {
            background: rgba(0, 240, 200, 0.12);
            color: #00F0C8;
            border-color: #00F0C8;
        }
        .zal-nav-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid #00F0C8;
            object-fit: cover;
        }

        /* Payment Details Centered Content */
        .payment-page-container {
            max-width: 580px;
            margin: 28px auto 60px;
            padding: 0 16px;
        }

        .payment-header-row {
            display: flex;
            align-items: center;
            position: relative;
            margin-bottom: 26px;
        }
        .btn-circle-back {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #121820;
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-decoration: none;
            transition: all 0.2s ease;
            position: absolute;
            left: 0;
        }
        .btn-circle-back:hover {
            background: rgba(0, 240, 200, 0.15);
            color: #00F0C8;
            border-color: #00F0C8;
        }
        .payment-main-title {
            width: 100%;
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.3px;
        }

        /* Common Dark Card */
        .zal-dark-card {
            background: #11161D;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 18px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            transition: border-color 0.25s ease;
        }
        .zal-dark-card.card-error {
            border-color: #FE2C55 !important;
            box-shadow: 0 0 15px rgba(254, 44, 85, 0.3) !important;
        }

        /* Shipping Address Card */
        .address-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .address-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
        }
        .address-pin-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #94A3B8;
            font-size: 13px;
        }
        .address-change-btn {
            color: #00F0C8;
            font-size: 13px;
            font-weight: 600;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: opacity 0.2s ease;
        }
        .address-change-btn:hover {
            opacity: 0.8;
            text-decoration: underline;
        }
        .address-details-body {
            padding-left: 38px;
        }
        .address-user-name {
            font-size: 14px;
            font-weight: 600;
            color: #F1F5F9;
            margin-bottom: 3px;
        }
        .address-user-phone {
            font-size: 13px;
            color: #8E9AA8;
            margin-bottom: 3px;
        }
        .address-user-street {
            font-size: 13px;
            color: #8E9AA8;
            line-height: 1.4;
        }

        /* Empty Address Prompt Box */
        .empty-address-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .empty-address-box:hover {
            border-color: #00F0C8;
            background: rgba(0, 240, 200, 0.04);
        }
        .btn-add-addr-cyan {
            background: #00F0C8;
            color: #000;
            border: none;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        /* Order Summary Card */
        .summary-card-label {
            font-size: 12px;
            font-weight: 700;
            color: #8E9AA8;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        .summary-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding-bottom: 14px;
            margin-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .summary-item-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }
        .summary-item-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .summary-item-thumb {
            width: 58px;
            height: 58px;
            border-radius: 10px;
            object-fit: cover;
            background: #18202A;
            border: 1px solid rgba(255, 255, 255, 0.05);
            flex-shrink: 0;
        }
        .summary-item-title {
            font-size: 14px;
            font-weight: 700;
            color: #FFFFFF;
            margin: 0 0 4px;
            line-height: 1.3;
        }
        .summary-item-qty {
            font-size: 12px;
            color: #8E9AA8;
        }
        .summary-item-price {
            color: #00F0C8;
            font-size: 16px;
            font-weight: 800;
            white-space: nowrap;
        }

        /* Total Payment Amount Card */
        .total-payment-card {
            background: #11161D;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .total-payment-label {
            font-size: 14px;
            font-weight: 600;
            color: #FFFFFF;
        }
        .total-payment-val {
            font-size: 18px;
            font-weight: 900;
            color: #00F0C8;
        }

        /* Payment Methods Section */
        .pay-methods-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .pay-methods-title {
            font-size: 16px;
            font-weight: 700;
            color: #FFFFFF;
            margin: 0;
        }
        .pay-methods-badge {
            color: #00F0C8;
            font-size: 12px;
            font-weight: 700;
        }

        .pay-methods-card {
            background: #11161D;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            padding: 4px 18px;
            margin-bottom: 24px;
        }
        .pay-option-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 4px;
            cursor: pointer;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            transition: background 0.2s ease;
        }
        .pay-option-row:last-child {
            border-bottom: none;
        }
        .pay-option-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .pay-icon-wrap {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #E2E8F0;
            font-size: 16px;
        }
        .pay-option-name {
            font-size: 14px;
            font-weight: 600;
            color: #FFFFFF;
        }
        
        /* Custom Cyan Radio Button */
        .custom-radio-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #4B5563;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .pay-option-row.active .custom-radio-dot {
            border-color: #00F0C8;
            background: #00F0C8;
        }
        .custom-radio-dot-inner {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #080C10;
            display: none;
        }
        .pay-option-row.active .custom-radio-dot-inner {
            display: block;
        }

        /* Big Bright Cyan Confirm Purchase Button */
        .btn-confirm-purchase {
            background: #00F0C8;
            color: #000000;
            border: none;
            border-radius: 12px;
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 20px rgba(0, 240, 200, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-confirm-purchase:hover {
            background: #1ed6d0;
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 240, 200, 0.4);
            color: #000000;
        }
        .btn-confirm-purchase:active {
            transform: translateY(0);
        }

        /* Footer */
        .payment-footer {
            text-align: center;
            padding: 40px 16px 20px;
        }
        .payment-footer-logo {
            height: 36px;
            object-fit: contain;
            margin-bottom: 12px;
        }
        .payment-footer-tagline {
            color: #64748B;
            font-size: 13px;
            max-width: 480px;
            margin: 0 auto 16px;
            line-height: 1.5;
        }
        .payment-footer-copy {
            color: #475569;
            font-size: 12px;
        }

        /* Floating Chat Widget */
        .floating-chat-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #00F0C8;
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 6px 20px rgba(0, 240, 200, 0.35);
            cursor: pointer;
            z-index: 9999;
            transition: all 0.25s ease;
        }
        .floating-chat-btn:hover {
            transform: scale(1.1);
            background: #1ed6d0;
        }

        /* Address Edit Modal Dark Styling */
        .modal-content.dark-modal {
            background: #121820;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 16px;
        }
        .modal-header.dark-modal {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .modal-footer.dark-modal {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        .form-control-dark {
            background: #18202A !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            border-radius: 8px;
            font-size: 13px;
            padding: 10px 12px;
        }
        .form-control-dark:focus {
            border-color: #00F0C8 !important;
            box-shadow: 0 0 0 2px rgba(0, 240, 200, 0.2) !important;
        }

        .saved-address-choice {
            background: rgba(255,255,255,0.03);
            border: 1.5px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 12px 14px;
            cursor: pointer;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }
        .saved-address-choice:hover,
        .saved-address-choice.active {
            border-color: #00F0C8;
            background: rgba(0, 240, 200, 0.06);
        }
    </style>
</head>
<body>

<!-- FLOATING NAVBAR -->
<div class="zal-navbar-floating-wrap">
    <header class="zal-navbar-floating">
        <!-- Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset(setting('site_logo', 'assets/logo.png')) }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="zal-brand-logo">
        </a>

        <!-- Center Nav Links -->
        <ul class="zal-nav-links-wrap d-none d-md-flex">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('shop.index') }}" class="active">Live Shopping</a></li>
            <li><a href="{{ route('auctions.index') }}">Live Auction</a></li>
            <li><a href="#">Live Academy</a></li>
            <li><a href="{{ route('streams.index') }}">Live Streaming</a></li>
            <li><a href="{{ route('streams.pk_battle', 1) }}">PK Battle</a></li>
        </ul>

        <!-- Right Action Icons -->
        <div class="zal-nav-actions-wrap">
            <a href="{{ route('search') }}" class="zal-nav-btn" title="Search"><i class="bi bi-search"></i></a>
            <a href="{{ route('wallet.coins') }}" class="zal-nav-btn" title="Wallet"><i class="bi bi-wallet2"></i></a>
            <a href="{{ route('notifications') }}" class="zal-nav-btn" title="Notifications">
                <i class="bi bi-bell"></i>
                <span style="position: absolute; top: 6px; right: 6px; width: 6px; height: 6px; background: #FE2C55; border-radius: 50%;"></span>
            </a>
            <a href="{{ route('shop.cart') }}" class="zal-nav-btn" title="Cart">
                <i class="bi bi-cart3"></i>
                <span class="icon-badge-num" id="globalCartBadge" style="position: absolute; top: -4px; right: -4px; background: #00F0C8; color: #000; font-size: 10px; font-weight: 800; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">{{ array_sum(array_column(session('cart', []), 'quantity')) ?: 0 }}</span>
            </a>
            @auth
                <a href="{{ route('dashboard.creator') }}" title="My Account">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&auto=format&fit=crop&q=80' }}" alt="{{ auth()->user()->name }}" class="zal-nav-avatar">
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-confirm-purchase" style="padding: 6px 16px; font-size: 13px; border-radius: 20px; width: auto;">Log In</a>
            @endauth
        </div>
    </header>
</div>

<!-- MAIN PAYMENT DETAILS CONTAINER -->
<main class="payment-page-container">

    <!-- Top Row with Circular Back Button & Title -->
    <div class="payment-header-row">
        <a href="{{ route('shop.cart') }}" class="btn-circle-back" title="Back to Cart">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="payment-main-title">Payment Details</h1>
    </div>

    @if(isset($errors) && $errors->any())
        <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid #FE2C55; color: #FE2C55; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="addressAlertBanner" style="display: none; background: rgba(254, 44, 85, 0.15); border: 1px solid #FE2C55; color: #FE2C55; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right: 6px;"></i>
        Please add or select a shipping delivery address to complete your purchase.
    </div>

    @php
        $hasDefault = !empty($defaultAddress);
        $initName = $defaultAddress->recipient_name ?? (auth()->user()->name ?? '');
        $initPhone = $defaultAddress->phone ?? (auth()->user()->phone ?? '');
        $initStreet = $defaultAddress->address_line1 ?? '';
        $initCity = $defaultAddress->city ?? '';
        $initProvince = $defaultAddress->region ?? '';
        $initPostal = $defaultAddress->postal_code ?? '';
        $initCountry = $defaultAddress->country ?? 'Canada';
    @endphp

    <form action="{{ route('shop.checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        
        <!-- Hidden Item inputs for backend validation -->
        @php $itemIndex = 0; @endphp
        @forelse($cart as $id => $item)
            <input type="hidden" name="items[{{ $itemIndex }}][product_id]" value="{{ $item['id'] ?? $id }}">
            <input type="hidden" name="items[{{ $itemIndex }}][quantity]" value="{{ $item['quantity'] }}">
            <input type="hidden" name="items[{{ $itemIndex }}][unit_price]" value="{{ $item['price'] }}">
            <input type="hidden" name="items[{{ $itemIndex }}][selected_color]" value="{{ $item['selected_color'] ?? '' }}">
            <input type="hidden" name="items[{{ $itemIndex }}][selected_size]" value="{{ $item['selected_size'] ?? '' }}">
            <input type="hidden" name="items[{{ $itemIndex }}][image]" value="{{ $item['image'] ?? '' }}">
            @php $itemIndex++; @endphp
        @empty
        @endforelse

        <!-- Hidden Address Inputs -->
        <input type="hidden" name="recipient_name" id="inputRecipientName" value="{{ old('recipient_name', $initName) }}">
        <input type="hidden" name="phone" id="inputPhone" value="{{ old('phone', $initPhone) }}">
        <input type="hidden" name="street" id="inputStreet" value="{{ old('street', $initStreet) }}">
        <input type="hidden" name="city" id="inputCity" value="{{ old('city', $initCity) }}">
        <input type="hidden" name="province" id="inputProvince" value="{{ old('province', $initProvince) }}">
        <input type="hidden" name="postal_code" id="inputPostalCode" value="{{ old('postal_code', $initPostal) }}">
        <input type="hidden" name="country" id="inputCountry" value="{{ old('country', $initCountry) }}">
        <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="stripe_card">
        <input type="hidden" name="shipping_method" value="{{ $selectedShippingMethod['id'] ?? 'standard_delivery' }}">
        <input type="hidden" name="coupon_code" value="{{ $couponSession['code'] ?? '' }}">

        <!-- CARD 1: SHIPPING ADDRESS -->
        <div class="zal-dark-card" id="shippingAddressCard">
            <div class="address-card-header">
                <div class="address-header-left">
                    <span class="address-pin-icon"><i class="bi bi-geo-alt"></i></span>
                    <span>Shipping Address</span>
                </div>
                <button type="button" class="address-change-btn" id="btnChangeAddress" style="{{ $hasDefault ? 'display: inline-block;' : 'display: none;' }}" data-bs-toggle="modal" data-bs-target="#addressModal">Change</button>
            </div>

            <!-- Empty Address State (Shown when no default address) -->
            <div id="emptyAddressBox" class="empty-address-box" style="{{ $hasDefault ? 'display: none;' : 'display: flex;' }}" data-bs-toggle="modal" data-bs-target="#addressModal">
                <div class="empty-address-left" style="display: flex; align-items: center; gap: 12px;">
                    <i class="bi bi-plus-circle" style="font-size: 22px; color: #00F0C8;"></i>
                    <div>
                        <div style="font-size: 14px; font-weight: 700; color: #fff;">No Shipping Address Added</div>
                        <small style="color: #8E9AA8; font-size: 12px;">Click here to add your delivery address</small>
                    </div>
                </div>
                <button type="button" class="btn-add-addr-cyan">
                    <i class="bi bi-plus-lg"></i> Add Address
                </button>
            </div>

            <!-- Filled Address Display (Shown when default address is present) -->
            <div class="address-details-body" id="filledAddressBox" style="{{ $hasDefault ? 'display: block;' : 'display: none;' }}">
                <div class="address-user-name" id="displayRecipientName">{{ $initName }}</div>
                <div class="address-user-phone" id="displayPhone">{{ $initPhone }}</div>
                <div class="address-user-street" id="displayFullAddress">
                    @if($hasDefault)
                        {{ $initStreet }}, {{ $initCity }}, {{ $initProvince }} {{ $initPostal }}, {{ $initCountry }}
                    @endif
                </div>
            </div>
        </div>

        <!-- CARD 2: ORDER SUMMARY -->
        <div class="zal-dark-card">
            <div class="summary-card-label">Order Summary</div>
            
            @forelse($cart as $id => $item)
                <div class="summary-item-row">
                    <div class="summary-item-left">
                        <img src="{{ $item['image'] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200' }}" alt="{{ $item['title'] }}" class="summary-item-thumb">
                        <div>
                            <h3 class="summary-item-title">{{ Str::limit($item['title'], 30) }}</h3>
                            <div class="summary-item-qty">Qty: {{ $item['quantity'] }}</div>
                        </div>
                    </div>
                    <div class="summary-item-price">
                        {{ setting('currency_symbol', '$') }}{{ number_format($item['price'] * $item['quantity'], 2) }}
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: #8E9AA8; padding: 12px 0;">
                    Your cart is currently empty.
                </div>
            @endforelse
        </div>

        <!-- CARD 3: TOTAL PAYMENT AMOUNT -->
        <div class="total-payment-card">
            <span class="total-payment-label">Total Payment Amount</span>
            <span class="total-payment-val">{{ setting('currency_symbol', '$') }}{{ number_format($total, 2) }}</span>
        </div>

        <!-- SECTION 4: PAYMENT METHODS -->
        <div class="pay-methods-header-row">
            <h2 class="pay-methods-title">Payment Methods</h2>
            <span class="pay-methods-badge">Best Value</span>
        </div>

        <div class="pay-methods-card">
            <!-- Option 1: Credit / Debit Card (Stripe) -->
            <div class="pay-option-row active" onclick="selectPayMethod(this, 'stripe_card')">
                <div class="pay-option-left">
                    <div class="pay-icon-wrap"><i class="bi bi-credit-card-2-front" style="color: #00F0C8;"></i></div>
                    <div>
                        <div class="pay-option-name">Credit / Debit Card (Stripe)</div>
                        <small style="font-size: 11px; color: #8E9AA8;">Visa, Mastercard, Amex, Apple Pay</small>
                    </div>
                </div>
                <div class="custom-radio-dot">
                    <div class="custom-radio-dot-inner"></div>
                </div>
            </div>

            <!-- Option 2: PayPal -->
            <div class="pay-option-row" onclick="selectPayMethod(this, 'paypal')">
                <div class="pay-option-left">
                    <div class="pay-icon-wrap"><i class="bi bi-paypal" style="color: #0079C1;"></i></div>
                    <div>
                        <div class="pay-option-name">PayPal</div>
                        <small style="font-size: 11px; color: #8E9AA8;">Fast & secure PayPal checkout</small>
                    </div>
                </div>
                <div class="custom-radio-dot">
                    <div class="custom-radio-dot-inner"></div>
                </div>
            </div>

            <!-- Option 3: Cash On Delivery (COD) -->
            <div class="pay-option-row" onclick="selectPayMethod(this, 'cash_on_delivery')">
                <div class="pay-option-left">
                    <div class="pay-icon-wrap"><i class="bi bi-cash-stack" style="color: #00F0C8;"></i></div>
                    <div>
                        <div class="pay-option-name">Cash on Delivery (COD)</div>
                        <small style="font-size: 11px; color: #8E9AA8;">Pay cash upon delivery at your doorstep</small>
                    </div>
                </div>
                <div class="custom-radio-dot">
                    <div class="custom-radio-dot-inner"></div>
                </div>
            </div>

            <!-- Option 4: Escrow Wallet Balance -->
            <div class="pay-option-row" onclick="selectPayMethod(this, 'escrow_balance')">
                <div class="pay-option-left">
                    <div class="pay-icon-wrap"><i class="bi bi-wallet2" style="color: #00F0C8;"></i></div>
                    <div>
                        <div class="pay-option-name">Escrow Wallet Balance ({{ setting('currency_symbol', '$') }}{{ number_format(auth()->user()->wallet_balance ?? 1500, 2) }})</div>
                        <small style="font-size: 11px; color: #8E9AA8;">Instant checkout from wallet funds</small>
                    </div>
                </div>
                <div class="custom-radio-dot">
                    <div class="custom-radio-dot-inner"></div>
                </div>
            </div>
        </div>

        <!-- BIG BRIGHT CYAN CONFIRM PURCHASE BUTTON -->
        @if(count($cart) > 0)
            <button type="submit" class="btn-confirm-purchase" id="btnConfirmPurchase">
                Confirm Purchase
            </button>
        @else
            <a href="{{ route('shop.index') }}" class="btn-confirm-purchase">
                Explore Live Shopping
            </a>
        @endif

    </form>

    <!-- FOOTER -->
    <footer class="payment-footer">
        <a href="{{ route('home') }}">
            <img src="{{ asset(setting('site_logo', 'assets/logo.png')) }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="payment-footer-logo">
        </a>
        <p class="payment-footer-tagline">
            Experience the future of shopping with realtime interaction, live demonstrations, and exclusive community deals.
        </p>
        <div class="payment-footer-copy">
            {{ setting('copyright_text', '© 2026 Zaldoris Live Commerce Ltd. All rights reserved.') }}
        </div>
    </footer>

</main>

<!-- FLOATING CHAT WIDGET -->
<div class="floating-chat-btn" title="Live Customer Support">
    <i class="bi bi-chat-dots-fill"></i>
</div>

<!-- CHANGE / SELECT ADDRESS MODAL -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content dark-modal">
            <div class="modal-header dark-modal">
                <h5 class="modal-title" id="addressModalLabel" style="font-weight: 800; font-size: 16px;">
                    <i class="bi bi-geo-alt-fill" style="color: #00F0C8;"></i> Delivery Address
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                @if(isset($savedAddresses) && $savedAddresses->count() > 0)
                    <!-- Saved Addresses Picker -->
                    <div id="savedAddressesPicker" style="margin-bottom: 20px;">
                        <div style="font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 10px;">Select From Saved Addresses:</div>
                        @foreach($savedAddresses as $sAddr)
                            @php
                                $isThisActive = ($defaultAddress->id ?? null) === $sAddr->id;
                            @endphp
                            <div class="saved-address-choice {{ $isThisActive ? 'active' : '' }}" onclick='applySavedAddress(@json($sAddr), this)'>
                                <div>
                                    <div style="font-weight: 700; color: #fff; font-size: 13px;">
                                        {{ $sAddr->recipient_name }}
                                        @if($sAddr->is_default)
                                            <span style="background: rgba(0,240,200,0.15); color: #00F0C8; font-size: 10px; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">DEFAULT</span>
                                        @endif
                                    </div>
                                    <div style="font-size: 11px; color: #8E9AA8;">{{ $sAddr->phone }}</div>
                                    <div style="font-size: 12px; color: #CBD5E1; margin-top: 2px;">
                                        {{ $sAddr->address_line1 }}, {{ $sAddr->city }}, {{ $sAddr->region }} {{ $sAddr->postal_code }}
                                    </div>
                                </div>
                                <div style="color: #00F0C8; font-size: 18px;">
                                    <i class="bi {{ $isThisActive ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Add New Address Form Fields -->
                <div style="font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                    <span>Enter Delivery Details:</span>
                    @if(isset($savedAddresses) && $savedAddresses->count() > 0)
                        <small style="color: #00F0C8; font-size: 11px; font-weight: 600;">or enter new below</small>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Recipient Full Name *</label>
                    <input type="text" id="modalRecipientName" class="form-control form-control-dark" placeholder="e.g. Alex Velocity" value="{{ $initName }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Contact Phone Number *</label>
                    <input type="text" id="modalPhone" class="form-control form-control-dark" placeholder="e.g. +1 (604) 555-0192" value="{{ $initPhone }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Street Address *</label>
                    <input type="text" id="modalStreet" class="form-control form-control-dark" placeholder="e.g. 100 King Street West, Suite 400" value="{{ $initStreet }}">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">City *</label>
                        <input type="text" id="modalCity" class="form-control form-control-dark" placeholder="e.g. Toronto" value="{{ $initCity }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Province / State *</label>
                        <input type="text" id="modalProvince" class="form-control form-control-dark" placeholder="e.g. ON" value="{{ $initProvince }}">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Postal / ZIP Code *</label>
                        <input type="text" id="modalPostalCode" class="form-control form-control-dark" placeholder="e.g. M5X 1A9" value="{{ $initPostal }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Country *</label>
                        <input type="text" id="modalCountry" class="form-control form-control-dark" placeholder="e.g. Canada" value="{{ $initCountry }}">
                    </div>
                </div>
                <div id="modalAddrError" style="display: none; color: #FE2C55; font-size: 12px; margin-top: 8px;">
                    Please fill out all required fields marked with *.
                </div>
            </div>
            <div class="modal-footer dark-modal">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.08); border: none; font-size: 13px;">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAddressChanges()" style="background: #00F0C8; color: #000; border: none; font-weight: 700; font-size: 13px;">Save & Apply Address</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function selectPayMethod(row, method) {
        document.querySelectorAll('.pay-option-row').forEach(r => r.classList.remove('active'));
        row.classList.add('active');
        document.getElementById('selectedPaymentMethod').value = method;
    }

    function applySavedAddress(addr, el) {
        document.querySelectorAll('.saved-address-choice').forEach(c => {
            c.classList.remove('active');
            const icon = c.querySelector('i');
            if (icon) {
                icon.className = 'bi bi-circle';
            }
        });

        if (el) {
            el.classList.add('active');
            const icon = el.querySelector('i');
            if (icon) {
                icon.className = 'bi bi-check-circle-fill';
            }
        }

        document.getElementById('modalRecipientName').value = addr.recipient_name || '';
        document.getElementById('modalPhone').value = addr.phone || '';
        document.getElementById('modalStreet').value = addr.address_line1 || '';
        document.getElementById('modalCity').value = addr.city || '';
        document.getElementById('modalProvince').value = addr.region || '';
        document.getElementById('modalPostalCode').value = addr.postal_code || '';
        document.getElementById('modalCountry').value = addr.country || 'Canada';
    }

    function saveAddressChanges() {
        const name = document.getElementById('modalRecipientName').value.trim();
        const phone = document.getElementById('modalPhone').value.trim();
        const street = document.getElementById('modalStreet').value.trim();
        const city = document.getElementById('modalCity').value.trim();
        const province = document.getElementById('modalProvince').value.trim();
        const postal = document.getElementById('modalPostalCode').value.trim();
        const country = document.getElementById('modalCountry').value.trim();
        const errEl = document.getElementById('modalAddrError');

        if (!name || !phone || !street || !city || !province || !postal || !country) {
            if (errEl) errEl.style.display = 'block';
            return;
        }

        if (errEl) errEl.style.display = 'none';

        // Update hidden inputs
        document.getElementById('inputRecipientName').value = name;
        document.getElementById('inputPhone').value = phone;
        document.getElementById('inputStreet').value = street;
        document.getElementById('inputCity').value = city;
        document.getElementById('inputProvince').value = province;
        document.getElementById('inputPostalCode').value = postal;
        document.getElementById('inputCountry').value = country;

        // Update UI display
        document.getElementById('displayRecipientName').textContent = name;
        document.getElementById('displayPhone').textContent = phone;
        document.getElementById('displayFullAddress').textContent = `${street}, ${city}, ${province} ${postal}, ${country}`;

        // Toggle Views
        document.getElementById('emptyAddressBox').style.display = 'none';
        document.getElementById('filledAddressBox').style.display = 'block';
        document.getElementById('btnChangeAddress').style.display = 'inline-block';
        
        // Remove error highlights
        const card = document.getElementById('shippingAddressCard');
        if (card) card.classList.remove('card-error');
        const alertBanner = document.getElementById('addressAlertBanner');
        if (alertBanner) alertBanner.style.display = 'none';

        // Close modal
        const modalEl = document.getElementById('addressModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    // Intercept form submit and enforce address requirement
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const street = document.getElementById('inputStreet').value.trim();
        const city = document.getElementById('inputCity').value.trim();

        if (!street || !city) {
            e.preventDefault();
            
            // Highlight shipping address card
            const card = document.getElementById('shippingAddressCard');
            if (card) {
                card.classList.add('card-error');
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            const alertBanner = document.getElementById('addressAlertBanner');
            if (alertBanner) alertBanner.style.display = 'block';

            // Automatically open address modal
            const modalEl = document.getElementById('addressModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    });
</script>
</body>
</html>
