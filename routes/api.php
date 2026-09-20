<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController as V1Auth;
use App\Http\Controllers\Api\V1\ProfileController as V1Profile;
use App\Http\Controllers\Api\V1\AddressController as V1Address;
use App\Http\Controllers\Api\V1\FollowController as V1Follow;
use App\Http\Controllers\Api\V1\HomeController as V1Home;
use App\Http\Controllers\Api\V1\ProductController as V1Product;
use App\Http\Controllers\Api\V1\WishlistController as V1Wishlist;
use App\Http\Controllers\Api\V1\CartController as V1Cart;
use App\Http\Controllers\Api\V1\PaymentMethodController as V1PaymentMethod;
use App\Http\Controllers\Api\V1\CheckoutController as V1Checkout;
use App\Http\Controllers\Api\V1\PaymentController as V1Payment;
use App\Http\Controllers\Api\V1\OrderController as V1Order;
use App\Http\Controllers\Api\V1\AuctionController as V1Auction;
use App\Http\Controllers\Api\V1\LiveStreamController as V1Stream;
use App\Http\Controllers\Api\V1\PkBattleController as V1PkBattle;
use App\Http\Controllers\Api\V1\WalletController as V1Wallet;
use App\Http\Controllers\Api\V1\GiftController as V1Gift;
use App\Http\Controllers\Api\V1\SubscriptionController as V1Subscription;
use App\Http\Controllers\Api\V1\RewardController as V1Reward;
use App\Http\Controllers\Api\V1\ChatController as V1Chat;
use App\Http\Controllers\Api\V1\NotificationController as V1Notification;
use App\Http\Controllers\Api\V1\ModerationController as V1Moderation;

/*
|--------------------------------------------------------------------------
| Zaldoris Comprehensive API v1 Suite
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Health & System Info
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'version' => '1.0.0',
            'app' => setting('site_name', 'Zaldoris Live Commerce Platform'),
            'serverTime' => now()->toISOString(),
        ]);
    });

    // 1. Authentication
    Route::get('/app-config', [V1Auth::class, 'appConfig']);
    Route::post('/auth/register', [V1Auth::class, 'register']);
    Route::post('/auth/login', [V1Auth::class, 'login']);
    Route::post('/auth/social/{provider}', [V1Auth::class, 'socialLogin']);
    Route::post('/auth/challenges/{id}/verify', [V1Auth::class, 'verifyChallenge']);
    Route::post('/auth/challenges/{id}/resend', [V1Auth::class, 'resendChallenge']);
    Route::patch('/auth/registrations/{id}/contact', [V1Auth::class, 'changeRegistrationContact']);
    Route::post('/auth/password-reset/requests', [V1Auth::class, 'requestPasswordReset']);
    Route::post('/auth/password-reset/complete', [V1Auth::class, 'completePasswordReset']);

    // 2. Public Profiles & Users
    Route::get('/users/{id}', [V1Profile::class, 'getPublicProfile']);
    Route::get('/users/{id}/followers', [V1Follow::class, 'getFollowers']);
    Route::get('/users/{id}/following', [V1Follow::class, 'getFollowing']);
    Route::get('/users/{id}/shared-activity', [V1Follow::class, 'getSharedActivity']);

    // 5. Home and Search
    Route::get('/home', [V1Home::class, 'index']);
    Route::get('/categories', [V1Home::class, 'categories']);
    Route::get('/search/discovery', [V1Home::class, 'discovery']);
    Route::get('/search', [V1Home::class, 'search']);

    // 6. Products
    Route::get('/products', [V1Product::class, 'index']);
    Route::get('/products/{id}', [V1Product::class, 'show']);

    // 13. Auctions
    Route::get('/auctions', [V1Auction::class, 'index']);
    Route::get('/auctions/{id}', [V1Auction::class, 'show']);
    Route::get('/auctions/{id}/bids', [V1Auction::class, 'getBids']);

    // 14. Live Streaming & 15. PK Battles
    Route::get('/live-sessions', [V1Stream::class, 'index']);
    Route::get('/live-sessions/{id}', [V1Stream::class, 'show']);
    Route::get('/live-sessions/{id}/comments', [V1Stream::class, 'getComments']);
    Route::get('/live-sessions/{id}/items', [V1Stream::class, 'getItems']);
    Route::post('/live-sessions/{id}/reactions', [V1Stream::class, 'sendReaction']);
    Route::post('/live-sessions/{id}/shares', [V1Stream::class, 'recordShare']);
    Route::get('/pk-battles/{id}', [V1PkBattle::class, 'show']);

    // 16. Economy & 17. Gifts & 18. Subscriptions & 19. Rewards (Public lookups)
    Route::get('/coin-packages', [V1Wallet::class, 'getCoinPackages']);
    Route::get('/gifts', [V1Gift::class, 'index']);
    Route::get('/creators/{id}/subscription-plans', [V1Subscription::class, 'getPlans']);
    Route::get('/reward-tiers', [V1Reward::class, 'getTiers']);

    // 2. Uploads lookups
    Route::get('/uploads/{id}', [V1Profile::class, 'getUpload']);
    Route::post('/uploads/{id}/complete', [V1Profile::class, 'completeUpload']);

    // 10. Public Quote & Payment Lookups
    Route::get('/checkout/quotes/{id}', [V1Checkout::class, 'getQuote']);
    Route::get('/payments/{id}', [V1Payment::class, 'getPayment']);

    // --------------------------------------------------------------------------
    // Authenticated API Endpoints (Bearer Token - Sanctum)
    // --------------------------------------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {

        // 1. Auth Maintenance
        Route::post('/auth/refresh', [V1Auth::class, 'refresh']);
        Route::post('/auth/logout', [V1Auth::class, 'logout']);

        // 2. Profile and Preferences
        Route::get('/me', [V1Profile::class, 'getProfile']);
        Route::patch('/me', [V1Profile::class, 'updateProfile']);
        Route::get('/me/dashboard', [V1Profile::class, 'getDashboard']);
        Route::post('/me/contact-change-requests', [V1Profile::class, 'requestContactChange']);
        Route::post('/me/contact-change-requests/{id}/verify', [V1Profile::class, 'verifyContactChange']);
        Route::get('/me/preferences', [V1Profile::class, 'getPreferences']);
        Route::patch('/me/preferences', [V1Profile::class, 'updatePreferences']);
        Route::post('/uploads', [V1Profile::class, 'createUpload']);

        // 3. Addresses
        Route::get('/me/addresses', [V1Address::class, 'index']);
        Route::post('/me/addresses', [V1Address::class, 'store']);
        Route::patch('/me/addresses/{id}', [V1Address::class, 'update']);
        Route::delete('/me/addresses/{id}', [V1Address::class, 'destroy']);

        // 4. Followers & Following
        Route::get('/me/follow-suggestions', [V1Follow::class, 'getSuggestions']);
        Route::put('/me/following/{userId}', [V1Follow::class, 'follow']);
        Route::delete('/me/following/{userId}', [V1Follow::class, 'unfollow']);
        Route::delete('/me/followers/{userId}', [V1Follow::class, 'removeFollower']);
        Route::post('/me/follow-suggestions/{userId}/dismiss', [V1Follow::class, 'dismissSuggestion']);

        // 5. Search History
        Route::get('/me/search-history', [V1Home::class, 'getSearchHistory']);
        Route::post('/me/search-history', [V1Home::class, 'saveSearchHistory']);
        Route::delete('/me/search-history', [V1Home::class, 'clearSearchHistory']);

        // 7. Wishlist
        Route::get('/me/wishlist', [V1Wishlist::class, 'index']);
        Route::put('/me/wishlist/{targetType}/{targetId}', [V1Wishlist::class, 'store']);
        Route::patch('/me/wishlist/{targetType}/{targetId}', [V1Wishlist::class, 'update']);
        Route::delete('/me/wishlist/{targetType}/{targetId}', [V1Wishlist::class, 'destroy']);

        // 8. Cart
        Route::get('/cart', [V1Cart::class, 'getCart']);
        Route::post('/cart/items', [V1Cart::class, 'addItem']);
        Route::patch('/cart/items/{id}', [V1Cart::class, 'updateItem']);
        Route::delete('/cart/items/{id}', [V1Cart::class, 'removeItem']);

        // 9. Payment Methods
        Route::get('/me/payment-methods', [V1PaymentMethod::class, 'index']);
        Route::post('/payment-method-setup-sessions', [V1PaymentMethod::class, 'createSetupSession']);
        Route::post('/me/payment-methods', [V1PaymentMethod::class, 'store']);
        Route::patch('/me/payment-methods/{id}', [V1PaymentMethod::class, 'setDefault']);
        Route::delete('/me/payment-methods/{id}', [V1PaymentMethod::class, 'destroy']);

        // 10. Checkout & Payments
        Route::post('/checkout/quotes', [V1Checkout::class, 'createQuote']);
        Route::post('/checkout/quotes/{id}/confirm', [V1Checkout::class, 'confirmQuote']);
        Route::post('/payments/{id}/confirm', [V1Payment::class, 'confirmPayment']);
        Route::post('/payments/{id}/challenges/{challengeId}/verify', [V1Payment::class, 'verifyPaymentChallenge']);
        Route::post('/payments/{id}/challenges/{challengeId}/resend', [V1Payment::class, 'resendPaymentChallenge']);

        // 11. Orders & Tracking
        Route::get('/me/orders', [V1Order::class, 'index']);
        Route::get('/orders/{id}', [V1Order::class, 'show']);
        Route::post('/orders/{id}/reorder', [V1Order::class, 'reorder']);
        Route::get('/orders/{id}/shipments', [V1Order::class, 'getShipments']);

        // 12. Returns & Refunds
        Route::get('/orders/{id}/return-eligibility', [V1Order::class, 'getReturnEligibility']);
        Route::post('/orders/{id}/returns', [V1Order::class, 'submitReturn']);
        Route::get('/returns/{id}', [V1Order::class, 'getReturn']);
        Route::get('/refunds/{id}', [V1Order::class, 'getRefund']);

        // 13. Auctions (Bidding & History)
        Route::post('/auctions/{id}/bid-previews', [V1Auction::class, 'previewBid']);
        Route::post('/auctions/{id}/bids', [V1Auction::class, 'submitBid']);
        Route::get('/me/auctions', [V1Auction::class, 'myAuctions']);
        Route::get('/me/auctions/{id}', [V1Auction::class, 'showMyAuction']);
        Route::post('/me/bid-verification-sessions', [V1Auction::class, 'createBidVerification']);

        // 14. Live Streaming (Interactions & Host Controls)
        Route::post('/live-sessions/{id}/join', [V1Stream::class, 'join']);
        Route::post('/live-sessions/{id}/heartbeat', [V1Stream::class, 'heartbeat']);
        Route::post('/live-sessions/{id}/leave', [V1Stream::class, 'leave']);
        Route::post('/live-sessions/{id}/comments', [V1Stream::class, 'sendComment']);
        Route::put('/live-sessions/{id}/pinned-item', [V1Stream::class, 'pinItem']);
        Route::delete('/live-sessions/{id}/pinned-item', [V1Stream::class, 'unpinItem']);

        // 16. Wallet & Coin Purchases
        Route::get('/me/wallet', [V1Wallet::class, 'getWallet']);
        Route::post('/coin-purchase-quotes', [V1Wallet::class, 'quoteCoinPurchase']);
        Route::post('/coin-purchases', [V1Wallet::class, 'purchaseCoins']);
        Route::get('/coin-purchases/{id}', [V1Wallet::class, 'getPurchaseStatus']);
        Route::post('/store-purchases/verify', [V1Wallet::class, 'verifyStorePurchase']);
        Route::get('/me/wallet/transactions', [V1Wallet::class, 'getTransactions']);

        // 17. Gifts
        Route::post('/gift-sends', [V1Gift::class, 'sendGift']);
        Route::get('/me/gifts', [V1Gift::class, 'myGifts']);

        // 18. Subscriptions
        Route::post('/subscription-quotes', [V1Subscription::class, 'quoteSubscription']);
        Route::post('/subscriptions', [V1Subscription::class, 'subscribe']);
        Route::get('/me/subscriptions', [V1Subscription::class, 'mySubscriptions']);
        Route::patch('/subscriptions/{id}', [V1Subscription::class, 'toggleRenewal']);

        // 19. Rewards
        Route::get('/me/rewards', [V1Reward::class, 'getMyRewards']);
        Route::post('/wallet/conversion-quotes', [V1Reward::class, 'quoteConversion']);
        Route::post('/wallet/conversions', [V1Reward::class, 'confirmConversion']);

        // 20. Direct Chat
        Route::get('/me/conversations', [V1Chat::class, 'getConversations']);
        Route::post('/conversations', [V1Chat::class, 'createOrGetConversation']);
        Route::get('/conversations/{id}/messages', [V1Chat::class, 'getMessages']);
        Route::post('/conversations/{id}/messages', [V1Chat::class, 'sendMessage']);
        Route::put('/conversations/{id}/read', [V1Chat::class, 'markAsRead']);
        Route::post('/conversations/{id}/delivered', [V1Chat::class, 'markAsDelivered']);

        // 21. Notifications
        Route::get('/me/notifications', [V1Notification::class, 'index']);
        Route::get('/me/notifications/unread-count', [V1Notification::class, 'unreadCount']);
        Route::patch('/me/notifications/{id}', [V1Notification::class, 'markAsRead']);
        Route::post('/me/notifications/read-all', [V1Notification::class, 'markAllAsRead']);
        Route::put('/me/devices/{installationId}', [V1Notification::class, 'registerDevice']);
        Route::delete('/me/devices/{installationId}', [V1Notification::class, 'unregisterDevice']);

        // 22. Moderation
        Route::post('/reports', [V1Moderation::class, 'submitReport']);
        Route::put('/me/blocked-users/{userId}', [V1Moderation::class, 'blockUser']);
        Route::delete('/me/blocked-users/{userId}', [V1Moderation::class, 'unblockUser']);
    });
});
