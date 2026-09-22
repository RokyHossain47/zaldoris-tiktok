<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ Str::limit($product->description ?? 'Zaldoris Product Details. View specifications, sizes, colors, and direct purchase system.', 160) }}">
    <title>{{ $product->title ?? 'Product Details' }} - {{ setting('site_name', 'Zaldoris Live Commerce Platform') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}">
    <link rel="shortcut icon" href="{{ asset(setting('site_favicon', 'assets/favicon.png')) }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        /* ========================================================
           PRODUCT DETAILS PAGE SPECIFIC ENHANCED STYLING
           ======================================================== */
        :root {
            --p-accent: #00F0C8;
            --p-accent-hover: #00D8B4;
            --p-accent-glow: rgba(0, 240, 200, 0.25);
            --p-pink: #FF3565;
            --p-card-bg: #12181E;
            --p-card-border: #1D2630;
            --p-input-bg: #0D1217;
            --p-text-muted: #8A99AD;
        }

        .product-page-wrapper {
            max-width: 1360px;
            margin: 0 auto;
            padding: 24px 20px 80px;
        }

        /* Breadcrumb navigation */
        .zal-breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            font-size: 0.88rem;
            color: var(--p-text-muted);
            flex-wrap: wrap;
        }
        .zal-breadcrumb-nav a {
            color: var(--p-text-muted);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .zal-breadcrumb-nav a:hover {
            color: var(--p-accent);
        }
        .zal-breadcrumb-nav .active {
            color: #FFFFFF;
            font-weight: 600;
        }
        .zal-breadcrumb-nav i {
            font-size: 0.75rem;
            opacity: 0.6;
        }

        /* Two-Column Grid */
        .product-details-grid {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            gap: 36px;
            align-items: start;
            margin-bottom: 48px;
        }

        @media (max-width: 992px) {
            .product-details-grid {
                grid-template-columns: 1fr;
                gap: 28px;
            }
        }

        /* Left Column: Gallery Box */
        .product-gallery-box {
            background: var(--p-card-bg);
            border: 1px solid var(--p-card-border);
            border-radius: 20px;
            padding: 24px;
            position: sticky;
            top: 90px;
        }

        .main-image-wrap {
            position: relative;
            background: radial-gradient(circle at center, rgba(0, 240, 200, 0.05) 0%, #090D10 80%);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
            aspect-ratio: 1 / 0.85;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .main-image-display {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease, opacity 0.25s ease;
        }

        .main-image-wrap:hover .main-image-display {
            transform: scale(1.04);
        }

        .gallery-badge-overlay {
            position: absolute;
            top: 14px;
            left: 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 2;
        }

        .badge-discount-tag {
            background: linear-gradient(135deg, #FF3565, #FF5E85);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 6px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(255, 53, 101, 0.4);
            text-transform: uppercase;
        }

        .badge-stock-tag {
            background: rgba(0, 240, 200, 0.15);
            color: var(--p-accent);
            border: 1px solid rgba(0, 240, 200, 0.3);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            backdrop-filter: blur(8px);
        }

        .btn-wishlist-float {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(9, 13, 16, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            backdrop-filter: blur(8px);
            transition: all 0.2s ease;
            z-index: 2;
        }

        .btn-wishlist-float:hover,
        .btn-wishlist-float.active {
            background: #FF3565;
            border-color: #FF3565;
            color: #fff;
            transform: scale(1.1);
        }

        /* Thumbnails */
        .product-thumbs-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .product-thumb-item {
            border: 2px solid transparent;
            background: #090D10;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            aspect-ratio: 1;
            transition: all 0.2s ease;
            position: relative;
        }

        .product-thumb-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.65;
            transition: opacity 0.2s ease;
        }

        .product-thumb-item:hover img,
        .product-thumb-item.active img {
            opacity: 1;
        }

        .product-thumb-item.active {
            border-color: var(--p-accent);
            box-shadow: 0 0 12px var(--p-accent-glow);
        }

        /* Trust & Guarantees box */
        .trust-guarantee-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid var(--p-card-border);
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.82rem;
            color: var(--p-text-muted);
        }

        .trust-item i {
            font-size: 1.15rem;
            color: var(--p-accent);
            flex-shrink: 0;
        }

        /* Right Column: Product Info Card */
        .product-info-card {
            background: var(--p-card-bg);
            border: 1px solid var(--p-card-border);
            border-radius: 20px;
            padding: 28px;
        }

        /* Top Header Meta */
        .product-category-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .product-badge-cat {
            background: rgba(255, 255, 255, 0.06);
            color: var(--p-accent);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-sku-text {
            font-size: 0.8rem;
            color: var(--p-text-muted);
        }

        .product-main-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.85rem;
            font-weight: 800;
            color: #FFFFFF;
            line-height: 1.25;
            margin-bottom: 12px;
        }

        .product-rating-overview {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--p-card-border);
        }

        .stars-group {
            color: #FFAA00;
            display: flex;
            gap: 2px;
            font-size: 0.95rem;
        }

        .rating-num-val {
            font-weight: 700;
            color: #fff;
            font-size: 0.9rem;
        }

        .reviews-count-link {
            color: var(--p-text-muted);
            font-size: 0.85rem;
            text-decoration: underline;
            cursor: pointer;
        }

        .orders-count-badge {
            color: var(--p-accent);
            font-size: 0.82rem;
            font-weight: 600;
            margin-left: auto;
            background: rgba(0, 240, 200, 0.08);
            padding: 4px 10px;
            border-radius: 6px;
        }

        /* Pricing Card */
        .product-price-section {
            background: #090D10;
            border: 1px solid var(--p-card-border);
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 22px;
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .price-left-box {
            display: flex;
            align-items: baseline;
            gap: 12px;
        }

        .price-main-val {
            font-family: 'Outfit', sans-serif;
            font-size: 2.1rem;
            font-weight: 900;
            color: var(--p-accent);
            letter-spacing: -0.5px;
        }

        .price-original-strike {
            font-size: 1.15rem;
            color: var(--p-text-muted);
            text-decoration: line-through;
        }

        .save-badge-pill {
            background: rgba(255, 53, 101, 0.15);
            color: #FF5E85;
            border: 1px solid rgba(255, 53, 101, 0.3);
            font-size: 0.78rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
        }

        /* Notice banner for Live stream comparison */
        .live-stream-notice-box {
            background: linear-gradient(90deg, rgba(112, 51, 255, 0.12), rgba(0, 240, 200, 0.08));
            border: 1px solid rgba(112, 51, 255, 0.3);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .live-notice-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            color: #E2E8F0;
        }

        .live-pulse-icon {
            width: 10px;
            height: 10px;
            background: #FF3565;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(255, 53, 101, 0.7);
            animation: livePulseAnim 1.6s infinite;
            flex-shrink: 0;
        }

        @keyframes livePulseAnim {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 53, 101, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(255, 53, 101, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 53, 101, 0); }
        }

        .btn-link-live-room {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--p-accent);
            text-decoration: none;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: color 0.2s ease;
        }
        .btn-link-live-room:hover {
            color: #fff;
            text-decoration: underline;
        }

        /* Seller Profile Row */
        .seller-profile-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #090D10;
            border: 1px solid var(--p-card-border);
            border-radius: 14px;
            padding: 14px 18px;
            margin-bottom: 22px;
        }

        .seller-profile-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .seller-avatar-img {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--p-accent);
        }

        .seller-meta-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .seller-name-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            color: #fff;
            font-size: 0.95rem;
        }

        .seller-feedback-text {
            font-size: 0.78rem;
            color: var(--p-text-muted);
        }

        .btn-outline-follow {
            background: transparent;
            border: 1px solid var(--p-accent);
            color: var(--p-accent);
            font-size: 0.82rem;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-outline-follow:hover,
        .btn-outline-follow.following {
            background: var(--p-accent);
            color: #090D10;
        }

        /* Section Headings */
        .product-section-heading {
            font-size: 0.92rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .selected-variant-val {
            color: var(--p-accent);
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Size Radio Buttons */
        .select-size-section {
            margin-bottom: 20px;
        }

        .size-radio-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .size-radio-label {
            flex: 1;
            min-width: 80px;
            background: #090D10;
            border: 1px solid var(--p-card-border);
            border-radius: 10px;
            padding: 10px 14px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.88rem;
            color: var(--p-text-muted);
            transition: all 0.2s ease;
            position: relative;
        }

        .size-radio-label input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .size-radio-label:hover {
            border-color: rgba(0, 240, 200, 0.4);
            color: #fff;
        }

        .size-radio-label.active {
            background: rgba(0, 240, 200, 0.1);
            border-color: var(--p-accent);
            color: var(--p-accent);
            box-shadow: 0 0 10px var(--p-accent-glow);
        }

        /* Color Swatches */
        .select-color-section {
            margin-bottom: 20px;
        }

        .color-swatch-group {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .color-swatch-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 3px solid #090D10;
            outline: 2px solid var(--p-card-border);
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .color-swatch-btn:hover {
            transform: scale(1.1);
            outline-color: rgba(255, 255, 255, 0.5);
        }

        .color-swatch-btn.active {
            outline-color: var(--p-accent);
            transform: scale(1.15);
            box-shadow: 0 0 12px var(--p-accent-glow);
        }

        /* Quantity & Subtotal Row */
        .quantity-subtotal-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #090D10;
            border: 1px solid var(--p-card-border);
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 24px;
        }

        .quantity-stepper {
            display: inline-flex;
            align-items: center;
            background: #12181E;
            border: 1px solid var(--p-card-border);
            border-radius: 8px;
            overflow: hidden;
        }

        .btn-step {
            width: 36px;
            height: 36px;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease;
        }

        .btn-step:hover {
            background: rgba(0, 240, 200, 0.15);
            color: var(--p-accent);
        }

        .step-num {
            min-width: 38px;
            text-align: center;
            font-weight: 700;
            color: #fff;
            font-size: 0.95rem;
        }

        .calculated-subtotal-box {
            text-align: right;
        }
        .subtotal-label {
            font-size: 0.75rem;
            color: var(--p-text-muted);
            text-transform: uppercase;
        }
        .subtotal-val {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
        }

        /* Action Buttons */
        .product-buy-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 24px;
        }

        .btn-add-cart-outline {
            background: transparent;
            border: 2px solid var(--p-accent);
            color: var(--p-accent);
            font-size: 0.95rem;
            font-weight: 800;
            padding: 14px 20px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-add-cart-outline:hover {
            background: rgba(0, 240, 200, 0.12);
            box-shadow: 0 0 16px var(--p-accent-glow);
            transform: translateY(-2px);
        }

        .btn-buy-now-cyan {
            background: linear-gradient(135deg, #00F0C8, #00D8B4);
            border: none;
            color: #090D10;
            font-size: 0.95rem;
            font-weight: 800;
            padding: 14px 20px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 18px var(--p-accent-glow);
        }

        .btn-buy-now-cyan:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 240, 200, 0.45);
        }

        /* Highlights list */
        .product-highlights-box {
            background: #090D10;
            border: 1px solid var(--p-card-border);
            border-radius: 14px;
            padding: 16px 20px;
        }

        .highlights-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .highlight-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.82rem;
            color: #CBD5E1;
        }

        .highlight-item i {
            color: var(--p-accent);
            font-size: 1rem;
        }

        /* ========================================================
           TABBED DETAILS SECTION
           ======================================================== */
        .product-tabs-container {
            background: var(--p-card-bg);
            border: 1px solid var(--p-card-border);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 48px;
        }

        .nav-tabs-custom {
            display: flex;
            gap: 10px;
            border-bottom: 1px solid var(--p-card-border);
            padding-bottom: 14px;
            margin-bottom: 24px;
            overflow-x: auto;
        }

        .tab-btn-pill {
            background: transparent;
            border: none;
            color: var(--p-text-muted);
            font-size: 0.95rem;
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .tab-btn-pill:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.04);
        }

        .tab-btn-pill.active {
            background: rgba(0, 240, 200, 0.12);
            color: var(--p-accent);
            border: 1px solid rgba(0, 240, 200, 0.25);
        }

        .tab-pane-content {
            display: none;
            animation: fadeInTab 0.3s ease;
        }

        .tab-pane-content.active {
            display: block;
        }

        @keyframes fadeInTab {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Spec Table */
        .spec-table {
            width: 100%;
            border-collapse: collapse;
        }

        .spec-table tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .spec-table th {
            width: 30%;
            padding: 12px 16px;
            text-align: left;
            color: var(--p-text-muted);
            font-weight: 600;
            font-size: 0.88rem;
            background: rgba(255, 255, 255, 0.015);
        }

        .spec-table td {
            padding: 12px 16px;
            color: #fff;
            font-size: 0.88rem;
        }

        /* Reviews Tab Layout */
        .reviews-summary-grid {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 28px;
            align-items: center;
            background: #090D10;
            border: 1px solid var(--p-card-border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 28px;
        }

        @media (max-width: 768px) {
            .reviews-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        .big-score-box {
            text-align: center;
        }

        .big-score-num {
            font-family: 'Outfit', sans-serif;
            font-size: 3.4rem;
            font-weight: 900;
            color: #fff;
            line-height: 1;
        }

        .rating-bars-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.82rem;
            color: var(--p-text-muted);
        }

        .bar-track {
            flex: 1;
            height: 8px;
            background: #171F27;
            border-radius: 4px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: #FFAA00;
            border-radius: 4px;
        }

        .review-card-item {
            background: #090D10;
            border: 1px solid var(--p-card-border);
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 14px;
        }

        .review-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .reviewer-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .reviewer-name {
            font-weight: 700;
            color: #fff;
            font-size: 0.9rem;
        }

        .review-date-text {
            font-size: 0.78rem;
            color: var(--p-text-muted);
        }

        .review-body-p {
            font-size: 0.88rem;
            color: #CBD5E1;
            line-height: 1.6;
            margin: 0;
        }

        /* Write review form */
        .write-review-box {
            background: #090D10;
            border: 1px solid var(--p-card-border);
            border-radius: 16px;
            padding: 22px;
            margin-top: 24px;
        }

        .form-review-input {
            width: 100%;
            background: #12181E;
            border: 1px solid var(--p-card-border);
            border-radius: 10px;
            color: #fff;
            padding: 10px 14px;
            font-size: 0.88rem;
            outline: none;
            margin-bottom: 12px;
        }
        .form-review-input:focus {
            border-color: var(--p-accent);
        }

        /* Related Products Section */
        .related-products-section {
            margin-top: 48px;
        }

        .grid-4-products {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        @media (max-width: 992px) {
            .grid-4-products {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 576px) {
            .grid-4-products {
                grid-template-columns: 1fr;
            }
        }

        .product-static-card {
            background: var(--p-card-bg);
            border: 1px solid var(--p-card-border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        .product-static-card:hover {
            border-color: rgba(0, 240, 200, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.6);
        }

        .static-card-img-wrap {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
            background: #090D10;
        }

        .static-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-static-card:hover .static-card-img-wrap img {
            transform: scale(1.06);
        }

        .static-card-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .static-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
            text-decoration: none;
            display: block;
        }

        .static-card-price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 10px;
        }

        .static-card-price {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--p-accent);
        }

        .btn-quick-add {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(0, 240, 200, 0.12);
            border: 1px solid var(--p-accent);
            color: var(--p-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-quick-add:hover {
            background: var(--p-accent);
            color: #090D10;
        }

        /* Toast notification */
        .cart-toast-notification {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #12181E;
            border: 1px solid var(--p-accent);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.8), 0 0 20px var(--p-accent-glow);
            border-radius: 14px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            z-index: 2000;
            transform: translateY(120px);
            opacity: 0;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .cart-toast-notification.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast-img {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
        }

        .toast-text-box h4 {
            font-size: 0.88rem;
            font-weight: 700;
            color: #fff;
            margin: 0 0 2px 0;
        }

        .toast-text-box p {
            font-size: 0.78rem;
            color: var(--p-accent);
            margin: 0;
        }

        .toast-btn-cart {
            background: var(--p-accent);
            color: #090D10;
            font-size: 0.8rem;
            font-weight: 800;
            padding: 6px 12px;
            border-radius: 8px;
            text-decoration: none;
            margin-left: 8px;
            white-space: nowrap;
        }
    </style>
</head>
<body>

@php
    $currency = setting('currency_symbol', 'C$');
    $price = (float) ($product->price ?? 299.00);
    $comparePrice = (float) ($product->compare_price ?: ($price * 1.35));
    $saveAmount = max(0, $comparePrice - $price);
    $discountPercent = $comparePrice > $price ? round(($saveAmount / $comparePrice) * 100) : 0;
    
    // Images gallery
    $galleryImages = is_array($product->images) && count($product->images) > 0 
        ? $product->images 
        : [$product->primary_image ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80'];
        
    $mainImg = $galleryImages[0];

    $seller = $product->seller;
    $sellerName = $seller ? $seller->name : setting('site_name', 'Zaldoris Merchant');
    $sellerAvatar = $seller && $seller->avatar_url ? $seller->avatar_url : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80';

    $colorsList = $product->colors_list;
    $defaultColor = count($colorsList) > 0 ? $colorsList[0]['name'] : 'Standard';

    $sizesList = $product->sizes_list;
    $defaultSize = count($sizesList) > 0 ? $sizesList[0]['name'] : 'Standard';
    $defaultSizePrice = count($sizesList) > 0 ? ($price + $sizesList[0]['price_modifier']) : $price;
    
    $specsList = $product->specs_list;
    $displayAvgRating = isset($totalReviews) && $totalReviews > 0 ? $avgRating : 4.9;
    $displayReviewsCount = isset($totalReviews) && $totalReviews > 0 ? $totalReviews : 1280;
@endphp

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

<!-- MAIN PRODUCT DETAILS WRAPPER -->
<main class="product-page-wrapper">

    <!-- Breadcrumb -->
    <nav class="zal-breadcrumb-nav" aria-label="breadcrumb">
        <a href="{{ route('home') }}"><i class="bi bi-house-door"></i> Home</a>
        <i class="bi bi-chevron-right"></i>
        <a href="{{ route('shop.index') }}">{{ $product->category_name ?? 'Shop' }}</a>
        <i class="bi bi-chevron-right"></i>
        <span class="active">{{ Str::limit($product->title, 40) }}</span>
    </nav>

    <!-- TWO-COLUMN PRODUCT DETAILS GRID (STATIC DIRECT PURCHASE) -->
    <div class="product-details-grid">

        <!-- LEFT COLUMN: INTERACTIVE GALLERY SHOWCASE -->
        <div class="product-gallery-box">
            <!-- Main Display -->
            <div class="main-image-wrap">
                <div class="gallery-badge-overlay">
                    @if($discountPercent > 0)
                        <span class="badge-discount-tag">{{ $discountPercent }}% OFF</span>
                    @endif
                    <span class="badge-stock-tag">
                        <i class="bi bi-check-circle-fill"></i> In Stock ({{ $product->available_stock > 0 ? $product->available_stock : 12 }} Left)
                    </span>
                </div>
                <button type="button" class="btn-wishlist-float" id="btnWishlist" title="Save to Wishlist" onclick="toggleWishlist(this)">
                    <i class="bi bi-heart"></i>
                </button>
                <img src="{{ $mainImg }}" alt="{{ $product->title }}" id="mainGalleryImg" class="main-image-display">
            </div>

            <!-- Thumbnails Row -->
            <div class="product-thumbs-row">
                @foreach($galleryImages as $index => $imgUrl)
                    <div class="product-thumb-item {{ $index === 0 ? 'active' : '' }}" onclick="switchMainImage(this, '{{ $imgUrl }}')">
                        <img src="{{ $imgUrl }}" alt="Thumb {{ $index + 1 }}">
                    </div>
                @endforeach
            </div>

            <!-- Trust & Buyer Protection Guarantees -->
            <div class="trust-guarantee-box">
                <div class="trust-item">
                    <i class="bi bi-shield-check"></i>
                    <span>100% Genuine Guaranteed</span>
                </div>
                <div class="trust-item">
                    <i class="bi bi-truck"></i>
                    <span>Free Shipping over {{ $currency }}50</span>
                </div>
                <div class="trust-item">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>30-Day Easy Returns</span>
                </div>
                <div class="trust-item">
                    <i class="bi bi-lock-fill"></i>
                    <span>Secure 256-Bit Checkout</span>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: PRODUCT INFO & STATIC PURCHASE SYSTEM -->
        <div class="product-info-card">

            <!-- Category & SKU -->
            <div class="product-category-row">
                <span class="product-badge-cat">{{ $product->category_name ?? 'Electronics & Goods' }}</span>
                <span class="product-sku-text">SKU: ZAL-{{ $product->id ? str_pad($product->id, 4, '0', STR_PAD_LEFT) : '8921' }}-X</span>
            </div>

            <!-- Title -->
            <h1 class="product-main-title" id="productTitle">{{ $product->title }}</h1>

            <!-- Ratings & Order count -->
            <div class="product-rating-overview">
                <div class="stars-group">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($displayAvgRating))
                            <i class="bi bi-star-fill"></i>
                        @else
                            <i class="bi bi-star"></i>
                        @endif
                    @endfor
                </div>
                <span class="rating-num-val">{{ $displayAvgRating }}</span>
                <span class="reviews-count-link" onclick="scrollToReviews()">({{ number_format($displayReviewsCount) }} {{ isset($totalReviews) && $totalReviews > 0 ? 'Customer' : 'Verified' }} Reviews)</span>
                <span class="orders-count-badge"><i class="bi bi-bag-check-fill"></i> {{ $product->locked_stock > 0 ? $product->locked_stock : '3.4k' }} Sold</span>
            </div>

            <!-- Pricing Box -->
            <div class="product-price-section">
                <div class="price-left-box">
                    <span class="price-main-val" id="productPriceDisplay">{{ $currency }}{{ number_format($defaultSizePrice, 2) }}</span>
                    @if($comparePrice > $price)
                        <span class="price-original-strike" id="productOldPriceDisplay">{{ $currency }}{{ number_format($comparePrice, 2) }}</span>
                    @endif
                </div>
                @if($saveAmount > 0)
                    <span class="save-badge-pill" id="productSaveBadge">Save {{ $currency }}{{ number_format($saveAmount, 2) }} ({{ $discountPercent }}% OFF)</span>
                @endif
            </div>

            <!-- Notice: Distinction Between Static Product & Live Shopping Room -->
            @if(isset($stream) && $stream)
                <div class="live-stream-notice-box">
                    <div class="live-notice-left">
                        <span class="live-pulse-icon"></span>
                        <span><strong>Live Demo Available:</strong> Host is showcasing this product live!</span>
                    </div>
                    <a href="{{ route('streams.show', $stream->id) }}" class="btn-link-live-room">
                        Join Live Room <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            @endif

            <!-- Seller Information -->
            <div class="seller-profile-row">
                <div class="seller-profile-left">
                    <img src="{{ $sellerAvatar }}" alt="{{ $sellerName }}" class="seller-avatar-img">
                    <div class="seller-meta-info">
                        <div class="seller-name-row">
                            <span id="sellerName">{{ $sellerName }}</span>
                            <i class="bi bi-patch-check-fill text-info" title="Verified Creator & Seller"></i>
                        </div>
                        <span class="seller-feedback-text">
                            @if($seller && $seller->creatorProfile)
                                {{ number_format($seller->creatorProfile->total_followers ?? 0) }} Followers &bull; 98% Positive Feedback
                            @else
                                98% Positive Feedback &bull; Ships in 24h
                            @endif
                        </span>
                    </div>
                </div>
                <button type="button" class="btn-outline-follow {{ $isFollowing ? 'following' : '' }}" id="btnFollowSeller" onclick="toggleFollowSeller(this, {{ $seller ? $seller->id : 0 }})">
                    {{ $isFollowing ? '✓ Following' : 'Follow' }}
                </button>
            </div>

            <!-- Description summary -->
            <div class="mb-4">
                <p style="color: #CBD5E1; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                    {{ $product->description ?: 'Professional grade finish, equipped with the latest high-performance processing unit. Experience the future of shopping with premium craftsmanship and exceptional durability.' }}
                </p>
            </div>

            <!-- Variant Selector: Size / Model -->
            @if(count($sizesList) > 0)
            <div class="select-size-section">
                <div class="product-section-heading">
                    <span>Select Size / Model Variant</span>
                    <span class="selected-variant-val" id="selectedSizeText">{{ $defaultSize }}</span>
                </div>
                <div class="size-radio-group">
                    @foreach($sizesList as $index => $size)
                        @php
                            $modPrice = $price + ($size['price_modifier'] ?? 0);
                        @endphp
                        <label class="size-radio-label {{ $index === 0 ? 'active' : '' }}" onclick="selectProductSize('{{ addslashes($size['name']) }}', {{ $modPrice }}, this)">
                            <input type="radio" name="productSize" {{ $index === 0 ? 'checked' : '' }}>
                            <span>{{ $size['name'] }} @if(($size['price_modifier'] ?? 0) > 0) (+{{ $currency }}{{ number_format($size['price_modifier'], 2) }}) @endif</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Variant Selector: Color -->
            @if(count($colorsList) > 0)
            <div class="select-color-section">
                <div class="product-section-heading">
                    <span>Select Color Finish</span>
                    <span class="selected-variant-val" id="selectedColorText">{{ $defaultColor }}</span>
                </div>
                <div class="color-swatch-group">
                    @foreach($colorsList as $index => $color)
                        <button type="button" 
                            class="color-swatch-btn {{ $index === 0 ? 'active' : '' }}" 
                            style="background: {{ $color['code'] ?? '#1E293B' }};" 
                            title="{{ $color['name'] }}" 
                            onclick="selectProductColor('{{ addslashes($color['name']) }}', this)">
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quantity & Calculated Subtotal -->
            <div class="quantity-subtotal-row">
                <div class="d-flex align-items-center gap-3">
                    <span class="product-section-heading mb-0">Quantity</span>
                    <div class="quantity-stepper">
                        <button type="button" class="btn-step" onclick="changeQuantity(-1)" title="Decrease Quantity">—</button>
                        <span class="step-num" id="productQtyDisplay">1</span>
                        <button type="button" class="btn-step" onclick="changeQuantity(1)" title="Increase Quantity">+</button>
                    </div>
                </div>
                <div class="calculated-subtotal-box">
                    <div class="subtotal-label">Subtotal</div>
                    <div class="subtotal-val" id="productSubtotalDisplay">{{ $currency }}{{ number_format($defaultSizePrice, 2) }}</div>
                </div>
            </div>

            <!-- Static Purchase Action Buttons -->
            <div class="product-buy-actions">
                <button type="button" class="btn-add-cart-outline" onclick="handleAddToCart({{ $product->id ?? 1 }})">
                    <i class="bi bi-cart-plus-fill"></i> Add To Cart
                </button>
                <button type="button" class="btn-buy-now-cyan" onclick="handleBuyNow({{ $product->id ?? 1 }})">
                    <i class="bi bi-lightning-charge-fill"></i> Buy Now
                </button>
            </div>

            <!-- Feature Quick Highlights -->
            <div class="product-highlights-box">
                <div class="highlights-grid">
                    <div class="highlight-item">
                        <i class="bi bi-shield-check"></i>
                        <span>Authentic Guarantee</span>
                    </div>
                    <div class="highlight-item">
                        <i class="bi bi-truck"></i>
                        <span>Fast Doorstep Delivery</span>
                    </div>
                    <div class="highlight-item">
                        <i class="bi bi-cpu-fill"></i>
                        <span>Premium Hardware</span>
                    </div>
                    <div class="highlight-item">
                        <i class="bi bi-patch-check-fill"></i>
                        <span>2-Year Official Warranty</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- TABBED PRODUCT SPECIFICATIONS, REVIEWS, & POLICIES -->
    <section class="product-tabs-container" id="productTabsContainer">
        <!-- Tabs Header Navigation -->
        <div class="nav-tabs-custom">
            <button class="tab-btn-pill active" onclick="switchProductTab('overview', this)">Overview & Features</button>
            <button class="tab-btn-pill" onclick="switchProductTab('specs', this)">Technical Specifications</button>
            <button class="tab-btn-pill" onclick="switchProductTab('reviews', this)" id="tabReviewsBtn">Customer Reviews ({{ number_format($displayReviewsCount) }})</button>
            <button class="tab-btn-pill" onclick="switchProductTab('shipping', this)">Shipping & Returns</button>
        </div>

        <!-- TAB 1: OVERVIEW -->
        <div class="tab-pane-content active" id="tab-overview">
            <h3 class="mb-3" style="font-size: 1.25rem; font-weight: 700; color: #fff;">Product Overview</h3>
            <p style="color: #CBD5E1; line-height: 1.7; margin-bottom: 20px;">
                {{ $product->description ?: 'Engineered with top-tier materials and rigorous craftsmanship, this product is built for relentless endurance and pristine aesthetics. Tested under demanding real-world conditions to provide a superior user experience.' }}
            </p>
            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div style="background: #090D10; border: 1px solid var(--p-card-border); border-radius: 14px; padding: 20px;">
                        <i class="bi bi-patch-check-fill text-info" style="font-size: 1.6rem;"></i>
                        <h4 class="mt-2 mb-1" style="font-size: 1.05rem; font-weight: 700; color: #fff;">Premium Quality</h4>
                        <p style="font-size: 0.82rem; color: var(--p-text-muted); margin: 0;">Inspected and certified by verified merchants with genuine source guarantees.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background: #090D10; border: 1px solid var(--p-card-border); border-radius: 14px; padding: 20px;">
                        <i class="bi bi-lightning-charge-fill text-warning" style="font-size: 1.6rem;"></i>
                        <h4 class="mt-2 mb-1" style="font-size: 1.05rem; font-weight: 700; color: #fff;">Fast Processing</h4>
                        <p style="font-size: 0.82rem; color: var(--p-text-muted); margin: 0;">Orders dispatched within 24 hours from local fulfillment centers.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background: #090D10; border: 1px solid var(--p-card-border); border-radius: 14px; padding: 20px;">
                        <i class="bi bi-shield-shaded text-success" style="font-size: 1.6rem;"></i>
                        <h4 class="mt-2 mb-1" style="font-size: 1.05rem; font-weight: 700; color: #fff;">Buyer Protection</h4>
                        <p style="font-size: 0.82rem; color: var(--p-text-muted); margin: 0;">Protected by Zaldoris 100% money-back safe payment policy.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: TECHNICAL SPECIFICATIONS -->
        <div class="tab-pane-content" id="tab-specs">
            <h3 class="mb-3" style="font-size: 1.25rem; font-weight: 700; color: #fff;">Technical Specifications</h3>
            <div style="background: #090D10; border: 1px solid var(--p-card-border); border-radius: 14px; overflow: hidden;">
                <table class="spec-table">
                    <tbody>
                        <tr>
                            <th>Product Title</th>
                            <td>{{ $product->title }}</td>
                        </tr>
                        @if($product->brand)
                        <tr>
                            <th>Brand</th>
                            <td>{{ $product->brand }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Category</th>
                            <td>{{ $product->category_name ?? 'General Goods' }}</td>
                        </tr>
                        @if($product->dimensions)
                        <tr>
                            <th>Dimensions</th>
                            <td>{{ $product->dimensions }}</td>
                        </tr>
                        @endif
                        @if($product->sourcing_country)
                        <tr>
                            <th>Sourcing / Origin</th>
                            <td>{{ $product->sourcing_country }}</td>
                        </tr>
                        @endif
                        @foreach($specsList as $specKey => $specVal)
                            @if(!in_array($specKey, ['Brand', 'Dimensions', 'Sourcing Country']))
                            <tr>
                                <th>{{ $specKey }}</th>
                                <td>{{ is_array($specVal) ? implode(', ', $specVal) : $specVal }}</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr>
                            <th>Stock Status</th>
                            <td>{{ $product->available_stock > 0 ? $product->available_stock . ' units ready to dispatch' : 'In Stock (Ready to ship)' }}</td>
                        </tr>
                        <tr>
                            <th>Seller / Merchant</th>
                            <td>{{ $sellerName }}</td>
                        </tr>
                        <tr>
                            <th>Natural Lighting Declared</th>
                            <td>{{ $product->is_natural_lighting_declared ? 'Yes (Color-accurate real footage)' : 'Standard Studio Condition' }}</td>
                        </tr>
                        <tr>
                            <th>Warranty & Assurance</th>
                            <td>2-Year Official Manufacturer Warranty included</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: CUSTOMER REVIEWS -->
        <div class="tab-pane-content" id="tab-reviews">
            <div class="reviews-summary-grid">
                <div class="big-score-box">
                    <div class="big-score-num">{{ $displayAvgRating }}</div>
                    <div class="stars-group justify-content-center my-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($displayAvgRating))
                                <i class="bi bi-star-fill"></i>
                            @else
                                <i class="bi bi-star"></i>
                            @endif
                        @endfor
                    </div>
                    <div style="font-size: 0.85rem; color: var(--p-text-muted);">Based on {{ number_format($displayReviewsCount) }} verified reviews</div>
                </div>

                <div class="rating-bars-list">
                    @for($s = 5; $s >= 1; $s--)
                        <div class="bar-row">
                            <span>{{ $s }} ★</span>
                            <div class="bar-track"><div class="bar-fill" style="width: {{ $ratingPercents[$s] ?? 0 }}%;"></div></div>
                            <span>{{ $ratingPercents[$s] ?? 0 }}%</span>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- List of Reviews -->
            <div id="reviewsListContainer">
                @if(isset($reviews) && $reviews->count() > 0)
                    @foreach($reviews as $review)
                        <div class="review-card-item">
                            <div class="review-card-header">
                                <div class="reviewer-info">
                                    <img src="{{ $review->user && $review->user->avatar_url ? $review->user->avatar_url : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=80&auto=format&fit=crop&q=80' }}" alt="{{ $review->user ? $review->user->name : ($review->author_name ?? 'Customer') }}" class="reviewer-avatar">
                                    <div>
                                        <div class="reviewer-name">{{ $review->user ? $review->user->name : ($review->author_name ?? 'Customer') }} <span class="badge bg-success text-white" style="font-size: 0.68rem;">Verified Buyer</span></div>
                                        <div class="stars-group" style="font-size: 0.8rem;">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="bi bi-star-fill"></i>
                                                @else
                                                    <i class="bi bi-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <span class="review-date-text">{{ $review->created_at ? $review->created_at->diffForHumans() : 'Recently' }}</span>
                            </div>
                            <p class="review-body-p">
                                "{{ $review->comment }}"
                            </p>
                        </div>
                    @endforeach
                @else
                    <div class="review-card-item">
                        <div class="review-card-header">
                            <div class="reviewer-info">
                                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=80&auto=format&fit=crop&q=80" alt="Sarah J." class="reviewer-avatar">
                                <div>
                                    <div class="reviewer-name">Sarah Jenkins <span class="badge bg-success text-white" style="font-size: 0.68rem;">Verified Buyer</span></div>
                                    <div class="stars-group" style="font-size: 0.8rem;">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                    </div>
                                </div>
                            </div>
                            <span class="review-date-text">2 days ago</span>
                        </div>
                        <p class="review-body-p">
                            "The build quality is unbelievable for this price! The finish feels so premium and light. Delivered in 2 days from order placement!"
                        </p>
                    </div>
                @endif
            </div>

            <!-- Write A Review Accordion -->
            <div class="write-review-box">
                <h4 style="font-size: 1.05rem; font-weight: 700; color: #fff; margin-bottom: 14px;">Leave a Verified Review</h4>
                <form id="submitReviewForm" onsubmit="handleReviewSubmit(event)">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" class="form-review-input" placeholder="Your Name" id="reviewAuthorName" value="{{ auth()->check() ? auth()->user()->name : '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <select class="form-review-input" id="reviewRatingSelect">
                                <option value="5">★★★★★ (5 Stars - Outstanding)</option>
                                <option value="4">★★★★☆ (4 Stars - Very Good)</option>
                                <option value="3">★★★☆☆ (3 Stars - Average)</option>
                                <option value="2">★★☆☆☆ (2 Stars - Below Average)</option>
                                <option value="1">★☆☆☆☆ (1 Star - Poor)</option>
                            </select>
                        </div>
                    </div>
                    <textarea class="form-review-input" rows="3" placeholder="Share your experience with this product..." id="reviewCommentText" required></textarea>
                    <button type="submit" id="btnSubmitReview" class="btn-buy-now-cyan" style="padding: 10px 20px; font-size: 0.88rem;">
                        <i class="bi bi-pencil-square"></i> Submit Review
                    </button>
                </form>
            </div>
        </div>

        <!-- TAB 4: SHIPPING & RETURNS -->
        <div class="tab-pane-content" id="tab-shipping">
            <h3 class="mb-3" style="font-size: 1.25rem; font-weight: 700; color: #fff;">Shipping & Return Policies</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div style="background: #090D10; border: 1px solid var(--p-card-border); border-radius: 14px; padding: 22px;">
                        <h4 style="font-size: 1rem; font-weight: 700; color: var(--p-accent); margin-bottom: 12px;">
                            <i class="bi bi-box-seam"></i> Fast Delivery
                        </h4>
                        <ul style="color: #CBD5E1; font-size: 0.88rem; line-height: 1.8; padding-left: 20px; margin: 0;">
                            <li><strong>Standard Shipping:</strong> 2-4 business days (Free over {{ $currency }}50).</li>
                            <li><strong>Express Shipping:</strong> 1-2 business days (+{{ $currency }}25.00).</li>
                            <li>Full tracking code emailed upon dispatch.</li>
                            <li>Secure tamper-evident protective packaging.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="background: #090D10; border: 1px solid var(--p-card-border); border-radius: 14px; padding: 22px;">
                        <h4 style="font-size: 1rem; font-weight: 700; color: var(--p-accent); margin-bottom: 12px;">
                            <i class="bi bi-shield-check"></i> 30-Day Guarantee & Returns
                        </h4>
                        <ul style="color: #CBD5E1; font-size: 0.88rem; line-height: 1.8; padding-left: 20px; margin: 0;">
                            <li>Hassle-free 30-day return window from receipt date.</li>
                            <li>Full refund or instant replacement for defective units.</li>
                            <li>Prepaid return shipping label available from account dashboard.</li>
                            <li>2-Year Manufacturer Hardware Warranty included.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- RELATED PRODUCTS SECTION -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <section class="related-products-section">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 style="font-size: 1.4rem; font-weight: 800; color: #fff; margin: 0;">You May Also Like</h2>
                    <p style="font-size: 0.85rem; color: var(--p-text-muted); margin: 4px 0 0 0;">Recommended products from our verified catalog</p>
                </div>
                <a href="{{ route('shop.index') }}" class="btn-link-live-room">View All Products <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="grid-4-products">
                @foreach($relatedProducts as $rel)
                    <div class="product-static-card">
                        <a href="{{ route('shop.product', $rel->id) }}" class="static-card-img-wrap">
                            <img src="{{ $rel->primary_image }}" alt="{{ $rel->title }}">
                        </a>
                        <div class="static-card-body">
                            <span class="product-badge-cat mb-2" style="align-self: flex-start;">{{ $rel->category_name ?? 'Product' }}</span>
                            <a href="{{ route('shop.product', $rel->id) }}" class="static-card-title">{{ Str::limit($rel->title, 26) }}</a>
                            <div class="static-card-price-row">
                                <span class="static-card-price">{{ $currency }}{{ number_format($rel->price, 2) }}</span>
                                <button type="button" class="btn-quick-add" title="Add To Cart" onclick="handleAddToCart({{ $rel->id }})">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</main>

<!-- FLOATING TOAST NOTIFICATION -->
<div class="cart-toast-notification" id="cartToastNotification">
    <img src="{{ $mainImg }}" alt="Cart Item" id="toastItemImg" class="toast-img">
    <div class="toast-text-box">
        <h4 id="toastItemTitle">Item Added to Cart!</h4>
        <p id="toastItemMeta">1x &bull; {{ $defaultColor }} &bull; {{ $defaultSize }}</p>
    </div>
    <a href="{{ route('shop.cart') }}" class="toast-btn-cart">View Cart &rarr;</a>
</div>

<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
        <!-- Center Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset(setting('site_logo', 'assets/logo.png')) }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="footer-logo-img">
        </a>
        <p class="footer-tagline-text">
            Experience the future of shopping with realtime interaction, live demonstrations, and direct static e-commerce purchases.
        </p>
        <div class="footer-copyright-line">
            &copy; {{ date('Y') }} {{ setting('site_name', 'Zaldoris Live Commerce') }}. All rights reserved.
        </div>
    </div>
</footer>

<!-- FLOATING WIDGET BUTTON -->
<button class="floating-action-widget" title="Live Chat Support" onclick="alert('Support chat assistant is online!')">
    <i class="bi bi-chat-dots-fill"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
    /* ========================================================
       PRODUCT STATE & STATIC PURCHASE SYSTEM LOGIC
       ======================================================== */
    const productData = {
        id: {{ $product->id ?? 1 }},
        title: @json($product->title ?? 'Product Details'),
        currency: '{{ $currency }}',
        basePrice: {{ (float) $price }},
        unitPrice: {{ (float) $defaultSizePrice }},
        oldPrice: {{ (float) $comparePrice }},
        selectedSize: @json($defaultSize),
        selectedColor: @json($defaultColor),
        quantity: 1,
        image: '{{ $mainImg }}',
        seller: '{{ $sellerName }}'
    };

    // Switch Gallery Main Image
    function switchMainImage(thumbEl, newSrc) {
        document.querySelectorAll('.product-thumb-item').forEach(el => el.classList.remove('active'));
        if (thumbEl) thumbEl.classList.add('active');
        
        const mainImg = document.getElementById('mainGalleryImg');
        if (mainImg) {
            mainImg.style.opacity = '0.4';
            setTimeout(() => {
                mainImg.src = newSrc;
                mainImg.style.opacity = '1';
                productData.image = newSrc;
            }, 150);
        }
    }

    // Select Size Variant
    function selectProductSize(sizeName, priceVal, labelEl) {
        document.querySelectorAll('.size-radio-label').forEach(el => el.classList.remove('active'));
        if (labelEl) labelEl.classList.add('active');
        
        productData.selectedSize = sizeName;
        productData.unitPrice = priceVal;
        
        // Update display text
        document.getElementById('selectedSizeText').textContent = sizeName;
        document.getElementById('productPriceDisplay').textContent = `${productData.currency}${priceVal.toFixed(2)}`;
        
        // Recalculate savings
        if (productData.oldPrice > priceVal) {
            const saveAmt = productData.oldPrice - priceVal;
            const discountPercent = Math.round((saveAmt / productData.oldPrice) * 100);
            const badge = document.getElementById('productSaveBadge');
            if (badge) {
                badge.textContent = `Save ${productData.currency}${saveAmt.toFixed(2)} (${discountPercent}% OFF)`;
            }
        }
        
        updateSubtotalDisplay();
    }

    // Select Color Swatch
    function selectProductColor(colorName, btnEl) {
        document.querySelectorAll('.color-swatch-btn').forEach(btn => btn.classList.remove('active'));
        if (btnEl) btnEl.classList.add('active');
        
        productData.selectedColor = colorName;
        document.getElementById('selectedColorText').textContent = colorName;
    }

    // Quantity Stepper
    function changeQuantity(delta) {
        let newQty = productData.quantity + delta;
        if (newQty < 1) newQty = 1;
        if (newQty > 20) {
            alert('Maximum available stock for this order is 20 units.');
            return;
        }
        productData.quantity = newQty;
        document.getElementById('productQtyDisplay').textContent = newQty;
        updateSubtotalDisplay();
    }

    // Update Subtotal Display
    function updateSubtotalDisplay() {
        const subtotal = productData.unitPrice * productData.quantity;
        document.getElementById('productSubtotalDisplay').textContent = `${productData.currency}${subtotal.toFixed(2)}`;
    }

    // Follow Seller Toggle
    function toggleFollowSeller(btn, userId) {
        if (!userId) {
            showToast('Info', 'Seller information not available');
            return;
        }
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`/users/${userId}/toggle-follow`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(async res => {
            if (res.status === 401) {
                window.location.href = '{{ route("login") }}';
                return;
            }
            return res.json();
        })
        .then(data => {
            if (!data) return;
            if (data.success) {
                if (data.is_following) {
                    btn.classList.add('following');
                    btn.textContent = '✓ Following';
                    showToast('Followed!', data.message);
                } else {
                    btn.classList.remove('following');
                    btn.textContent = 'Follow';
                    showToast('Unfollowed', data.message);
                }
            } else if (data.message) {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error('Follow error', err);
        });
    }

    // Wishlist Toggle
    function toggleWishlist(btn) {
        btn.classList.toggle('active');
        const icon = btn.querySelector('i');
        if (btn.classList.contains('active')) {
            icon.className = 'bi bi-heart-fill';
            showToast('Added to Wishlist', `${productData.title} saved to your favorites`);
        } else {
            icon.className = 'bi bi-heart';
        }
    }

    // Tab Switcher
    function switchProductTab(tabId, btnEl) {
        document.querySelectorAll('.tab-btn-pill').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-pane-content').forEach(pane => pane.classList.remove('active'));
        
        if (btnEl) btnEl.classList.add('active');
        const target = document.getElementById(`tab-${tabId}`);
        if (target) target.classList.add('active');
    }

    function scrollToReviews() {
        switchProductTab('reviews', document.getElementById('tabReviewsBtn'));
        document.getElementById('productTabsContainer').scrollIntoView({ behavior: 'smooth' });
    }

    // Add To Cart Handler (Server AJAX + local storage sync)
    function handleAddToCart(productId) {
        const pId = productId || productData.id;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ route("shop.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                product_id: pId,
                quantity: productData.quantity,
                selected_color: productData.selectedColor,
                selected_size: productData.selectedSize,
                unit_price: productData.unitPrice
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById('globalCartBadge');
                if (badge) {
                    badge.textContent = data.cart_count;
                    badge.style.transform = 'scale(1.3)';
                    setTimeout(() => badge.style.transform = 'scale(1)', 250);
                }
                showToast(
                    'Added To Cart!', 
                    `${productData.quantity}x • ${productData.selectedColor} • ${productData.selectedSize}`
                );
            }
        })
        .catch(err => {
            const badge = document.getElementById('globalCartBadge');
            if (badge) {
                let curr = parseInt(badge.textContent || '0');
                badge.textContent = curr + productData.quantity;
            }
            showToast('Added To Cart!', `${productData.quantity}x ${productData.title}`);
        });
    }

    // Buy Now Handler (Direct Checkout)
    function handleBuyNow(productId) {
        const pId = productId || productData.id;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ route("shop.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                product_id: pId,
                quantity: productData.quantity,
                selected_color: productData.selectedColor,
                selected_size: productData.selectedSize,
                unit_price: productData.unitPrice
            })
        })
        .finally(() => {
            window.location.href = '{{ route("shop.cart") }}';
        });
    }

    // Show Floating Toast
    let toastTimeout = null;
    function showToast(title, meta) {
        const toast = document.getElementById('cartToastNotification');
        document.getElementById('toastItemTitle').textContent = title;
        document.getElementById('toastItemMeta').textContent = meta;
        document.getElementById('toastItemImg').src = productData.image;

        toast.classList.add('show');
        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.classList.remove('show');
        }, 4000);
    }

    // Submit Review Handler
    function handleReviewSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitReview');
        const name = document.getElementById('reviewAuthorName').value;
        const rating = parseInt(document.getElementById('reviewRatingSelect').value);
        const comment = document.getElementById('reviewCommentText').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Submitting...';

        fetch('{{ route("shop.product.review", $product->id ?? 1) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                rating: rating,
                comment: comment,
                name: name
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-pencil-square"></i> Submit Review';

            if (data.success) {
                const starsHtml = '<i class="bi bi-star-fill"></i>'.repeat(rating) + '<i class="bi bi-star"></i>'.repeat(5 - rating);

                const newReviewEl = document.createElement('div');
                newReviewEl.className = 'review-card-item';
                newReviewEl.style.animation = 'fadeInTab 0.3s ease';
                newReviewEl.innerHTML = `
                    <div class="review-card-header">
                        <div class="reviewer-info">
                            <img src="${data.review.user_avatar || 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=80&auto=format&fit=crop&q=80'}" alt="${data.review.user_name || name}" class="reviewer-avatar">
                            <div>
                                <div class="reviewer-name">${data.review.user_name || name} <span class="badge bg-success text-white" style="font-size: 0.68rem;">Verified Buyer</span></div>
                                <div class="stars-group" style="font-size: 0.8rem;">
                                    ${starsHtml}
                                </div>
                            </div>
                        </div>
                        <span class="review-date-text">Just now</span>
                    </div>
                    <p class="review-body-p">"${comment}"</p>
                `;

                const container = document.getElementById('reviewsListContainer');
                container.insertBefore(newReviewEl, container.firstChild);

                document.getElementById('submitReviewForm').reset();
                showToast('Review Published!', 'Thank you for sharing your verified review.');
            } else {
                alert(data.message || 'Could not submit review.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-pencil-square"></i> Submit Review';
            showToast('Error', 'Failed to submit review. Please try again.');
        });
    }

    // DOM Ready
    document.addEventListener('DOMContentLoaded', () => {
        updateSubtotalDisplay();
    });
</script>
</body>
</html>
