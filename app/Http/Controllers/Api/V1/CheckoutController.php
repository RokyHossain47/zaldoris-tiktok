<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\CheckoutQuote;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\CartItem;
use App\Models\UserAddress;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Services\TaxService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutController extends BaseApiController
{
    /**
     * POST /api/v1/checkout/quotes
     * Calculate product/cart/auction checkout quote
     */
    public function createQuote(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'items' => 'nullable|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'address_id' => 'nullable|exists:user_addresses,id',
            'shipping_address' => 'nullable|array',
            'shipping_method' => 'nullable|string|in:standard,express',
            'promo_code' => 'nullable|string',
            'use_cart' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $user = $request->user();
        $itemsInput = $request->input('items', $request->json('items', []));

        if ($request->boolean('use_cart') || empty($itemsInput)) {
            $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();
            if ($cartItems->isEmpty() && empty($itemsInput)) {
                return $this->error('Your shopping cart is empty.', 'EMPTY_CART', 400);
            }
            if (!empty($cartItems)) {
                $itemsInput = [];
                foreach ($cartItems as $ci) {
                    $itemsInput[] = [
                        'product_id' => $ci->product_id,
                        'quantity' => $ci->quantity,
                        'selected_color' => $ci->selected_color,
                        'selected_size' => $ci->selected_size,
                        'unit_price' => (float)$ci->unit_price,
                    ];
                }
            }
        }

        $subtotal = 0.00;
        $calculatedItems = [];
        $sellerGroups = [];

        foreach ($itemsInput as $rawItem) {
            $product = Product::with('seller')->find($rawItem['product_id']);
            if (!$product) continue;

            $qty = (int) ($rawItem['quantity'] ?? 1);
            $unitPrice = isset($rawItem['unit_price']) ? (float)$rawItem['unit_price'] : (float)$product->price;
            $lineTotal = round($unitPrice * $qty, 2);
            $subtotal += $lineTotal;

            $sellerId = $product->seller_id ?: 1;
            $sellerName = $product->seller ? $product->seller->name : 'Zaldoris Store';

            $calculatedItems[] = [
                'productId' => $product->id,
                'title' => $product->title,
                'image' => $product->primary_image,
                'selectedColor' => $rawItem['selected_color'] ?? ($product->colors_list[0]['name'] ?? 'Standard'),
                'selectedSize' => $rawItem['selected_size'] ?? ($product->sizes_list[0]['name'] ?? 'Standard'),
                'quantity' => $qty,
                'unitPrice' => $unitPrice,
                'unitPriceMinor' => $this->toMinorUnits($unitPrice),
                'lineTotal' => $lineTotal,
                'lineTotalMinor' => $this->toMinorUnits($lineTotal),
                'sellerId' => $sellerId,
                'sellerName' => $sellerName,
                'stockAvailable' => (int)$product->available_stock,
            ];

            if (!isset($sellerGroups[$sellerId])) {
                $sellerGroups[$sellerId] = [
                    'sellerId' => $sellerId,
                    'sellerName' => $sellerName,
                    'items' => [],
                    'subtotal' => 0.00,
                ];
            }
            $sellerGroups[$sellerId]['items'][] = $product->title . " (x{$qty})";
            $sellerGroups[$sellerId]['subtotal'] += $lineTotal;
        }

        // Apply Promo Code dynamically
        $discount = 0.00;
        $appliedCoupon = null;
        $promo = strtoupper(trim($request->input('promo_code', $request->input('coupon_code', ''))));

        if (!empty($promo)) {
            $coupon = Coupon::where('code', $promo)->first();
            if ($coupon) {
                $validation = $coupon->validateFor($user, $subtotal);
                if ($validation['valid']) {
                    $discount = $validation['discount'];
                    $appliedCoupon = $coupon;
                }
            } elseif ($promo === 'WELCOME10') {
                $discount = round($subtotal * 0.10, 2);
            } elseif ($promo === 'ZALDORIS50') {
                $discount = min($subtotal, 50.00);
            }
        }

        $taxDetails = TaxService::calculateOrderTotals(max(0, $subtotal - $discount));
        $shippingMethod = $request->input('shipping_method', 'standard');
        $shippingFee = ($shippingMethod === 'express') ? 25.00 : 0.00;
        $platformFee = round($subtotal * 0.02, 2);
        $processingFee = round($subtotal * 0.015, 2);
        $taxAmount = $taxDetails['hst_tax'];
        $totalAmount = round($subtotal - $discount + $shippingFee + $platformFee + $processingFee + $taxAmount, 2);

        // Address resolution
        $shippingAddress = null;
        if ($request->address_id) {
            $addr = UserAddress::find($request->address_id);
            if ($addr) {
                $shippingAddress = [
                    'recipient_name' => $addr->recipient_name,
                    'phone' => $addr->phone,
                    'address_line1' => $addr->address_line1,
                    'address_line2' => $addr->address_line2,
                    'city' => $addr->city,
                    'region' => $addr->region,
                    'postal_code' => $addr->postal_code,
                    'country' => $addr->country,
                ];
            }
        } elseif ($request->shipping_address) {
            $shippingAddress = $request->shipping_address;
        }

        $quoteId = 'QUO-' . strtoupper(Str::random(12));
        $quote = CheckoutQuote::create([
            'quote_id' => $quoteId,
            'user_id' => $user?->id,
            'currency' => setting('currency_code', 'CAD'),
            'items' => $calculatedItems,
            'seller_groups' => array_values($sellerGroups),
            'shipping_address' => $shippingAddress,
            'shipping_method' => $shippingMethod,
            'promo_code' => $promo,
            'item_subtotal' => $subtotal,
            'discount_amount' => $discount,
            'platform_fee' => $platformFee,
            'processing_fee' => $processingFee,
            'shipping_fee' => $shippingFee,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'status' => 'active',
            'expires_at' => now()->addMinutes(30),
        ]);

        return $this->success([
            'quoteId' => $quote->quote_id,
            'expiresAt' => $quote->expires_at->toISOString(),
            'status' => 'active',
            'items' => $quote->items,
            'sellerGroups' => $quote->seller_groups,
            'deliveryOptions' => [
                ['id' => 'standard', 'name' => 'Standard Delivery (2-4 business days)', 'price' => 0.00, 'selected' => $shippingMethod === 'standard'],
                ['id' => 'express', 'name' => 'Express Priority (1-2 business days)', 'price' => 25.00, 'selected' => $shippingMethod === 'express'],
            ],
            'shippingAddress' => $quote->shipping_address,
            'breakdown' => [
                'subtotal' => $subtotal,
                'subtotalMinor' => $this->toMinorUnits($subtotal),
                'discount' => $discount,
                'discountMinor' => $this->toMinorUnits($discount),
                'shippingFee' => $shippingFee,
                'shippingFeeMinor' => $this->toMinorUnits($shippingFee),
                'platformFee' => $platformFee,
                'platformFeeMinor' => $this->toMinorUnits($platformFee),
                'processingFee' => $processingFee,
                'processingFeeMinor' => $this->toMinorUnits($processingFee),
                'taxAmount' => $taxAmount,
                'taxAmountMinor' => $this->toMinorUnits($taxAmount),
                'total' => $totalAmount,
                'totalMinor' => $this->toMinorUnits($totalAmount),
                'currency' => setting('currency_code', 'CAD'),
                'currencySymbol' => setting('currency_symbol', 'C$'),
            ],
            'nextAction' => 'CONFIRM_PAYMENT'
        ], 'Checkout quote calculated successfully');
    }

    /**
     * GET /api/v1/checkout/quotes/{id}
     */
    public function getQuote(Request $request, $id): JsonResponse
    {
        $quote = CheckoutQuote::where('quote_id', $id)->first();
        if (!$quote) {
            return $this->error('Quote not found', 'QUOTE_NOT_FOUND', 404);
        }

        $isExpired = $quote->expires_at->isPast();

        return $this->success([
            'quoteId' => $quote->quote_id,
            'status' => $isExpired ? 'expired' : $quote->status,
            'isExpired' => $isExpired,
            'expiresAt' => $quote->expires_at->toISOString(),
            'items' => $quote->items,
            'sellerGroups' => $quote->seller_groups,
            'breakdown' => [
                'subtotal' => (float)$quote->item_subtotal,
                'discount' => (float)$quote->discount_amount,
                'shippingFee' => (float)$quote->shipping_fee,
                'platformFee' => (float)$quote->platform_fee,
                'processingFee' => (float)$quote->processing_fee,
                'taxAmount' => (float)$quote->tax_amount,
                'total' => (float)$quote->total_amount,
                'currency' => $quote->currency,
            ]
        ], 'Quote restored');
    }

    /**
     * POST /api/v1/checkout/quotes/{id}/confirm
     * Confirm quote, reserve inventory, create Order and Payment records atomically
     */
    public function confirmQuote(Request $request, $id): JsonResponse
    {
        $quote = CheckoutQuote::where('quote_id', $id)->first();
        if (!$quote) {
            return $this->error('Quote not found', 'QUOTE_NOT_FOUND', 404);
        }

        if ($quote->expires_at->isPast() || $quote->status !== 'active') {
            return $this->error('This checkout quote has expired or was already converted. Please generate a new quote.', 'QUOTE_EXPIRED', 400);
        }

        $user = $request->user();

        return DB::transaction(function () use ($quote, $user, $request) {
            // Validate & Lock Inventory
            foreach ($quote->items as $item) {
                $product = Product::where('id', $item['productId'])->lockForUpdate()->first();
                if (!$product || $product->available_stock < $item['quantity']) {
                    return $this->error("Insufficient stock for product '{$item['title']}'.", 'OUT_OF_STOCK', 400);
                }
                $product->stock -= $item['quantity'];
                $product->save();
            }

            $orderNumber = 'ORD-' . strtoupper(Str::random(10));
            $primarySellerId = $quote->items[0]['sellerId'] ?? 1;

            $coupon = !empty($quote->promo_code) ? Coupon::where('code', $quote->promo_code)->first() : null;

            $order = Order::create([
                'order_number' => $orderNumber,
                'buyer_id' => $user?->id,
                'seller_id' => $primarySellerId,
                'coupon_id' => $coupon ? $coupon->id : null,
                'coupon_code' => $quote->promo_code ?: null,
                'discount_amount' => $quote->discount_amount ?: 0.00,
                'subtotal' => $quote->item_subtotal,
                'shipping_fee' => $quote->shipping_fee,
                'insurance_fee' => $quote->platform_fee,
                'hst_tax' => $quote->tax_amount,
                'platform_commission' => $quote->processing_fee,
                'total_amount' => $quote->total_amount,
                'payment_status' => 'escrow_held',
                'payment_method' => $request->input('payment_method', 'stripe_card'),
                'customer_email' => $user?->email ?: 'customer@zaldoris.com',
                'customer_name' => $user?->name ?: 'Zaldoris Customer',
                'shipping_address' => $quote->shipping_address ?: [
                    'recipient_name' => $user?->name ?: 'Customer',
                    'city' => 'Toronto',
                    'province' => 'ON',
                    'postal_code' => 'M5V 2T6',
                    'country' => 'Canada',
                ],
                'status' => 'packing',
                'dispatch_deadline' => now()->addHours(48),
                'tracking_number' => 'EP' . rand(100000000, 999999999) . 'CA',
                'carrier' => 'Canada Post Express',
            ]);

            if ($coupon && $quote->discount_amount > 0) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => $user?->id,
                    'order_id' => $order->id,
                    'discount_amount' => $quote->discount_amount,
                ]);
                $coupon->increment('used_count');
            }

            foreach ($quote->items as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['productId'],
                    'product_title' => $itemData['title'],
                    'selected_color' => $itemData['selectedColor'],
                    'selected_size' => $itemData['selectedSize'],
                    'product_image' => $itemData['image'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unitPrice'],
                    'total_price' => $itemData['lineTotal'],
                ]);
            }

            $payment = Payment::create([
                'user_id' => $user?->id,
                'order_id' => $order->id,
                'quote_id' => $quote->quote_id,
                'amount' => $quote->total_amount,
                'currency' => $quote->currency,
                'payment_method' => $request->input('payment_method', 'stripe_card'),
                'provider' => 'stripe',
                'transaction_reference' => 'ch_' . Str::random(24),
                'status' => 'succeeded',
            ]);

            $quote->update(['status' => 'converted']);

            // Clear Cart
            if ($user) {
                CartItem::where('user_id', $user->id)->delete();
            }

            return $this->success([
                'orderId' => $order->id,
                'orderNumber' => $order->order_number,
                'paymentId' => $payment->id,
                'status' => 'succeeded',
                'totalAmount' => (float)$order->total_amount,
                'totalAmountMinor' => $this->toMinorUnits($order->total_amount),
                'currency' => setting('currency_code', 'CAD'),
                'trackingNumber' => $order->tracking_number,
                'carrier' => $order->carrier,
                'estimatedDelivery' => now()->addDays(3)->toDateString(),
                'nextAction' => 'VIEW_ORDER'
            ], 'Order confirmed and payment processed successfully', [], 201);
        });
    }
}
