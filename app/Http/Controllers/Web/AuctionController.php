<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auction;
use App\Models\Bid;
use App\Services\AuctionEngineService;

class AuctionController extends Controller
{
    public function index(Request $request)
    {
        $auctions = Auction::with(['seller', 'product', 'highestBidder', 'bids'])
            ->where('status', 'active')
            ->where('is_blurred', false)
            ->orderBy('ends_at', 'asc')
            ->get();

        $upcomingAuctions = Auction::with(['seller', 'product', 'highestBidder', 'bids'])
            ->where('status', 'upcoming')
            ->orderBy('starts_at', 'asc')
            ->get();

        $activeAuction = $auctions->first();

        return view('auctions.index', compact('auctions', 'upcomingAuctions', 'activeAuction'));
    }

    public function show($id = null)
    {
        $query = Auction::with(['seller.sellerProfile', 'product', 'highestBidder', 'bids.user']);
        
        $auction = $id ? $query->find($id) : null;
        if (!$auction) {
            $auction = Auction::with(['seller.sellerProfile', 'product', 'highestBidder', 'bids.user'])
                ->where('status', 'active')
                ->where('is_blurred', false)
                ->first() 
                ?? Auction::with(['seller.sellerProfile', 'product', 'highestBidder', 'bids.user'])->first() 
                ?? new Auction();
        }

        $similarAuctions = collect();
        if ($auction->id) {
            $similarAuctions = Auction::with(['seller', 'product', 'bids'])
                ->where('id', '!=', $auction->id)
                ->where('status', 'active')
                ->where('is_blurred', false)
                ->orderBy('ends_at', 'asc')
                ->take(4)
                ->get();
        }

        return view('auctions.show', compact('auction', 'similarAuctions'));
    }

    public function result($id = null)
    {
        $auction = $id ? Auction::with(['seller', 'highestBidder', 'product'])->find($id) : null;
        if (!$auction) {
            $auction = Auction::with(['seller', 'highestBidder', 'product'])->first() ?? new Auction();
        }

        return view('auctions.result', compact('auction'));
    }

    public function placeBid(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Please login to place a bid.'], 401);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $engine = new AuctionEngineService();
        $ip = $request->ip() ?? '127.0.0.1';
        $res = $engine->placeBid((int) $id, auth()->user(), (float) $request->amount, $ip);

        return response()->json($res, $res['success'] ? 200 : 422);
    }
}
