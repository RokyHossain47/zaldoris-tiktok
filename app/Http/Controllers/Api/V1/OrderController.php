<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Refund;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseApiController
{
    /**
     * GET /api/v1/me/orders
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $status = $request->input('status');
        $q = $request->input('q');

        $query = Order::with(['items.product', 'seller'])
            ->where('buyer_id', $userId);

        if ($status) {
            $query->where('status', $status);
        }

        if ($q) {
            $query->where(function($b) use ($q) {
                $b->where('order_number', 'like', "%{$q}%")
                  ->orWhere('tracking_number', 'like', "%{$q}%");
            });
        }

        $orders = $query->orderBy('id', 'desc')->paginate($request->input('limit', 15));

        $items = collect($orders->items())->map(function($o) {
            return [
                'id' => $o->id,
                'orderNumber' => $o->order_number,
                'createdAt' => $o->created_at->toISOString(),
                'status' => $o->status,
                'paymentStatus' => $o->payment_status,
                'totalAmount' => (float)$o->total_amount,
                'totalAmountMinor' => $this->toMinorUnits($o->total_amount),
                'currency' => setting('currency_code', 'CAD'),
                'itemCount' => $o->items->sum('quantity'),
                'trackingNumber' => $o->tracking_number,
                'carrier' => $o->carrier,
                'seller' => $o->seller ? [
                    'id' => $o->seller->id,
                    'name' => $o->seller->name,
                ] : null,
                'items' => $o->items->map(function($it) {
                    return [
                        'id' => $it->id,
                        'productId' => $it->product_id,
                        'title' => $it->product_title,
                        'image' => $it->product_image,
                        'selectedColor' => $it->selected_color,
                        'selectedSize' => $it->selected_size,
                        'quantity' => (int)$it->quantity,
                        'unitPrice' => (float)$it->unit_price,
                        'totalPrice' => (float)$it->total_price,
                    ];
                }),
            ];
        });

        return $this->paginated($orders, $items, 'Order history retrieved');
    }

    /**
     * GET /api/v1/orders/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;
        $order = Order::with(['items.product', 'seller.sellerProfile', 'dispute'])->find($id);

        if (!$order) {
            return $this->error('Order not found', 'ORDER_NOT_FOUND', 404);
        }

        if ($order->buyer_id !== $userId && $order->seller_id !== $userId && $request->user()->role !== 'admin') {
            return $this->error('Access denied to this order', 'FORBIDDEN', 403);
        }

        return $this->success([
            'id' => $order->id,
            'orderNumber' => $order->order_number,
            'createdAt' => $order->created_at->toISOString(),
            'status' => $order->status,
            'paymentStatus' => $order->payment_status,
            'paymentMethod' => $order->payment_method,
            'breakdown' => [
                'subtotal' => (float)$order->subtotal,
                'shippingFee' => (float)$order->shipping_fee,
                'insuranceFee' => (float)$order->insurance_fee,
                'tax' => (float)$order->hst_tax,
                'total' => (float)$order->total_amount,
                'totalMinor' => $this->toMinorUnits($order->total_amount),
                'currency' => setting('currency_code', 'CAD'),
                'currencySymbol' => setting('currency_symbol', 'C$'),
            ],
            'shippingAddress' => $order->shipping_address,
            'shipping' => [
                'carrier' => $order->carrier ?: 'Canada Post Express',
                'trackingNumber' => $order->tracking_number,
                'trackingUrl' => $order->tracking_number ? "https://www.canadapost-postescanada.ca/track-reperage/en#/result?trackingNumber={$order->tracking_number}" : null,
                'dispatchDeadline' => $order->dispatch_deadline?->toISOString(),
                'requiresSignature' => (bool)$order->requires_signature,
            ],
            'seller' => $order->seller ? [
                'id' => $order->seller->id,
                'name' => $order->seller->name,
                'avatarUrl' => $order->seller->avatar_url,
                'storeName' => $order->seller->sellerProfile?->store_name ?: $order->seller->name,
            ] : null,
            'items' => $order->items->map(function($it) {
                return [
                    'id' => $it->id,
                    'productId' => $it->product_id,
                    'title' => $it->product_title,
                    'image' => $it->product_image,
                    'selectedColor' => $it->selected_color,
                    'selectedSize' => $it->selected_size,
                    'quantity' => (int)$it->quantity,
                    'unitPrice' => (float)$it->unit_price,
                    'totalPrice' => (float)$it->total_price,
                ];
            }),
        ], 'Order details retrieved');
    }

    /**
     * POST /api/v1/orders/{id}/reorder
     */
    public function reorder(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;
        $order = Order::with('items.product')->find($id);

        if (!$order) {
            return $this->error('Order not found', 'ORDER_NOT_FOUND', 404);
        }

        $addedCount = 0;
        foreach ($order->items as $item) {
            $product = $item->product ?: Product::find($item->product_id);
            if ($product && $product->available_stock > 0) {
                CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $product->id,
                    'selected_color' => $item->selected_color,
                    'selected_size' => $item->selected_size,
                    'quantity' => min((int)$item->quantity, $product->available_stock),
                    'unit_price' => (float)$product->price,
                ]);
                $addedCount++;
            }
        }

        return $this->success([
            'itemsAdded' => $addedCount,
            'cartCount' => CartItem::where('user_id', $userId)->sum('quantity'),
        ], 'Reorder items added to cart at current prices');
    }

    /**
     * GET /api/v1/orders/{id}/shipments
     */
    public function getShipments(Request $request, $id): JsonResponse
    {
        $order = Order::find($id);
        if (!$order) {
            return $this->error('Order not found', 'ORDER_NOT_FOUND', 404);
        }

        $created = $order->created_at;
        $timeline = [
            [
                'status' => 'order_placed',
                'title' => 'Order Placed & Payment Held in Escrow',
                'description' => 'Payment authorized securely and dispatched to merchant for packing.',
                'timestamp' => $created->toISOString(),
                'completed' => true,
            ],
            [
                'status' => 'packing',
                'title' => 'Processing & Quality Inspection',
                'description' => 'Merchant is preparing the parcel with tamper-evident packaging.',
                'timestamp' => $created->addHours(4)->toISOString(),
                'completed' => in_array($order->status, ['packing', 'shipped', 'delivered']),
            ],
            [
                'status' => 'shipped',
                'title' => 'Dispatched & In Transit',
                'description' => "Carrier: {$order->carrier}. Tracking code: {$order->tracking_number}",
                'timestamp' => $created->addHours(18)->toISOString(),
                'completed' => in_array($order->status, ['shipped', 'delivered']),
            ],
            [
                'status' => 'delivered',
                'title' => 'Delivered to Customer Doorstep',
                'description' => 'Package safely handed over or placed in secure parcel locker.',
                'timestamp' => $created->addDays(2)->toISOString(),
                'completed' => ($order->status === 'delivered'),
            ],
        ];

        return $this->success([
            'orderId' => $order->id,
            'orderNumber' => $order->order_number,
            'carrier' => $order->carrier ?: 'Canada Post Express',
            'trackingNumber' => $order->tracking_number,
            'trackingUrl' => $order->tracking_number ? "https://www.canadapost-postescanada.ca/track-reperage/en#/result?trackingNumber={$order->tracking_number}" : null,
            'estimatedArrival' => now()->addDays(2)->toDateString(),
            'fulfillmentStatus' => $order->status,
            'timeline' => $timeline,
            'mapLocation' => [
                'lat' => 43.6532,
                'lng' => -79.3832,
                'city' => 'Toronto, ON',
            ]
        ], 'Shipment tracking information retrieved');
    }

    /**
     * GET /api/v1/orders/{id}/return-eligibility
     */
    public function getReturnEligibility(Request $request, $id): JsonResponse
    {
        $order = Order::with('items')->find($id);
        if (!$order) {
            return $this->error('Order not found', 'ORDER_NOT_FOUND', 404);
        }

        $isEligible = $order->created_at->diffInDays(now()) <= 30;
        $deadline = $order->created_at->addDays(30);

        return $this->success([
            'orderId' => $order->id,
            'isEligible' => $isEligible,
            'returnWindowDays' => 30,
            'returnDeadline' => $deadline->toISOString(),
            'eligibleItems' => $order->items->map(function($it) {
                return [
                    'orderItemId' => $it->id,
                    'title' => $it->product_title,
                    'quantity' => (int)$it->quantity,
                    'unitPrice' => (float)$it->unit_price,
                    'eligible' => true,
                ];
            }),
            'allowedReasonCodes' => [
                ['code' => 'defective', 'label' => 'Item is defective or does not turn on'],
                ['code' => 'wrong_item', 'label' => 'Received wrong item, color, or variant'],
                ['code' => 'not_as_described', 'label' => 'Item significantly differs from description / stream'],
                ['code' => 'damaged_in_transit', 'label' => 'Package arrived visibly damaged'],
                ['code' => 'buyer_remorse', 'label' => 'No longer needed / Changed mind'],
            ]
        ], 'Return eligibility checked');
    }

    /**
     * POST /api/v1/orders/{id}/returns
     */
    public function submitReturn(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_item_id' => 'nullable|exists:order_items,id',
            'reason_code' => 'required|string',
            'description' => 'required|string|min:10|max:1000',
            'evidence_asset_ids' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $userId = $request->user()->id;
        $order = Order::find($id);

        if (!$order || $order->buyer_id !== $userId) {
            return $this->error('Order not found or access denied', 'FORBIDDEN', 403);
        }

        $refundAmount = (float) $order->total_amount;
        if ($request->order_item_id) {
            $item = OrderItem::find($request->order_item_id);
            if ($item) $refundAmount = (float) $item->total_price;
        }

        $return = OrderReturn::create([
            'order_id' => $order->id,
            'order_item_id' => $request->order_item_id,
            'user_id' => $userId,
            'reason_code' => $request->reason_code,
            'description' => $request->description,
            'evidence_asset_ids' => $request->evidence_asset_ids,
            'refund_amount' => $refundAmount,
            'status' => 'pending_review',
            'instructions' => 'Please package the unit securely. A prepaid shipping label has been authorized.',
            'return_tracking_number' => 'RET' . rand(10000000, 99999999) . 'CA',
            'return_deadline' => now()->addDays(14),
        ]);

        return $this->success([
            'returnId' => $return->id,
            'status' => $return->status,
            'refundAmount' => (float)$return->refund_amount,
            'returnTrackingNumber' => $return->return_tracking_number,
            'instructions' => $return->instructions,
            'returnDeadline' => $return->return_deadline->toISOString(),
        ], 'Return request submitted successfully', [], 201);
    }

    /**
     * GET /api/v1/returns/{id}
     */
    public function getReturn(Request $request, $id): JsonResponse
    {
        $return = OrderReturn::with('order')->find($id);
        if (!$return) {
            return $this->error('Return request not found', 'NOT_FOUND', 404);
        }

        return $this->success([
            'returnId' => $return->id,
            'orderId' => $return->order_id,
            'status' => $return->status,
            'reasonCode' => $return->reason_code,
            'description' => $return->description,
            'refundAmount' => (float)$return->refund_amount,
            'instructions' => $return->instructions,
            'returnTrackingNumber' => $return->return_tracking_number,
            'returnDeadline' => $return->return_deadline?->toISOString(),
            'createdAt' => $return->created_at->toISOString(),
        ], 'Return status and instructions retrieved');
    }

    /**
     * GET /api/v1/refunds/{id}
     */
    public function getRefund(Request $request, $id): JsonResponse
    {
        $refund = Refund::with('order')->find($id);
        if (!$refund) {
            return $this->error('Refund record not found', 'NOT_FOUND', 404);
        }

        return $this->success([
            'refundId' => $refund->id,
            'orderId' => $refund->order_id,
            'amount' => (float)$refund->amount,
            'amountMinor' => $this->toMinorUnits($refund->amount),
            'currency' => $refund->currency,
            'status' => $refund->status,
            'reason' => $refund->reason,
            'originalPaymentMethod' => $refund->original_payment_method,
            'processedAt' => $refund->processed_at?->toISOString() ?: now()->toISOString(),
        ], 'Refund status and details retrieved');
    }
}
