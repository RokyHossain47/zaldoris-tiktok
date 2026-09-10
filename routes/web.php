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
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/otp', [AuthController::class, 'showOtp'])->name('auth.otp');
Route::post('/otp', [AuthController::class, 'verifyOtp'])->name('auth.otp.verify');
Route::get('/notifications', [AuthController::class, 'notifications'])->name('notifications');

// Live Streaming & PK Battles
Route::get('/live-streams', [StreamController::class, 'index'])->name('streams.index');
Route::get('/live/{id}', [StreamController::class, 'show'])->name('streams.show');
Route::get('/pk-battle/{id}', [StreamController::class, 'pkBattle'])->name('streams.pk_battle');

// Whatnot-Style Live Auctions
Route::get('/live-auctions', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/auction/{id}', [AuctionController::class, 'show'])->name('auctions.show');
Route::get('/auction/{id}/result', [AuctionController::class, 'result'])->name('auctions.result');
Route::post('/auction/{id}/bid', [AuctionController::class, 'placeBid'])->name('auctions.bid');

// Live Commerce & Shop
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{id}', [ShopController::class, 'product'])->name('shop.product');
Route::get('/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
Route::post('/checkout', [ShopController::class, 'processCheckout'])->name('shop.checkout.process');
Route::get('/payment', [ShopController::class, 'payment'])->name('shop.payment');
Route::get('/payment-success/{order}', [ShopController::class, 'success'])->name('shop.success');
Route::get('/my-orders', [ShopController::class, 'orders'])->name('shop.orders');

// Coins, Gifts & Subscriptions
Route::get('/wallet/coins', [WalletController::class, 'coins'])->name('wallet.coins');
Route::post('/wallet/coins/buy', [WalletController::class, 'buyCoins'])->name('wallet.coins.buy');
Route::get('/subscribe/{creatorId?}', [WalletController::class, 'subscribe'])->name('wallet.subscribe');
Route::post('/subscribe/{creatorId}', [WalletController::class, 'processSubscription'])->name('wallet.subscribe.process');

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

    // User Management
    Route::post('/users/{id}/toggle-block', [AdminController::class, 'toggleUserBlock'])->name('admin.users.toggle_block');
    Route::post('/users/{id}/update-coins', [AdminController::class, 'updateUserCoins'])->name('admin.users.update_coins');
    Route::post('/users/{id}/update-role', [AdminController::class, 'updateUserRole'])->name('admin.users.update_role');

    // Ads & Banners
    Route::get('/ads', [AdminController::class, 'ads'])->name('admin.ads');
    Route::post('/ads/{id}/toggle', [AdminController::class, 'toggleAd'])->name('admin.ads.toggle');

    // Security & Fraud
    Route::get('/fraud', [AdminController::class, 'fraud'])->name('admin.fraud');
    Route::get('/disputes', [AdminController::class, 'disputes'])->name('admin.disputes');
    Route::post('/disputes/{id}/resolve', [AdminController::class, 'resolveDispute'])->name('admin.disputes.resolve');
    Route::get('/moderation', [AdminController::class, 'moderation'])->name('admin.moderation');
});
