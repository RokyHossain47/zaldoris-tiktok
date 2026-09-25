<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\TaxService;
use App\Services\ShippingService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category', 'all');
        $filter = $request->query('filter');

        $categories = Category::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        $query = Product::with(['seller', 'category'])->where('status', 'active');

        if ($categorySlug !== 'all') {
            $cat = Category::where('slug', $categorySlug)->first();
            if ($cat) {
                $query->where('category_id', $cat->id);
            } else {
                $query->where('category', 'like', "%{$categorySlug}%");
            }
        }

        if ($filter === 'featured') {
            $query->where('is_featured', true);
        } elseif ($filter === 'trending') {
            $query->where('is_trending', true);
        }

        $products = $query->orderBy('id', 'desc')->paginate(12);

        return view('shop.index', compact('products', 'categories', 'categorySlug', 'filter'));
    }

    public function product($id = null)
    {
        $product = $id ? Product::with(['seller.sellerProfile', 'seller.creatorProfile', 'category', 'reviews.user'])->find($id) : null;
        if (!$product) {
            $product = Product::with(['seller.sellerProfile', 'seller.creatorProfile', 'category', 'reviews.user'])->first() ?? new Product();
        }

        // Store recently viewed product in browser session (up to 10 latest unique items)
        if ($product->id) {
            $recent = session()->get('recently_viewed_products', []);
            $recent = array_values(array_diff($recent, [$product->id]));
            array_unshift($recent, $product->id);
            $recent = array_slice($recent, 0, 10);
            session()->put('recently_viewed_products', $recent);
        }

        $relatedProducts = Product::where('id', '!=', $product->id ?? 0)
            ->where('status', 'active')
            ->when($product->category_id, function($q) use ($product) {
                $q->where('category_id', $product->category_id);
            })
            ->take(4)
            ->get();

        if ($relatedProducts->isEmpty()) {
            $relatedProducts = Product::where('id', '!=', $product->id ?? 0)->where('status', 'active')->take(4)->get();
        }

        $stream = ($product->id ? $product->streams()->where('is_live', true)->first() : null)
            ?? ($product->stream_id ? \App\Models\Stream::find($product->stream_id) : null)
            ?? \App\Models\Stream::with('host')->where('is_live', true)->first() 
            ?? \App\Models\Stream::with('host')->first();

        // Check if authenticated user follows the seller/host
        $isFollowing = false;
        if (auth()->check() && $product->seller_id) {
            $isFollowing = \App\Models\Follow::where('follower_id', auth()->id())
                ->where('following_id', $product->seller_id)
                ->exists();
        }

        // Real Reviews & Rating Statistics
        $reviews = $product->id ? $product->reviews()->with('user')->latest()->get() : collect();
        $totalReviews = $reviews->count();
        $avgRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 4.9;

        $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($reviews as $rev) {
            $r = (int) $rev->rating;
            if (isset($ratingCounts[$r])) {
                $ratingCounts[$r]++;
            }
        }

        $ratingPercents = [];
        foreach ($ratingCounts as $star => $count) {
            $ratingPercents[$star] = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : ($star === 5 ? 88 : ($star === 4 ? 9 : 1));
        }

        $reactions = \App\Models\Reaction::where('is_active', true)
            ->where('type', 'emoji')
            ->orderBy('sort_order', 'asc')
            ->get();

        $gifs = \App\Models\Reaction::where('is_active', true)
            ->where('type', 'gif')
            ->orderBy('sort_order', 'asc')
            ->get();

        $gifts = \App\Models\Gift::where('is_active', true)
            ->orderBy('coin_cost', 'asc')
            ->get();

        return view('shop.product-details', compact(
            'product', 'relatedProducts', 'stream', 'isFollowing', 
            'reviews', 'totalReviews', 'avgRating', 'ratingPercents',
            'reactions', 'gifs', 'gifts'
        ));
    }

    public function storeReview(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
            'name' => 'nullable|string|max:100',
        ]);

        $user = auth()->user();

        $review = \App\Models\Review::create([
            'product_id' => $product->id,
            'order_id' => null,
            'user_id' => $user ? $user->id : 1,
            'seller_id' => $product->seller_id ?: 1,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'verified_purchase' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully!',
            'review' => [
                'id' => $review->id,
                'author' => $validated['name'] ?: ($user ? $user->name : 'Verified Customer'),
                'avatar' => $user && $user->avatar_url ? $user->avatar_url : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=80',
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => 'Just now',
            ]
        ]);
    }

    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = max(1, (int) $request->input('quantity', 1));
        $selectedColor = $request->input('selected_color');
        $selectedSize = $request->input('selected_size');
        $customPrice = $request->input('unit_price');

        $product = Product::with('seller')->find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $cart = session()->get('cart', []);
        $cartKey = $productId . '_' . md5(($selectedColor ?? '') . '_' . ($selectedSize ?? ''));

        $unitPrice = $customPrice ? (float) $customPrice : (float) $product->price;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'id' => $product->id,
                'cart_key' => $cartKey,
                'title' => $product->title,
                'price' => $unitPrice,
                'compare_price' => (float) ($product->compare_price ?: ($unitPrice * 1.25)),
                'image' => $product->primary_image,
                'selected_color' => $selectedColor ?: ($product->colors_list[0]['name'] ?? 'Standard'),
                'selected_size' => $selectedSize ?: ($product->sizes_list[0]['name'] ?? 'Standard'),
                'quantity' => $quantity,
                'seller_name' => $product->seller ? $product->seller->name : 'Verified Store',
                'category_name' => $product->category_name,
            ];
        }

        session()->put('cart', $cart);
        session()->save();

        $totalItems = 0;
        foreach ($cart as $item) {
            $totalItems += $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $totalItems,
            'cart' => $cart
        ]);
    }

    public function updateCart(Request $request)
    {
        $key = (string)($request->input('product_id') ?? $request->input('id') ?? $request->input('cart_key'));
        $quantity = (int) $request->input('quantity', 1);

        $cart = session()->get('cart', []);

        $targetKey = null;
        if (isset($cart[$key])) {
            $targetKey = $key;
        } else {
            foreach ($cart as $k => $item) {
                if ((string)($item['id'] ?? '') === $key || (string)($item['cart_key'] ?? '') === $key || (string)$k === $key) {
                    $targetKey = $k;
                    break;
                }
            }
        }

        $itemLineTotal = 0;
        $itemUnitPrice = 0;
        $itemQty = 0;

        if ($targetKey) {
            if ($quantity <= 0) {
                unset($cart[$targetKey]);
            } else {
                $cart[$targetKey]['quantity'] = $quantity;
                $itemQty = $quantity;
                $itemUnitPrice = (float)($cart[$targetKey]['price'] ?? 0);
                $itemLineTotal = round($itemUnitPrice * $quantity, 2);
            }
        }

        session()->put('cart', $cart);
        session()->save();

        $subtotal = 0;
        $totalItems = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
            $totalItems += $item['quantity'];
        }

        // Recalculate coupon if applied
        $couponSession = session()->get('coupon');
        $discount = 0.00;
        if ($couponSession) {
            $coupon = Coupon::find($couponSession['id']);
            if ($coupon) {
                $validation = $coupon->validateFor(auth()->user(), $subtotal);
                if ($validation['valid']) {
                    $discount = $validation['discount'];
                } else {
                    session()->forget('coupon');
                    $couponSession = null;
                }
            }
        }

        $taxableAmount = max(0, $subtotal - $discount);
        $tax = round($taxableAmount * 0.13, 2);
        $shipping = 0.00;
        $total = round($taxableAmount + $tax + $shipping, 2);

        return response()->json([
            'success' => true,
            'cart_count' => $totalItems,
            'subtotal' => number_format($subtotal, 2),
            'subtotal_raw' => $subtotal,
            'discount' => number_format($discount, 2),
            'discount_raw' => $discount,
            'tax' => number_format($tax, 2),
            'shipping' => number_format($shipping, 2),
            'total' => number_format($total, 2),
            'coupon' => $couponSession,
            'item_key' => $targetKey,
            'item_quantity' => $itemQty,
            'item_unit_price' => number_format($itemUnitPrice, 2),
            'item_line_total' => number_format($itemLineTotal, 2),
            'cart' => $cart
        ]);
    }

    public function removeFromCart($id)
    {
        $idStr = (string)$id;
        $cart = session()->get('cart', []);
        
        if (isset($cart[$idStr])) {
            unset($cart[$idStr]);
        } else {
            foreach ($cart as $k => $item) {
                if ((string)($item['id'] ?? '') === $idStr || (string)($item['cart_key'] ?? '') === $idStr || (string)$k === $idStr) {
                    unset($cart[$k]);
                    break;
                }
            }
        }

        session()->put('cart', $cart);
        session()->save();

        $subtotal = 0;
        $totalItems = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
            $totalItems += $item['quantity'];
        }

        $couponSession = session()->get('coupon');
        $discount = 0.00;
        if ($couponSession) {
            $coupon = Coupon::find($couponSession['id']);
            if ($coupon) {
                $validation = $coupon->validateFor(auth()->user(), $subtotal);
                if ($validation['valid']) {
                    $discount = $validation['discount'];
                } else {
                    session()->forget('coupon');
                    $couponSession = null;
                }
            }
        }

        $taxableAmount = max(0, $subtotal - $discount);
        $tax = round($taxableAmount * 0.13, 2);
        $shipping = 0.00;
        $total = round($taxableAmount + $tax + $shipping, 2);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'cart_count' => $totalItems,
            'subtotal' => number_format($subtotal, 2),
            'subtotal_raw' => $subtotal,
            'discount' => number_format($discount, 2),
            'discount_raw' => $discount,
            'tax' => number_format($tax, 2),
            'shipping' => number_format($shipping, 2),
            'total' => number_format($total, 2),
            'coupon' => $couponSession,
        ]);
    }

    /**
     * Apply Coupon to Cart / Session.
     */
    public function applyCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('code', $request->input('coupon_code', ''))));

        if (empty($code)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Please enter a coupon code.'], 422);
            }
            return back()->withErrors(['coupon' => 'Please enter a coupon code.']);
        }

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => "Coupon '{$code}' not found or invalid."], 404);
            }
            return back()->withErrors(['coupon' => "Coupon '{$code}' not found or invalid."]);
        }

        $cart = session()->get('cart', []);
        $subtotal = 0.00;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        $user = auth()->user();
        $validation = $coupon->validateFor($user, $subtotal);

        if (!$validation['valid']) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $validation['message']], 422);
            }
            return back()->withErrors(['coupon' => $validation['message']]);
        }

        $discount = $validation['discount'];
        session()->put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'value' => (float)$coupon->value,
            'discount' => $discount,
        ]);

        $taxableAmount = max(0, $subtotal - $discount);
        $tax = round($taxableAmount * 0.13, 2);
        $shipping = 0.00;
        $total = round($taxableAmount + $tax + $shipping, 2);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $validation['message'],
                'discount' => number_format($discount, 2),
                'discount_raw' => $discount,
                'subtotal' => number_format($subtotal, 2),
                'tax' => number_format($tax, 2),
                'total' => number_format($total, 2),
                'coupon' => session()->get('coupon'),
            ]);
        }

        return back()->with('success', $validation['message']);
    }

    /**
     * Remove Coupon from Session.
     */
    public function removeCoupon(Request $request)
    {
        session()->forget('coupon');

        $cart = session()->get('cart', []);
        $subtotal = 0.00;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        $tax = round($subtotal * 0.13, 2);
        $shipping = 0.00;
        $total = round($subtotal + $tax + $shipping, 2);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon removed.',
                'subtotal' => number_format($subtotal, 2),
                'discount' => '0.00',
                'tax' => number_format($tax, 2),
                'total' => number_format($total, 2),
            ]);
        }

        return back()->with('success', 'Coupon removed.');
    }

    public function cart()
    {
        $cart = session()->get('cart', []);
        $subtotal = 0.00;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        $couponSession = session()->get('coupon');
        $discount = 0.00;
        if ($couponSession) {
            $coupon = Coupon::find($couponSession['id']);
            if ($coupon) {
                $validation = $coupon->validateFor(auth()->user(), $subtotal);
                if ($validation['valid']) {
                    $discount = $validation['discount'];
                } else {
                    session()->forget('coupon');
                    $couponSession = null;
                }
            }
        }

        $shippingMethods = ShippingService::getActiveMethods();
        $defaultMethod = ShippingService::getDefaultMethod();
        $selectedShippingMethod = $defaultMethod ?? ($shippingMethods[0] ?? ['id' => 'standard_delivery', 'name' => 'Standard Ground Delivery', 'cost' => 0.00]);
        $shipping = (float)($selectedShippingMethod['cost'] ?? 0.00);

        $taxableAmount = max(0, $subtotal - $discount);
        $tax = round(($taxableAmount + $shipping) * 0.13, 2);
        $total = round($taxableAmount + $tax + $shipping, 2);

        return view('shop.cart', compact('cart', 'subtotal', 'discount', 'tax', 'shipping', 'total', 'couponSession', 'shippingMethods', 'selectedShippingMethod'));
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        $subtotal = 0.00;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        $couponSession = session()->get('coupon');
        $discount = 0.00;
        if ($couponSession) {
            $coupon = Coupon::find($couponSession['id']);
            if ($coupon) {
                $validation = $coupon->validateFor(auth()->user(), $subtotal);
                if ($validation['valid']) {
                    $discount = $validation['discount'];
                } else {
                    session()->forget('coupon');
                    $couponSession = null;
                }
            }
        }

        $shippingMethods = ShippingService::getActiveMethods();
        $defaultMethod = ShippingService::getDefaultMethod();
        $selectedShippingMethod = $defaultMethod ?? ($shippingMethods[0] ?? ['id' => 'standard_delivery', 'name' => 'Standard Ground Delivery', 'cost' => 0.00]);
        $shipping = (float)($selectedShippingMethod['cost'] ?? 0.00);

        $taxableAmount = max(0, $subtotal - $discount);
        $tax = round(($taxableAmount + $shipping) * 0.13, 2);
        $total = round($taxableAmount + $tax + $shipping, 2);

        return view('shop.checkout', compact('cart', 'subtotal', 'discount', 'tax', 'shipping', 'total', 'couponSession', 'shippingMethods', 'selectedShippingMethod'));
    }

    public function payment()
    {
        return view('shop.payment');
    }

    public function processCheckout(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'street' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'payment_method' => 'nullable|string',
            'shipping_method' => 'nullable|string',
            'coupon_code' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $user, $request) {
            $subtotal = 0.00;
            $itemsToCreate = [];
            $primarySellerId = null;

            foreach ($validated['items'] as $item) {
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();

                if ($product->available_stock < $item['quantity']) {
                    return back()->withErrors(['error' => "Product '{$product->title}' has insufficient stock."]);
                }

                $product->stock -= $item['quantity'];
                $product->save();

                $primarySellerId = $product->seller_id;
                $itemUnitPrice = isset($item['unit_price']) ? (float)$item['unit_price'] : (float)$product->price;
                $lineTotal = round($itemUnitPrice * $item['quantity'], 2);
                $subtotal += $lineTotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'selected_color' => $item['selected_color'] ?? ($product->colors_list[0]['name'] ?? null),
                    'selected_size' => $item['selected_size'] ?? ($product->sizes_list[0]['name'] ?? null),
                    'product_image' => $item['image'] ?? $product->primary_image,
                    'quantity' => $item['quantity'],
                    'unit_price' => $itemUnitPrice,
                    'total_price' => $lineTotal,
                ];
            }

            // Evaluate Coupon Discount
            $discount = 0.00;
            $appliedCoupon = null;
            $couponCode = strtoupper(trim($request->input('coupon_code', session('coupon.code', ''))));

            if (!empty($couponCode)) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon) {
                    $validation = $coupon->validateFor($user, $subtotal);
                    if ($validation['valid']) {
                        $discount = $validation['discount'];
                        $appliedCoupon = $coupon;
                    }
                }
            }

            // Determine Shipping Method & Cost
            $shippingMethodId = $request->input('shipping_method');
            $chosenMethod = ShippingService::findMethod($shippingMethodId);
            if (!$chosenMethod) {
                $chosenMethod = ShippingService::getDefaultMethod() ?? ['id' => 'standard_delivery', 'name' => 'Standard Ground Delivery', 'cost' => 0.00];
            }
            $shippingFee = (float)($chosenMethod['cost'] ?? 0.00);
            $carrierName = $chosenMethod['name'] ?? 'Standard Ground Delivery';

            $taxableSubtotal = max(0, $subtotal - $discount);
            $totals = TaxService::calculateOrderTotals($taxableSubtotal, $shippingFee);
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            $order = Order::create([
                'order_number' => $orderNumber,
                'buyer_id' => $user ? $user->id : null,
                'seller_id' => $primarySellerId,
                'coupon_id' => $appliedCoupon ? $appliedCoupon->id : null,
                'coupon_code' => $appliedCoupon ? $appliedCoupon->code : null,
                'discount_amount' => $discount,
                'subtotal' => $subtotal,
                'shipping_fee' => $totals['shipping_fee'],
                'insurance_fee' => $totals['insurance_fee'],
                'hst_tax' => $totals['hst_tax'],
                'platform_commission' => $totals['platform_commission'],
                'total_amount' => $totals['total_amount'],
                'payment_status' => 'escrow_held',
                'payment_method' => $validated['payment_method'] ?? 'stripe_card',
                'customer_email' => $user ? $user->email : 'buyer@example.com',
                'customer_name' => $user ? $user->name : 'Valued Customer',
                'shipping_address' => [
                    'street' => $validated['street'],
                    'city' => $validated['city'],
                    'province' => $validated['province'],
                    'postal_code' => $validated['postal_code'],
                    'country' => $validated['country'],
                    'shipping_method_id' => $chosenMethod['id'] ?? 'standard_delivery',
                    'shipping_method_name' => $carrierName,
                    'shipping_delivery_time' => $chosenMethod['delivery_time'] ?? '',
                ],
                'status' => 'packing',
                'dispatch_deadline' => now()->addHours(48),
                'tracking_number' => 'EP' . rand(100000000, 999999999) . 'CA',
                'carrier' => $carrierName,
                'requires_signature' => $totals['requires_signature'],
            ]);

            foreach ($itemsToCreate as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            // Track coupon usage & increment count
            if ($appliedCoupon) {
                CouponUsage::create([
                    'coupon_id' => $appliedCoupon->id,
                    'user_id' => $user ? $user->id : null,
                    'order_id' => $order->id,
                    'discount_amount' => $discount,
                ]);

                $appliedCoupon->increment('used_count');
            }

            // Clear Cart & Coupon Session
            session()->forget('cart');
            session()->forget('coupon');

            return redirect()->route('shop.success', ['order' => $order->id]);
        });
    }

    public function success($orderId = null)
    {
        $order = $orderId ? Order::with(['items.product', 'seller.sellerProfile', 'coupon'])->find($orderId) : null;
        if (!$order) {
            $order = Order::with(['items.product', 'seller.sellerProfile', 'coupon'])->latest()->first() ?? new Order();
        }

        return view('shop.success', compact('order'));
    }

    public function orders()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $orders = Order::with(['items.product', 'seller', 'coupon'])
            ->where('buyer_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('shop.orders', compact('orders'));
    }
}
