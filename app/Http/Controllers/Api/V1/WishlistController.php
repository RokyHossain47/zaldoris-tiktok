<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Wishlist;
use App\Models\Product;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WishlistController extends BaseApiController
{
    /**
     * GET /api/v1/me/wishlist
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $wishlists = Wishlist::where('user_id', $userId)->latest()->get();

        $items = $wishlists->map(function($w) {
            $itemDetails = null;
            if ($w->target_type === 'product') {
                $p = Product::find($w->target_id);
                if ($p) {
                    $itemDetails = [
                        'title' => $p->title,
                        'currentPrice' => (float)$p->price,
                        'originalPrice' => (float)($p->compare_price ?: $p->price),
                        'image' => $p->primary_image,
                        'rating' => (float)$p->average_rating,
                        'isAvailable' => $p->available_stock > 0,
                    ];
                }
            } elseif ($w->target_type === 'auction') {
                $a = Auction::find($w->target_id);
                if ($a) {
                    $itemDetails = [
                        'title' => $a->title,
                        'currentBid' => (float)$a->current_highest_bid,
                        'startingBid' => (float)$a->starting_bid,
                        'image' => $a->cover_image,
                        'status' => $a->status,
                        'endTime' => $a->end_time?->toISOString(),
                    ];
                }
            }

            return [
                'id' => $w->id,
                'targetType' => $w->target_type,
                'targetId' => $w->target_id,
                'preferredVariant' => $w->preferred_variant,
                'preferredQuantity' => (int)$w->preferred_quantity,
                'details' => $itemDetails,
                'createdAt' => $w->created_at->toISOString(),
            ];
        });

        return $this->success($items, 'Saved wishlist items retrieved');
    }

    /**
     * PUT /api/v1/me/wishlist/{targetType}/{targetId}
     */
    public function store(Request $request, string $targetType, $targetId): JsonResponse
    {
        if (!in_array($targetType, ['product', 'auction'])) {
            return $this->error('Target type must be product or auction', 'INVALID_TARGET_TYPE', 422);
        }

        $userId = $request->user()->id;

        $wishlist = Wishlist::updateOrCreate(
            ['user_id' => $userId, 'target_type' => $targetType, 'target_id' => $targetId],
            [
                'preferred_variant' => $request->input('variant'),
                'preferred_quantity' => (int) $request->input('quantity', 1),
            ]
        );

        return $this->success($wishlist, 'Item added to wishlist successfully');
    }

    /**
     * PATCH /api/v1/me/wishlist/{targetType}/{targetId}
     */
    public function update(Request $request, string $targetType, $targetId): JsonResponse
    {
        $userId = $request->user()->id;

        $wishlist = Wishlist::where('user_id', $userId)
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->first();

        if (!$wishlist) {
            return $this->error('Wishlist item not found', 'NOT_FOUND', 404);
        }

        if ($request->has('variant')) $wishlist->preferred_variant = $request->input('variant');
        if ($request->has('quantity')) $wishlist->preferred_quantity = (int) $request->input('quantity');
        $wishlist->save();

        return $this->success($wishlist, 'Wishlist item preferences updated');
    }

    /**
     * DELETE /api/v1/me/wishlist/{targetType}/{targetId}
     */
    public function destroy(Request $request, string $targetType, $targetId): JsonResponse
    {
        $userId = $request->user()->id;

        Wishlist::where('user_id', $userId)
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->delete();

        return $this->success(null, 'Item removed from wishlist');
    }
}
