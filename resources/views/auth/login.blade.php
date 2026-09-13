<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Zaldoris - Login to continue shopping, joining live streams, and participating in auctions.">
    <title>Login - Zaldoris Live Commerce Platform</title>
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

<!-- MAIN LOGIN CONTAINER -->
<main class="zal-auth-container">

    <!-- Top Left Back Button -->
    <div class="auth-back-btn-wrap">
        <a href="{{ route('home') }}" class="auth-back-btn" title="Back to Home">
            <i class="bi bi-chevron-left"></i>
        </a>
    </div>

    <!-- Centered Auth Card -->
    <div class="auth-card-inner">
        <!-- Header Title & Subtitle -->
        <div class="auth-header">
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">
                Login to continue shopping, joining live streams,<br>and participating in auctions.
            </p>
        </div>

        <!-- Login Form -->
        <form action="{{ route('login') }}" method="POST" class="auth-form">
            @csrf

            @if(session('status'))
                <div style="background: rgba(0, 240, 200, 0.15); border: 1px solid #00F0C8; color: #00F0C8; padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 18px;">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid #FE2C55; color: #FE2C55; padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 18px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Email or Mobile Number -->
            <div class="auth-form-group">
                <label class="auth-label" for="loginEmail">Email Address</label>
                <div class="auth-input-wrap">
                    <input type="email" id="loginEmail" name="email" value="{{ old('email') }}" class="auth-input" placeholder="name@example.com" required autocomplete="username" autofocus>
                </div>
            </div>

            <!-- Password -->
            <div class="auth-form-group">
                <label class="auth-label" for="loginPassword">Password</label>
                <div class="auth-input-wrap">
                    <input type="password" id="loginPassword" name="password" class="auth-input" placeholder="********" required autocomplete="current-password">
                    <button type="button" class="toggle-password-btn" id="togglePasswordBtn" title="Toggle password visibility">
                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="auth-options-row">
                <label class="auth-checkbox-label">
                    <input type="checkbox" id="rememberMe" name="remember" class="auth-checkbox" value="1" checked>
                    <span>Remember Me</span>
                </label>
                <a href="#" class="forgot-pass-link">Forgot Password?</a>
            </div>

            <!-- Login Submit Button -->
            <button type="submit" class="auth-btn-submit" id="loginSubmitBtn">
                Login
            </button>

            <!-- Or Divider -->
            <div class="auth-divider-cyan">
                <span>Or</span>
            </div>

            <!-- Social Login Buttons -->
            <div class="social-login-group">
                <button type="button" class="social-btn" title="Sign in with Google">
                    <svg viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                </button>
                <button type="button" class="social-btn" title="Sign in with Apple">
                    <svg viewBox="0 0 24 24" fill="#FFFFFF">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.32c.67-.82 1.13-1.96.99-3.12-1 .04-2.19.67-2.88 1.47-.62.72-1.16 1.88-1.01 3.01 1.12.09 2.23-.54 2.9-1.36z"/>
                    </svg>
                </button>
                <button type="button" class="social-btn" title="Sign in with Facebook">
                    <svg viewBox="0 0 24 24" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </button>
            </div>

            <!-- Create Account Row -->
            <div class="auth-switch-row">
                Don't have an account? <a href="{{ route('register') }}" class="auth-cyan-link">Create Account</a>
            </div>

            <!-- Terms & Privacy -->
            <div class="auth-legal-text">
                By continuing you agree to our<br>
                <a href="#" class="auth-legal-link">Terms & Conditions</a> and <a href="#" class="auth-legal-link">Privacy Policy.</a>
            </div>
        </form>
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
    // Password visibility toggle handler
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('loginPassword');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');

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
