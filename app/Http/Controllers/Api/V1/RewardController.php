<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\RewardTier;
use App\Models\UserReward;
use App\Models\CoinTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RewardController extends BaseApiController
{
    /**
     * GET /api/v1/reward-tiers
     */
    public function getTiers(): JsonResponse
    {
        $tiers = RewardTier::orderBy('tier_level', 'asc')->get();
        if ($tiers->isEmpty()) {
            $defaults = [
                ['tier_level' => 1, 'name' => 'Bronze Explorer', 'min_xp' => 0, 'max_xp' => 500, 'badge_color' => '#CD7F32', 'benefits' => ['Standard badge', '1% coin cashback on store purchases']],
                ['tier_level' => 2, 'name' => 'Silver VIP', 'min_xp' => 501, 'max_xp' => 2000, 'badge_color' => '#C0C0C0', 'benefits' => ['Silver badge', '3% coin cashback', 'Priority auction bidding status']],
                ['tier_level' => 3, 'name' => 'Gold Legend', 'min_xp' => 2001, 'max_xp' => 5000, 'badge_color' => '#FFD700', 'benefits' => ['Gold crown badge', '5% coin cashback', 'Exclusive live stream chat glow']],
                ['tier_level' => 4, 'name' => 'Diamond Elite', 'min_xp' => 5001, 'max_xp' => 20000, 'badge_color' => '#00F0C8', 'benefits' => ['Diamond ultra badge', '10% coin cashback', 'VIP concierge support', 'Free monthly gift package']],
            ];
            foreach ($defaults as $d) {
                RewardTier::create($d);
            }
            $tiers = RewardTier::orderBy('tier_level', 'asc')->get();
        }

        return $this->success($tiers, 'Reward tiers and progression benefits retrieved');
    }

    /**
     * GET /api/v1/me/rewards
     */
    public function getMyRewards(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $reward = UserReward::firstOrCreate(
            ['user_id' => $userId],
            ['xp_points' => 350, 'lifetime_xp' => 350, 'streak_days' => 3]
        );

        $tier = RewardTier::where('min_xp', '<=', $reward->xp_points)
            ->where('max_xp', '>=', $reward->xp_points)
            ->first() ?: RewardTier::first();

        $nextTier = RewardTier::where('tier_level', '>', $tier?->tier_level ?: 1)->orderBy('tier_level', 'asc')->first();

        $progressPercent = 100;
        if ($nextTier && ($nextTier->min_xp - ($tier?->min_xp ?: 0)) > 0) {
            $currentRange = $nextTier->min_xp - ($tier?->min_xp ?: 0);
            $earnedInCurrent = $reward->xp_points - ($tier?->min_xp ?: 0);
            $progressPercent = min(100, max(0, round(($earnedInCurrent / $currentRange) * 100)));
        }

        return $this->success([
            'currentXp' => (int)$reward->xp_points,
            'lifetimeXp' => (int)$reward->lifetime_xp,
            'streakDays' => (int)$reward->streak_days,
            'currentTier' => [
                'level' => $tier?->tier_level ?: 1,
                'name' => $tier?->name ?: 'Bronze Explorer',
                'badgeColor' => $tier?->badge_color ?: '#00F0C8',
                'benefits' => $tier?->benefits ?: [],
            ],
            'nextTier' => $nextTier ? [
                'level' => $nextTier->tier_level,
                'name' => $nextTier->name,
                'requiredXp' => $nextTier->min_xp,
                'xpRemaining' => max(0, $nextTier->min_xp - $reward->xp_points),
                'progressPercent' => $progressPercent,
            ] : null,
        ], 'User gamification reward status retrieved');
    }

    /**
     * POST /api/v1/wallet/conversion-quotes
     */
    public function quoteConversion(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1', // coins or earnings
            'direction' => 'required|string|in:earnings_to_coins,coins_to_earnings',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $rate = 0.01; // 100 coins = $1.00 USD
        $inputAmount = (float)$request->amount;

        if ($request->direction === 'earnings_to_coins') {
            $converted = (int) round($inputAmount / $rate);
            $fee = 0.00;
        } else {
            $converted = round($inputAmount * $rate * 0.90, 2); // 10% platform conversion fee
            $fee = round($inputAmount * $rate * 0.10, 2);
        }

        return $this->success([
            'quoteId' => 'CNVQ-' . strtoupper(Str::random(12)),
            'direction' => $request->direction,
            'inputAmount' => $inputAmount,
            'convertedAmount' => $converted,
            'conversionFee' => $fee,
            'exchangeRate' => $rate,
            'expiresInSeconds' => 120,
        ], 'Conversion quote calculated');
    }

    /**
     * POST /api/v1/wallet/conversions
     */
    public function confirmConversion(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'direction' => 'required|string|in:earnings_to_coins,coins_to_earnings',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $user = $request->user();
        $direction = $request->direction;
        $amount = (float)$request->amount;

        if ($direction === 'earnings_to_coins') {
            if ($user->earnings_usd < $amount) {
                return $this->error('Insufficient creator earnings balance for conversion.', 'INSUFFICIENT_FUNDS', 400);
            }
            $coinsToAdd = (int) round($amount / 0.01);

            $user->earnings_usd -= $amount;
            $user->coin_balance += $coinsToAdd;
            $user->save();

            CoinTransaction::create([
                'user_id' => $user->id,
                'type' => 'conversion',
                'amount' => $coinsToAdd,
                'balance_after' => $user->coin_balance,
                'description' => "Converted \${$amount} earnings to {$coinsToAdd} coins",
                'reference_id' => 'CNV-' . strtoupper(Str::random(10)),
            ]);

            return $this->success([
                'direction' => $direction,
                'convertedAmount' => $coinsToAdd,
                'newCoinBalance' => (int)$user->coin_balance,
                'newEarningsUsd' => (float)$user->earnings_usd,
            ], "Successfully converted \${$amount} earnings into {$coinsToAdd} coins");
        }

        return $this->error('Direct coin-to-fiat conversion is subject to KYC merchant verification.', 'KYC_REQUIRED', 403);
    }
}
