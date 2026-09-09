<?php

namespace App\Services;

use App\Models\User;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\GiftTransaction;
use App\Models\CoinTransaction;
use App\Models\FraudAlert;
use Carbon\Carbon;

class FraudDetectionService
{
    /**
     * Check for fake / bot accounts & shill bidding (SRS #3).
     * Rule: Flag accounts created within 30 days bidding repeatedly on same seller,
     * or duplicate IP address bidding on same seller's multiple auctions within 7 days.
     */
    public function checkShillBidding(User $bidder, Auction $auction, string $ipAddress, ?string $deviceFingerprint = null): array
    {
        $sellerId = $auction->seller_id;
        $isFlagged = false;
        $reasons = [];

        // 1. Cannot bid on own auction
        if ($bidder->id === $sellerId) {
            return [
                'allowed' => false,
                'reason' => 'You cannot bid on your own auction.',
                'flagged' => true,
            ];
        }

        // 2. Check account age < 30 days repeatedly bidding on same seller
        $accountAgeDays = $bidder->created_at ? $bidder->created_at->diffInDays(now()) : 0;
        if ($accountAgeDays < 30) {
            $priorBidsOnSeller = Bid::where('user_id', $bidder->id)
                ->whereHas('auction', function ($q) use ($sellerId) {
                    $q->where('seller_id', $sellerId);
                })->count();

            if ($priorBidsOnSeller >= 5) {
                $isFlagged = true;
                $reasons[] = "New account (<30 days) placed {$priorBidsOnSeller} bids on seller #{$sellerId}";
            }
        }

        // 3. Same IP address bidding on same seller's auctions within 7 days
        $sameIpSellerBids = Bid::where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subDays(7))
            ->whereHas('auction', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            })
            ->where('user_id', '!=', $bidder->id)
            ->exists();

        if ($sameIpSellerBids) {
            $isFlagged = true;
            $reasons[] = "Multiple accounts bidding from same IP ({$ipAddress}) on seller #{$sellerId} within 7 days";
        }

        if ($isFlagged) {
            FraudAlert::create([
                'user_id' => $bidder->id,
                'type' => 'shill_bidding',
                'severity' => 'high',
                'description' => implode(' | ', $reasons),
                'metadata' => [
                    'auction_id' => $auction->id,
                    'seller_id' => $sellerId,
                    'ip_address' => $ipAddress,
                    'device_fingerprint' => $deviceFingerprint,
                ],
                'status' => 'pending',
            ]);
        }

        return [
            'allowed' => true,
            'reason' => null,
            'flagged' => $isFlagged,
        ];
    }

    /**
     * Check Economy abuse and wash trading (SRS #13).
     * Rule: Flag any account sending gifts worth > $200 to a single seller within 24 hours.
     * Coin purchase limits: max $500 per 24h without identity verification.
     */
    public function checkGiftVelocity(User $sender, int $receiverId, float $giftUsdValue): array
    {
        $past24hGifts = GiftTransaction::where('sender_id', $sender->id)
            ->where('receiver_id', $receiverId)
            ->where('created_at', '>=', now()->subHours(24))
            ->sum('creator_earning_usd');

        if (($past24hGifts + $giftUsdValue) > 200.00) {
            FraudAlert::create([
                'user_id' => $sender->id,
                'type' => 'wash_trading',
                'severity' => 'high',
                'description' => "User sent over $200 in gifts to seller #{$receiverId} in 24 hours. Potential wash trading.",
                'metadata' => [
                    'receiver_id' => $receiverId,
                    'total_24h' => $past24hGifts + $giftUsdValue,
                ],
                'status' => 'pending',
            ]);

            return [
                'flagged' => true,
                'warning' => 'High gifting velocity detected. Transaction logged for review.',
            ];
        }

        return ['flagged' => false, 'warning' => null];
    }

    /**
     * Check 24-hour coin purchase limit ($500 max without ID verification) (SRS #13).
     */
    public function checkCoinPurchaseLimit(User $user, float $purchaseAmountUsd): array
    {
        if (!$user->is_verified) {
            $past24hPurchases = CoinTransaction::where('user_id', $user->id)
                ->where('type', 'purchase')
                ->where('created_at', '>=', now()->subHours(24))
                ->sum('amount_usd');

            if (($past24hPurchases + $purchaseAmountUsd) > 500.00) {
                return [
                    'allowed' => false,
                    'message' => 'Daily coin purchase limit of $500 reached for unverified accounts. Please complete ID verification to increase limits.',
                ];
            }
        }

        return ['allowed' => true, 'message' => null];
    }
}
