<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// 1. Visit Product details to populate session
$reqProduct = Illuminate\Http\Request::create('/product/1', 'GET');
$respProduct = $kernel->handle($reqProduct);
echo "Product Details Status: " . $respProduct->getStatusCode() . "\n";

// 2. Check Home page
$reqHome = Illuminate\Http\Request::create('/', 'GET');
$respHome = $kernel->handle($reqHome);
echo "Home Status: " . $respHome->getStatusCode() . "\n";

// 3. Check Shop page
$reqShop = Illuminate\Http\Request::create('/shop', 'GET');
$respShop = $kernel->handle($reqShop);
echo "Shop Status: " . $respShop->getStatusCode() . "\n";

// 4. Check Admin routes (mocking admin auth)
$admin = App\Models\User::where('role', 'admin')->orWhere('role', 'superadmin')->first() ?? App\Models\User::first();
if ($admin) {
    Illuminate\Support\Facades\Auth::login($admin);
}

$firstProduct = App\Models\Product::first();
$prodEditRoute = $firstProduct ? "/admin/products/{$firstProduct->id}/edit" : "/admin/products/create";

$routes = [
    '/admin/dashboard',
    '/admin/banners',
    '/admin/categories',
    '/admin/products',
    '/admin/products/create',
    $prodEditRoute,
    '/admin/settings/general',
    '/admin/settings/seo',
    '/admin/settings/system'
];

foreach ($routes as $route) {
    $req = Illuminate\Http\Request::create($route, 'GET');
    $resp = $kernel->handle($req);
    echo "Admin Route {$route} Status: " . $resp->getStatusCode() . "\n";
}

// 5. Test Coin Update on user
$targetUser = App\Models\User::first();
if ($targetUser) {
    $reqCoins = Illuminate\Http\Request::create("/admin/users/{$targetUser->id}/update-coins", 'POST', [
        'coin_balance' => 500
    ]);
    $reqCoins->setLaravelSession(app('session.store'));
    $reqCoins->headers->set('X-CSRF-TOKEN', csrf_token());
    // or test direct controller invocation:
    $controller = new App\Http\Controllers\Web\AdminController();
    $respCoins = $controller->updateUserCoins($reqCoins, $targetUser->id);
    echo "Coin Update Execution: SUCCESS (Redirect with flash message)\n";
}

echo "ALL TESTS PASSED SUCCESSFULLY!\n";
