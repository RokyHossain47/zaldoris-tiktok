<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CartController extends BaseApiController
{
    /**
     * GET /api/v1/cart
     */
    public function getCart(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $items = CartItem::with(['product.seller'])->where('user_id', $userId)->get();

        $subtotal = 0.00;
        $cartLines = [];

        foreach ($items as $item) {
            $p = $item->product;
            $lineTotal = round(((float)$item->unit_price) * $item->quantity, 2);
            $subtotal += $lineTotal;

            $cartLines[] = [
                'lineId' => $item->id,
                'productId' => $item->product_id,
                'title' => $p ? $p->title : 'Product Unavailable',
                'image' => $p ? $p->primary_image : null,
                'selectedColor' => $item->selected_color,
                'selectedSize' => $item->selected_size,
                'quantity' => (int)$item->quantity,
                'unitPrice' => (float)$item->unit_price,
                'unitPriceMinor' => $this->toMinorUnits($item->unit_price),
                'lineTotal' => $lineTotal,
                'lineTotalMinor' => $this->toMinorUnits($lineTotal),
                'seller' => $p?->seller ? [
                    'id' => $p->seller->id,
                    'name' => $p->seller->name,
                ] : null,
                'stockAvailable' => $p ? (int)$p->available_stock : 0,
                'isStockValid' => $p ? ($p->available_stock >= $item->quantity) : false,
            ];
        }

        $tax = round($subtotal * 0.13, 2);
        $shipping = 0.00; // Free over $50
        $total = round($subtotal + $tax + $shipping, 2);

        return $this->success([
            'cartVersion' => time(),
            'itemCount' => array_sum(array_column($cartLines, 'quantity')),
            'lines' => $cartLines,
            'summary' => [
                'subtotal' => $subtotal,
                'subtotalMinor' => $this->toMinorUnits($subtotal),
                'tax' => $tax,
                'taxMinor' => $this->toMinorUnits($tax),
                'shipping' => $shipping,
                'shippingMinor' => $this->toMinorUnits($shipping),
                'total' => $total,
                'totalMinor' => $this->toMinorUnits($total),
                'currency' => setting('currency_code', 'CAD'),
                'currencySymbol' => setting('currency_symbol', 'C$'),
            ]
        ], 'Cart items retrieved');
    }

    /**
     * POST /api/v1/cart/items
     */
    public function addItem(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:50',
            'selected_color' => 'nullable|string',
            'selected_size' => 'nullable|string',
            'variant' => 'nullable|array',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $userId = $request->user()->id;
        $product = Product::find($request->product_id);
        $quantity = (int) $request->input('quantity', 1);

        if ($product->available_stock < $quantity) {
            return $this->error("Insufficient stock. Only {$product->available_stock} units available.", 'INSUFFICIENT_STOCK', 400);
        }

        $selectedColor = $request->selected_color ?: ($request->input('variant.color') ?: ($product->colors_list[0]['name'] ?? 'Standard'));
        $selectedSize = $request->selected_size ?: ($request->input('variant.size') ?: ($product->sizes_list[0]['name'] ?? 'Standard'));
        $unitPrice = $request->filled('unit_price') ? (float)$request->unit_price : (float)$product->price;

        $existing = CartItem::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->where('selected_color', $selectedColor)
            ->where('selected_size', $selectedSize)
            ->first();

        if ($existing) {
            $existing->quantity += $quantity;
            $existing->save();
            $cartItem = $existing;
        } else {
            $cartItem = CartItem::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'selected_color' => $selectedColor,
                'selected_size' => $selectedSize,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        return $this->success([
            'lineId' => $cartItem->id,
            'productId' => $product->id,
            'title' => $product->title,
            'selectedColor' => $cartItem->selected_color,
            'selectedSize' => $cartItem->selected_size,
            'quantity' => (int)$cartItem->quantity,
            'unitPrice' => (float)$cartItem->unit_price,
            'totalItemsInCart' => CartItem::where('user_id', $userId)->sum('quantity'),
        ], 'Item added to cart', [], 201);
    }

    /**
     * PATCH /api/v1/cart/items/{id}
     */
    public function updateItem(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:0|max:50',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $userId = $request->user()->id;
        $cartItem = CartItem::where('id', $id)->where('user_id', $userId)->first();

        if (!$cartItem) {
            return $this->error('Cart item not found', 'NOT_FOUND', 404);
        }

        $qty = (int) $request->quantity;
        if ($qty <= 0) {
            $cartItem->delete();
            return $this->success(null, 'Item removed from cart');
        }

        $product = Product::find($cartItem->product_id);
        if ($product && $product->available_stock < $qty) {
            return $this->error("Only {$product->available_stock} units available in stock.", 'INSUFFICIENT_STOCK', 400);
        }

        $cartItem->quantity = $qty;
        $cartItem->save();

        return $this->success([
            'lineId' => $cartItem->id,
            'quantity' => $cartItem->quantity,
            'lineTotal' => round(((float)$cartItem->unit_price) * $cartItem->quantity, 2),
        ], 'Cart item quantity updated');
    }

    /**
     * DELETE /api/v1/cart/items/{id}
     */
    public function removeItem(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;
        CartItem::where('id', $id)->where('user_id', $userId)->delete();

        return $this->success(null, 'Item removed from cart');
    }
}
