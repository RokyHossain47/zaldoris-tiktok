<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stream;
use App\Models\Product;
use App\Models\Auction;
use App\Models\Advertisement;
use App\Models\User;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order', 'asc')->get();
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

        // Featured Products
        $featuredProducts = Product::with(['seller', 'category'])
            ->where('status', 'active')
            ->where('is_featured', true)
            ->take(8)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::with(['seller', 'category'])
                ->where('status', 'active')
                ->take(8)
                ->get();
        }

        // Trending Products
        $trendingProducts = Product::with(['seller', 'category'])
            ->where('status', 'active')
            ->where('is_trending', true)
            ->take(6)
            ->get();

        if ($trendingProducts->isEmpty()) {
            $trendingProducts = Product::with(['seller', 'category'])
                ->where('status', 'active')
                ->latest()
                ->take(6)
                ->get();
        }

        // Recently Viewed Products from Browser Session
        $recentIds = session()->get('recently_viewed_products', []);
        $recentlyViewedProducts = !empty($recentIds)
            ? Product::with(['seller', 'category'])
                ->whereIn('id', $recentIds)
                ->where('status', 'active')
                ->get()
                ->sortBy(function ($p) use ($recentIds) {
                    return array_search($p->id, $recentIds);
                })
                ->values()
            : Product::with(['seller', 'category'])
                ->where('status', 'active')
                ->take(4)
                ->get();

        $topCreators = User::where('role', 'creator')
            ->with('creatorProfile')
            ->take(5)
            ->get();

        // Dynamic Banners (Slider & Sidebar)
        $mainBanners = Advertisement::where('is_active', true)
            ->where(function($q) {
                $q->where('placement', 'homepage_banner')
                  ->orWhereNull('placement');
            })
            ->latest()
            ->get();

        if ($mainBanners->isEmpty()) {
            $mainBanners = Advertisement::where('is_active', true)->latest()->get();
        }

        $mainBanner = $mainBanners->first();

        $sidebarBanner = Advertisement::where('is_active', true)
            ->where('placement', 'homepage_sidebar')
            ->latest()
            ->first();

        $banners = Advertisement::where('is_active', true)->get();

        return view('home', compact(
            'categories',
            'liveStreams',
            'liveShoppingStreams',
            'activeAuctions',
            'featuredProducts',
            'trendingProducts',
            'recentlyViewedProducts',
            'topCreators',
            'mainBanner',
            'mainBanners',
            'sidebarBanner',
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
