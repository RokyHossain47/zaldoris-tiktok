<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Join Zaldoris - The premier real-time live shopping and interactive auction platform. Create your account today.">
    <title>Create Account - {{ setting('site_name', 'Zaldoris Live Commerce Platform') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ setting('site_favicon') ? asset(setting('site_favicon')) : asset('assets/favicon.png') }}">
    <link rel="shortcut icon" href="{{ setting('site_favicon') ? asset(setting('site_favicon')) : asset('assets/favicon.png') }}">
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
            <img src="{{ setting('site_logo') ? asset(setting('site_logo')) : asset('assets/logo.png') }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="zal-brand-logo">
        </a>

        <!-- Center Nav Links -->
        <ul class="zal-nav-menu">
            <li><a href="{{ route('home') }}" class="zal-nav-link">Home</a></li>
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

<!-- MAIN REGISTER CONTAINER -->
<main class="zal-auth-container" style="padding: 40px 16px;">

    <!-- Top Left Back Button -->
    <div class="auth-back-btn-wrap">
        <a href="{{ route('login') }}" class="auth-back-btn" title="Back to Login">
            <i class="bi bi-chevron-left"></i>
        </a>
    </div>

    <!-- Centered Auth Card -->
    <div class="auth-card-inner" style="max-width: 480px;">
        <!-- Header Title & Subtitle -->
        <div class="auth-header">
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">
                Join Zaldoris to discover live shopping, exclusive auctions, and interactive community drops.
            </p>
        </div>

        <!-- Register Form -->
        <form action="{{ route('register') }}" method="POST" class="auth-form">
            @csrf

            @if($errors->any())
                <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid #FE2C55; color: #FE2C55; padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 18px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Full Name -->
            <div class="auth-form-group">
                <label class="auth-label" for="regName">Full Name</label>
                <div class="auth-input-wrap">
                    <input type="text" id="regName" name="name" value="{{ old('name') }}" class="auth-input" placeholder="Sarah Jenkins" required autocomplete="name" autofocus>
                </div>
            </div>

            <!-- Email Address -->
            <div class="auth-form-group">
                <label class="auth-label" for="regEmail">Email Address</label>
                <div class="auth-input-wrap">
                    <input type="email" id="regEmail" name="email" value="{{ old('email') }}" class="auth-input" placeholder="sarah@example.com" required autocomplete="email">
                </div>
            </div>

            <!-- Account Type / Role Selection -->
            <div class="auth-form-group">
                <label class="auth-label">I want to join as a</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-top: 6px;">
                    <label style="cursor: pointer; background: var(--bg-card-inner, #12181E); border: 1px solid var(--border-color, #1F2937); border-radius: 10px; padding: 10px 6px; text-align: center; color: #fff; font-size: 13px; font-weight: 600; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                        <input type="radio" name="role" value="buyer" checked style="accent-color: #00F0C8;">
                        <span>🛍️ Shopper</span>
                    </label>
                    <label style="cursor: pointer; background: var(--bg-card-inner, #12181E); border: 1px solid var(--border-color, #1F2937); border-radius: 10px; padding: 10px 6px; text-align: center; color: #fff; font-size: 13px; font-weight: 600; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                        <input type="radio" name="role" value="creator" {{ old('role') === 'creator' ? 'checked' : '' }} style="accent-color: #00F0C8;">
                        <span>🎥 Creator</span>
                    </label>
                    <label style="cursor: pointer; background: var(--bg-card-inner, #12181E); border: 1px solid var(--border-color, #1F2937); border-radius: 10px; padding: 10px 6px; text-align: center; color: #fff; font-size: 13px; font-weight: 600; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                        <input type="radio" name="role" value="seller" {{ old('role') === 'seller' ? 'checked' : '' }} style="accent-color: #00F0C8;">
                        <span>🏪 Seller</span>
                    </label>
                </div>
            </div>

            <!-- Password -->
            <div class="auth-form-group">
                <label class="auth-label" for="regPassword">Password (Min. 6 characters)</label>
                <div class="auth-input-wrap">
                    <input type="password" id="regPassword" name="password" class="auth-input" placeholder="Create password" required autocomplete="new-password">
                    <button type="button" class="toggle-password-btn" id="toggleRegPasswordBtn" title="Toggle password visibility">
                        <i class="bi bi-eye" id="toggleRegPasswordIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="auth-form-group">
                <label class="auth-label" for="regPasswordConfirm">Confirm Password</label>
                <div class="auth-input-wrap">
                    <input type="password" id="regPasswordConfirm" name="password_confirmation" class="auth-input" placeholder="Repeat password" required autocomplete="new-password">
                </div>
            </div>

            <!-- Terms & Conditions Checkbox -->
            <div class="auth-options-row" style="margin-bottom: 20px;">
                <label class="auth-checkbox-label">
                    <input type="checkbox" required name="agree" class="auth-checkbox" checked>
                    <span style="font-size: 12px; color: var(--text-muted, #94A3B8);">I agree to the <a href="#" class="auth-cyan-link">Terms</a> & <a href="#" class="auth-cyan-link">Privacy Policy</a></span>
                </label>
            </div>

            <!-- Register Submit Button -->
            <button type="submit" class="auth-btn-submit" id="regSubmitBtn">
                Create Account
            </button>

            <!-- Or Divider -->
            <div class="auth-divider-cyan">
                <span>Or sign up with</span>
            </div>

            <!-- Social Login Buttons -->
            <div class="social-login-group">
                <button type="button" class="social-btn" title="Sign up with Google">
                    <svg viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                </button>
                <button type="button" class="social-btn" title="Sign up with Apple">
                    <svg viewBox="0 0 24 24" fill="#FFFFFF">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.32c.67-.82 1.13-1.96.99-3.12-1 .04-2.19.67-2.88 1.47-.62.72-1.16 1.88-1.01 3.01 1.12.09 2.23-.54 2.9-1.36z"/>
                    </svg>
                </button>
                <button type="button" class="social-btn" title="Sign up with Facebook">
                    <svg viewBox="0 0 24 24" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </button>
            </div>

            <!-- Switch to Login Row -->
            <div class="auth-switch-row">
                Already have an account? <a href="{{ route('login') }}" class="auth-cyan-link">Log In</a>
            </div>
        </form>
    </div>

</main>

<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
        <!-- Center Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ setting('site_logo') ? asset(setting('site_logo')) : asset('assets/logo.png') }}" alt="{{ setting('site_name', 'Zaldoris') }}" class="footer-logo-img">
        </a>
        <p class="footer-tagline-text">
            {{ setting('site_tagline', 'Experience the future of shopping with realtime interaction, live demonstrations, and exclusive community deals.') }}
        </p>
        <div class="footer-copyright-line">
            {{ setting('copyright_text', '© 2026 Zaldoris LiveStreamShop. All rights reserved.') }}
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
    // Password visibility toggle handler
    const togglePasswordBtn = document.getElementById('toggleRegPasswordBtn');
    const passwordInput = document.getElementById('regPassword');
    const togglePasswordIcon = document.getElementById('toggleRegPasswordIcon');

    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePasswordIcon.classList.toggle('bi-eye');
            togglePasswordIcon.classList.toggle('bi-eye-slash');
        });
    }
</script>
</body>
</html>
