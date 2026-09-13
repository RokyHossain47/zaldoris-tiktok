<?php

$dir = __DIR__ . '/../resources/views';

$authNavbar = <<<HTML
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
HTML;

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$count = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $path = $file->getPathname();
        
        // Skip admin files as admin has its own layout
        if (str_contains($path, 'views/admin') || str_contains($path, 'views\\admin')) {
            continue;
        }

        $content = file_get_contents($path);

        // Pattern to match <div class="zal-nav-actions"> ... </div>
        $pattern = '/<!-- Right Action Icons -->\s*<div class="zal-nav-actions">[\s\S]*?<\/div>/';

        if (preg_match($pattern, $content)) {
            $newContent = preg_replace($pattern, $authNavbar, $content, 1);
            if ($newContent !== $content) {
                file_put_contents($path, $newContent);
                echo "Updated: " . $file->getFilename() . "\n";
                $count++;
            }
        } else {
            // Alternative pattern without comment
            $pattern2 = '/<div class="zal-nav-actions">[\s\S]*?<\/div>/';
            if (preg_match($pattern2, $content)) {
                $newContent = preg_replace($pattern2, $authNavbar, $content, 1);
                if ($newContent !== $content) {
                    file_put_contents($path, $newContent);
                    echo "Updated (p2): " . $file->getFilename() . "\n";
                    $count++;
                }
            }
        }
    }
}

echo "Total updated files: $count\n";
