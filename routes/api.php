<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiStreamController;
use App\Http\Controllers\Api\ApiPkBattleController;
use App\Http\Controllers\Api\ApiAuctionController;
use App\Http\Controllers\Api\ApiShopController;
use App\Http\Controllers\Api\ApiWalletController;
use App\Http\Controllers\Api\ApiAiController;
use App\Http\Controllers\Api\ApiSellerController;

/*
|--------------------------------------------------------------------------
| Mobile REST API Routes (Android & iOS) - v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Health Check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'version' => '1.0.0',
            'app' => 'TikTok-Style Live Commerce & Auction API',
            'server_time' => now()->toIso8601String(),
        ]);
    });

    // Authentication & OTP (SRS #3)
    Route::post('/auth/register', [ApiAuthController::class, 'register']);
    Route::post('/auth/login', [ApiAuthController::class, 'login']);
    Route::post('/auth/verify-otp', [ApiAuthController::class, 'verifyOtp']);

    // Public Live Streams & Discover
    Route::get('/streams', [ApiStreamController::class, 'index']);
    Route::get('/streams/{id}', [ApiStreamController::class, 'show']);
    Route::get('/streams/{id}/messages', [ApiStreamController::class, 'getMessages']);
    Route::post('/streams/{id}/warmup-bot', [ApiStreamController::class, 'triggerBotWarmUp']);

    // PK Battles
    Route::get('/pk-battles/{id}', [ApiPkBattleController::class, 'show']);

    // Whatnot-Style Auctions
    Route::get('/auctions', [ApiAuctionController::class, 'index']);
    Route::get('/auctions/{id}', [ApiAuctionController::class, 'show']);

    // Live Commerce & Shop
    Route::get('/shop/products', [ApiShopController::class, 'index']);
    Route::get('/shop/products/{id}', [ApiShopController::class, 'show']);
    Route::post('/shop/calculate-cart', [ApiShopController::class, 'calculateCart']);
    Route::post('/shop/checkout', [ApiShopController::class, 'checkout']);

    // Coins & Economy
    Route::get('/wallet/packages', [ApiWalletController::class, 'getPackages']);
    Route::get('/wallet/gifts', [ApiWalletController::class, 'getGifts']);

    // AI Features
    Route::post('/ai/moderate', [ApiAiController::class, 'moderate']);
    Route::get('/ai/shopping-assistant', [ApiAiController::class, 'shoppingAssistant']);
    Route::post('/ai/voice-search', [ApiAiController::class, 'voiceSearch']);

    // Authenticated Mobile Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Profile
        Route::get('/auth/profile', [ApiAuthController::class, 'profile']);

        // Streams (Create, Chat, End)
        Route::post('/streams', [ApiStreamController::class, 'store']);
        Route::post('/streams/{id}/chat', [ApiStreamController::class, 'sendMessage']);
        Route::post('/streams/{id}/end', [ApiStreamController::class, 'endStream']);

        // PK Battle Real-Time Gifting
        Route::post('/pk-battles/{id}/send-gift', [ApiPkBattleController::class, 'sendBattleGift']);

        // Auction Bidding
        Route::post('/auctions/{id}/bid', [ApiAuctionController::class, 'placeBid']);

        // Orders & Cancellation
        Route::get('/shop/my-orders', [ApiShopController::class, 'orders']);
        Route::post('/shop/orders/{id}/cancel', [ApiShopController::class, 'cancelOrder']);
        Route::post('/shop/orders/{id}/dispute', [ApiShopController::class, 'fileDispute']);

        // Wallet & Gifting
        Route::post('/wallet/purchase-coins', [ApiWalletController::class, 'purchaseCoins']);
        Route::post('/streams/{id}/send-gift', [ApiWalletController::class, 'sendStreamGift']);
        Route::post('/creators/{id}/subscribe', [ApiWalletController::class, 'subscribeToCreator']);

        // Seller Operations (SRS #2, #7, #14)
        Route::post('/seller/orders/{id}/packing-video', [ApiSellerController::class, 'uploadPackingVideo']);
        Route::post('/seller/orders/{id}/dispatch', [ApiSellerController::class, 'dispatchOrder']);
        Route::post('/seller/inventory/lock', [ApiSellerController::class, 'lockLiveInventory']);
    });
});
