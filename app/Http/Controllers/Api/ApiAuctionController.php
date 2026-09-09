<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auction;
use App\Services\AuctionEngineService;

class ApiAuctionController extends Controller
{
    /**
     * List Live & Upcoming Auctions.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');

        $auctions = Auction::with(['seller', 'product', 'highestBidder'])
            ->where('status', $status)
            ->where('is_blurred', false)
            ->orderBy('ends_at', 'asc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $auctions,
        ]);
    }

    /**
     * Get Auction Details and Bid History.
     */
    public function show($id)
    {
        $auction = Auction::with(['seller.sellerProfile', 'product', 'highestBidder', 'bids.user'])
            ->findOrFail($id);

        $timeRemaining = $auction->ends_at ? max(0, now()->diffInSeconds($auction->ends_at, false)) : 0;

        return response()->json([
            'success' => true,
            'auction' => $auction,
            'next_min_bid' => $auction->next_minimum_bid,
            'time_remaining_seconds' => $timeRemaining,
            'is_expired' => $auction->isExpired(),
        ]);
    }

    /**
     * Place a Real-Time Bid.
     */
    public function placeBid(Request $request, $id)
    {
        $user = $request->user();

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'device_fingerprint' => 'nullable|string',
        ]);

        $auctionEngine = new AuctionEngineService();
        $ip = $request->ip() ?? '127.0.0.1';
        $result = $auctionEngine->placeBid((int) $id, $user, (float) $request->amount, $ip, $request->device_fingerprint);

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }
}
