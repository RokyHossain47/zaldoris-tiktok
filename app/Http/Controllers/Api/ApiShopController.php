<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Dispute;
use App\Services\TaxService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiShopController extends Controller
{
    /**
     * Browse Products Catalog.
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $search = $request->query('search');

        $query = Product::with('seller.sellerProfile')
            ->where('status', 'active');

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('id', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Product Details.
     */
    public function show($id)
    {
        $product = Product::with('seller.sellerProfile')->findOrFail($id);

        return response()->json([
            'success' => true,
            'product' => $product,
            'available_stock' => $product->available_stock,
        ]);
    }

    /**
     * Calculate Cart Totals with 13% HST & Mandatory Shipping Insurance (> $50).
     */
    public function calculateCart(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $subtotal = 0.00;
        $orderItemsData = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $qty = (int) $item['quantity'];
            $lineTotal = round($product->price * $qty, 2);
            $subtotal += $lineTotal;

            $orderItemsData[] = [
                'product_id' => $product->id,
                'title' => $product->title,
                'unit_price' => $product->price,
                'quantity' => $qty,
                'line_total' => $lineTotal,
                'image' => $product->primary_image,
            ];
        }

        $totals = TaxService::calculateOrderTotals($subtotal);

        return response()->json([
            'success' => true,
            'items' => $orderItemsData,
            'breakdown' => $totals,
        ]);
    }

    /**
     * 1-Tap Mobile Express Checkout (SRS #5, #15).
     */
    public function checkout(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|array',
            'shipping_address.street' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.province' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'shipping_address.country' => 'required|string',
            'payment_method' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'customer_name' => 'nullable|string',
            'stream_id' => 'nullable|exists:streams,id',
        ]);

        return DB::transaction(function () use ($validated, $user) {
            $subtotal = 0.00;
            $itemsToCreate = [];
            $primarySellerId = null;

            foreach ($validated['items'] as $item) {
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();

                // Prevent overselling check (SRS #14)
                if ($product->available_stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Item '{$product->title}' is out of stock.",
                    ], 400);
                }

                $product->stock -= $item['quantity'];
                $product->save();

                $primarySellerId = $product->seller_id;
                $lineTotal = round($product->price * $item['quantity'], 2);
                $subtotal += $lineTotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $lineTotal,
                ];
            }

            $totals = TaxService::calculateOrderTotals($subtotal);
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            $order = Order::create([
                'order_number' => $orderNumber,
                'buyer_id' => $user ? $user->id : null,
                'seller_id' => $primarySellerId,
                'stream_id' => $validated['stream_id'] ?? null,
                'subtotal' => $totals['subtotal'],
                'shipping_fee' => $totals['shipping_fee'],
                'insurance_fee' => $totals['insurance_fee'],
                'hst_tax' => $totals['hst_tax'],
                'platform_commission' => $totals['platform_commission'],
                'total_amount' => $totals['total_amount'],
                'payment_status' => 'escrow_held', // Escrow release via EasyPost (SRS #1)
                'payment_method' => $validated['payment_method'] ?? 'apple_pay',
                'customer_email' => $user ? $user->email : ($validated['customer_email'] ?? 'guest@example.com'),
                'customer_name' => $user ? $user->name : ($validated['customer_name'] ?? 'Guest Buyer'),
                'shipping_address' => $validated['shipping_address'],
                'status' => 'packing',
                'dispatch_deadline' => now()->addHours(48), // 48h dispatch requirement (SRS #7)
                'tracking_number' => 'EP' . rand(100000000, 999999999) . 'CA',
                'carrier' => 'easypost',
                'requires_signature' => $totals['requires_signature'],
            ]);

            foreach ($itemsToCreate as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully! Escrow funds held securely.',
                'order' => $order->load('items'),
                'totals' => $totals,
            ], 201);
        });
    }

    /**
     * User Orders List.
     */
    public function orders(Request $request)
    {
        $orders = Order::with(['items.product', 'seller.sellerProfile'])
            ->where('buyer_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * 24-hour Impulse Cancellation for Store Credit (SRS #6).
     */
    public function cancelOrder(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('buyer_id', $user->id)->findOrFail($id);

        if (!$order->isWithinCancellationGracePeriod()) {
            return response()->json([
                'success' => false,
                'message' => '24-hour cancellation grace period has expired or item has already been dispatched.',
            ], 400);
        }

        return DB::transaction(function () use ($order, $user) {
            $order->status = 'cancelled';
            $order->is_store_credit_refund = true;
            $order->save();

            // Refund as coins/store credit
            $refundCoins = (int) round($order->total_amount * 70); // 70 coins per $1
            $user->coin_balance += $refundCoins;
            $user->save();

            // Restock items
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Order cancelled. Store credit of {$refundCoins} coins credited to your wallet.",
                'new_coin_balance' => $user->coin_balance,
            ]);
        });
    }

    /**
     * File Dispute with photo proof (SRS #2, #10).
     */
    public function fileDispute(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('buyer_id', $user->id)->findOrFail($id);

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'received_item_photo_url' => 'required|string', // Mandatory photo proof (SRS #2)
        ]);

        $dispute = Dispute::create([
            'order_id' => $order->id,
            'buyer_id' => $user->id,
            'seller_id' => $order->seller_id,
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'received_item_photo_url' => $validated['received_item_photo_url'],
            'status' => 'open',
            'response_due_at' => now()->addHours(4), // 4-hour hard SLA (SRS #10)
        ]);

        $order->status = 'disputed';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Dispute submitted. Support team will review within the 4-hour SLA window.',
            'dispute' => $dispute,
        ], 201);
    }
}
