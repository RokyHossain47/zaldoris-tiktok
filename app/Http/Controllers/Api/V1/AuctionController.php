<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuctionController extends BaseApiController
{
    /**
     * GET /api/v1/auctions
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->input('status', 'live'); // 'live', 'upcoming', 'ended', 'all'
        $q = $request->input('q');
        $categoryId = $request->input('categoryId');

        $query = Auction::with(['seller', 'product']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($q) {
            $query->where('title', 'like', "%{$q}%");
        }

        $auctions = $query->orderBy('end_time', 'asc')->paginate($request->input('limit', 15));

        $items = collect($auctions->items())->map(function($a) {
            $currBid = (float)$a->current_highest_bid;
            $startBid = (float)$a->starting_bid;
            $minNext = $currBid > 0 ? ($currBid + (float)$a->bid_increment) : $startBid;

            return [
                'id' => $a->id,
                'title' => $a->title,
                'coverImage' => $a->cover_image,
                'status' => $a->status,
                'settlementCurrency' => setting('currency_code', 'CAD'),
                'startingBid' => $startBid,
                'startingBidMinor' => $this->toMinorUnits($startBid),
                'currentHighestBid' => $currBid,
                'currentHighestBidMinor' => $this->toMinorUnits($currBid),
                'minimumNextBid' => $minNext,
                'minimumNextBidMinor' => $this->toMinorUnits($minNext),
                'bidIncrement' => (float)$a->bid_increment,
                'totalBids' => (int)$a->total_bids_count,
                'startTime' => $a->start_time?->toISOString(),
                'endTime' => $a->end_time?->toISOString(),
                'seller' => $a->seller ? [
                    'id' => $a->seller->id,
                    'name' => $a->seller->name,
                    'avatarUrl' => $a->seller->avatar_url,
                ] : null,
            ];
        });

        return $this->paginated($auctions, $items, 'Auctions list retrieved');
    }

    /**
     * GET /api/v1/auctions/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $auction = Auction::with(['seller.sellerProfile', 'product', 'highestBidder', 'bids.user'])->find($id);
        if (!$auction) {
            return $this->error('Auction not found', 'AUCTION_NOT_FOUND', 404);
        }

        $user = $request->user();
        $ownHighestBid = null;
        if ($user) {
            $ownHighestBid = Bid::where('auction_id', $auction->id)
                ->where('user_id', $user->id)
                ->max('bid_amount');
        }

        $currBid = (float)$auction->current_highest_bid;
        $startBid = (float)$auction->starting_bid;
        $minNext = $currBid > 0 ? ($currBid + (float)$auction->bid_increment) : $startBid;

        return $this->success([
            'id' => $auction->id,
            'title' => $auction->title,
            'description' => $auction->description,
            'coverImage' => $auction->cover_image,
            'status' => $auction->status,
            'settlementCurrency' => setting('currency_code', 'CAD'),
            'startingBid' => $startBid,
            'startingBidMinor' => $this->toMinorUnits($startBid),
            'currentHighestBid' => $currBid,
            'currentHighestBidMinor' => $this->toMinorUnits($currBid),
            'ownHighestBid' => $ownHighestBid ? (float)$ownHighestBid : null,
            'minimumNextBid' => $minNext,
            'minimumNextBidMinor' => $this->toMinorUnits($minNext),
            'bidIncrement' => (float)$auction->bid_increment,
            'totalBids' => (int)$auction->total_bids_count,
            'startTime' => $auction->start_time?->toISOString(),
            'endTime' => $auction->end_time?->toISOString(),
            'serverTime' => now()->toISOString(),
            'isSuddenDeathExtended' => (bool)$auction->is_sudden_death_extended,
            'winnerId' => $auction->winner_user_id,
            'highestBidder' => $auction->highestBidder ? [
                'id' => $auction->highestBidder->id,
                'name' => $auction->highestBidder->name,
                'avatarUrl' => $auction->highestBidder->avatar_url,
            ] : null,
            'seller' => $auction->seller ? [
                'id' => $auction->seller->id,
                'name' => $auction->seller->name,
                'avatarUrl' => $auction->seller->avatar_url,
                'storeName' => $auction->seller->sellerProfile?->store_name ?: $auction->seller->name,
            ] : null,
            'rules' => [
                'snipingProtectionSeconds' => 15,
                'holdAmountPercent' => 10,
                'termsVersion' => 'v1.4',
            ]
        ], 'Auction details retrieved');
    }

    /**
     * GET /api/v1/auctions/{id}/bids
     */
    public function getBids(Request $request, $id): JsonResponse
    {
        $auction = Auction::find($id);
        if (!$auction) {
            return $this->error('Auction not found', 'NOT_FOUND', 404);
        }

        $bids = Bid::with('user')
            ->where('auction_id', $id)
            ->orderBy('id', 'desc')
            ->paginate($request->input('limit', 20));

        $items = collect($bids->items())->map(function($b) {
            return [
                'id' => $b->id,
                'bidAmount' => (float)$b->bid_amount,
                'bidAmountMinor' => $this->toMinorUnits($b->bid_amount),
                'currency' => setting('currency_code', 'CAD'),
                'isWinning' => (bool)$b->is_winning_bid,
                'createdAt' => $b->created_at->toISOString(),
                'user' => [
                    'id' => $b->user?->id,
                    'name' => $b->user?->name ?: 'Anonymous Bidder',
                    'avatarUrl' => $b->user?->avatar_url,
                ]
            ];
        });

        return $this->paginated($bids, $items, 'Bid history retrieved');
    }

    /**
     * POST /api/v1/auctions/{id}/bid-previews
     */
    public function previewBid(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $auction = Auction::find($id);
        if (!$auction) {
            return $this->error('Auction not found', 'NOT_FOUND', 404);
        }

        $proposedAmount = (float) $request->amount;
        $currBid = (float) $auction->current_highest_bid;
        $minAllowed = $currBid > 0 ? ($currBid + (float)$auction->bid_increment) : (float)$auction->starting_bid;

        if ($proposedAmount < $minAllowed) {
            return $this->error("Proposed bid must be at least {$minAllowed}.", 'BID_TOO_LOW', 400);
        }

        $holdAmount = round($proposedAmount * 0.10, 2);

        return $this->success([
            'auctionId' => $auction->id,
            'proposedAmount' => $proposedAmount,
            'proposedAmountMinor' => $this->toMinorUnits($proposedAmount),
            'requiredHoldDeposit' => $holdAmount,
            'requiredHoldDepositMinor' => $this->toMinorUnits($holdAmount),
            'currency' => setting('currency_code', 'CAD'),
            'isValid' => true,
            'expiresInSeconds' => 30,
        ], 'Bid preview validated');
    }

    /**
     * POST /api/v1/auctions/{id}/bids
     * Atomic bid submission with anti-sniping extension
     */
    public function submitBid(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $user = $request->user();
        if ($user->is_banned_from_auctions) {
            return $this->error('Your account is restricted from placing live bids.', 'USER_RESTRICTED', 403);
        }

        $proposedAmount = (float) $request->amount;

        return DB::transaction(function () use ($id, $user, $proposedAmount) {
            $auction = Auction::where('id', $id)->lockForUpdate()->first();
            if (!$auction) {
                return $this->error('Auction not found', 'NOT_FOUND', 404);
            }

            if ($auction->status !== 'live') {
                return $this->error('This auction is no longer active for bidding.', 'AUCTION_CLOSED', 400);
            }

            if ($auction->end_time && $auction->end_time->isPast()) {
                $auction->update(['status' => 'ended']);
                return $this->error('Bidding time has expired.', 'AUCTION_ENDED', 400);
            }

            $currBid = (float) $auction->current_highest_bid;
            $minAllowed = $currBid > 0 ? ($currBid + (float)$auction->bid_increment) : (float)$auction->starting_bid;

            if ($proposedAmount < $minAllowed) {
                return $this->error("Bid too low. Minimum required bid is {$minAllowed}.", 'OUTBID_RACE_CONDITION', 409);
            }

            // Unmark previous winning bids
            Bid::where('auction_id', $auction->id)->update(['is_winning_bid' => false]);

            $bid = Bid::create([
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'bid_amount' => $proposedAmount,
                'max_proxy_amount' => $proposedAmount,
                'is_winning_bid' => true,
                'status' => 'placed',
            ]);

            // Anti-sniping: If bid placed within last 15s, extend by 15s
            $endTime = $auction->end_time;
            if ($endTime && $endTime->diffInSeconds(now()) < 15) {
                $endTime = now()->addSeconds(15);
                $auction->is_sudden_death_extended = true;
            }

            $auction->current_highest_bid = $proposedAmount;
            $auction->highest_bidder_id = $user->id;
            $auction->total_bids_count = ($auction->total_bids_count ?: 0) + 1;
            $auction->end_time = $endTime;
            $auction->save();

            return $this->success([
                'bidId' => $bid->id,
                'auctionId' => $auction->id,
                'bidAmount' => (float)$bid->bid_amount,
                'bidAmountMinor' => $this->toMinorUnits($bid->bid_amount),
                'currency' => setting('currency_code', 'CAD'),
                'isWinning' => true,
                'totalBids' => (int)$auction->total_bids_count,
                'newEndTime' => $auction->end_time?->toISOString(),
            ], 'Bid submitted successfully and confirmed as current highest bid', [], 201);
        });
    }

    /**
     * GET /api/v1/me/auctions
     */
    public function myAuctions(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $status = $request->input('status'); // 'won', 'active', 'lost'

        $auctionIds = Bid::where('user_id', $userId)->pluck('auction_id')->unique()->toArray();

        $query = Auction::with(['seller', 'product'])->whereIn('id', $auctionIds);

        if ($status === 'won') {
            $query->where('status', 'ended')->where('winner_user_id', $userId);
        } elseif ($status === 'active') {
            $query->where('status', 'live');
        }

        $auctions = $query->latest()->paginate($request->input('limit', 15));

        $items = collect($auctions->items())->map(function($a) use ($userId) {
            $myMax = Bid::where('auction_id', $a->id)->where('user_id', $userId)->max('bid_amount');
            $outcome = 'participating';
            if ($a->status === 'ended') {
                $outcome = ($a->winner_user_id === $userId) ? 'won' : 'lost';
            }

            return [
                'id' => $a->id,
                'title' => $a->title,
                'coverImage' => $a->cover_image,
                'status' => $a->status,
                'outcome' => $outcome,
                'currentHighestBid' => (float)$a->current_highest_bid,
                'myHighestBid' => (float)$myMax,
                'currency' => setting('currency_code', 'CAD'),
                'endTime' => $a->end_time?->toISOString(),
            ];
        });

        return $this->paginated($auctions, $items, 'My participated auctions retrieved');
    }

    /**
     * GET /api/v1/me/auctions/{id}
     */
    public function showMyAuction(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;
        $auction = Auction::with(['seller', 'product', 'highestBidder'])->find($id);

        if (!$auction) {
            return $this->error('Auction not found', 'NOT_FOUND', 404);
        }

        $myBids = Bid::where('auction_id', $auction->id)
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        $isWinner = ($auction->winner_user_id === $userId);

        return $this->success([
            'auctionId' => $auction->id,
            'title' => $auction->title,
            'status' => $auction->status,
            'isWinner' => $isWinner,
            'winningBid' => (float)$auction->current_highest_bid,
            'myBidsCount' => $myBids->count(),
            'myHighestBid' => (float)$myBids->max('bid_amount'),
            'bidsHistory' => $myBids,
            'checkoutAvailable' => $isWinner && $auction->status === 'ended',
        ], 'Personal auction outcome retrieved');
    }

    /**
     * POST /api/v1/me/bid-verification-sessions
     */
    public function createBidVerification(Request $request): JsonResponse
    {
        return $this->success([
            'sessionId' => 'bvs_' . Str::random(20),
            'status' => 'verified',
            'eligibility' => [
                'canBid' => true,
                'maxBidLimit' => 5000.00,
                'holdAuthorized' => true,
            ]
        ], 'Bidder verification session approved');
    }
}
