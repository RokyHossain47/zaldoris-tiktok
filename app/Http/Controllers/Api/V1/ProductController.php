<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseApiController
{
    /**
     * GET /api/v1/products
     */
    public function index(Request $request): JsonResponse
    {
        $q = $request->input('q');
        $categoryId = $request->input('categoryId');
        $status = $request->input('status', 'active');
        $minPrice = $request->input('from'); // minor units or major
        $maxPrice = $request->input('to');
        $sort = $request->input('sort', 'latest');
        $limit = (int) $request->input('limit', 20);

        $query = Product::with(['seller', 'categoryRel'])
            ->where('status', $status)
            ->where('is_live_product', false);

        if ($q) {
            $query->where(function($b) use ($q) {
                $b->where('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%")
                  ->orWhere('brand', 'like', "%{$q}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($minPrice !== null) {
            $query->where('price', '>=', (float)$minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', (float)$maxPrice);
        }

        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'popular') {
            $query->orderBy('stock', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $paginator = $query->cursorPaginate($limit);

        $items = collect($paginator->items())->map(function($p) {
            $price = (float)$p->price;
            $comparePrice = (float)($p->compare_price ?: $price);
            $discountPercent = $comparePrice > $price ? round((($comparePrice - $price) / $comparePrice) * 100) : 0;

            return [
                'id' => $p->id,
                'title' => $p->title,
                'brand' => $p->brand,
                'categoryName' => $p->category_name,
                'price' => $price,
                'priceMinor' => $this->toMinorUnits($price),
                'comparePrice' => $comparePrice,
                'discountPercent' => $discountPercent,
                'currency' => setting('currency_code', 'CAD'),
                'stock' => (int)$p->stock,
                'availableStock' => (int)$p->available_stock,
                'primaryImage' => $p->primary_image,
                'rating' => (float)$p->average_rating,
                'reviewsCount' => (int)$p->reviews_count,
                'seller' => $p->seller ? [
                    'id' => $p->seller->id,
                    'name' => $p->seller->name,
                    'avatarUrl' => $p->seller->avatar_url,
                ] : null,
                'isFeatured' => (bool)$p->is_featured,
                'isTrending' => (bool)$p->is_trending,
            ];
        });

        return $this->paginated($paginator, $items, 'Products list retrieved');
    }

    /**
     * GET /api/v1/products/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $product = Product::with(['seller.sellerProfile', 'seller.creatorProfile', 'categoryRel', 'reviews.user'])->find($id);
        if (!$product) {
            return $this->error('Product not found', 'PRODUCT_NOT_FOUND', 404);
        }

        $price = (float)$product->price;
        $comparePrice = (float)($product->compare_price ?: ($price * 1.35));
        $saveAmount = max(0, $comparePrice - $price);
        $discountPercent = $comparePrice > $price ? round(($saveAmount / $comparePrice) * 100) : 0;

        $gallery = is_array($product->images) && count($product->images) > 0
            ? $product->images
            : [$product->primary_image];

        return $this->success([
            'id' => $product->id,
            'title' => $product->title,
            'brand' => $product->brand,
            'description' => $product->description,
            'category' => [
                'id' => $product->category_id,
                'name' => $product->category_name,
            ],
            'pricing' => [
                'price' => $price,
                'priceMinor' => $this->toMinorUnits($price),
                'comparePrice' => $comparePrice,
                'comparePriceMinor' => $this->toMinorUnits($comparePrice),
                'saveAmount' => $saveAmount,
                'discountPercent' => $discountPercent,
                'currency' => setting('currency_code', 'CAD'),
                'currencySymbol' => setting('currency_symbol', 'C$'),
            ],
            'inventory' => [
                'stock' => (int)$product->stock,
                'lockedStock' => (int)$product->locked_stock,
                'availableStock' => (int)$product->available_stock,
                'inStock' => $product->available_stock > 0,
            ],
            'gallery' => $gallery,
            'variants' => [
                'colors' => $product->colors_list,
                'sizes' => $product->sizes_list,
            ],
            'specifications' => $product->specs_list,
            'meta' => [
                'dimensions' => $product->dimensions,
                'sourcingCountry' => $product->sourcing_country,
                'isNaturalLightingDeclared' => (bool)$product->is_natural_lighting_declared,
                'isFeatured' => (bool)$product->is_featured,
                'isTrending' => (bool)$product->is_trending,
                'isLiveProduct' => (bool)$product->is_live_product,
            ],
            'rating' => [
                'average' => (float)$product->average_rating,
                'count' => (int)$product->reviews_count,
            ],
            'seller' => $product->seller ? [
                'id' => $product->seller->id,
                'name' => $product->seller->name,
                'avatarUrl' => $product->seller->avatar_url,
                'isVerified' => (bool)$product->seller->is_verified,
                'positiveFeedback' => 98,
                'storeName' => $product->seller->sellerProfile?->store_name ?: $product->seller->name,
            ] : null,
            'reviews' => $product->reviews->take(10)->map(function($r) {
                return [
                    'id' => $r->id,
                    'userName' => $r->user?->name ?: ($r->author_name ?? 'Customer'),
                    'userAvatar' => $r->user?->avatar_url,
                    'rating' => (int)$r->rating,
                    'comment' => $r->comment,
                    'createdAt' => $r->created_at?->toISOString(),
                ];
            }),
        ], 'Product details retrieved successfully');
    }
}
