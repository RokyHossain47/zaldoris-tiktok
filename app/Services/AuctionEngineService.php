<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuctionEngineService
{
    /**
     * Place a bid on an active auction with concurrency safety and validation.
     */
    public function placeBid(int $auctionId, User $bidder, float $amount, string $ipAddress, ?string $deviceFingerprint = null): array
    {
        return DB::transaction(function () use ($auctionId, $bidder, $amount, $ipAddress, $deviceFingerprint) {
            $auction = Auction::where('id', $auctionId)->lockForUpdate()->first();

            if (!$auction) {
                return ['success' => false, 'message' => 'Auction not found.'];
            }

            if ($auction->status !== 'active') {
                return ['success' => false, 'message' => 'Auction is not currently active.'];
            }

            if ($auction->isExpired()) {
                $this->expireAuction($auction);
                return ['success' => false, 'message' => 'Auction has already ended.'];
            }

            if ($bidder->is_banned_from_auctions) {
                return ['success' => false, 'message' => 'Your account is restricted from placing bids due to policy violations.'];
            }

            if ($bidder->id === $auction->seller_id) {
                return ['success' => false, 'message' => 'You cannot bid on your own auction.'];
            }

            $minAllowed = $auction->current_bid + $auction->min_bid_step;
            if ($amount < $minAllowed) {
                return [
                    'success' => false,
                    'message' => "Bid amount must be at least $" . number_format($minAllowed, 2),
                ];
            }

            // Check anti-shill rules
            $fraudService = new FraudDetectionService();
            $shillCheck = $fraudService->checkShillBidding($bidder, $auction, $ipAddress, $deviceFingerprint);

            if (!$shillCheck['allowed']) {
                return ['success' => false, 'message' => $shillCheck['reason']];
            }

            // Create Bid record
            $bid = Bid::create([
                'auction_id' => $auction->id,
                'user_id' => $bidder->id,
                'amount' => $amount,
                'ip_address' => $ipAddress,
                'device_fingerprint' => $deviceFingerprint,
                'is_flagged_shill' => $shillCheck['flagged'],
            ]);

            // Update Auction state
            $auction->current_bid = $amount;
            $auction->highest_bidder_id = $bidder->id;

            // Anti-sniping: extend timer by 15 seconds if bid placed within last 15s
            if ($auction->ends_at && now()->diffInSeconds($auction->ends_at, false) < 15) {
                $auction->ends_at = now()->addSeconds(15);
            }

            $auction->save();

            return [
                'success' => true,
                'message' => "Bid of $" . number_format($amount, 2) . " placed successfully!",
                'current_bid' => $auction->current_bid,
                'highest_bidder' => $bidder->name,
                'ends_at' => $auction->ends_at ? $auction->ends_at->toIso8601String() : null,
                'bid' => $bid,
            ];
        });
    }

    /**
     * Expire auction and apply Whatnot-style SRS rules.
     */
    public function expireAuction(Auction $auction): void
    {
        if ($auction->status !== 'active') {
            return;
        }

        // Check if reserve price was met (or if no reserve, check if any bid placed)
        $hasWinner = false;
        if ($auction->highest_bidder_id) {
            if (!$auction->reserve_price || $auction->current_bid >= $auction->reserve_price) {
                $hasWinner = true;
            }
        }

        if ($hasWinner) {
            $auction->status = 'sold';
            $auction->save();
        } else {
            // Failed auction
            $auction->fail_count += 1;
            $auction->status = 'failed';
            $auction->is_blurred = true; // Listing blur when auction expires (SRS Page 3)

            // Automatic removal after 3 failed auctions (SRS Page 3)
            if ($auction->fail_count >= 3) {
                $auction->status = 'cancelled';
                $auction->relist_available_at = now()->addMonth(); // 1-month restriction before re-listing
            }

            $auction->save();
        }
    }

    /**
     * Extend auction duration by 5 minutes due to technical stream failure (SRS #16).
     */
    public function extendForStreamFailover(Auction $auction): void
    {
        if ($auction->status === 'active' && $auction->ends_at) {
            $auction->ends_at = $auction->ends_at->addMinutes(5);
            $auction->save();
        }
    }
}
