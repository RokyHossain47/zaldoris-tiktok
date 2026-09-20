<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;

echo "=== ZALDORIS API v1 COMPREHENSIVE SMOKE TEST ===\n\n";

$testsPassed = 0;
$testsFailed = 0;

function runApiTest($method, $uri, $data = [], $token = null) {
    global $testsPassed, $testsFailed;

    $server = [
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
        'HTTP_CONTENT_TYPE' => 'application/json',
    ];
    if ($token) {
        $server['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
    }

    $content = !empty($data) ? json_encode($data) : null;
    $request = Request::create('/api/v1' . $uri, $method, $data, [], [], $server, $content);
    $response = app()->handle($request);
    $status = $response->getStatusCode();
    $resJson = json_decode($response->getContent(), true);

    $isOk = ($status >= 200 && $status < 300) && ($resJson['success'] ?? false) === true;

    if ($isOk) {
        echo " [PASS] ($status) $method /api/v1$uri\n";
        $testsPassed++;
    } else {
        echo " [FAIL] ($status) $method /api/v1$uri -> " . substr($response->getContent(), 0, 150) . "\n";
        $testsFailed++;
    }

    return [$status, $resJson];
}

// 1. App config (Public)
runApiTest('GET', '/app-config');

// 2. Categories (Public)
runApiTest('GET', '/categories');

// 3. Products list & single product (Public)
list($s, $prodRes) = runApiTest('GET', '/products');
$firstProduct = Product::first();
if ($firstProduct) {
    $firstProduct->stock = 50;
    $firstProduct->locked_stock = 0;
    $firstProduct->save();
    $firstProductId = $firstProduct->id;
} else {
    $firstProductId = 1;
}
runApiTest('GET', "/products/$firstProductId");

// 4. Live Sessions (Public)
runApiTest('GET', '/live-sessions');
runApiTest('GET', '/pk-battles/1');

// 5. Auctions (Public)
runApiTest('GET', '/auctions');

// 6. Coin Packages (Public)
runApiTest('GET', '/coin-packages');

// 7. Gifts (Public)
runApiTest('GET', '/gifts');

// 8. Reward Tiers (Public)
runApiTest('GET', '/reward-tiers');

// 9. Auth Login (Public -> Get Token)
$adminUser = User::where('role', 'admin')->first() ?? User::first();
if (!$adminUser) {
    $adminUser = User::create([
        'name' => 'Admin User',
        'username' => 'admin_test',
        'email' => 'admin_test@zaldoris.com',
        'password' => bcrypt('password'),
        'role' => 'admin'
    ]);
}

$token = $adminUser->createToken('smoke_test_token')->plainTextToken;
echo "\n Generated Test Auth Bearer Token for User #{$adminUser->id} ({$adminUser->email})\n\n";

// 10. Private /me endpoints
runApiTest('GET', '/me', [], $token);
runApiTest('GET', '/me/dashboard', [], $token);
runApiTest('GET', '/me/preferences', [], $token);
runApiTest('GET', '/me/addresses', [], $token);
runApiTest('GET', '/me/wallet', [], $token);
runApiTest('GET', '/me/wishlist', [], $token);
runApiTest('GET', '/me/orders', [], $token);
runApiTest('GET', '/me/conversations', [], $token);
runApiTest('GET', '/me/notifications', [], $token);

// 11. Cart and Cart Items
runApiTest('GET', '/cart', [], $token);
runApiTest('POST', '/cart/items', [
    'product_id' => $firstProductId,
    'quantity' => 1,
    'variant' => ['color' => 'Default']
], $token);

// 12. Checkout Quote
runApiTest('POST', '/checkout/quotes', [
    'items' => [
        [
            'product_id' => $firstProductId,
            'quantity' => 1,
            'variant' => ['color' => 'Default']
        ]
    ]
], $token);

// 13. Discovery Search
runApiTest('GET', '/search/discovery');

// 14. Moderation Report
runApiTest('POST', '/reports', [
    'target_type' => 'product',
    'target_id' => $firstProductId,
    'reason' => 'inappropriate',
    'description' => 'Test report verification'
], $token);

echo "\n============================================\n";
echo "Tests Passed: $testsPassed\n";
echo "Tests Failed: $testsFailed\n";
echo "============================================\n";
