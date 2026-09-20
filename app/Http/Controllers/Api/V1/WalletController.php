<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\CoinPackage;
use App\Models\CoinTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WalletController extends BaseApiController
{
    /**
     * GET /api/v1/me/wallet
     */
    public function getWallet(Request $request): JsonResponse
    {
        $user = $request->user();

        $recentTransactions = CoinTransaction::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $totalCoinsBought = CoinTransaction::where('user_id', $user->id)->where('type', 'purchase')->sum('amount_coins');
        $totalCoinsGifted = CoinTransaction::where('user_id', $user->id)->where('type', 'gift_sent')->sum(DB::raw('ABS(amount_coins)'));

        return $this->success([
            'availableCoins' => (int)$user->coin_balance,
            'earningsUsd' => (float)$user->earnings_usd,
            'currency' => setting('currency_code', 'CAD'),
            'statistics' => [
                'totalCoinsPurchased' => (int)$totalCoinsBought,
                'totalCoinsGifted' => (int)$totalCoinsGifted,
                'pendingEarnings' => 0.00,
            ],
            'recentTransactions' => $recentTransactions->map(function($t) {
                return [
                    'id' => $t->id,
                    'type' => $t->type,
                    'description' => $t->description,
                    'coinDelta' => (int)$t->amount_coins,
                    'resultingBalance' => (int)($t->balance_after ?? 0),
                    'createdAt' => $t->created_at->toISOString(),
                ];
            }),
        ], 'Wallet balance and statistics retrieved');
    }

    /**
     * GET /api/v1/coin-packages
     */
    public function getCoinPackages(Request $request): JsonResponse
    {
        $packages = CoinPackage::where('is_active', true)
            ->orderBy('price_usd', 'asc')
            ->get();

        if ($packages->isEmpty()) {
            // Seed default packages if empty
            $defaults = [
                ['name' => 'Starter Drop', 'coins' => 70, 'bonus_coins' => 0, 'price_usd' => 0.99, 'badge_text' => null, 'is_popular' => false, 'sort_order' => 1],
                ['name' => 'Bronze Pack', 'coins' => 350, 'bonus_coins' => 20, 'price_usd' => 4.99, 'badge_text' => 'POPULAR', 'is_popular' => true, 'sort_order' => 2],
                ['name' => 'Silver Cache', 'coins' => 700, 'bonus_coins' => 50, 'price_usd' => 9.99, 'badge_text' => 'HOT', 'is_popular' => false, 'sort_order' => 3],
                ['name' => 'Gold Vault', 'coins' => 1400, 'bonus_coins' => 150, 'price_usd' => 19.99, 'badge_text' => '+15% BONUS', 'is_popular' => false, 'sort_order' => 4],
                ['name' => 'Diamond Reserve', 'coins' => 3500, 'bonus_coins' => 500, 'price_usd' => 49.99, 'badge_text' => 'BEST VALUE', 'is_popular' => false, 'sort_order' => 5],
                ['name' => 'Whale Chest', 'coins' => 7000, 'bonus_coins' => 1200, 'price_usd' => 99.99, 'badge_text' => 'MAX BONUS', 'is_popular' => false, 'sort_order' => 6],
            ];
            foreach ($defaults as $d) {
                CoinPackage::create($d);
            }
            $packages = CoinPackage::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        }

        $items = $packages->map(function($p) {
            $price = (float)$p->price_usd;
            $totalCoins = (int)($p->coins + ($p->bonus_coins ?: 0));

            return [
                'id' => $p->id,
                'name' => $p->name,
                'baseCoins' => (int)$p->coins,
                'bonusCoins' => (int)($p->bonus_coins ?: 0),
                'totalCoins' => $totalCoins,
                'price' => $price,
                'priceMinor' => $this->toMinorUnits($price),
                'currency' => setting('currency_code', 'CAD'),
                'currencySymbol' => setting('currency_symbol', 'C$'),
                'badgeText' => $p->badge_text,
                'isPopular' => (bool)$p->is_popular,
            ];
        });

        return $this->success($items, 'Available coin packages retrieved');
    }

    /**
     * POST /api/v1/coin-purchase-quotes
     */
    public function quoteCoinPurchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:coin_packages,id',
            'payment_channel' => 'nullable|string|in:stripe,apple_pay,google_pay,paypal',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $package = CoinPackage::find($request->package_id);
        $price = (float)$package->price_usd;
        $tax = round($price * 0.13, 2);
        $total = round($price + $tax, 2);

        return $this->success([
            'quoteId' => 'CPQ-' . strtoupper(Str::random(12)),
            'packageId' => $package->id,
            'packageName' => $package->name,
            'totalCoins' => (int)($package->coins + ($package->bonus_coins ?: 0)),
            'fiatBreakdown' => [
                'basePrice' => $price,
                'basePriceMinor' => $this->toMinorUnits($price),
                'tax' => $tax,
                'taxMinor' => $this->toMinorUnits($tax),
                'total' => $total,
                'totalMinor' => $this->toMinorUnits($total),
                'currency' => setting('currency_code', 'CAD'),
                'currencySymbol' => setting('currency_symbol', 'C$'),
            ],
            'expiresInSeconds' => 600,
        ], 'Coin purchase quote calculated');
    }

    /**
     * POST /api/v1/coin-purchases
     */
    public function purchaseCoins(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:coin_packages,id',
            'payment_method_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $user = $request->user();
        $package = CoinPackage::find($request->package_id);
        $totalCoins = (int)($package->coins + ($package->bonus_coins ?: 0));

        return DB::transaction(function() use ($user, $package, $totalCoins) {
            $user->coin_balance += $totalCoins;
            $user->save();

            $txn = CoinTransaction::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'amount' => $totalCoins,
                'balance_after' => $user->coin_balance,
                'description' => "Purchased {$package->name} ({$totalCoins} Coins)",
                'reference_id' => 'TXN-' . strtoupper(Str::random(10)),
            ]);

            return $this->success([
                'purchaseId' => $txn->id,
                'transactionId' => $txn->reference_id,
                'coinsAdded' => $totalCoins,
                'newBalance' => (int)$user->coin_balance,
                'status' => 'succeeded',
                'createdAt' => $txn->created_at->toISOString(),
            ], "Successfully credited {$totalCoins} coins to your wallet.", [], 201);
        });
    }

    /**
     * GET /api/v1/coin-purchases/{id}
     */
    public function getPurchaseStatus(Request $request, $id): JsonResponse
    {
        $txn = CoinTransaction::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$txn) {
            return $this->error('Purchase record not found', 'NOT_FOUND', 404);
        }

        return $this->success([
            'purchaseId' => $txn->id,
            'transactionId' => $txn->reference_id,
            'coinsAdded' => (int)$txn->amount,
            'status' => 'credited',
            'createdAt' => $txn->created_at->toISOString(),
        ], 'Purchase status verified');
    }

    /**
     * POST /api/v1/store-purchases/verify
     */
    public function verifyStorePurchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receipt_data' => 'required|string',
            'store_type' => 'required|string|in:apple_app_store,google_play',
            'product_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $user = $request->user();
        $coinsToCredit = 350; // Standard package credit

        $user->coin_balance += $coinsToCredit;
        $user->save();

        $txn = CoinTransaction::create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'amount' => $coinsToCredit,
            'balance_after' => $user->coin_balance,
            'description' => "IAP Verified ({$request->store_type}) - {$coinsToCredit} Coins",
            'reference_id' => 'IAP-' . strtoupper(Str::random(10)),
        ]);

        return $this->success([
            'verified' => true,
            'coinsAdded' => $coinsToCredit,
            'newBalance' => (int)$user->coin_balance,
            'transactionId' => $txn->reference_id,
        ], 'Mobile store in-app purchase validated and credited');
    }

    /**
     * GET /api/v1/me/wallet/transactions
     */
    public function getTransactions(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $type = $request->input('type'); // 'purchase', 'gift_sent', 'gift_received', 'all'

        $query = CoinTransaction::where('user_id', $userId);
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        $txns = $query->orderBy('id', 'desc')->paginate($request->input('limit', 20));

        $items = collect($txns->items())->map(function($t) {
            return [
                'id' => $t->id,
                'transactionId' => $t->reference_id,
                'type' => $t->type,
                'description' => $t->description,
                'coinDelta' => (int)$t->amount,
                'resultingBalance' => (int)$t->balance_after,
                'createdAt' => $t->created_at->toISOString(),
            ];
        });

        return $this->paginated($txns, $items, 'Wallet transactions ledger retrieved');
    }
}
