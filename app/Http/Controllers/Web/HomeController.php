<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stream;
use App\Models\Product;
use App\Models\Auction;
use App\Models\Advertisement;
use App\Models\User;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $liveStreams = Stream::with(['host', 'products'])
            ->where('is_live', true)
            ->orderBy('is_boosted', 'desc')
            ->orderBy('viewer_count', 'desc')
            ->take(8)
            ->get();

        $liveShoppingStreams = Stream::with(['host', 'products'])
            ->where('is_live', true)
            ->where('stream_type', 'live_shopping')
            ->take(4)
            ->get();

        $activeAuctions = Auction::with(['seller', 'highestBidder'])
            ->where('status', 'active')
            ->where('is_blurred', false)
            ->orderBy('ends_at', 'asc')
            ->take(6)
            ->get();

        $featuredProducts = Product::with('seller')
            ->where('status', 'active')
            ->take(8)
            ->get();

        $topCreators = User::where('role', 'creator')
            ->with('creatorProfile')
            ->take(5)
            ->get();

        $banners = Advertisement::where('is_active', true)
            ->where('ad_type', 'banner')
            ->get();

        return view('home', compact(
            'liveStreams',
            'liveShoppingStreams',
            'activeAuctions',
            'featuredProducts',
            'topCreators',
            'banners'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $category = $request->input('category', 'all');

        $streams = Stream::with('host')
            ->where('is_live', true)
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->when($category !== 'all', function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->get();

        $products = Product::with('seller')
            ->where('status', 'active')
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->when($category !== 'all', function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->get();

        $auctions = Auction::where('status', 'active')
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->get();

        return view('search', compact('query', 'category', 'streams', 'products', 'auctions'));
    }
}
