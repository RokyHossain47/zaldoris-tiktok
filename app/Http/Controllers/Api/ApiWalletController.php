<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoinPackage;
use App\Models\CoinTransaction;
use App\Models\Gift;
use App\Models\GiftTransaction;
use App\Models\CreatorSubscription;
use App\Models\Stream;
use App\Models\User;
use App\Services\TaxService;
use App\Services\FraudDetectionService;
use Illuminate\Support\Facades\DB;

class ApiWalletController extends Controller
{
    /**
     * Get Coin Packages & Current Balance.
     */
    public function getPackages(Request $request)
    {
        $packages = CoinPackage::where('is_active', true)->get()->map(function ($pkg) {
            $hst = TaxService::calculateHst($pkg->price_usd);
            $pkg->hst_tax = $hst;
            $pkg->total_price_with_tax = round($pkg->price_usd + $hst, 2);
            return $pkg;
        });

        return response()->json([
            'success' => true,
            'current_coin_balance' => $request->user() ? $request->user()->coin_balance : 0,
            'packages' => $packages,
        ]);
    }

    /**
     * Purchase Coin Package (with 13% HST & velocity checks).
     */
    public function purchaseCoins(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'package_id' => 'required|exists:coin_packages,id',
            'payment_method' => 'nullable|string',
        ]);

        $package = CoinPackage::findOrFail($request->package_id);
        $fraudService = new FraudDetectionService();
        $limitCheck = $fraudService->checkCoinPurchaseLimit($user, (float) $package->price_usd);

        if (!$limitCheck['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $limitCheck['message'],
            ], 422);
        }

        return DB::transaction(function () use ($user, $package, $request) {
            $hstTax = TaxService::calculateHst($package->price_usd);
            $totalCoins = $package->total_coins;

            $user->coin_balance += $totalCoins;
            if ($package->badge_tier) {
                $user->is_vip = true;
                $user->vip_badge_tier = $package->badge_tier;
            }
            $user->save();

            $transaction = CoinTransaction::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'amount_coins' => $totalCoins,
                'amount_usd' => $package->price_usd,
                'hst_tax_usd' => $hstTax,
                'payment_method' => $request->payment_method ?? 'apple_pay',
                'reference_id' => 'PKG-' . $package->id . '-' . time(),
                'description' => "Purchased {$package->name} ({$totalCoins} Coins)",
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully added {$totalCoins} coins to your wallet!",
                'new_coin_balance' => $user->coin_balance,
                'transaction' => $transaction,
            ]);
        });
    }

    /**
     * List 8 Core SRS Gifts (Page 4).
     */
    public function getGifts()
    {
        $gifts = Gift::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'gifts' => $gifts,
        ]);
    }

    /**
     * Send Live Stream Gift (Calculates 60% Creator Cut, 40% Platform).
     */
    public function sendStreamGift(Request $request, $streamId)
    {
        $user = $request->user();
        $stream = Stream::findOrFail($streamId);

        $request->validate([
            'gift_id' => 'required|exists:gifts,id',
        ]);

        $gift = Gift::findOrFail($request->gift_id);

        if ($user->coin_balance < $gift->coin_cost) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient coins. Please purchase more coins to send this gift.',
                'required' => $gift->coin_cost,
                'balance' => $user->coin_balance,
            ], 400);
        }

        $usdValue = round($gift->coin_cost * 0.0142, 2);
        $fraudService = new FraudDetectionService();
        $velocityCheck = $fraudService->checkGiftVelocity($user, $stream->host_id, $usdValue);

        return DB::transaction(function () use ($user, $stream, $gift, $usdValue, $velocityCheck) {
            // Deduct coins from user
            $user->coin_balance -= $gift->coin_cost;
            $user->save();

            CoinTransaction::create([
                'user_id' => $user->id,
                'type' => 'gift_sent',
                'amount_coins' => -$gift->coin_cost,
                'description' => "Sent {$gift->name} to {$stream->host->name}",
            ]);

            // 60% Creator Revenue Share (SRS Page 4)
            $creatorShare = round($usdValue * TaxService::CREATOR_GIFT_REVENUE_SHARE, 2);
            $platformCommission = round($usdValue - $creatorShare, 2);

            $host = $stream->host;
            $host->earnings_usd += $creatorShare;
            $host->save();

            // Stream stats
            $stream->increment('total_gift_coins', $gift->coin_cost);

            $gt = GiftTransaction::create([
                'stream_id' => $stream->id,
                'sender_id' => $user->id,
                'receiver_id' => $host->id,
                'gift_id' => $gift->id,
                'coin_amount' => $gift->coin_cost,
                'creator_earning_usd' => $creatorShare,
                'platform_commission_usd' => $platformCommission,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Gift {$gift->name} sent!",
                'gift' => $gift,
                'new_coin_balance' => $user->coin_balance,
                'stream_total_gift_coins' => $stream->total_gift_coins,
                'fraud_warning' => $velocityCheck['warning'] ?? null,
            ]);
        });
    }

    /**
     * Subscribe to Creator ($7.99 Monthly, SRS Page 4).
     */
    public function subscribeToCreator(Request $request, $creatorId)
    {
        $user = $request->user();
        $creator = User::findOrFail($creatorId);

        $subscription = CreatorSubscription::updateOrCreate(
            ['subscriber_id' => $user->id, 'creator_id' => $creator->id],
            [
                'monthly_price' => TaxService::CREATOR_SUBSCRIPTION_PRICE,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Subscribed to {$creator->name} for $7.99/month!",
            'subscription' => $subscription,
        ]);
    }
}
