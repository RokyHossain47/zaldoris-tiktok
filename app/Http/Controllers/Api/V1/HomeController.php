<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use App\Models\Stream;
use App\Models\Auction;
use App\Models\Product;
use App\Models\Advertisement;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HomeController extends BaseApiController
{
    /**
     * GET /api/v1/home
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $banners = Advertisement::where('status', 'active')->orderBy('id', 'desc')->take(5)->get();

        $recommendedStreams = Stream::with('host')
            ->where('is_live', true)
            ->orderBy('viewer_count', 'desc')
            ->take(6)
            ->get();

        if ($recommendedStreams->isEmpty()) {
            $recommendedStreams = Stream::with('host')->latest()->take(6)->get();
        }

        $activeAuctions = Auction::with(['seller', 'product'])
            ->where('status', 'live')
            ->orderBy('end_time', 'asc')
            ->take(6)
            ->get();

        if ($activeAuctions->isEmpty()) {
            $activeAuctions = Auction::with(['seller', 'product'])->latest()->take(6)->get();
        }

        $featuredProducts = Product::with('seller')
            ->where('status', 'active')
            ->where('is_featured', true)
            ->take(8)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::with('seller')->where('status', 'active')->latest()->take(8)->get();
        }

        return $this->success([
            'banners' => $banners,
            'categories' => $categories,
            'recommendedStreams' => $recommendedStreams,
            'activeAuctions' => $activeAuctions,
            'featuredProducts' => $featuredProducts,
        ], 'Home feed data retrieved');
    }

    /**
     * GET /api/v1/categories
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        return $this->success($categories, 'Categories retrieved');
    }

    /**
     * GET /api/v1/search/discovery
     */
    public function discovery(): JsonResponse
    {
        $trendingProducts = Product::where('status', 'active')
            ->where('is_trending', true)
            ->take(10)
            ->get();

        if ($trendingProducts->isEmpty()) {
            $trendingProducts = Product::where('status', 'active')->latest()->take(10)->get();
        }

        $activeAuctions = Auction::with('product')
            ->where('status', 'live')
            ->take(10)
            ->get();

        $popularTags = ['#Smartwatch', '#Beauty', '#Sneakers', '#VintageCards', '#Electronics', '#Drops'];

        return $this->success([
            'trendingProducts' => $trendingProducts,
            'activeAuctions' => $activeAuctions,
            'popularTags' => $popularTags,
        ], 'Search discovery items retrieved');
    }

    /**
     * GET /api/v1/search
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        $type = $request->input('type', 'all'); // 'all', 'products', 'auctions', 'streams'
        $categoryId = $request->input('categoryId');
        $minPrice = $request->input('minPrice');
        $maxPrice = $request->input('maxPrice');
        $sort = $request->input('sort', 'latest'); // 'latest', 'price_asc', 'price_desc', 'popular'

        $results = [];

        // Save search history if user is logged in
        if ($q && $request->user()) {
            SearchHistory::firstOrCreate([
                'user_id' => $request->user()->id,
                'query' => trim($q)
            ]);
        }

        if ($type === 'all' || $type === 'products') {
            $prodQuery = Product::with('seller')->where('status', 'active');
            if ($q) {
                $prodQuery->where(function($query) use ($q) {
                    $query->where('title', 'like', "%{$q}%")
                          ->orWhere('description', 'like', "%{$q}%")
                          ->orWhere('brand', 'like', "%{$q}%");
                });
            }
            if ($categoryId) $prodQuery->where('category_id', $categoryId);
            if ($minPrice) $prodQuery->where('price', '>=', $minPrice);
            if ($maxPrice) $prodQuery->where('price', '<=', $maxPrice);

            if ($sort === 'price_asc') $prodQuery->orderBy('price', 'asc');
            elseif ($sort === 'price_desc') $prodQuery->orderBy('price', 'desc');
            else $prodQuery->orderBy('id', 'desc');

            $results['products'] = $prodQuery->take(20)->get();
        }

        if ($type === 'all' || $type === 'auctions') {
            $aucQuery = Auction::with(['seller', 'product']);
            if ($q) {
                $aucQuery->where('title', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%");
            }
            $results['auctions'] = $aucQuery->take(20)->get();
        }

        if ($type === 'all' || $type === 'streams') {
            $streamQuery = Stream::with('host');
            if ($q) {
                $streamQuery->where('title', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%");
            }
            $results['streams'] = $streamQuery->take(20)->get();
        }

        return $this->success($results, 'Search results retrieved');
    }

    /**
     * GET /api/v1/me/search-history
     */
    public function getSearchHistory(Request $request): JsonResponse
    {
        $history = SearchHistory::where('user_id', $request->user()->id)
            ->latest()
            ->take(15)
            ->get();

        return $this->success($history, 'Search history retrieved');
    }

    /**
     * POST /api/v1/me/search-history
     */
    public function saveSearchHistory(Request $request): JsonResponse
    {
        $request->validate(['query' => 'required|string|max:150']);

        $entry = SearchHistory::create([
            'user_id' => $request->user()->id,
            'query' => trim($request->query),
        ]);

        return $this->success($entry, 'Search term saved', [], 201);
    }

    /**
     * DELETE /api/v1/me/search-history
     */
    public function clearSearchHistory(Request $request): JsonResponse
    {
        SearchHistory::where('user_id', $request->user()->id)->delete();
        return $this->success(null, 'Search history cleared');
    }
}
