<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\StreamController;
use App\Http\Controllers\Web\AuctionController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\WalletController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes - TikTok-Style Live Commerce & Whatnot Auctions Platform
|--------------------------------------------------------------------------
*/

// Home & Search
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// User Authentication & Profile
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/otp', [AuthController::class, 'showOtp'])->name('auth.otp');
Route::post('/otp', [AuthController::class, 'verifyOtp'])->name('auth.otp.verify');
Route::get('/notifications', [AuthController::class, 'notifications'])->name('notifications');

// Live Streaming & PK Battles
Route::get('/live-streams', [StreamController::class, 'index'])->name('streams.index');
Route::get('/live-streaming', [StreamController::class, 'index']);
Route::get('/live-stream', [StreamController::class, 'index']);
Route::get('/live/{id?}', [StreamController::class, 'show'])->name('streams.show');
Route::get('/live-product-room/{id?}', [StreamController::class, 'show']);
Route::get('/pk-battle/{id?}', [StreamController::class, 'pkBattle'])->name('streams.pk_battle');
Route::get('/pit-battle/{id?}', [StreamController::class, 'pkBattle']);

// Whatnot-Style Live Auctions
Route::get('/live-auctions', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/live-auction', [AuctionController::class, 'index']);
Route::get('/auction/{id?}', [AuctionController::class, 'show'])->name('auctions.show');
Route::get('/auction-details/{id?}', [AuctionController::class, 'show']);
Route::get('/auction/{id?}/result', [AuctionController::class, 'result'])->name('auctions.result');
Route::get('/auction-result/{id?}', [AuctionController::class, 'result']);
Route::post('/auction/{id}/bid', [AuctionController::class, 'placeBid'])->name('auctions.bid');

// Live Commerce & Shop
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/live-shopping', [ShopController::class, 'index']);
Route::get('/product/{id?}', [ShopController::class, 'product'])->name('shop.product');
Route::get('/product-details/{id?}', [ShopController::class, 'product']);
Route::get('/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
Route::post('/checkout', [ShopController::class, 'processCheckout'])->name('shop.checkout.process');
Route::get('/payment', [ShopController::class, 'payment'])->name('shop.payment');
Route::get('/payment-success/{order?}', [ShopController::class, 'success'])->name('shop.success');
Route::get('/my-orders', [ShopController::class, 'orders'])->name('shop.orders');

// Coins, Gifts & Subscriptions
Route::get('/wallet/coins', [WalletController::class, 'coins'])->name('wallet.coins');
Route::get('/coins-checkout', [WalletController::class, 'coins']);
Route::get('/gift-checkout', [WalletController::class, 'coins']);
Route::post('/wallet/coins/buy', [WalletController::class, 'buyCoins'])->name('wallet.coins.buy');
Route::get('/subscribe/{creatorId?}', [WalletController::class, 'subscribe'])->name('wallet.subscribe');
Route::get('/subscriber/{creatorId?}', [WalletController::class, 'subscribe']);
Route::post('/subscribe/{creatorId}', [WalletController::class, 'processSubscription'])->name('wallet.subscribe.process');

// 301 Redirects for legacy .html URLs to guarantee clean URLs in browser address bar
Route::get('/index.html', fn() => redirect('/', 301));
Route::get('/search.html', fn() => redirect('/search', 301));
Route::get('/login.html', fn() => redirect('/login', 301));
Route::get('/otp.html', fn() => redirect('/otp', 301));
Route::get('/notification.html', fn() => redirect('/notifications', 301));
Route::get('/live-streaming-list.html', fn() => redirect('/live-streams', 301));
Route::get('/live-stream-list.html', fn() => redirect('/live-streams', 301));
Route::get('/live-streaming.html', fn() => redirect('/live/1', 301));
Route::get('/live-stream.html', fn() => redirect('/live/1', 301));
Route::get('/live-product-room.html', fn() => redirect('/live/1', 301));
Route::get('/pk-battle.html', fn() => redirect('/pk-battle/1', 301));
Route::get('/pit-battle.html', fn() => redirect('/pk-battle/1', 301));
Route::get('/live-auction.html', fn() => redirect('/live-auctions', 301));
Route::get('/auction-details.html', fn() => redirect('/auction/1', 301));
Route::get('/auction-result.html', fn() => redirect('/auction/1/result', 301));
Route::get('/live-shopping.html', fn() => redirect('/live-shopping', 301));
Route::get('/product-details.html', fn() => redirect('/product/1', 301));
Route::get('/cart.html', fn() => redirect('/cart', 301));
Route::get('/checkout.html', fn() => redirect('/checkout', 301));
Route::get('/payment.html', fn() => redirect('/payment', 301));
Route::get('/payment-success.html', fn() => redirect('/payment-success/1', 301));
Route::get('/coins-checkout.html', fn() => redirect('/wallet/coins', 301));
Route::get('/gift-checkout.html', fn() => redirect('/wallet/coins', 301));
Route::get('/subscribe.html', fn() => redirect('/subscribe', 301));
Route::get('/subscriber.html', fn() => redirect('/subscribe', 301));


// User Dashboards (Auth-Gated)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/seller', [DashboardController::class, 'seller'])->name('dashboard.seller');
    Route::get('/dashboard/creator', [DashboardController::class, 'creator'])->name('dashboard.creator');
});

// Admin Authentication (Public Login)
Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Protected Admin Portal
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('admin.index');

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Dedicated User Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/users/{id}/toggle-block', [AdminController::class, 'toggleUserBlock'])->name('admin.users.toggle_block');
    Route::post('/users/{id}/update-coins', [AdminController::class, 'updateUserCoins'])->name('admin.users.update_coins');
    Route::post('/users/{id}/update-role', [AdminController::class, 'updateUserRole'])->name('admin.users.update_role');

    // Banners & Ads Management (Full CRUD)
    Route::get('/banners', [AdminController::class, 'banners'])->name('admin.banners.index');
    Route::post('/banners', [AdminController::class, 'storeBanner'])->name('admin.banners.store');
    Route::put('/banners/{id}', [AdminController::class, 'updateBanner'])->name('admin.banners.update');
    Route::delete('/banners/{id}', [AdminController::class, 'deleteBanner'])->name('admin.banners.delete');
    Route::post('/banners/{id}/toggle', [AdminController::class, 'toggleBanner'])->name('admin.banners.toggle');

    // Categories Management (Sub-menu of Products)
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories.index');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('admin.categories.delete');

    // Products Management (Full CRUD + Featured / Trending)
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products.index');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');
    Route::post('/products/{id}/toggle-featured', [AdminController::class, 'toggleProductFeatured'])->name('admin.products.toggle_featured');
    Route::post('/products/{id}/toggle-trending', [AdminController::class, 'toggleProductTrending'])->name('admin.products.toggle_trending');

    // Gifts Management (Full CRUD)
    Route::get('/gifts', [AdminController::class, 'gifts'])->name('admin.gifts.index');
    Route::post('/gifts', [AdminController::class, 'storeGift'])->name('admin.gifts.store');
    Route::put('/gifts/{id}', [AdminController::class, 'updateGift'])->name('admin.gifts.update');
    Route::delete('/gifts/{id}', [AdminController::class, 'deleteGift'])->name('admin.gifts.delete');
    Route::post('/gifts/{id}/toggle', [AdminController::class, 'toggleGift'])->name('admin.gifts.toggle');

    // Reactions & GIFs Management (Full CRUD)
    Route::get('/reactions', [AdminController::class, 'reactions'])->name('admin.reactions.index');
    Route::post('/reactions', [AdminController::class, 'storeReaction'])->name('admin.reactions.store');
    Route::put('/reactions/{id}', [AdminController::class, 'updateReaction'])->name('admin.reactions.update');
    Route::delete('/reactions/{id}', [AdminController::class, 'deleteReaction'])->name('admin.reactions.delete');
    Route::post('/reactions/{id}/toggle', [AdminController::class, 'toggleReaction'])->name('admin.reactions.toggle');

    // Settings (3 Sub-menus: General, SEO, System)
    Route::get('/settings/general', [AdminController::class, 'generalSettings'])->name('admin.settings.general');
    Route::post('/settings/general', [AdminController::class, 'updateGeneralSettings'])->name('admin.settings.general.update');
    Route::get('/settings/seo', [AdminController::class, 'seoSettings'])->name('admin.settings.seo');
    Route::post('/settings/seo', [AdminController::class, 'updateSeoSettings'])->name('admin.settings.seo.update');
    Route::get('/settings/system', [AdminController::class, 'systemSettings'])->name('admin.settings.system');
    Route::post('/settings/system', [AdminController::class, 'updateSystemSettings'])->name('admin.settings.system.update');

    // Auctions Management (Full CRUD)
    Route::get('/auctions', [AdminController::class, 'auctions'])->name('admin.auctions.index');
    Route::get('/auctions/create', [AdminController::class, 'createAuction'])->name('admin.auctions.create');
    Route::post('/auctions', [AdminController::class, 'storeAuction'])->name('admin.auctions.store');
    Route::get('/auctions/{id}/edit', [AdminController::class, 'editAuction'])->name('admin.auctions.edit');
    Route::put('/auctions/{id}', [AdminController::class, 'updateAuction'])->name('admin.auctions.update');
    Route::delete('/auctions/{id}', [AdminController::class, 'deleteAuction'])->name('admin.auctions.delete');
    Route::post('/auctions/{id}/toggle-status', [AdminController::class, 'toggleAuctionStatus'])->name('admin.auctions.toggle_status');
    Route::post('/auctions/{id}/toggle-blur', [AdminController::class, 'toggleAuctionBlur'])->name('admin.auctions.toggle_blur');

    Route::get('/disputes', [AdminController::class, 'disputes'])->name('admin.disputes');
    Route::post('/disputes/{id}/resolve', [AdminController::class, 'resolveDispute'])->name('admin.disputes.resolve');
});

